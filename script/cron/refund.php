<?php
require("../lib/mainconfig.php");

$check_order = mysqli_query($db, "SELECT * FROM orders WHERE status IN ('Error','Partial','Canceled') AND refund = '0'");

if (mysqli_num_rows($check_order) == 0) {
	die("No Orders Are Error / Partial Status Not Directed yet.");
} else {
	while ($data_order = mysqli_fetch_assoc($check_order)) {
		$o_oid = $data_order['oid'];
		$u_remains = $data_order['remains'];

		$priceone = $data_order['price'] / $data_order['quantity'];
		$priceone_profit = $data_order['price_provider'] / $data_order['quantity'];
		$refund = $priceone * $u_remains;
		$buyer = $data_order['user'];
		$new = "1";
		$yang_udah_masuk = $data_order['quantity'] - $data_order['remains'];
		$harga_masuk = $yang_udah_masuk * $priceone;
		$harga_masuk_profit = $yang_udah_masuk * $priceone_profit;
		if ($u_remains == 0) {
			$refund = $data_order['price'];
		}
		$update_user = mysqli_query($db, "UPDATE users SET balance = balance+$refund WHERE username = '$buyer'");

		$check_balance = mysqli_query($db, "SELECT * FROM users WHERE username = '$buyer'");
		$data_balance = mysqli_fetch_assoc($check_balance);
		$temp_balance = rupiah($data_balance['balance']);

		$update_order = mysqli_query($db, "UPDATE orders SET refund = '1'  WHERE oid = '$o_oid'");
		$update_order = mysqli_query($db, "UPDATE orders SET price = '$harga_masuk', price_provider = '$harga_masuk_profit' WHERE oid = '$o_oid'");
		$update_order = mysqli_query($db, "UPDATE orders SET pengembalian = '$refund'  WHERE oid = '$o_oid'");
		$update_order = mysqli_query($db, "INSERT INTO balance_history (username, action, quantity, clearance, msg, date, time, type) VALUES ('$buyer', 'Add Balance', '$refund', '$temp_balance', 'Order Refund, ID : $o_oid', '$date', '$time', '+ Rp')");
		if ($update_order == TRUE) {
			echo "$o_oid - " . rupiah($refund) . " - Has been Refunded<br />";
		} else {
			echo "Error database.";
		}
	}
}
