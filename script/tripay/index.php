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
  <script src="https://code.jquery.com/jquery-3.3.1.min.js"></script>

  <div class="page has-sidebar-left">
    <header class="blue accent-3 relative">
      <div class="container-fluid text-white">
        <div class="row p-t-b-10 ">
          <div class="col">
            <h4>
              <i class="icon light-green-text icon-credit-card"></i>
              Tripay Deposit
            </h4>
          </div>
        </div>
      </div>
    </header>

    <div class="animatedParent animateOnce">
      <div class="container-fluid my-3">
        <div class="row">
          <div class="col-md-7">
            <div class="card">
              <div class="card-body b-b">
                <!-- TRIPAY DEPOSIT FORM -->
                <h4>Tripay Deposit</h4>
                <hr>
                <form name="tripayForm" method="post" action="verify.php">
                  <div class="form-group">
                    <label for="sms" class="col-form-label">Deposit Amount (IDR)</label>
                    <input id="amount_field" required type="number" name="amount" min="10000" class="form-control" placeholder="Rp. ">
                  </div>
                  <div class="form-group">
                    <label for="service" class="col-form-label">Payment method</label>
                    <div class="form-check">
                      <?php
                      $check_paket = mysqli_query($db, "SELECT * FROM deposit_method WHERE Active = 'YES' AND name_method = 'TRIPAY' ORDER BY id DESC");
                      while ($data_paket = mysqli_fetch_assoc($check_paket)) {
                      ?>
                        <label class="form-check-label" style="margin-top: 10px; margin-bottom: 10px;">
                          <input class="form-check-input" value="<?php echo $data_paket['code']; ?>" id="<?php echo $data_paket['code']; ?>" name="method" type="radio">
                          <?php echo $data_paket['name']; ?> <img height="25px" src="../assets/img/tripay/<?php echo $data_paket['code']; ?>.webp">
                        </label>
                        <br>
                      <?php
                      }
                      ?>
                    </div>
                  </div>
                  <hr>
                  <input type="submit" class="btn btn-primary" value="Deposit" name="deposit">
              </div>
              </form>
            </div>
          </div><br>
          <div class="col-md-5">
            <!-- INFORMATION TAB -->
            <div class="card">
              <div class="card-body b-b">
                <h3>Information</h3>
                <hr>
                <div class="panel-body">
                  <div id="information"></div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.3/umd/popper.min.js"></script>

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

<?php
  include("../lib/footer.php");
} else {
  header("Location: " . $cfg_baseurl);
}
?>