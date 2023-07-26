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
    
    
    include("../lib/header.php");
    ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <!-- Mobile Specific Metas -->
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1" />
    <title><?php echo $data_settings['web_name']; ?> | Deposit</title>
     <!-- Favicon and Touch Icons  -->
     <link rel="shortcut icon" href="<?php echo $data_settings['link_fav']; ?>" />
     <link rel="apple-touch-icon-precomposed" href="<?php echo $data_settings['link_fav']; ?>" />
    <!-- Font -->
    <link rel="stylesheet" href="../storage/fonts/fonts.css" />
    <!-- Icons -->
    <link rel="stylesheet" href="../storage/fonts/icons-alipay.css">
    <link rel="stylesheet" href="../storage/styles/bootstrap.css">

    <link rel="stylesheet" type="text/css" href="../storage/styles/styles.css" />
    <link rel="manifest" href="_manifest.json" data-pwa-version="set_in_manifest_and_pwa_js">
    <link rel="apple-touch-icon" sizes="192x192" href="<?php echo $data_settings['link_fav']; ?>">
    <?php echo $data_settings['seo_analytics']; ?>
</head>

<body class="bg_surface_color">
   <!-- preloade -->
   <div class="preload preload-container">
    <div class="preload-logo">
      <div class="spinner"></div>
    </div>
  </div>
<!-- /preload -->
    <div class="header bg_white_color is-fixed">
        <div class="tf-container">
            <div class="tf-statusbar d-flex justify-content-center align-items-center">
                <a href="#" class="back-btn"> <i class="icon-left"></i> </a>
                <h3>Metode Deposit</h3>
            </div>
        </div>
    </div>
    <div id="app-wrap">
        
        <div class="wrap-banks mt-5">
            <div class="tf-container">
                
                <ul class="bank-box">
                    <li>
                        <a class="bank-list" href="manual_deposit">
                            <img class="logo-bank" src="../storage/images/logo-banks/bank1.png" alt="image">
                            Deposit Manual
                        </a>
                    </li>
                    <li>
                        <a class="bank-list" href="../tripay">
                            <img class="logo-bank" src="../storage/images/logo-banks/bank2.png" alt="image">
                            Otomatis
                        </a>
                    </li>
                    <li>
                        <a class="bank-list" href="#">
                            <img class="logo-bank" src="../storage/images/logo-banks/bank2.png" alt="image">
                            Transfer Bank
                        </a>
                    </li>
                </ul>
            </div>
        </div>
        
    </div>
    


    <script type="text/javascript" src="../storage/javascript/jquery.min.js"></script>
    <script type="text/javascript" src="../storage/javascript/bootstrap.min.js"></script>
    <script type="text/javascript" src="../storage/javascript/main.js"></script>

</body>

</html>
<?php
    
} else {
    header("Location: ".$cfg_baseurl);
}