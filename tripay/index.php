<?php
session_start();
require("../lib/mainconfig.php");

/* CHECK USER SESSION */
if (isset($_SESSION['user'])) {
  $sess_username = $_SESSION['user']['username'];
  $check_user = mysqli_query($db, "SELECT * FROM users WHERE username = '$sess_username'");
  $data_user = mysqli_fetch_assoc($check_user);
  if (mysqli_num_rows($check_user) == 0) {
    header("Location: " . $cfg_baseurl . "/logout/");
  } else if ($data_user['status'] == "Suspended") {
    header("Location: " . $cfg_baseurl . "/logout/");
  }
  $email = $data_user['email'];
  if ($email == "") {
    header("Location: " . $cfg_baseurl . "settings");
  }


  $check_paket = mysqli_query($db, "SELECT * FROM deposit_method WHERE Active = 'YES' AND name_method = 'TRIPAY'");
  $payment_method = array();

  if (mysqli_num_rows($check_paket) > 0) {
    // output data of each row
    while ($row = mysqli_fetch_assoc($check_paket)) {
      array_push($payment_method, $row);
    }
  }

  /* GENERAL WEB SETTINGS */
  $title = "Deposit";
  include("../lib/header.php");
  $msg_type = "nothing";
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <!-- Mobile Specific Metas -->
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1" />
    <title>top up</title>
    <!-- Favicon and Touch Icons  -->
    <link rel="shortcut icon" href="../storage/images/logo.png" />
    <link rel="apple-touch-icon-precomposed" href="../storage/images/logo.png" />
    <!-- Font -->
    <link rel="stylesheet" href="../storage/fonts/fonts.css" />
    <!-- Icons -->
    <link rel="stylesheet" href="../storage/fonts/icons-alipay.css">
    <link rel="stylesheet" href="../storage/styles/bootstrap.css">
    <link rel="stylesheet" type="text/css" href="../storage/styles/styles.css" />
    <link rel="manifest" href="../storage/_manifest.json" data-pwa-version="set_in_manifest_and_pwa_js">
    <link rel="apple-touch-icon" sizes="192x192" href="../storage/app/icons/icon-192x192.png">
</head>

<body>
     <!-- preloade -->
     <div class="preload preload-container">
        <div class="preload-logo">
          <div class="spinner"></div>
        </div>
      </div>
    <!-- /preload -->
    <div class="app-header st1">
        <div class="tf-container">
            <div class="tf-topbar d-flex justify-content-center align-items-center">
               <a href="#" class="back-btn"><i class="icon-left white_color"></i></a> 
                <h3 class="white_color">Top Up</h3>
            </div>
        </div>
    </div>
    <div class="card-secton topup-content">
        <form method="POST" action="verify.php">
        <div class="tf-container">
            <div class="tf-balance-box">
                <div class="d-flex justify-content-between align-items-center">
                    <p>Saldo Kamu:</p>
                    <?php
                                        if ($data_user['balance'] == "0" or $data_user['balance'] < 0) {
                                        ?>
                    <h3><?php echo rupiah($data_user['balance']); ?></h3>
                    <?php
                                        } ?>
                                        <?php
                                        if ($data_user['balance'] > 0) {
                                        ?>
                    <h3><?php echo rupiah($data_user['balance']); ?></h3>
                    <?php
                                        } ?>
                </div>
                <div class="tf-spacing-16"></div>
                <div class="tf-form">
                    <div class="group-input input-field input-money">
                        <label for="">Jumlah</label>
                        <input type="number" id="amount_field" name="amount" min="10000" required class="search-field value_input st1 form-control" placeholder="Rp. 15.000">
                        <span class="icon-clear"></span>
                    </div>
                </div>
                
            </div>
            <div class="tf-container">
                <?php
                      $check_paket = mysqli_query($db, "SELECT * FROM deposit_method WHERE Active = 'YES' AND name_method = 'TRIPAY' ORDER BY id DESC");
                      while ($data_paket = mysqli_fetch_assoc($check_paket)) {
                      ?>
                <div class="tf-card-block d-flex align-items-center justify-content-between">
                    <div class="inner d-flex align-items-center">
                        <div class="logo-img">
                            <img src="../storage/images/tripay/<?php echo $data_paket['code']; ?>.webp" alt="image">
                        </div>
                        <div class="content">
                            <h4><a href="#" class="fw_6"><?php echo $data_paket['name']; ?></a></h4>
                            <p>Minimal Deposit Rp. 10.000</p>
                        </div>
                    </div>
                    <input class="form-check-input" value="<?php echo $data_paket['code']; ?>" id="<?php echo $data_paket['code']; ?>" name="method" type="radio">
                </div>
                <?php
                      }
                      ?>
            </div>
            <br>
            <div class="tf-container">
                <button type="submit" class="tf-btn accent large" value="Deposit" name="deposit">OKE</button>
            </div>
        </div>
    </form>
    </div>
   
    
   
  
    


    <script type="text/javascript" src="../storage/javascript/jquery.min.js"></script>
    <script type="text/javascript" src="../storage/javascript/bootstrap.min.js"></script>
    <script type="text/javascript" src="../storage/javascript/main.js"></script>
    <script type="text/javascript">
    $(document).ready(function() {
      var paymentMethod = <?php echo json_encode($payment_method) ?>;
      var selectedPayment;
      $('input:radio[name="method"]')
        .change(function() {
          var str = "";
          str += $(this).val();
          selectedPayment = paymentMethod.filter(function(payment) {
            return payment.code == str;
          });
          if (selectedPayment.length > 0) {
            $("#information").html("<span>" + selectedPayment[0]?.note + "</span><br><b>Admin Fee: " + selectedPayment[0]?.rate + "</b><hr><br><h5>Auto Approval</h5>");
          } else {
            $("#information").html("<span>Please Select Payment</span>");
          }
        })
        .change();
    });
  </script>
    
</body>
</html>
<?php
  
} else {
  header("Location: " . $cfg_baseurl);
}
?>