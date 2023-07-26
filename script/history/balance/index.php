<?php
session_start();
require("../../lib/mainconfig.php");

/* CHECK USER SESSION */
if (isset($_SESSION['user'])) {
	$sess_username = $_SESSION['user']['username'];
	$check_user = mysqli_query($db, "SELECT * FROM users WHERE username = '$sess_username'");
	$data_user = mysqli_fetch_assoc($check_user);
	if (mysqli_num_rows($check_user) == 0) {
		header("Location: ".$cfg_baseurl."/logout/");
	} else if ($data_user['status'] == "Suspended") {
		header("Location: ".$cfg_baseurl."/logout/");
	}
	$email = $data_user['email'];
	if ($email == "") {
	header("Location: ".$cfg_baseurl."settings");
	}
	
	$title = "Balance History";
	include("../../lib/header.php");
?>
<div class="page has-sidebar-left bg-light height-full">
    <header class="blue accent-3 relative nav-sticky">
        <div class="container-fluid text-white">
            <div class="row">
                <div class="col">
                    <h3 class="my-3">
                        <i class="icon icon-time-is-money-1"></i>
                        Balance History
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
                        <div class="card-title">Balance History</div>
                        <table class="table table-bordered table-hover data-tables"
                               data-options='{ "paging": false; "searching":false}'>
                            <thead>
                            <tr>
                                <th>No</th>
								<th>Date</th>
								<th>Type</th>
								<th>Amount</th>
								<th>Clearance</th>
								<th>Note</th>
                            </tr>
                            </thead>
                            <tbody>
                            <?php
								// start paging config
								$query_order = mysqli_query($db, "SELECT * FROM balance_history WHERE username = '$sess_username' ORDER BY id DESC");
								// end paging config
												while ($data_order = mysqli_fetch_assoc($query_order)) {
													if($data_order['action'] == "Add Balance") {
													    $label = "success";
														$label2 = "Add Balance";
													} else if($data_order['action'] == "Cut Balance") {
													    $label = "danger";
														$label2 = "Cut Balance";
													} else if($data_order['action'] == "Refunded Balance") {
													    $label = "success";
														$label2 = "Add Balance";
													} else if($data_order['action'] == "Upgrade") {
													    $label = "danger";
														$label2 = "Cut Balance";
													}
												?>
												<?php 
												$no = $no+1; ?>
													<tr>
													    <td><center><?php echo $no ?></center></td>
														<td><?php echo $data_order['date']; ?> <?php echo $data_order['time']; ?></td>
														<td><label class="badge badge-<?php echo $label; ?>"><?php echo $label2; ?></label></td>
														<td><?php echo $data_order['type']; ?><?php echo $data_order['quantity']; ?></td>
														<td><?php echo $data_order['clearance']; ?></td>
														<td><?php echo $data_order['msg']; ?></td>
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
	header("Location: ".$cfg_baseurl);
}