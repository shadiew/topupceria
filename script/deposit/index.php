<?php
session_start();
require("../lib/mainconfig.php");

/* CHECK USER SESSION */
if (isset($_SESSION['user'])) {
	$sess_username = $_SESSION['user']['username'];
	$check_user = mysqli_query($db, "SELECT * FROM users WHERE username = '$sess_username'");
	$data_user = mysqli_fetch_assoc($check_user);
	if (mysqli_num_rows($check_user) == 0) {
		header("Location: " . $cfg_baseurl . "/logout/");
	} else if ($data_user['status'] == "Suspended") {
		header("Location: " . $cfg_baseurl . "/logout/");
	}
	$email = $data_user['email'];
	if ($email == "") {
		header("Location: " . $cfg_baseurl . "settings");
	}

	$title = "Deposit";
	include("../lib/header.php");
	$msg_type = "nothing";


	/* DEPOSIT SUBMISSION HANDLER */
	if (isset($_POST['submit'])) {
		$post_method0 = htmlspecialchars($_POST['method']);
		$post_method = htmlspecialchars(filter_var($post_method0, FILTER_SANITIZE_NUMBER_FLOAT));
		$post_quantity = (float)$_POST['quantity'];
		$post_transid = htmlspecialchars($_POST['transid']);
		$bank = mysqli_query($db, "SELECT * FROM deposit_method where id ='$post_method'");
		$ambil = mysqli_fetch_assoc($bank);
		$bankname = $ambil['name_method'];
		$rate_method2 = $ambil['rate'];
		$rate_method = strval($rate_method2);
		$qcheckd = mysqli_query($db, "SELECT * FROM history_topup WHERE username = '$sess_username' AND status = 'NO'");
		$countd = mysqli_num_rows($qcheckd);

		$balance_amount = $post_quantity - ($post_quantity * ($rate_method / 100));

		$demo = $data_user['status'];
		if ($demo == "Demo") {
			$msg_type = "error";
			$msg_content = "Sorry this feature is not available for Demo users.";
		} else if (empty($post_quantity) || empty($post_method) || empty($post_transid)) {
			$msg_type = "error";
			$msg_content = "Please fill in all inputs first." . $post_quantity . $post_method . $post_transid;
		} else if ($countd >= 3) {
			$msg_type = "error";
			$msg_content = "Please Complete the Request for a Previous Deposit to Make a Request for a New Deposit.";
		} else if ($post_quantity < 10000) {
			$msg_type = "error";
			$msg_content = "Minimum deposit is Rp 10.000";
		} else {

			/* GENERATE DEPOSIT ID */
			$check_highest_id = mysqli_query($db, "SELECT * FROM `history_topup` ORDER BY `id_depo` DESC LIMIT 1");
			$highest_id = mysqli_fetch_array($check_highest_id);
			$id_depo = $highest_id['id_depo'] + 1;

			/* UPDATE DEPOSIT HISTORY */
			$insert_topup = mysqli_query($db, "INSERT INTO history_topup (provider, amount, jumlah_transfer, username, user, norek_tujuan_trf, nopengirim, date, time, status, type, id_depo, top_ten, name_method) VALUES ('$post_method','$balance_amount','$post_quantity','$sess_username','$sess_username','$bankname','$post_transid','$date','$time','NO','WEB','$id_depo','ON', '$bankname')");
			if ($insert_topup == TRUE) {
				$msg_type = "success";
				$msg_content = "<b>Request for deposit balance received.</b><br /><b>Method:</b> $bankname<br /><b>Total Transfer:</b> " . rupiah($quantity) . "<br /><b>Details:</b> " . $post_transid . "<br /><b>Obtained Balance:</b> " . rupiah($balance_amount);
				$msg_depo = "Please transfer as big as <span class='color-red'><b>$" . rupiah($quantity) . "</b></span> to [ $bankname ]<br /><span class='color-red'>If the transfer amount does not match then the system will not process your deposit request.</span><br><hr>
							If you have transferred please wait for your balance to increase.<br>
							If the balance is not entered more than 1 day, please contact the admin.";
			} else {
				$msg_type = "error";
				$msg_content = "<b>Failed:</b> System error.";
			}
		}
	}

?>

	<div class="page has-sidebar-left">
		<header class="blue accent-3 relative">
			<div class="container-fluid text-white">
				<div class="row p-t-b-10 ">
					<div class="col">
						<h4>
							<i class="icon-credit-card"></i>
							Deposit
						</h4>
					</div>
				</div>
			</div>
		</header>

		<div class="animatedParent animateOnce">
			<div class="container-fluid my-3">
				<div class="row">
					<div class="col-md-7">
						<div class="card">
							<div class="card-body b-b">
								<h4>Deposit</h4>
								<form name='autoSumForm' role="form" method="POST">

									<!-- MESSAGE NOTIFICATION SYSTEM -->
									<?php
									if ($msg_type == "success") {
									?>
										<div class="alert alert-success alert-dismissible" role="alert">
											<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">×</span>
											</button>
											<strong>Success!</strong> <?php echo $msg_content; ?>
										</div>

										<div class="alert alert-info alert-dismissible" role="alert">
											<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">×</span>
											</button>
											<strong>Info!</strong> <?php echo $msg_depo; ?>
										</div>
									<?php
									} else if ($msg_type == "error") {
									?>
										<div class="alert alert-danger alert-dismissible" role="alert">
											<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">×</span>
											</button>
											<strong>Failed!</strong> <?php echo $msg_content; ?>
										</div>
									<?php
									}
									?>

									<!-- DEPOSIT FORM FIELDS -->
									<div class="form-group">
										<label for="service" class="col-form-label">Payment method</label>
										<select class="form-control" name="method" id="method">
											<option value="">Choose Payment Method...</option>
											<?php
											$check_paket = mysqli_query($db, "SELECT * FROM deposit_method WHERE Active = 'YES' ORDER BY id DESC");
											while ($data_paket = mysqli_fetch_assoc($check_paket)) {
											?>
												<option value="<?php echo $data_paket['id']; ?>"><?php echo $data_paket['name_method']; ?></option>
											<?php
											}
											?>
										</select>
									</div>
									<div id="note">
									</div>
									<div class="form-group">
										<label for="sms" class="col-form-label">Details</label>
										<textarea type="text" name="transid" class="form-control" placeholder="ID Transaction / Sender / Email"></textarea>
									</div>
									<div class="form-group">
										<label for="sms" class="col-form-label">Deposit Amount</label>
										<input type="number" name="quantity" class="form-control" placeholder="Rp" onkeyup="get_total(this.value).value;">
									</div>
									<button type="submit" class="btn btn-primary" name="submit">Deposit</button>
								</form>
							</div>
						</div>
					</div>
					<div class="col-md-5">
						<!-- INFORMATION TAB -->
						<div class="card">
							<div class="card-body b-b">
								<h3>Information</h3>
								<hr>
								<div class="panel-body">
									<?php echo $data_settings['manual_deposit_ins']; ?>
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>

	<script type="text/javascript" src="https://code.jquery.com/jquery-1.10.2.js"></script>
	<script type="text/javascript" src="../js/deposit.js"></script>

<?php
	include("../lib/footer.php");
} else {
	header("Location: " . $cfg_baseurl);
}
?>