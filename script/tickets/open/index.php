<?php
session_start();
require("../../lib/mainconfig.php");
$msg_type = "nothing";

if (isset($_SESSION['user'])) {
	$sess_username = $_SESSION['user']['username'];
	$check_user = mysqli_query($db, "SELECT * FROM users WHERE username = '$sess_username'");
	$data_user = mysqli_fetch_assoc($check_user);
	if (mysqli_num_rows($check_user) == 0) {
		header("Location: ".$cfg_baseurl."/logout/");
	} else if ($data_user['status'] == "Suspended") {
		header("Location: ".$cfg_baseurl."/logout/");
	}
	include("../../lib/header.php");
	$msg_type = "nothing";

	$post_target = mysqli_real_escape_string($db, $_GET['id']);
	$check_ticket = mysqli_query($db, "SELECT * FROM tickets WHERE id = '$post_target' AND user = '$sess_username'");
	$data_ticket = mysqli_fetch_array($check_ticket);
	$check_reply = mysqli_query($db, "SELECT * FROM tickets_message WHERE ticket_id = '$post_target'");
	if (mysqli_num_rows($check_ticket) == 0) {
		header("Location: ".$cfg_baseurl."ticket/tickets.php");
	} else {
		mysqli_query($db, "UPDATE tickets SET seen_user = '1' WHERE id = '$post_target'");
		if (isset($_POST['submit'])) {
			$post_message = htmlspecialchars($_POST['message']);
			$antibug = (false === strpbrk($post_message, "#$^*[]';{}|<>~")) ? 'Allowed' : "Allowed";
			if ($data_ticket['status'] == "Closed") {
				$msg_type = "error";
				$msg_content = "Ticket has been closed, please create a new ticket.";
			} else if (empty($post_message)) {
				$msg_type = "error";
				$msg_content = "Please Fill in All Inputs.";
			} else if ($antibug == "Disallowed") {
					$msg_type = "error";
					$msg_content = "The Character You Input Is Not Allowed.";
			} else if (strlen($post_message) > 500) {
				$msg_type = "error";
				$msg_content = "Maximum of 500 characters.";
			} else {
               	$check_staff = mysqli_query($db, "SELECT * FROM staff");
            	$data_staff = mysqli_fetch_assoc($check_staff);
	            $ip = $_SERVER['REMOTE_ADDR'];
         		$last_update = "$date $time";
				$insert_ticket = mysqli_query($db, "INSERT INTO tickets_message (ticket_id, sender, user, username_sender, message, datetime, ip) VALUES ('$post_target', '$sess_username', '$sess_username', '$sess_username', '$post_message', '$last_update', '$ip')");
				$update_ticket = mysqli_query($db, "UPDATE tickets SET last_update = '$last_update' WHERE id = '$post_target'");
				if (mysqli_num_rows($check_reply) > 0) {
					mysqli_query($db, "UPDATE tickets SET status = 'Waiting', seen_admin = '0' WHERE id = '$post_target'");
				}
				if ($insert_ticket == TRUE) {
					$msg_type = "success";
					$msg_content = "Ticket Sent.";
				} else {
					$msg_type = "error";
					$msg_content = "<b>Failed:</b> System error.";
				}
			}
		}
	}
?>
<div class="page has-sidebar-left bg-light height-full">
    <header class="blue accent-3 relative nav-sticky">
        <div class="container-fluid text-white">
            <div class="row">
                <div class="col">
                    <h3 class="my-3">
                        <i class="icon icon-message"></i>
                       Ticket Support
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
                        <div class="card-title"><h3 class="panel-title"><i class="mdi mdi-history fa-fw"></i> <?php echo $data_ticket['subject']; ?></h3></div>
<div class="panel-body">
						<?php 
						if ($msg_type == "success") {
						?>
						<div class="alert alert-success alert-dismissible" role="alert">
						<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">×</span>
						</button>
						<strong>Succcess!</strong> <?php echo $msg_content; ?>
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
						<?php
						$usernameAbhi = $data_user['username'];
						$ticketUser = $data_ticket['user'];
						if ($usernameAbhi == $ticketUser){ ?>
									<div class="ticket-box">
						<div class="alert alert-info alert-white text-right">
						    <b><?php echo $data_ticket['user']; ?></b><br /><?php echo nl2br($data_ticket['message']); ?><br /><i class="text-muted font-size-10"><?php echo $data_ticket['datetime']; ?></i>
						</div>			
<?php
	$check_message = mysqli_query($db, "SELECT * FROM tickets_message WHERE ticket_id = '$post_target' ORDER BY `datetime` ASC");
while ($data_message = mysqli_fetch_array($check_message)) {
	if ($data_message['sender'] == "Admin") {
		$msg_alert = "success";
		$msg_text = "";
		$msg_sender = $data_message['sender'];
	} else {
		$msg_alert = "info";
		$msg_text = "text-right";
		$msg_sender = $data_message['user'];
	}
?>
                    	<div class="alert alert-<?php echo $msg_alert; ?> alert-white <?php echo $msg_text; ?>">
						    <b><?php echo $data_message['sender']; ?></b><br /><?php echo nl2br($data_message['message']); ?><br /><i class="text-muted font-size-10"><?php echo $data_message['datetime']; ?></i>
						</div>
<?php
}
?>
					</div></div><br><br>
								<div class="panel-footer">
										<form class="form-horizontal" role="form" method="POST">
											<div class="col-md-12">
													<textarea name="message" class="form-control" placeholder="Message" rows="3"></textarea>
											</div>
											<br>
											<div>
											<a href="<?php echo $cfg_baseurl; ?>/tickets/" class="pull-left btn btn-danger">Back</a>
											<button type="submit" class="pull-right btn btn-success" name="submit">Reply</button>
										</form>
										<div class="clearfix"></div>
									</div>
								</div>
						</div>
                    </div>
<?php
}
?>
                </div>
            </div>

<?php
	include("../../lib/footer.php");
} else {
	header("Location: ".$cfg_baseurl);
}
?>