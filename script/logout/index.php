<?php
session_start();
require("../lib/mainconfig.php");

session_destroy();
header("Location: ".$cfg_baseurl."/login/");