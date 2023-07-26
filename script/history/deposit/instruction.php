<?php
require("../../lib/mainconfig.php");

$id = intval($_GET['id']);
$query_order = mysqli_query($db, "SELECT * FROM deposits WHERE id = '$id' LIMIT 1");
$data_order = mysqli_fetch_assoc($query_order);
$data_decoded = json_decode($data_order["instructions"]);

foreach ($data_decoded as $value) {
    echo "<h3>" . $value->title . "</h3>";
    echo "<ol>";
    foreach ($value->steps as $step) {
        echo "<li>" . $step . "</li>";
    }
    echo "</ol>";
    if ($data_order["code"] == 'QRIS') {

        echo "<br>";
        echo "<img src='" . $data_order["qr_url"] . "' width='450px'>";
    }
}
