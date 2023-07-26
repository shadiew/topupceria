<?php
session_start();
require("../lib/mainconfig.php");
$check_settings = mysqli_query($db, "SELECT * FROM settings WHERE id = '1'");
$data_settings = mysqli_fetch_assoc($check_settings);
$msg_type = "nothing";

if (isset($_POST['signup'])) {
	$post_email = htmlspecialchars(trim($_POST['email']));
	$post_name = htmlspecialchars(trim($_POST['name']));
	$post_username = htmlspecialchars(trim($_POST['username']));
	$post_nohp = htmlspecialchars(trim($_POST['nohp']));
	$post_password = htmlspecialchars(trim($_POST['password']));
	$post_confirm = htmlspecialchars(trim($_POST['confirm']));

	$check_user = mysqli_query($db, "SELECT * FROM users WHERE username = '$post_username'");
	$check_email = mysqli_query($db, "SELECT * FROM users WHERE email = '$post_email'");
	$check_nohp = mysqli_query($db, "SELECT * FROM users WHERE email = '$post_nohp'");
	$ip = $_SERVER['REMOTE_ADDR'];
	if (empty($post_email) || empty($post_username) || empty($post_name) || empty($post_nohp) || empty($post_password)) {
		$msg_type = "error";
		$msg_content = "Input To Fill All.";
	} else if (mysqli_num_rows($check_email) > 0) {
		$msg_type = "error";
		$msg_content = "The Email You Enter is Registered.";
	} else if (mysqli_num_rows($check_user) > 0) {
		$msg_type = "error";
		$msg_content = "The username you entered is already registered.";
	} else if (strlen($post_password) < 5) {
		$msg_type = "error";
		$msg_content = "Minimum 5 characters password.";
	} else if ($post_password <> $post_confirm) {
		$msg_type = "error";
		$msg_content = "Password is not the same.";
	} else {
		$post_apikey = random(20);
		$post_kunci = random(5);
		$ip = $_SERVER['REMOTE_ADDR'];
		$insert_user = mysqli_query($db, "INSERT INTO users (email, name, username, password, nohp, balance, level, registered, status, api_key, uplink, otp, point, ip) VALUES ('$post_email', '$post_name', '$post_username', '$post_password', '$post_nohp', '0', 'Member', '$date $time', 'Active', '$post_apikey', 'Server', '$post_kunci', '0', '$ip')");
		if ($insert_user == true) {
			$msg_type = "success";
			$msg_content = "Account Registered, Please Enter.";
		} else {
			$msg_type = "error";
			$msg_content = "A System Error Occurred.";
		}
	}
}

?>
<!DOCTYPE html>
<html lang="en">

<head>
	<meta charset="UTF-8" />
	<meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
	<meta http-equiv="Content-Language" content="en">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<meta name="description" content="<?php echo $data_settings['web_description']; ?>">
	<meta name="keywords" content="<?php echo $data_settings['seo_keywords']; ?>">
	<title><?php echo $data_settings['web_title']; ?></title>
	<link rel="icon" href="<?php echo $data_settings['link_fav']; ?>" type="image/x-icon">
	<!-- CSS -->

	<!--HEADER TAG-->
	<?php echo $data_settings['seo_meta']; ?>
	<!--HEADER TAG END-->

	<!--GTAG TAG-->
	<?php echo $data_settings['seo_analytics']; ?>
	<!--GTAG TAG END-->

	<link rel="stylesheet" href="../assets/css/app.css">
	<link rel="stylesheet" href="../css/style.css">
	<meta name="theme-color" content="#127AFB" />
</head>

<body class="light">
	<div id="app">
		<div class="light b-t">
			<div id="primary" class="content-area circles-bg" data-bg-possition="center" data-bg-repeat="false" <main id="main" class="site-main">
				<div class="container">
					<div class="col-xl-8 mx-lg-auto p-t-b-80">
						<header class="text-center">
							<a href="<?php echo $cfg_baseurl; ?>">
								<img src="<?php echo $data_settings['link_logo_dark']; ?>" alt="<?php echo $data_settings['web_name']; ?>" class="login-card-logo">
							</a>
							<div class="reg-card-1">
								<h1>Create a new account</h1>
								<p>Join us, the most complete panel in The World</p>
							</div>


						</header>
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
						}
						?>
						<form role="form" method="POST">
							<div class="row">
								<div class="col-lg-6">
									<div class="form-group">
										<input type="text" class="form-control form-control-lg" placeholder="Name" name="name">
									</div>
								</div>
								<div class="col-lg-6">
									<div class="form-group">
										<input type="text" class="form-control form-control-lg" placeholder="Username" name="username">
									</div>
								</div>
								<div class="col-lg-12">
									<div class="form-group">
										<input type="number" class="form-control form-control-lg" placeholder="Mobile No" name="nohp">
									</div>
								</div>
								<div class="col-lg-12">
									<div class="form-group">
										<input type="email" class="form-control form-control-lg" placeholder="Email Address" name="email">
									</div>
								</div>
								<div class="col-lg-6">
									<div class="form-group">
										<input type="password" class="form-control form-control-lg" placeholder="Password" name="password">
									</div>
								</div>
								<div class="col-lg-6">
									<div class="form-group">
										<input type="password" class="form-control form-control-lg" placeholder="Confirm Password" name="confirm">
									</div>
								</div>
								<div class="col-lg-12">
									<input type="submit" class="btn btn-success btn-lg btn-block" value="Sign Up" name="signup">
									<center>
										<p class="forget-pass">A verification email will be sent to your email</p>
									</center>
								</div>
								<div class="col-lg-6">
									<a href="<?php echo $cfg_baseurl; ?>/login/" class="btn btn-info btn-lg btn-block">
										<i class="icon-sign-in"></i> Sign In
									</a>
								</div> <br />
								<div class="col-lg-6">
									<a href="<?php echo $cfg_baseurl; ?>/forgot/" class="btn btn-danger btn-lg btn-block">
										<i class="icon-lock"></i> Reset Password
									</a>
								</div>
							</div>
						</form>
					</div>
				</div>
				</main>
				<!-- #main -->
			</div>
			<!-- #primary -->
		</div>
		<div class="control-sidebar-bg shadow white fixed"></div>
	</div>
	<!--/#app -->
	<script src="../assets/js/app.js"></script>
</body>

</html>