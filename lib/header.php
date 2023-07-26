<?php
if (isset($_SESSION['user'])) {
    $email = $data_user['email'];
    $hp = $data_user['nohp'];
    $nama = $data_user['name'];

    $check_settings = mysqli_query($db, "SELECT * FROM settings WHERE id = '1'");
    $data_settings = mysqli_fetch_assoc($check_settings);
    if ($email == "") {
        header("Location: " . $cfg_baseurl . "/settings/");
    }
}
?>
