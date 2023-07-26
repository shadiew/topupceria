<?php
session_start();
require("../../lib/mainconfig.php");


if (!isset($_SESSION)) {
	session_start();
}
/* CLEARING POST DATA IF EXISTS*/
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
	$_SESSION['postdata'] = $_POST;
	unset($_POST);
	header("Location: " . $_SERVER[REQUEST_URI]);
	exit;
}

if (@$_SESSION['postdata']) {
	$_POST = $_SESSION['postdata'];
	unset($_SESSION['postdata']);
}
//clear

/* CHECK USER SESSION */
if (isset($_SESSION['user'])) {
	$sess_username = $_SESSION['user']['username'];
	$sess_id = $_SESSION['user']['id'];
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
	$title = "Deposit History";
	include("../../lib/header.php");
?>
	<div class="page has-sidebar-left bg-light height-full">
		<header class="blue accent-3 relative nav-sticky">
			<div class="container-fluid text-white">
				<div class="row">
					<div class="col">
						<h3 class="my-3">
							<i class="icon icon-time-is-money-1"></i>
							Deposit History
						</h3>
					</div>
				</div>
			</div>
		</header>
		<div class="container-fluid my-3">
			<div class="row">
				<div class="col-md-12">
					<div class="card my-3 no-b">
						<div class="card-body">
							<div class="table-responsive">
								<div class="card-title">Deposit History</div>
								<table class="table table-bordered table-hover data-tables" data-options='{ "paging": false; "searching":false}'>
									<thead>
										<tr>
											<th>No</th>
											<th>Invoice Number</th>
											<th>Date</th>
											<th>Method</th>
											<th>Amount</th>
											<th>#</th>
											<th>Status</th>
										</tr>
									</thead>
									<tbody>
										<?php
										// start paging config
										$query_order = mysqli_query($db, "SELECT * FROM deposits WHERE user = '$sess_id' ORDER BY id DESC");
										// end paging config

										while ($data_order = mysqli_fetch_assoc($query_order)) {
											if ($data_order['status'] == "Error") {
												$label = "danger";
												$label2 = "Error";
											} else if ($data_order['status'] == "Pending") {
												$label = "warning";
												$label2 = "Pending";
											} else if ($data_order['status'] == "Success") {
												$label = "success";
												$label2 = "Success";
											} else if ($data_order['status'] == "Expired") {
												$label = "secondary";
												$label2 = "Expired";
											}
										?>
											<?php $no = $no + 1; ?>
											<tr>
												<td>
													<center><?php echo $no ?></center>
												</td>
												<td><?php echo $data_order['invoice_number']; ?></td>
												<td><?php echo $data_order['created_at']; ?></td>
												<td><?php echo $data_order['code']; ?></td>
												<td><?php echo rupiah($data_order['balance']); ?></td>
												<td>
													<?php
													if ($data_order['status'] == "Expired") { ?>
														<button type="button" disabled="true" class="btn btn-outline-secondary" onclick="showInstruction(<?php echo $data_order['id']; ?>)">
															<i class="icon light-info-text icon-eye-slash"></i> Cara Bayar
														</button>
													<?php
													} else { ?>
														<button type="button" class="btn btn-outline-info" onclick="showInstruction(<?php echo $data_order['id']; ?>)">
															<i class="icon light-info-text icon-eye"></i> Cara Bayar
														</button>
													<?php
													}
													?>

												</td>
												<td><label class="badge badge-<?php echo $label; ?>"><?php echo $label2; ?></label></td>
											</tr>
										<?php
										}
										?>
									</tbody>
								</table>
							</div>
						</div>
					</div>

					<!-- Modal -->
					<div class="modal fade" id="instructionsModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
						<div class="modal-dialog" role="document">
							<div class="modal-content">
								<div class="modal-header">
									<h2 class="modal-title" id="exampleModalLabel">Cara Pembayaran</h2>
									<button type="button" class="close" data-dismiss="modal" aria-label="Close">
										<span aria-hidden="true">&times;</span>
									</button>
								</div>
								<div class="modal-body">
									<div id="data" style="margin: 10px;"></div>
								</div>
								<div class="modal-footer">
									<button type="button" class="btn btn-primary" data-dismiss="modal">OK</button>
								</div>
							</div>
						</div>
					</div>

				<?php
				include("../../lib/footer.php");
			} else {
				header("Location: " . $cfg_baseurl);
			} ?>

				</div>
			</div>
		</div>
	</div>

	<script>
		function showInstruction(id) {
			if (id == "") {
				document.getElementById("data").innerHTML = "";
				return;
			} else {
				$('#instructionsModal').modal()
				var xmlhttp = new XMLHttpRequest();
				xmlhttp.onreadystatechange = function() {
					if (this.readyState == 4 && this.status == 200) {
						document.getElementById("data").innerHTML = this.responseText;
					}
				};
				xmlhttp.open("GET", "instruction.php?id=" + id, true);
				xmlhttp.send();
			}
		}
	</script>