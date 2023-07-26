<?php
session_start();
require("../lib/mainconfig.php");

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
	
	$title = "Services List";
	include("../lib/header.php");
	?>
<script src="https://unpkg.com/sweetalert/dist/sweetalert.min.js"></script>
<div class="page has-sidebar-left bg-light height-full">
    <header class="blue accent-3 relative nav-sticky">
        <div class="container-fluid text-white">
            <div class="row">
                <div class="col">
                    <h3 class="my-3">
                        <i class="icon icon-list-ul"></i>
                        Services List
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
                        <div class="card-title">Services List</div>
                        <table class="table table-bordered table-hover data-tables"
                               data-options='{ "paging": false; "searching":false}'>
                            <thead>
                            <tr>
                                <th>ID</th>
								<th>Service</th>
								<th>Price/1000</th>
								<th>Min</th>
								<th>Max</th>
								<th>Description</th>
                            </tr>
                            </thead>
                            <tbody>
								<?php
								$query_list = mysqli_query($db, "SELECT * FROM services WHERE status = 'Active'");
								
								// end paging config
								while ($data_service = mysqli_fetch_assoc($query_list)) {
								?>
									<tr>
										<td scope="row"><b><?php echo $data_service['sid']; ?></b></td>
										<td><?php echo $data_service['service']; ?></td>
										<td>$<?php echo $data_service['price']; ?></td>
										<td><?php echo number_format($data_service['min']); ?></td>
										<td><?php echo number_format($data_service['max']); ?></td>
										<td class="text-center"><div class="eye-symbol" onclick="<?php echo htmlspecialchars("swal(\"".$data_service['service']."\", ".json_encode($data_service['note']).")"); ?>"><i class="icon-eye2 eye-blue"></i></div></td>
									</tr>
								<?php
								}
								?>
								</tr>
                            </tbody>
                        </table>
                    </div></div>
                </div>
            </div>
            
<?php
	include("../lib/footer.php");
} else {
	header("Location: ".$cfg_baseurl);
}