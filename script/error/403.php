<?php
require("../lib/mainconfig.php");
$check_settings = mysqli_query($db, "SELECT * FROM settings WHERE id = '1'");
$data_settings = mysqli_fetch_assoc($check_settings);
?>
<!DOCTYPE html>
<html lang="en" >
<head>
  <meta charset="UTF-8">
  <title>403 Forbidden Page</title>
  <link rel="stylesheet" href="<?php echo $cfg_baseurl; ?>/error/style.css">
  <link rel="icon" href="<?php echo $data_settings['link_fav']; ?>" type="image/x-icon">
</head>
<body>
<svg xmlns="http://www.w3.org/2000/svg" id="robot-error" viewBox="0 0 260 118.9">
            <defs>
                <clipPath id="white-clip"><circle id="white-eye" fill="#cacaca" cx="130" cy="65" r="22" /> </clipPath>
             <text id="text-s" class="error-text" y="106"> 403 </text>
            </defs>
             <use xlink:href="#text-s" x="-0.5px" y="-1px" fill="white"></use>
             <use xlink:href="#text-s" fill="#00366F"></use>
            <g id="robot">
              <circle class="lightblue" cx="105" cy="32" r="2.5" id="tornillo" />
              <use xlink:href="#tornillo" x="50"></use>
              <use xlink:href="#tornillo" x="50" y="60"></use>
              <use xlink:href="#tornillo" y="60"></use>
            </g>
          </svg>
<h1>FORBIDDEN PAGE</h1>
<h2>Go <a href="<?php echo $cfg_baseurl; ?>">Home!</a></h2>
<script src="<?php echo $cfg_baseurl; ?>/error/script.js"></script>
</body>
</html>
