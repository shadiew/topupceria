<?php
require_once('../lib/mainconfig.php');

// ambil data JSON
$json = file_get_contents("php://input");

// ambil callback signature
$callbackSignature = isset($_SERVER['HTTP_X_CALLBACK_SIGNATURE']) ? $_SERVER['HTTP_X_CALLBACK_SIGNATURE'] : '';

// generate signature untuk dicocokkan dengan X-Callback-Signature
$signature = hash_hmac('sha256', $json, $tripay_private_key);

// validasi signature
if ($callbackSignature !== $signature) {
    exit("Invalid Signature"); // signature tidak valid, hentikan proses
}

$data = json_decode($json);
$event = $_SERVER['HTTP_X_CALLBACK_EVENT'];

if ($event == 'payment_status') {
    if ($data->status == 'PAID') {

        $merchantRef = $db->real_escape_string($data->merchant_ref);
        $tripayRef = $db->real_escape_string($data->reference);

        $sql = mysqli_query($db, "SELECT * FROM deposits WHERE invoice_number = '{$merchantRef}' AND payment_gateway_reference = '{$tripayRef}' AND status = 'Pending' LIMIT 1");
        $depositData = mysqli_fetch_assoc($sql);

        if ($depositData) {

            $update = "UPDATE deposits SET status = 'Success' WHERE id = {$depositData["id"]}";
            $db->query($update) or die($db->error);

            $sqlUser = mysqli_query($db, "SELECT * FROM users WHERE id = '{$depositData["user"]}' LIMIT 1");
            $userData = mysqli_fetch_assoc($sqlUser);

            if ($userData) {
                $balance = (int)str_replace(' ', '', $userData["balance"]);
                $newBalance = $balance + (int)str_replace(' ', '', $depositData["balance"]);
                $updateBalance = "UPDATE users SET balance = '$newBalance' WHERE id = {$userData["id"]}";
                $db->query($updateBalance) or die($db->error);
            }

            echo json_encode(['success' => true]); // berikan respon yang sesuai
            exit;
        }
    } elseif ($data->status == 'EXPIRED') {

        $update = "UPDATE deposits SET status = 'Expired' WHERE id = {$depositData["id"]}";
        $db->query($update) or die($db->error);

        echo json_encode(['success' => true]); // berikan respon yang sesuai
        exit;
    } elseif ($data->status == 'Failed') {

        $update = "UPDATE deposits SET status = 'Error' WHERE id = {$depositData["id"]}";
        $db->query($update) or die($db->error);

        echo json_encode(['success' => true]); // berikan respon yang sesuai
        exit;
    }
}

die("No action was taken");
