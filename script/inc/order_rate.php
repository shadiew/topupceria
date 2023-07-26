<?php
require("../lib/mainconfig.php");

if (isset($_POST['service'])) {
	$post_sid = mysqli_real_escape_string($db, $_POST['service']);
	$check_service = mysqli_query($db, "SELECT * FROM services WHERE sid = '$post_sid' AND status IN ('Active','normal')");
	if (mysqli_num_rows($check_service) == 1) {
		$data_service = mysqli_fetch_assoc($check_service);
		$result = $data_service['price'] / 1000;
		$result = rtrim(sprintf('%.8f', floatval($result)), '0');
		echo $result;
	} else {
		die("0");
	}
} else {
	die("0");
}
