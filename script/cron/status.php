<?php
require("../lib/mainconfig.php");

$check_order = mysqli_query($db, "SELECT * FROM orders WHERE status IN ('Checking','Pending','Processing','In Progress') AND provider != 'MANUAL' AND provider != ''");


if (mysqli_num_rows($check_order) == 0) {
  echo ("Pending orders not found.");
} else {
  while ($data_order = mysqli_fetch_assoc($check_order)) {
    $o_oid = $data_order['oid'];
    $o_poid = $data_order['poid'];
    $o_provider = $data_order['provider'];

    $check_provider = mysqli_query($db, "SELECT * FROM provider WHERE code = '$o_provider'");
    $data_provider = mysqli_fetch_assoc($check_provider);

    $p_apikey = $data_provider['api_key'];
    $p_api_id = $data_provider['api_id'];
    $p_link = $data_provider['link'];
    $p_pin = $data_provider['pin'];
    $p_code = $data_provider['code'];


    if (isset($p_pin)) {
      //DAILYPANEL
      $order_postdata = "pin=$p_pin&api_key=$p_apikey&action=status&order_id=$o_poid";
    } else if (isset($p_api_id)) {
      //IRVANKEDE
      $p_link = $p_link . '/status';
      $order_postdata = "api_id=$p_api_id&api_key=$p_apikey&id=$o_poid";
    } else if (!isset($p_api_id) && !isset($p_pin) && $p_code == "SMMTRY") {
      //SMMTRY
      $order_postdata = "api_key=$p_apikey&action=status&id=$o_poid";
    } else {
      $order_postdata = "key=$p_apikey&action=status&order=$o_poid";
    }

    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $p_link);
    curl_setopt($ch, CURLOPT_POST, 1);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $order_postdata);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    $chresult = curl_exec($ch);
    curl_close($ch);
    $order_data = json_decode($chresult, true);

    if (isset($p_pin)) { //DAILYPANEL
      $u_status = $order_data["msg"]['status'];
      $u_start = $order_data["msg"]['jumlah_awal'];
      $u_remains = $order_data["msg"]['jumlah_kurang'];
    } else if (isset($p_api_id)) { //IRVANKEDE
      $u_status = $order_data["data"]['status'];
      $u_start = $order_data["data"]['start_count'];
      $u_remains = $order_data["data"]['remains'];
    } else if (!isset($p_api_id) && !isset($p_pin) && $p_code == "SMMTRY") {
      //SMMTRY
      $u_status = $order_data["data"]['status'];
      $u_start = $order_data["data"]['start_count'];
      $u_remains = $order_data["data"]['remains'];
    } else {
      $u_status = $order_data['status'];
      $u_start = $order_data['start_count'];
      $u_remains = $order_data['remains'];
    }

    if ($u_status == "Pending") {
      $real_status = "Pending";
    } else if ($u_status == "Processing") {
      $real_status = "Processing";
    } else if ($u_status == "In progress") {
      $real_status = "In progress";
    } else if ($u_status == "Partial") {
      $real_status = "Partial";
    } else if ($u_status == "Canceled") {
      $real_status = "Canceled";
    } else if ($u_status == "Completed") {
      $real_status = "Success";
    } else if ($u_status == "Success") {
      $real_status = "Success";
    } else if ($u_status == "Error") {
      $real_status = "Error";
    } else {
      $real_status = "Pending";
    }

    if (empty($u_start)) {
      $u_start = "0";
    }

    $update_order = mysqli_query($db, "UPDATE orders SET status = '$real_status', start_count = '$u_start', remains = '$u_remains' WHERE oid = '$o_oid'");
    $update_order = mysqli_query($db, "UPDATE profit SET status = '$real_status', start_count = '$u_start', remains = '$u_remains' WHERE oid = '$o_oid'");
    if ($update_order == TRUE) {
      echo "order id : $o_oid status: $real_status | start: $u_start | remains: $u_remains provider: $p_code <br>";
    } else {
      echo "Error database." . $real_status . "-----" . $u_start . "-----" . $u_remains . "-----" . $o_oid;
    }
  }
}


// CHECK DEPOSIT PAYMENT STATUS TRIPAY
$check_deposits = mysqli_query($db, "SELECT * FROM deposits WHERE method = 'TRIPAY' AND status = 'Pending'");
if (mysqli_num_rows($check_deposits) == 0) {
  echo ("Pending deposits not found.");
} else {
  while ($data_deposits = mysqli_fetch_assoc($check_deposits)) {
    $depo_expired = $data_deposits["expired_time"];
    $now = time();
    $depo_id = $data_deposits["id"];

    if ($now >= $depo_expired) {
      $update_deposit = mysqli_query($db, "UPDATE deposits SET status = 'Expired' WHERE id = '$depo_id'");
      if ($update_deposit == TRUE) {
        echo "DEPOSIT " . $depo_id . " - UPDATED";
      } else {
        echo "Error database.";
      }
    }
  }
}
