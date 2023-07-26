<?php
session_start();
require("../lib/mainconfig.php");
$check_settings = mysqli_query($db, "SELECT * FROM settings WHERE id = '1'");
$data_settings = mysqli_fetch_assoc($check_settings);
$msg_type = "nothing";

if (isset($_POST['verify'])) {
  $code = $_POST['code'];

  $check_user = mysqli_query($db, "SELECT email, status, otp FROM users WHERE otp = '$code'");
  $data_user = mysqli_fetch_assoc($check_user);

  if (isset($data_user)) {
    $user_email = $data_user["email"];
    $update_status = mysqli_query($db, "UPDATE users SET status = 'Active' WHERE email = '$user_email'");
    $msg_type = "success";
    $msg_content = "Verification Success, You will be directed to login page in 5 second. <br>If not <a href='$cfg_baseurl/login' style='text-decoration: underline;font-weight: bold;'>Click here!</a>";
    header("refresh:5;url=$cfg_baseurl/login");
  } else {
    $msg_type = "error";
    $msg_content = "Please check verification code on email.";
  }
}

?>
<!DOCTYPE html>
<html>

<head>
  <meta charset="utf-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <title>Verification Page</title>
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
  <style>
    body {
      background-color: #cfdffc;
    }

    .main-verification-input {
      background: #fff;
      padding: 0 120px 0 0;
      border-radius: 1px;
      margin-top: 6px
    }

    .fl-wrap {
      float: left;
      width: 100%;
      position: relative;
      border-radius: 4px
    }

    .main-verification-input:before {
      content: '';
      position: absolute;
      bottom: -40px;
      width: 50px;
      height: 1px;
      background: rgba(255, 255, 255, 0.41);
      left: 50%;
      margin-left: -25px
    }

    .main-verification-input-item {
      float: left;
      width: 100%;
      box-sizing: border-box;
      border-right: 1px solid #eee;
      height: 50px;
      position: relative
    }

    .main-verification-input-item input:first-child {
      border-radius: 100%
    }

    .main-verification-input-item input {
      float: left;
      border: none;
      width: 100%;
      height: 50px;
      padding-left: 20px
    }

    .main-verification-button {
      background: #4DB7FE
    }

    .main-verification-button {
      position: absolute;
      right: 0px;
      height: 50px;
      width: 120px;
      color: #fff;
      top: 0;
      border: none;
      border-top-right-radius: 4px;
      border-bottom-right-radius: 4px;
      cursor: pointer
    }

    .main-verification-input-wrap {
      max-width: 500px;
      margin: 20px auto;
      position: relative;
      margin-top: 129px
    }

    .main-verification-input-wrap ul {
      background-color: #fff;
      padding: 27px;
      color: #757575;
      border-radius: 4px
    }

    a {
      text-decoration: none !important;
      color: #9C27B0
    }

    :focus {
      outline: 0
    }

    @media only screen and (max-width: 768px) {
      .main-verification-input {
        background: rgba(255, 255, 255, 0.2);
        padding: 14px 20px 10px;
        border-radius: 10px;
        box-shadow: 0px 0px 0px 10px rgba(255, 255, 255, 0.0)
      }

      .main-verification-input-item {
        width: 100%;
        border: 1px solid #eee;
        height: 50px;
        border: none;
        margin-bottom: 10px
      }

      .main-verification-input-item input {
        border-radius: 6px !important;
        background: #fff
      }

      .main-verification-button {
        position: relative;
        float: left;
        width: 100%;
        border-radius: 6px
      }
    }
  </style>
</head>

<body>
  <?php
  if ($msg_type == "success") {
  ?>
    <div class="alert alert-success alert-dismissible" role="alert" style="text-align: center;">
      <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">×</span>
      </button>
      <strong>Success!</strong> <?php echo $msg_content; ?>
    </div>
  <?php
  } else if ($msg_type == "error") {
  ?>
    <div class="alert alert-danger alert-dismissible" role="alert" style="text-align: center;">
      <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">×</span>
      </button>
      <strong>Failed!</strong> <?php echo $msg_content; ?>
    </div>
  <?php
  }
  ?>

  <div class="row">
    <div class="col-md-12">
      <div class="main-verification-input-wrap">
        <ul>
          <li><b>Your cannot use our system until your account is VERIFIED.</b></li>
          <li>You will recieve a verification code on your mail after you registered. Enter that code below.</li>
          <li>If somehow, you did not recieve the verification email then <a href="#" data-toggle="modal" data-target="#resendModal" style="text-decoration: underline !important;">resend the verification email</a></li>
        </ul>
        <div class="main-verification-input fl-wrap">
          <form role="form" method="POST">
            <div class="main-verification-input-item">
              <input type="text" value="" placeholder="Enter the verification code" name="code">
            </div>
            <button class="main-verification-button" type="submit" name="verify">Verify Now</button>
          </form>
        </div>
      </div>
    </div>
  </div>

  <!-- Modal -->
  <div class="modal fade" id="resendModal" tabindex="-1" role="dialog" aria-labelledby="resendModal" aria-hidden="true">
    <div class="modal-dialog" role="document">
      <div class="modal-content">
        <div class="modal-header">
          <h2 class="modal-title" id="exampleModalLabel">Resend Your Verification Email</h2>
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
        <div class="modal-body">
          <form class="resend">
            <div class="form-group">
              <label for="recipient-name" class="col-form-label">Input your email that used in registration:</label>
              <input type="text" class="form-control" id="email" name="email">
            </div>
          </form>
        </div>
        <div class="modal-footer">
          <!-- <button type="button" class="btn btn-primary" type="submit" name="resend">Send</button> -->
          <button class="btn btn-success" id="submit" name="resend">Send Email</button>
          <button class="btn btn-success" disabled="" id="wait" style="display: none;">Please Wait..</button>
        </div>
      </div>
    </div>
  </div>

  <!-- jQuery -->
  <script src="plugins/jquery/jquery.min.js"></script>
  <!-- Bootstrap 4 -->
  <script src="plugins/bootstrap/js/bootstrap.bundle.min.js"></script>
  <!-- AdminLTE App -->
  <script src="dist/js/adminlte.min.js"></script>
  <script>
    $(function() {
      $("button#submit").click(function() {
        $("#submit").hide();
        $("#wait").show();
        $.ajax({
          type: "POST",
          url: "<?php echo $cfg_baseurl . '/login/resend.php' ?>",
          data: $('form.resend').serialize(),
          success: function(msg) {
            $("#resendModal").modal('hide');
            $("#submit").show();
            alert(msg);
            $("#wait").hide();
          },
          error: function() {
            alert("failure");
          }
        });
      });
    });
  </script>
</body>

</html>