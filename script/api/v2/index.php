<?php
require("../../lib/mainconfig.php");
header("Content-Type: application/json");

if (isset($_REQUEST['key']) AND isset($_REQUEST['action'])) {
	$post_key = mysqli_real_escape_string($db, trim($_REQUEST['key']));
	$post_action = $_REQUEST['action'];
	if (empty($post_key) || empty($post_action)) {
		$array = array("error" => "Incorrect request 1");
	} else {
		$check_user = mysqli_query($db, "SELECT * FROM users WHERE api_key = '$post_key'");
		$data_user = mysqli_fetch_assoc($check_user);
		if (mysqli_num_rows($check_user) == 1) {
			$username = $data_user['username'];
			if ($post_action == "add") {
				if (isset($_REQUEST['service']) AND isset($_REQUEST['link']) AND isset($_REQUEST['quantity'])) {
					$post_service = $_REQUEST['service'];
					$post_link = $_REQUEST['link'];
					$post_quantity = $_REQUEST['quantity'];
					if (empty($post_service) || empty($post_link) || empty($post_quantity)) {
						$array = array("error" => "Incorrect request 2");
					} else {
						$check_service = mysqli_query($db, "SELECT * FROM services WHERE sid = '$post_service' AND status = 'Active'");
						$data_service = mysqli_fetch_assoc($check_service);
							if (mysqli_num_rows($check_service) == 0) {
								$array = array("error" => "Service not found");
							} else {
								$rate = $data_service['price'] / 1000;
								$rate2 = $data_service['price_provider'] / 1000;
								$price = $rate*$post_quantity;
								$price_provider = $rate2*$post_quantity;
								$harga = $data_service['price'];
								$service = $data_service['service'];
								$provider = $data_service['provider'];
								$pid = $data_service['pid'];
								if ($post_quantity < $data_service['min']) {
									$array = array("error" => "Quantity inccorect");
								} else if ($post_quantity > $data_service['max']) {
									$array = array("error" => "Quantity inccorect");
								} else if ($data_user['balance'] < $price) {
									$array = array("error" => "Low balance");
								} else {
									$check_provider = mysqli_query($db, "SELECT * FROM provider WHERE code = '$provider'");
									$data_provider = mysqli_fetch_assoc($check_provider);
									$provider_link = $data_provider['link'];
									$provider_key = $data_provider['api_key'];

									$check_highest_oid = mysqli_query($db, "SELECT * FROM `orders` ORDER BY `oid` DESC LIMIT 1");
									$highest_oid = mysqli_fetch_array($check_highest_oid);
									$oid = $highest_oid['oid'] + 1;
									
									if ($provider == "MANUAL" || empty($provider)) {
										$api_postdata = "";
										$poid = $oid;
									} else {
										$api_postdata = "key=$provider_key&action=add&service=$pid&link=$post_link&quantity=$post_quantity&comments=$post_comment&username=$post_custom_link&usernames=$post_custom_mentions";
										$ch = curl_init();
										curl_setopt($ch, CURLOPT_URL, $provider_link);
										curl_setopt($ch, CURLOPT_POST, 1);
										curl_setopt($ch, CURLOPT_POSTFIELDS, $api_postdata);
										curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
										curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
										$chresult = curl_exec($ch);
										curl_close($ch);
										$json_result = json_decode($chresult);
										$poid = $json_result->order;

										$check_highest_oid = mysqli_query($db, "SELECT * FROM `orders` ORDER BY `oid` DESC LIMIT 1");
										$highest_oid = mysqli_fetch_array($check_highest_oid);
										$oid = $highest_oid['oid'] + 1;
									}

									if (empty($poid)) {
											$array = array("error" => "Server Maintenance");
										}
										else {
											$array = array("order" => "$oid");
											$start_count = 0;
											$update_user = mysqli_query($db, "UPDATE users SET balance = balance-$price WHERE username = '$username'");
											if ($update_user == TRUE) {

												$check_balance = mysqli_query($db, "SELECT * FROM users WHERE username = '$username'");
												$data_balance = mysqli_fetch_assoc($check_balance);
												$temp_balance = number_format($data_balance['balance'], 4);

												$insert_order = mysqli_query($db, "INSERT INTO balance_history (username, action, quantity, clearance, msg, date, time, type) VALUES ('$username', 'Cut Balance', '$price', '$$temp_balance', 'Balance deducted for purchase $post_quantity $service OID : $oid', '$date', '$time', '- $')");				    
												$insert_order = mysqli_query($db, "INSERT INTO orders (oid, poid, user, service, link, quantity, remains, start_count, price, price_provider, status, date, time, provider, place_from, top_ten) VALUES ('$oid', '$poid', '$username', '$service', '$post_link', '$post_quantity', '$post_quantity', '$start_count', '$price', '$price_provider', 'Pending', '$date', '$time', '$provider', 'API', 'ON')");
												$insert_order = mysqli_query($db, "INSERT INTO profit (oid, poid, user, service, link, quantity, remains, start_count, price, price_provider, status, date, time, provider, place_from, datetime) VALUES ('$oid', '$poid', '$username', '$service', '$post_link', '$post_quantity', '$post_quantity', '$start_count', '$price', '$price_provider', 'Pending', '$date', '$time', '$provider', 'API', '$date $time')");
												if ($insert_order == TRUE) {
												} else {
													echo "Penality";
												}
											} else {
												echo "Penality";
											}
									}
								}
							}
					}
				} else {
					$array = array("error" => "Incorrect request 3");
				}
			} else if ($post_action == "status") {
				if (isset($_REQUEST['order'])) {
					$post_poid = $_REQUEST['order'];
					$check_order = mysqli_query($db, "SELECT * FROM orders WHERE oid = '$post_poid' AND user = '$username'");
					$data_order = mysqli_fetch_array($check_order);
					if (mysqli_num_rows($check_order) == 0) {
						$array = array("error" => "Order not found");
					} else {
						$array = array("charge" => $data_order['price'], "start_count" => $data_order['start_count'], "status" => $data_order['status'], "remains" => $data_order['remains']);
					}
				} else {
					$array = array("error" => "Incorrect request 4");
				}
			} else {
				$array = array("error" => "Wrong action");
			}
		} else {
			$array = array("error" => "Invalid API key");
		}
	}
} else {
	$array = array("error" => "Incorrect request 5");
}

$print = json_encode($array);
print_r($print);