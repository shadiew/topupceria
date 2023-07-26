<?php
session_start();
require("../../lib/mainconfig.php");

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

	$title = "Order History";
	include("../../lib/header.php");
?>
	<div class="page has-sidebar-left bg-light height-full">
		<header class="blue accent-3 relative nav-sticky">
			<div class="container-fluid text-white">
				<div class="row">
					<div class="col">
						<h3 class="my-3">
							<i class="icon icon-time-is-money-1"></i>
							Order History
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
								<div class="card-title">Order History</div>
								<table class="table table-bordered table-hover data-tables" data-options='{ "paging": false; "searching":false}'>
									<thead>
										<tr>
											<th width="3%"></th>
											<th width="5%">ID Order</th>
											<th width="10%">Date</th>
											<th width="15%">Service</th>
											<th>Link</th>
											<th>Quantity</th>
											<th width="12%"> Price</th>
											<th>Status</th>
											<th>Refund</th>
										</tr>
									</thead>
									<tbody>
										<?php
										// start paging config
										$query_order = mysqli_query($db, "SELECT * FROM orders WHERE user = '$sess_username' ORDER BY id DESC");
										// end paging config
										while ($data_order = mysqli_fetch_assoc($query_order)) {
											if ($data_order['status'] == "Pending") {
												$label = "warning";
											} else if ($data_order['status'] == "Processing") {
												$label = "info";
											} else if ($data_order['status'] == "In Progress") {
												$label = "info";
											} else if ($data_order['status'] == "Error") {
												$label = "danger";
											} else if ($data_order['status'] == "Canceled") {
												$label = "danger";
											} else if ($data_order['status'] == "Partial") {
												$label = "danger";
											} else if ($data_order['status'] == "Success") {
												$label = "success";
											} else if ($data_order['status'] == "Completed") {
												$label = "success";
											}
										?>
											<tr>
												<td align="center"><?php if ($data_order['place_from'] == "API") { ?><i class="icon icon-random"></i><?php } else { ?><i class="icon icon-globe"></i><?php } ?></td>
												<td><a href="<?php echo $cfg_baseurl; ?>/order/invoice.php?oid=<?php echo $data_order['oid']; ?>"><?php echo $data_order['oid']; ?></a></td>
												<td><?php echo $data_order['date']; ?> <?php echo $data_order['time']; ?></td>
												<td><?php echo $data_order['service']; ?></td>
												<td><?php echo $data_order['link']; ?></td>
												<td><?php echo number_format($data_order['quantity'], 0, ',', '.'); ?></td>
												<td><?php echo rupiah($data_order['price']); ?></td>
												<td align="center"><label class="badge badge-<?php echo $label; ?>"><?php echo $data_order['status']; ?></label></td>
												<td align="center"><label class="badge badge-<?php if ($data_order['refund'] == 0) {
																									echo "danger";
																								} else {
																									echo "success";
																								} ?>"><?php if ($data_order['refund'] == 0) { ?><i class="icon icon-times"></i><?php } else { ?><i class="icon icon-check"></i><?php } ?></label></td>
											</tr>
										<?php
										}
										?>
									</tbody>
								</table>
							</div>
						</div>
					</div>
				</div>

			<?php
			include("../../lib/footer.php");
		} else {
			header("Location: " . $cfg_baseurl);
		}
