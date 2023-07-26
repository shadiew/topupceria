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
    $hashed_password = password_hash($post_password, PASSWORD_DEFAULT);
    $insert_user = mysqli_query($db, "INSERT INTO users (email, name, username, password, nohp, balance, level, registered, status, api_key, uplink, otp, point, ip) 
    VALUES ('$post_email', '$post_name', '$post_username', '$hashed_password', '$post_nohp', '0', 'Member', '$date $time', 'Not Active', '$post_apikey', 'Server', '$post_kunci', '0', '$ip')");
    if ($insert_user == true) {
      $to = $post_email;
      $msg = "<hr></hr><br>Hallo <b> $post_username </b>, Please use input this OTP to verify your account<br><br>OTP: <b>$post_kunci<b> <br><br><br><hr></hr><br>You cannot contact this Noreply message, Please Contact Admin Contact Through the Application or via Ticket. <br><br>Thanks.<br><hr></hr>";
      $subject = "Verify Account";
      $headers = "From: SMM PANEL <$email_webmail_forgot> \r\n";
      $headers .= "Cc:$email_webmail_forgot \r\n";
      $headers .= "MIME-Version: 1.0\r\n";
      $headers .= "Content-type: text/html\r\n";
      $send = mail($to, $subject, $msg, $headers);
      if ($send == true) {
        header("Location: $cfg_baseurl/login/verification.php");
      } else {
        $msg_type = "error";
        $msg_content = "<script>swal('Error!', 'Error system (1).', 'error');</script><b>Failed:</b> Error system (1).";
      }
    } else {
      $msg_type = "error";
      $msg_content = "A System Error Occurred.";
    }
  }
}

?>
<!DOCTYPE html>
<html>

<head>
  <meta charset="utf-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <title>Registration Page</title>
  <!-- Tell the browser to be responsive to screen width -->
  <meta name="viewport" content="width=device-width, initial-scale=1">

  <!-- Font Awesome -->
  <link rel="stylesheet" href="plugins/fontawesome-free/css/all.min.css">
  <!-- Ionicons -->
  <link rel="stylesheet" href="https://code.ionicframework.com/ionicons/2.0.1/css/ionicons.min.css">
  <!-- icheck bootstrap -->
  <link rel="stylesheet" href="plugins/icheck-bootstrap/icheck-bootstrap.min.css">
  <!-- Theme style -->
  <link rel="stylesheet" href="dist/css/adminlte.min.css">
  <!-- Google Font: Source Sans Pro -->
  <link href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700" rel="stylesheet">
</head>

<body class="hold-transition register-page">
  <div class="register-box">


    <div class="card">
      <div class="card-body register-card-body">
        <p class="login-box-msg">Register a new membership</p>
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
          <div class="input-group mb-3">
            <input type="text" class="form-control" placeholder="Full name" name="name">
            <div class="input-group-append">
              <div class="input-group-text">
                <span class="fas fa-user"></span>
              </div>
            </div>
          </div>
          <div class="input-group mb-3">
            <input type="text" class="form-control" placeholder="Username" name="username">
            <div class="input-group-append">
              <div class="input-group-text">
                <span class="fas fa-user"></span>
              </div>
            </div>
          </div>
          <div class="input-group mb-3">
            <input type="number" class="form-control" placeholder="Mobile Phone" name="nohp">
            <div class="input-group-append">
              <div class="input-group-text">
                <span class="fas fa-phone"></span>
              </div>
            </div>
          </div>
          <div class="input-group mb-3">
            <input type="email" class="form-control" placeholder="Email" name="email">
            <div class="input-group-append">
              <div class="input-group-text">
                <span class="fas fa-envelope"></span>
              </div>
            </div>
          </div>
          <div class="input-group mb-3">
            <input type="password" class="form-control" placeholder="Password" name="password">
            <div class="input-group-append">
              <div class="input-group-text">
                <span class="fas fa-lock"></span>
              </div>
            </div>
          </div>
          <div class="input-group mb-3">
            <input type="password" class="form-control" placeholder="Retype password" name="confirm">
            <div class="input-group-append">
              <div class="input-group-text">
                <span class="fas fa-lock"></span>
              </div>
            </div>
          </div>
          <div class="row">
            <div class="col-8">
              <div class="icheck-primary">
                <input type="checkbox" id="agreeTerms" name="terms" value="agree">
                <label for="agreeTerms">
                  I agree to the <a href="#">terms</a>
                </label>
              </div>
            </div>
            <!-- /.col -->
            <div class="col-4">
              <button type="submit" class="btn btn-primary btn-block" value="Sign Up" name="signup">Register</button>
            </div>
            <!-- /.col -->
          </div>
        </form>


        <div class="social-auth-links text-center mb-3">
          <p>- OR -</p>
          <a href="index.php" class="btn btn-block btn-primary">
            login
          </a>
        </div>
      </div>
      <!-- /.form-box -->
    </div><!-- /.card -->
  </div>
  <!-- /.register-box -->

  <!-- jQuery -->
  <script src="plugins/jquery/jquery.min.js"></script>
  <!-- Bootstrap 4 -->
  <script src="plugins/bootstrap/js/bootstrap.bundle.min.js"></script>
  <!-- AdminLTE App -->
  <script src="dist/js/adminlte.min.js"></script>
</body>

</html>