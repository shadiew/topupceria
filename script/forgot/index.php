<?php
session_start();
require("../lib/mainconfig.php");
$check_settings = mysqli_query($db, "SELECT * FROM settings WHERE id = '1'");
$data_settings = mysqli_fetch_assoc($check_settings);
$msg_type = "nothing";

if (isset($_POST['submit'])) {
    $post_email = trim($_POST['email']);
    $post_username = trim($_POST['username']);
    $check_email = mysqli_query($db, "SELECT * FROM users WHERE email = '$post_email'");
    $data_email = mysqli_fetch_assoc($check_email);
    $check_username = mysqli_query($db, "SELECT * FROM users WHERE username = '$post_username'");
    $data_username = mysqli_fetch_assoc($check_username);
    $nama = $data_username['name'];
    $emailnya = $data_username['email'];
    $check_email = mysqli_query($db, "SELECT * FROM users WHERE email = '$post_email'");
    $data_email = mysqli_fetch_assoc($check_email);
    if (empty($post_email) || empty($post_username)) {
        $msg_type = "error";
        $msg_content = "Please Fill In All Inputs.";
    } else if ($post_email <> $data_email['email']) {
        $msg_type = "error";
        $msg_content = "The Emails You Enter Are Not Registered With Any Account.";
    } else if ($post_username <> $data_username['username']) {
        $msg_type = "error";
        $msg_content = "The Emails You Enter Are Not Registered With Any Account.";
    } else if ($post_email <> $data_username['email']) {
        $msg_type = "error";
        $msg_content = "<script>swal('Error!', 'Email is not appropriate.', 'error');</script> Email is not appropriate.";
    } else {
        $to = $post_email;
        $new_password = random(8);
        $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);
        $msg = "<hr></hr><br>Hallo <b> $post_username </b>,<br>Your account password is <b>$new_password.</b><br><hr></hr><br>You Have Used the Forgot Password Feature, If used without your knowledge, please be careful of any messages that address our Admin and ask for a Screenshot or Request a Password from This Inbox, Our Party Never Requests a Password with Unclear Things<br><hr></hr><br>You cannot contact this Noreply message, Please Contact Admin Contact Through the Application or via Ticket. <br><br>Thanks.<br><hr></hr>";
        $subject = "Forgot Password";
        $headers = "From: SMM PANEL <$email_webmail_forgot> \r\n";
        $headers .= "Cc:$email_webmail_forgot \r\n";
        $headers .= "MIME-Version: 1.0\r\n";
        $headers .= "Content-type: text/html\r\n";
        mail($to, $subject, $msg, $headers);
        $send = mysqli_query($db, "UPDATE users SET password = '$hashed_password' WHERE username = '$post_username'");
        if ($send == true) {
            $msg_type = "success";
            $msg_content = "<b>Success:</b> A password has been sent to your email, please check the Inbox folder or spam folder.";
        } else {
            $msg_type = "error";
            $msg_content = "<script>swal('Error!', 'Error system (1).', 'error');</script><b>Failed:</b> Error system (1).";
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
            <div id="primary" class="content-area circles-bg" data-bg-possition="center" data-bg-repeat="false"">
        <main id=" main" class="site-main">
                <div class="container">
                    <div class="col-xl-8 mx-lg-auto p-t-b-80">
                        <header class="text-center">
                            <h1>Reset Password</h1>
                            <p class="section-subtitle">Fill in the data correctly</p>
                            <img class="p-t-b-50" src="../assets/img/icon/icon-login.png" alt="">
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
                                    <div class="form-group has-icon"><i class="icon-envelope-o"></i>
                                        <input type="text" class="form-control form-control-lg" placeholder="Email Address" name="email">
                                    </div>
                                </div>
                                <div class="col-lg-6">
                                    <div class="form-group has-icon"><i class="icon-user"></i>
                                        <input type="text" class="form-control form-control-lg" placeholder="Username" name="username">
                                    </div>
                                </div>
                                <div class="col-lg-12">
                                    <input type="submit" class="btn btn-danger btn-lg btn-block" value="Reset Password" name="submit">
                                    <center>
                                        <p class="forget-pass">Have you forgot your username or password ?</p>
                                    </center>
                                </div>
                                <div class="col-lg-6">
                                    <a href="<?php echo $cfg_baseurl; ?>/login/" class="btn btn-info btn-lg btn-block">
                                        <i class="icon-sign-in"></i> Sign In
                                    </a>
                                </div> <br />
                                <div class="col-lg-6">
                                    <a href="<?php echo $cfg_baseurl; ?>/register/" class="btn btn-success btn-lg btn-block">
                                        <i class="icon-lock"></i> Sign Up
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
        <!-- Add the sidebar's background. This div must be placed
         immediately after the control sidebar -->
        <div class="control-sidebar-bg shadow white fixed"></div>
    </div>
    <!--/#app -->
    <script src="../assets/js/app.js"></script>
</body>

</html>