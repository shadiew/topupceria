<?php
session_start();
require("../../lib/mainconfig.php");

if (isset($_SESSION['user'])) {
	$sess_username = $_SESSION['user']['username'];
	$check_user = mysqli_query($db, "SELECT * FROM users WHERE username = '$sess_username'");
	$data_user = mysqli_fetch_assoc($check_user);
	if (mysqli_num_rows($check_user) == 0) {
		header("Location: ".$cfg_baseurl."/logout/");
	} else if ($data_user['status'] == "Suspended") {
		header("Location: ".$cfg_baseurl."/logout/");
	}
    if (isset($_POST['edit'])) {
			$post_all = $_POST['all'];
			$post_pending = $_POST['pending'];
			$post_waiting = $_POST['waiting'];
			$post_responded = $_POST['responded'];

			if($post_all == "on"){
				$update_history_topup = mysqli_query($db, "UPDATE tickets SET status = 'Closed' WHERE user ='$sess_username'");
			}else if($post_pending == "on"){
				$update_history_topup = mysqli_query($db, "UPDATE tickets SET status = 'Closed' WHERE status = 'Pending' AND user ='$sess_username'");
			}else if($post_waiting == "on"){
				$update_history_topup = mysqli_query($db, "UPDATE tickets SET status = 'Closed' WHERE status = 'Waiting' AND user ='$sess_username'");
			}else if($post_responded == "on"){
				$update_history_topup = mysqli_query($db, "UPDATE tickets SET status = 'Closed' WHERE status = 'Responded' AND user ='$sess_username'");
			}
							
				if ($update_history_topup == TRUE) {
					$msg_type = "success";
					$msg_content = "Ticket has been closed.";
				} else {
					$msg_type = "error";
					$msg_content = "Try More Moments.";
				}
		}
$title = "Ticket Support";
include("../../lib/header.php");
?>
<div class="page has-sidebar-left height-full">
    <header class="blue accent-3 relative nav-sticky">
        <div class="container-fluid text-white">
            <div class="row p-t-b-10 ">
                <div class="col">
                    <h4>
                        <i class="icon icon-message"></i>
                        Ticket Support
                    </h4>
                </div>
            </div>
        </div>
    </header>
    <div class="container-fluid relative animatedParent animateOnce">
        <div class="tab-content pb-3" id="v-pills-tabContent">
            <!--Today Tab Start-->
            <div class="tab-pane animated fadeInUpShort show active">
                <div class="row my-3">
                    <div class="col-md-12">
                        <div class="white p-5 r-5">
                             <div class="panel-body">
                                        <?php 
										if ($msg_type == "success") {
										?>
										<div class="alert alert-success alert-dismissable">
                                        <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
                                            <h4><i class="fa fa-check-circle"></i> <strong>Success!</strong></h4>
											<p><?php echo $msg_content; ?></p>
                                        </div>
										<?php
										} else if ($msg_type == "error") {
										?>
										<div class="alert alert-danger alert-dismissable">
                                        <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
                                            <h4><i class="fa fa-times-circle"></i> <strong>Ups!</strong></h4>
											<p><?php echo $msg_content; ?></p>
                                        </div>
										<?php
										}
										?>
										<form class="form-horizontal" role="form" method="POST" action="<?php echo $_SERVER['PHP_SELF']; ?>">
										<center><b>Select the tickets you want to close.</b><br>
										<br>
										<div class="ticket-close-box">
											<input type="checkbox" name="all" onclick="toggle(this);" />All Tickets<br />
											<input type="checkbox" name="pending" />Pending Tickets<br />
											<input type="checkbox" name="waiting" />Waiting Tickets<br />
											<input type="checkbox" name="responded" />Responded Tickets<br />
										</div>
										<br><br>
										<a href="<?php echo $cfg_baseurl; ?>/tickets/" class="pull-right btn btn-success"><i class="fa fa-edit"></i> Back</a>
											<button type="submit" class="pull-right btn btn-danger" name="edit"><i class="fa fa-send"></i> Close the Ticket</button>
											</center>
										</form>
							</div>
                        </div>
                    </div>
                </div>
            </div>
            <br>
        </div>
    </div>
</div>
<script type="text/javascript" src="../../js/ticket.js"></script>
<?php
include("../../lib/footer.php");
}
?>