<?php
session_start();
require("../lib/mainconfig.php");
$check_settings = mysqli_query($db, "SELECT * FROM settings WHERE id = '1'");
$data_settings = mysqli_fetch_assoc($check_settings);
$msg_type = "nothing";

if (isset($_POST['login'])) {
  $post_username = htmlspecialchars(trim($_POST['username']));
  $post_password = htmlspecialchars(trim($_POST['password']));
  $ip = $_SERVER['REMOTE_ADDR'];
  if (empty($post_username) || empty($post_password)) {
    $msg_type = "error";
    $msg_content = "Please Fill In All Inputs.";
  } else {
    $check_user = mysqli_query($db, "SELECT * FROM users WHERE username = '$post_username'");
    if (mysqli_num_rows($check_user) == 0) {
      $msg_type = "error";
      $msg_content = "The username you entered is not registered.";
    } else {
      $data_user = mysqli_fetch_assoc($check_user);
      if (password_verify($post_password, $data_user['password'])) {
        $verified = true;
      } else {
        $verified = false;
      }

      if ($data_user['level'] == "Developers" && !$verified) {
        $ip = $_SERVER['REMOTE_ADDR'];
        $msg_type = "error";
        $msg_content = "The Password You Enter Is Wrong.";
      } else if (!$verified) {
        $msg_type = "error";
        $msg_content = "The Password You Enter Is Wrong!.";
      } else if ($data_user['status'] == "Suspended") {
        $msg_type = "error";
        $msg_content = "Account Suspended.";
      } else if ($data_user['status'] == "Not Active") {
        header("Location: " . $cfg_baseurl . "/login/verification.php");
      } else {
        $_SESSION['user'] = $data_user;
        header("Location: " . $cfg_baseurl);
      }
    }
  }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <!-- Mobile Specific Metas -->
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1" />
    <title><?php echo $data_settings['web_name']; ?> | Login</title>
    <!-- Favicon and Touch Icons  -->
    <link rel="shortcut icon" href="<?php echo $data_settings['link_fav']; ?>" />
    <link rel="apple-touch-icon-precomposed" href="<?php echo $data_settings['link_fav']; ?>" />
    <!-- Font -->
    <link rel="stylesheet" href="<?php echo $cfg_baseurl; ?>/storage/fonts/fonts.css" />
    <!-- Icons -->
    <link rel="stylesheet" href="<?php echo $cfg_baseurl; ?>/storage/fonts/icons-alipay.css">
    <link rel="stylesheet" href="<?php echo $cfg_baseurl; ?>/storage/styles/bootstrap.css">

    <link rel="stylesheet" type="text/css" href="<?php echo $cfg_baseurl; ?>/storage/styles/styles.css" />
    <link rel="manifest" href="<?php echo $cfg_baseurl; ?>/storage/_manifest.json" data-pwa-version="set_in_manifest_and_pwa_js">
    <link rel="apple-touch-icon" sizes="192x192" href="<?php echo $data_settings['link_fav']; ?>">
    <?php echo $data_settings['seo_meta']; ?>
    <?php echo $data_settings['seo_analytics']; ?>  

</head>

<body>
     <!-- preloade -->
     <div class="preload preload-container">
        <div class="preload-logo">
          <div class="spinner"></div>
        </div>
      </div>
    <!-- /preload -->    
    <div class="mt-7 login-section">
        <div class="tf-container">
            <form class="tf-form" method="POST">
                    <h1>Login</h1>
                    <div class="group-input">
                        <label>Email/Username</label>
                        <input type="text" placeholder="Example@gmail" name="username">
                    </div>
                    <div class="group-input auth-pass-input last">
                        <label>Password</label>
                        <input type="password" class="password-input" placeholder="Password" name="password">
                        <a class="icon-eye password-addon" id="password-addon"></a>
                    </div>
                    <a href="08_reset-password.html" class="auth-forgot-password mt-3">Forgot Password?</a>

                <button type="submit" name="login" class="tf-btn accent large">Log In</button>

            </form>
            
            
            <p class="mb-9 fw-3 text-center ">Already have a Account? <a href="05_register.html" class="auth-link-rg" >Sign up</a></p>
        </div>
    </div>
    
  <script type="text/javascript" src="<?php echo $cfg_baseurl; ?>/storage/javascript/jquery.min.js"></script>
    <script type="text/javascript" src="<?php echo $cfg_baseurl; ?>/storage/javascript/bootstrap.min.js"></script>
    <script type="text/javascript" src="<?php echo $cfg_baseurl; ?>/storage/javascript/password-addon.js"></script>
    <script type="text/javascript" src="<?php echo $cfg_baseurl; ?>/storage/javascript/main.js"></script>
    <script type="text/javascript" src="<?php echo $cfg_baseurl; ?>/storage/javascript/init.js"></script>
</body>
</html>