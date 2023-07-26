<?php
session_start();
require("../lib/mainconfig.php");

if (isset($_POST['email'])) {
    $to = $_POST['email'];
    $check_user = mysqli_query($db, "SELECT name, email, status, otp FROM users WHERE email = '$to'");
    $data_user = mysqli_fetch_assoc($check_user);
    if (isset($data_user)) {
        $name = $data_user["name"];
        $otp = $data_user["otp"];
        $msg = "<hr></hr><br>Hallo <b> $name </b>, Please use input this OTP to verify your account<br><br>OTP: <b>$otp<b> <br><br><br><hr></hr><br>You cannot contact this Noreply message, Please Contact Admin Contact Through the Application or via Ticket. <br><br>Thanks.<br><hr></hr>";
        $subject = "Verify Account";
        $headers = "From: SMM PANEL <$email_webmail_forgot> \r\n";
        $headers .= "Cc:$email_webmail_forgot \r\n";
        $headers .= "MIME-Version: 1.0\r\n";
        $headers .= "Content-type: text/html\r\n";
        $send = mail($to, $subject, $msg, $headers);
        echo "Sukses";
    } else {
        echo "Failed! Please use registered email.";
    }
}
