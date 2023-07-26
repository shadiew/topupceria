<?php
session_start();
require("../lib/mainconfig.php");

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

/* NEW TICKET HANDLER */
if (isset($_POST['submit'])) {
		$post_subject = htmlspecialchars($_POST['subject']);
		$post_message = htmlspecialchars($_POST['message']);

		$antibug2 = (false === strpbrk($post_message, "#$^*[]';{}|<>~")) ? 'Allowed' : "Allowed";
		$antibug = (false === strpbrk($post_subject, "#$^*[]';{}|<>~")) ? 'Allowed' : "Allowed";
	      $ip = $_SERVER['REMOTE_ADDR'];
		if (empty($post_subject) || empty($post_message)) {
			$msg_type = "error";
			$msg_content = "<b>Failed :</b> Please fill all input.";
		} else if (strlen($post_subject) > 200) {
			$msg_type = "error";
			$msg_content = "<b>Failed :</b> Maximum subject is 200 characters.";
		} else if ($antibug == "Disallowed" OR $antibug2 == "Disallowed") {
					$msg_type = "error";
					$msg_content = "The Character You Input Is Not Allowed.";
		} else if (strlen($post_message) > 500) {
			$msg_type = "error";
			$msg_content = "<b>Failed :</b> Maximum message is 500 characters.";
		} else if (strlen($post_message) < 20) {
			$msg_type = "error";
			$msg_content = "<b>Failed :</b> Minimum Message Is 20 Characters.";
		} else {
            $ip = $_SERVER['REMOTE_ADDR'];
			$insert_ticket = mysqli_query($db, "INSERT INTO tickets (user, subject, message, datetime, last_update, status, ip) VALUES ('$sess_username', '$post_subject', '$post_message', '$date $time', '$date $time', 'Pending', '$ip')");
			if ($insert_ticket == TRUE) {
				$msg_type = "success";
				$msg_content = "<b>Success :</b> Ticket Has Been Sent.";
			} else {
				$msg_type = "error";
				$msg_content = "<b>Failed :</b> System error.";
			}
		}
}	
	$check_user = mysqli_query($db, "SELECT * FROM users WHERE username = '$sess_username'");
	$data_user = mysqli_fetch_assoc($check_user);
$title = "Ticket Support";
include("../lib/header.php");
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
                            <div class="card-title">
                                <h5> Ticket Support</h5>
                            </div>
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
										 <div class="panel-body">
										<form class="form-horizontal" role="form" method="POST" action="<?php echo $_SERVER['PHP_SELF']; ?>">
											<div class="form-group">
												<label class="control-label">Subject</label>
												<div>
													<input type="text" name="subject" class="form-control" placeholder="Subject">
												</div>
											</div>
											<div class="form-group">
												<label class="control-label">Message</label>
												<div>
													<textarea name="message" class="form-control" placeholder="Message" rows="3"></textarea>
												</div>
											</div>
											<right><button type="submit" class="pull-right btn btn-success" name="submit"><i class="fa fa-send"></i> Send</button></right>
										</form>
								</div>
							</div>
                        </div>
                    </div>
                </div>
                <div class="row my-3">
                    <div class="col-md-12">
                        <div class="white p-5 r-5">
                            <div class="card-title">
                                <h5> Ticket History</h5>
                            </div>
                            <div class="panel-body">
                				<right><a href="<?php echo $cfg_baseurl; ?>/tickets/close/" class="pull-right btn btn-danger"><i class="fa fa-edit"></i> Close the Ticket</a></right>
							   <br><br>
										<div class="table-responsive">
										<table class="table table-bordered table-hover data-tables"
                               				data-options='{ "paging": false; "searching":false}'>
												<thead>
													<tr>
														<th>Date</th>
														<th>Status</th>
														<th>Subject</th>
														<th>Last Update</th>
													</tr>
												</thead>
												<tbody>
													<?php
													// start paging config
													$query_parent = "SELECT * FROM tickets WHERE user = '$sess_username' ORDER BY id DESC"; // edit
													$records_per_page = 10; // edit

													$starting_position = 0;
													if(isset($_GET["page_no"])) {
														$starting_position = ($_GET["page_no"]-1) * $records_per_page;
													}
													$new_query = $query_parent." LIMIT $starting_position, $records_per_page";
													$new_query = mysqli_query($db, $new_query);
													$now_records = mysqli_num_rows($new_query);
													// end paging config

													if ($now_records == 0) {
													?>
												<tr>
													<td colspan="3">No data</td>
												</tr>
													<?php
													} else {
														while ($data_show = mysqli_fetch_assoc($new_query)) {
															if($data_show['status'] == "Closed") {
																$label = "danger";
															} else if($data_show['status'] == "Responded") {
																$label = "success";
															} else if($data_show['status'] == "Waiting") {
																$label = "info";
															} else {
																$label = "warning";
															}
													?>
													<tr>
													    <td><?php echo $data_show['datetime']; ?></td>
														<td align=""><span class="badge badge-<?php echo $label; ?>"><?php echo $data_show['status']; ?></span></td>
														<td><?php if($data_show['seen_user'] == 0) { ?><label class="badge badge">NEW!</label><?php } ?> <a href="<?php echo $base_url; ?>/tickets/open/?id=<?php echo $data_show['id']; ?>"><?php echo $data_show['subject']; ?></a></td>
														<td><?php echo $data_show['last_update']; ?></td>
													</tr>
													<?php
														}
													}
													?>
												</tbody>
											</table>
										</div>
									</div>
                        </div>
                    </div>
                </div>
            </div>
            <br>
        </div>
    </div>
</div>
<?php
	include("../lib/footer.php");
} else {
	header("Location: ".$cfg_baseurl);
}
?>