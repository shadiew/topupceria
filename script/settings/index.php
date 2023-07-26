<?php
session_start();
require("../lib/mainconfig.php");

if (isset($_SESSION['user'])) {
	$sess_username = $_SESSION['user']['username'];
	$check_user = mysqli_query($db, "SELECT * FROM users WHERE username = '$sess_username'");
	$data_user = mysqli_fetch_assoc($check_user);
	$email = $data_user['email'];
	$demo = $data_user['status'];
	$hp = $data_user['nohp'];
	$nama = $data_user['name'];
	if (mysqli_num_rows($check_user) == 0) {
		header("Location: " . $cfg_baseurl . "/logout/");
	} else if ($data_user['status'] == "Suspended") {
		header("Location: " . $cfg_baseurl . "/logout/");
	}
	$title = "Settings";
	include("../lib/header.php");
	$msg_type = "nothing";


	if (isset($_POST['change_pswd'])) {
		$post_password = htmlspecialchars(trim($_POST['password']));
		$post_npassword = htmlspecialchars(trim($_POST['npassword']));
		$post_cnpassword = htmlspecialchars(trim($_POST['cnpassword']));
		$hashed_password = password_hash($post_password, PASSWORD_DEFAULT);
		$hashed_password_new = password_hash($post_npassword, PASSWORD_DEFAULT);
		if ($demo == "Demo") {
			$msg_type = "error";
			$msg_content = "Sorry this feature is not available for Demo users";
		} else if (empty($post_password) || empty($post_npassword) || empty($post_cnpassword)) {
			$msg_type = "error";
			$msg_content = "Please Fill In All Inputs.";
		} else if (password_verify($post_password, $data_user['password']) == false) {
			$msg_type = "error";
			$msg_content = "The Password You Enter Is Wrong.";
		} else if (strlen($post_npassword) < 6) {
			$msg_type = "error";
			$msg_content = "New password is too short, at least 6 characters.";
		} else if ($post_cnpassword <> $post_npassword) {
			$msg_type = "error";
			$msg_content = "Confirm New Password Not Correct.";
		} else {
			$update_user = mysqli_query($db, "UPDATE users SET password = '$hashed_password_new' WHERE username = '$sess_username'");
			if ($update_user == TRUE) {
				$msg_type = "success";
				$msg_content = "Password has been changed to <b>$post_npassword</b>.";
			} else {
				$msg_type = "error";
				$msg_content = "A System Error Occurred.";
			}
		}
	} else if (isset($_POST['change_api'])) {
		$set_api_key = random(20);
		$update_user = mysqli_query($db, "UPDATE users SET api_key = '$set_api_key' WHERE username = '$sess_username'");
		if ($update_user == TRUE) {
			$msg_type = "success";
			$msg_content = "Key API has been changed to <b>$set_api_key</b>.";
		} else {
			$msg_type = "error";
			$msg_content = "A System Error Occurred.";
		}
	} else if (isset($_POST['change_profile'])) {
		$post_email = htmlspecialchars(trim($_POST['emailn']));
		$post_password = htmlspecialchars(trim($_POST['password']));
		$post_nama = htmlspecialchars(trim($_POST['nama']));
		$check_email = mysqli_query($db, "SELECT * FROM users WHERE email = '$post_email'");
		if ($demo == "Demo") {
			$msg_type = "error";
			$msg_content = "Sorry this feature is not available for Demo users.";
		} else if (empty($post_email) || empty($post_password) || empty($post_nama)) {
			$msg_type = "error";
			$msg_content = "Please Fill In All Inputs.";
		} else if (mysqli_num_rows($check_email) > 0 && ($post_email <> $data_user['email'])) {
			$msg_type = "error";
			$msg_content = "The Email You Enter is Registered.";
		} else if (password_verify($post_password, $data_user['password']) == false) {
			$msg_type = "error";
			$msg_content = "Wrong Password Confirmation.";
		} else {
			$update_user = mysqli_query($db, "UPDATE users SET email = '$post_email' WHERE username = '$sess_username'");
			$update_user = mysqli_query($db, "UPDATE users SET name = '$post_nama' WHERE username = '$sess_username'");
			if ($update_user == TRUE) {
				$msg_type = "success";
				$msg_content = "Email has been changed to <b>$post_email</b>.";
			} else {
				$msg_type = "error";
				$msg_content = "A System Error Occurred.";
			}
		}
	}

	$check_user = mysqli_query($db, "SELECT * FROM users WHERE username = '$sess_username'");
	$data_user = mysqli_fetch_assoc($check_user);
?>

	<div class="page has-sidebar-left">
		<div>
			<header class="blue accent-3 relative">
				<div class="container-fluid text-white">

					<div class="row">
						<ul class="nav nav-material nav-material-white responsive-tab" id="v-pills-tab" role="tablist">
							<li>
								<a class="nav-link active" id="profile-tab" data-toggle="pill" href="#profile" role="tab" aria-controls="profile"><i class="icon icon-user"></i>Profile</a>
							</li>
							<li>
								<a class="nav-link" id="password-tab" data-toggle="pill" href="#password" role="tab" aria-controls="password" aria-selected="false"><i class="icon icon-lock"></i>Password</a>
							</li>
							<li>
								<a class="nav-link" id="apikey-tab" data-toggle="pill" href="#apikey" role="tab" aria-controls="apikey" aria-selected="false"><i class="icon icon-random"></i>API Key</a>
							</li>
						</ul>
					</div>

				</div>
			</header>

			<div class="container-fluid animatedParent animateOnce my-3">
				<div class="animated fadeInUpShort">
					<div class="tab-content" id="v-pills-tabContent">
						<div class="tab-pane fade show active" id="profile" role="tabpanel" aria-labelledby="profile-tab">
							<?php
							if ($msg_type == "success") {
							?>
								<div class="alert alert-success alert-dismissible" role="alert">
									<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">×</span>
									</button>
									<strong>Success!</strong> <?php echo $msg_content; ?>
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
							} else if ($email == "") {
							?>
								<div class="alert alert-danger alert-dismissible" role="alert">
									<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">×</span>
									</button>
									<strong>Failed!</strong> Please update your email.
								</div>
							<?php
							}
							?>
							<div class="row">
								<div class="col-md-3">
									<div class="card ">

										<ul class="list-group list-group-flush">
											<li class="list-group-item"><i class="icon icon-mobile text-primary"></i><strong class="s-12">Phone</strong> <span class="float-right s-12"><?php echo $data_user['nohp']; ?></span></li>
											<li class="list-group-item"><i class="icon icon-mail text-success"></i><strong class="s-12">Email</strong> <span class="float-right s-12"><?php echo $data_user['email']; ?></span></li>
											<li class="list-group-item"><i class="icon icon-address-card-o text-warning"></i><strong class="s-12">Name</strong> <span class="float-right s-12"><?php echo $data_user['name']; ?></span></li>
											<li class="list-group-item"><i class="icon icon-web text-danger"></i> <strong class="s-12">Level</strong> <span class="float-right s-12"><?php echo $data_user['level']; ?></span></li>
										</ul>
									</div>
									<div class="card mt-3 mb-3">
										<div class="card-header bg-white">
											<strong class="card-title">Contact</strong>

										</div>
										<ul class="no-b">
											<?php
											$check_staff = mysqli_query($db, "SELECT * FROM staff ORDER BY level ASC");
											while ($data_staff = mysqli_fetch_assoc($check_staff)) {
											?>
												<li class="list-group-item">
													<a href="<?php echo $cfg_baseurl; ?>/contact">
														<div class="image mr-3  float-left">
															<img class="user_avatar" src="<?php echo $cfg_baseurl; ?>/assets/img/dummy/u1.png" alt="User Image">
														</div>
														<h6 class="p-t-10"><?php echo $data_staff['name']; ?></h6>
														<span><?php echo $data_staff['nomor']; ?></span>
													</a>
												</li>
											<?php
											}
											?>
										</ul>
									</div>
								</div>
								<div class="col-md-9">
									<div class="row">
										<div class="col-md-12">
											<div class="card">
												<div class="card-header white">
													<h6>Profile </h6>
												</div>
												<div class="card-body">
													<form class="form-horizontal" role="form" method="POST">
														<div class="form-group">
															<label class="control-label">Name</label>
															<?php if ($nama == "") { ?>
																<input type="text" name="nama" class="form-control" placeholder="Enter Full Name">
															<?php } else { ?>
																<input type="nama" name="nama" class="form-control" value="<?php echo $data_user['name']; ?>">
															<?php } ?>
														</div>
														<div class="form-group">
															<label class="control-label">Username</label>
															<input type="username" class="form-control" value="<?php echo $data_user['username']; ?>" readonly>
														</div>
														<div class="form-group">
															<label class="control-label">Email</label>
															<input type="email" class="form-control" value="<?php echo $data_user['email']; ?>" readonly>
														</div>
														<div class="form-group">
															<label class="control-label">Mobile Number</label>
															<input type="hp" class="form-control" value="<?php echo $data_user['nohp']; ?>" readonly>
														</div>
														<div class="form-group">
															<label class="control-label">Level</label>
															<input type="hp" class="form-control" value="<?php echo $data_user['level']; ?>" readonly>
														</div>
														<div class="form-group">
															<label class="control-label">New email</label>
															<input type="email" name="emailn" class="form-control" placeholder="New email">
														</div>
														<div class="form-group">
															<label class="control-label">Password Confirmation</label>
															<input type="password" name="password" class="form-control" placeholder="Password Confirmation">
														</div>
														<button type="submit" class="pull-right btn btn-success btn-bordered waves-effect w-md waves-light" name="change_profile">Update Profile</button>
													</form>
												</div>
											</div>
										</div>
									</div>
								</div>
							</div>
						</div>

						<div class="tab-pane fade" id="password" role="tabpanel" aria-labelledby="password-tab">
							<div class="col-md-12">
								<div class="card">
									<div class="card-header white">
										<h6>Change Password</h6>
									</div>
									<div class="card-body">
										<form class="form-horizontal" role="form" method="POST">
											<div class="form-group">
												<label class="control-label">Old password</label>
												<input type="password" name="password" class="form-control" placeholder="Old password">
											</div>
											<div class="form-group">
												<label class="control-label">New password</label>
												<input type="password" name="npassword" class="form-control" placeholder="New password">
											</div>
											<div class="form-group">
												<label class="control-label">Confirm New Password</label>
												<input type="password" name="cnpassword" class="form-control" placeholder="Confirm New Password">
											</div>
											<button type="submit" class="pull-right btn btn-success btn-bordered waves-effect w-md waves-light" name="change_pswd">Change Password</button>
										</form>
									</div>
								</div>
							</div>
						</div>
						<div class="tab-pane fade" id="apikey" role="tabpanel" aria-labelledby="apikey-tab">

							<div class="col-md-12">
								<div class="card">
									<div class="card-header white">
										<h6>API Key</h6>
									</div>
									<div class="card-body">
										<form class="form-horizontal" role="form" method="POST">
											<div class="form-group">
												<label class="col-md-2 control-label">API Key</label>
												<div class="col-md-10">
													<input type="text" class="form-control" value="<?php echo $data_user['api_key']; ?>" readonly>
												</div>
											</div>
											<button type="submit" class="pull-right btn btn-success btn-bordered waves-effect w-md waves-light" name="change_api">Change API Key</button>
										</form>
									</div>
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>

		</div>
	</div>

<?php
	include("../lib/footer.php");
} else {
	header("Location: " . $cfg_baseurl);
}
?>