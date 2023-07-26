<!DOCTYPE html>
<html>

<head>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css" integrity="sha384-Gn5384xqQ1aoWXA+058RXPxPg6fy4IWvTNh0E263XmFcJlSAwiGgFAW/dAiS6JXm" crossorigin="anonymous">
    <style>
        body {
            background-image: url("../assets/img/nature.jpg");
            background-color: #cccccc;
            background-position: center;
            /* Center the image */
            background-repeat: no-repeat;
            /* Do not repeat the image */
            background-size: cover;
            /* Resize the background image to cover the entire container */
        }

        .payment__infoTitle {
            font-weight: bold;
            text-align: left;
        }

        .payment__infoSubtitle {
            text-align: left;
        }

        .tooltip {
            position: relative;
            display: contents;
            border-bottom: 1px dotted black;
        }

        .tooltip .tooltiptext {
            visibility: hidden;
            width: 120px;
            background-color: #555;
            color: #fff;
            text-align: center;
            border-radius: 6px;
            padding: 5px 0;
            position: absolute;
            z-index: 1;
            bottom: 100%;
            left: 68%;
            /* margin-left: -60px; */
            opacity: 0;
            transition: opacity 0.3s;
        }

        .tooltip .tooltiptext::after {
            content: "";
            position: absolute;
            top: 100%;
            left: 50%;
            margin-left: -5px;
            border-width: 5px;
            border-style: solid;
            border-color: #555 transparent transparent transparent;
        }

        .tooltip:hover .tooltiptext {
            visibility: visible;
            opacity: 1;
        }
    </style>
</head>

<body>
    <?php
    session_start();
    require_once('../lib/mainconfig.php');
    date_default_timezone_set("Asia/Jakarta");

    if (isset($_SESSION['user'])) {
        $customer_name = $_SESSION['user']['name'];
        $customer_email = $_SESSION['user']['email'];
        $customer_phone = $_SESSION['user']['nohp'];
        $customer_id = $_SESSION['user']['id'];
    }

    function tanggal_indo($tanggal, $cetak_hari = false)
    {
        $hari = array(
            1 =>    'Senin',
            'Selasa',
            'Rabu',
            'Kamis',
            'Jumat',
            'Sabtu',
            'Minggu'
        );

        $bulan = array(
            1 =>   'Januari',
            'Februari',
            'Maret',
            'April',
            'Mei',
            'Juni',
            'Juli',
            'Agustus',
            'September',
            'Oktober',
            'November',
            'Desember'
        );
        $split_jam = explode(' ', $tanggal);
        $split       = explode('-', $split_jam[0]);
        $tgl_indo = $split[2] . ' ' . $bulan[(int)$split[1]] . ' ' . $split[0];

        if ($cetak_hari) {
            $num = date('N', strtotime($tanggal));
            return $hari[$num] . ', ' . $tgl_indo . ' ' . $split_jam[1] . ' WIB';
        }
        return $tgl_indo . ' ' . $split_jam[1] . ' WIB';
    }

    // MAKE PAYMENT TO TRIPAY

    if (isset($_POST['deposit'])) {
        $amount = $_POST['amount'];
        $method = $_POST['method'];
    }

    // 1. Create Signature
    $privateKey = $tripay_private_key;
    $merchantCode = $tripay_merchant_code;
    $apiKey = $tripay_api_key;

    // Invoice Code
    $checkDeposit = mysqli_query($db, "SELECT * FROM deposits ORDER BY ID DESC LIMIT 1");
    $a = mysqli_fetch_assoc($checkDeposit);
    $depositId = $a['id'] + 1;
    $merchantRef = 'DPO' . '-' . $depositId . date("ymd"); //DPO-9210313
    $signature = hash_hmac('sha256', $merchantCode . $merchantRef . $amount, $privateKey);

    // 2. Collect The Data
    $data = [
        'method'            => $method,
        'merchant_ref'      => $merchantRef,
        'amount'            => $amount,
        'customer_name'     => $customer_name,
        'customer_email'    => $customer_email,
        'customer_phone'    => $customer_phone,
        'order_items'       => [
            [
                'sku'       => 'Deposit',
                'name'      => 'Deposit Rp. ' . rupiah($amount),
                'price'     => $amount,
                'quantity'  => 1
            ]
        ],
        'callback_url'      => $cfg_baseurl . '/tripay/callback.php',
        'return_url'        => $cfg_baseurl . '/tripay/redirect.php',
        'expired_time'      => (time() + (24 * 60 * 60)), // 24 jam
        'signature'         => hash_hmac('sha256', $merchantCode . $merchantRef . $amount, $privateKey)
    ];


    $curl = curl_init();

    curl_setopt_array($curl, array(
        CURLOPT_FRESH_CONNECT     => true,
        CURLOPT_URL               => "https://tripay.co.id/api/transaction/create",
        CURLOPT_RETURNTRANSFER    => true,
        CURLOPT_HEADER            => false,
        CURLOPT_HTTPHEADER        => array(
            "Authorization: Bearer " . $apiKey
        ),
        CURLOPT_FAILONERROR       => false,
        CURLOPT_POST              => true,
        CURLOPT_POSTFIELDS        => http_build_query($data)
    ));

    $response = curl_exec($curl);
    $err = curl_error($curl);
    curl_close($curl);

    $decodedResponse = json_decode($response, true);
    $payment_gateway_reference = $decodedResponse["data"]["reference"];
    $instructions = json_encode($decodedResponse["data"]["instructions"]);
    $checkout_url = $decodedResponse["data"]["checkout_url"];
    $qr_string = $decodedResponse["data"]["qr_string"];
    $qr_url = $decodedResponse["data"]["qr_url"];
    $expired_time = $decodedResponse["data"]["expired_time"];
    $pay_code = $decodedResponse["data"]["pay_code"];
    $total_payment = $decodedResponse["data"]["amount"];

    // var_dump($decodedResponse);
    // die();

    // 3. If Success Redirect To Tripay Checkout URL And Input to Database Deposits
    if (!empty($err)) {
        // Input To Database

        $insert_deposit = mysqli_query(
            $db,
            "INSERT INTO 
        deposits (
            invoice_number, payment_gateway_reference, code, user, method, note, quantity, balance, status, instructions, checkout_url, qr_string, qr_url, expired_time, created_at) 
        VALUES (
            '$merchantRef', '$payment_gateway_reference', '$method', '$customer_id', 'TRIPAY', '', '1', '$amount', 'Error','$instructions','$checkout_url', '$qr_string', '$qr_url', '$expired_time', NOW())"
        );
        echo $err;
        echo "<br><b>Please Contact Admin!<b><br><a href='" . $cfg_baseurl . "'>Go To Home</a>";
    } else {
        // Input To Database
        $insert_deposit = mysqli_query(
            $db,
            "INSERT INTO 
        deposits (
            invoice_number, payment_gateway_reference, code, user, method, note, quantity, balance, status, instructions, checkout_url, qr_string, qr_url, expired_time, created_at) 
        VALUES (
            '$merchantRef', '$payment_gateway_reference', '$method', '$customer_id', 'TRIPAY', '', '1', '$amount', 'Pending','$instructions','$checkout_url', '$qr_string', '$qr_url', '$expired_time', NOW())"
        );
        $checkoutUrl = $decodedResponse["data"]["checkout_url"];
        // var_dump($response);
        // echo "<br>";
        // var_dump($decodedResponse);
        // header("Location: $checkoutUrl");
        // exit();

    ?>
        <div style="background-image: ;">
            <div class="container" style="margin-left: auto; margin-right:auto; margin-top:2rem; margin-bottom:2rem; width:50%">

                <div class="card text-center">
                    <!-- <div class="card-header">
                    Featured
                </div> -->
                    <div class="card-body">
                        <h5 class="card-title">Pembayaran Dengan <?php echo $decodedResponse["data"]["payment_name"]; ?></h5>
                        <h6 class="card-title">Pastikan anda melakukan pembayaran sebelum melewati batas
                            pembayaran dan dengan nominal yang tepat</h6>

                        <div class="row" style="margin-top: 50px; padding:50px;">
                            <div class="col-md-7">
                                <div class="mb-3">
                                    <div class="payment__infoTitle">
                                        Nama Pelanggan
                                    </div>
                                    <div class="payment__infoSubtitle">
                                        <?php echo $decodedResponse["data"]["customer_name"]; ?>
                                    </div>
                                </div>
                                <div class="mb-3">
                                    <div class="payment__infoTitle">
                                        No. HP
                                    </div>
                                    <div class="payment__infoSubtitle">
                                        <?php echo $decodedResponse["data"]["customer_phone"]; ?>
                                    </div>
                                </div>
                                <div class="mb-3">
                                    <div class="payment__infoTitle">
                                        Email
                                    </div>
                                    <div class="payment__infoSubtitle">
                                        <?php echo $decodedResponse["data"]["customer_email"]; ?>
                                    </div>
                                </div>
                                <div class="mb-3">
                                    <div class="payment__infoTitle">
                                        Rincian Transaksi
                                    </div>
                                    <div class="payment__infoSubtitle">
                                        <div class="row mb-1">
                                            <div class="col-8">
                                                + [Deposit] -<?php echo $decodedResponse["data"]["order_items"][0]["name"]; ?>
                                                <br>
                                                <small class="ml-3 font-weight-bold" style="color: #17a2b8;"><?php echo rupiah($decodedResponse["data"]["order_items"][0]["price"]); ?> x 1</small>
                                            </div>
                                            <div class="col-4 text-right font-weight-bold" style="color: #17a2b8;">
                                                <?php echo rupiah($decodedResponse["data"]["order_items"][0]["price"]); ?>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-8">
                                                + Biaya Administrasi
                                            </div>
                                            <div class="col-4 text-right font-weight-bold" style="color: #17a2b8;">
                                                <?php echo rupiah($decodedResponse["data"]["fee"]); ?>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                            </div>
                            <div class="col-md-5">
                                <div class="card">
                                    <div class="card-body">

                                        <div class="mb-3">
                                            <div class="payment__infoTitle">
                                                Nomor Referensi
                                            </div>
                                            <div class="payment__infoSubtitle">
                                                <div class="input-group pt-1">
                                                    <input type="text" class="form-control border-right-0" id="payment_gateway_reference" title="" value="<?php echo $payment_gateway_reference; ?>" disabled="" readonly="" style="background: #fff">
                                                    <div class="input-group-prepend">
                                                        <span class="input-group-text bg-white border-left-0">
                                                            <span class="tooltip" style="cursor: pointer;" onclick="copy('payment_gateway_reference')">
                                                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-files" viewBox="0 0 16 16">
                                                                    <path d="M13 0H6a2 2 0 0 0-2 2 2 2 0 0 0-2 2v10a2 2 0 0 0 2 2h7a2 2 0 0 0 2-2 2 2 0 0 0 2-2V2a2 2 0 0 0-2-2zm0 13V4a2 2 0 0 0-2-2H5a1 1 0 0 1 1-1h7a1 1 0 0 1 1 1v10a1 1 0 0 1-1 1zM3 4a1 1 0 0 1 1-1h7a1 1 0 0 1 1 1v10a1 1 0 0 1-1 1H4a1 1 0 0 1-1-1V4z" />
                                                                </svg>
                                                                <span class="tooltiptext">Copy</span>
                                                            </span>
                                                        </span>
                                                    </div>

                                                </div>
                                            </div>
                                        </div>
                                        <div class="mb-3">
                                            <div class="payment__infoTitle">
                                                Kode Bayar/Nomor VA
                                            </div>
                                            <div class="payment__infoSubtitle">
                                                <div class="input-group pt-1">
                                                    <input type="text" class="form-control border-right-0" id="noVA" data-toggle="tooltip" title="" value="<?php echo $pay_code; ?>" disabled="" readonly="" style="background: #fff">

                                                    <div class="input-group-prepend">
                                                        <span class="input-group-text bg-white border-left-0">
                                                            <span class="tooltip" style="cursor: pointer;" onclick="copy('noVA')">
                                                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-files" viewBox="0 0 16 16">
                                                                    <path d="M13 0H6a2 2 0 0 0-2 2 2 2 0 0 0-2 2v10a2 2 0 0 0 2 2h7a2 2 0 0 0 2-2 2 2 0 0 0 2-2V2a2 2 0 0 0-2-2zm0 13V4a2 2 0 0 0-2-2H5a1 1 0 0 1 1-1h7a1 1 0 0 1 1 1v10a1 1 0 0 1-1 1zM3 4a1 1 0 0 1 1-1h7a1 1 0 0 1 1 1v10a1 1 0 0 1-1 1H4a1 1 0 0 1-1-1V4z" />
                                                                </svg>
                                                                <span class="tooltiptext">Copy</span>
                                                            </span>
                                                        </span>
                                                    </div>

                                                </div>
                                            </div>
                                        </div>
                                        <div class="mb-3">
                                            <div class="payment__infoTitle">
                                                Jumlah Tagihan
                                            </div>
                                            <div class="payment__infoSubtitle">
                                                <div class="input-group pt-1">
                                                    <input type="text" class="form-control border-right-0" id="jumTagihan" data-toggle="tooltip" title="" value="<?php echo $total_payment; ?>" aria-describedby="inputGroupPrepend" disabled="" readonly="" style="background: #fff" data-original-title="Berhasil menyalin teks">

                                                    <div class="input-group-prepend">
                                                        <span class="input-group-text bg-white border-left-0">
                                                            <span class="tooltip" style="cursor: pointer;" onclick="copy('jumTagihan')">
                                                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-files" viewBox="0 0 16 16">
                                                                    <path d="M13 0H6a2 2 0 0 0-2 2 2 2 0 0 0-2 2v10a2 2 0 0 0 2 2h7a2 2 0 0 0 2-2 2 2 0 0 0 2-2V2a2 2 0 0 0-2-2zm0 13V4a2 2 0 0 0-2-2H5a1 1 0 0 1 1-1h7a1 1 0 0 1 1 1v10a1 1 0 0 1-1 1zM3 4a1 1 0 0 1 1-1h7a1 1 0 0 1 1 1v10a1 1 0 0 1-1 1H4a1 1 0 0 1-1-1V4z" />
                                                                </svg>
                                                                <span class="tooltiptext">Copy</span>
                                                            </span>
                                                        </span>
                                                    </div>

                                                </div>
                                            </div>
                                        </div>
                                        <div class="mb-3">
                                            <div class="payment__infoTitle">
                                                Batas Pembayaran
                                            </div>
                                            <div class="payment__expired" style="text-align: left; color:red; font-weight:bold;">
                                                <h6><?php echo tanggal_indo(date('Y-m-d h:i', $expired_time), true); ?></h6>
                                            </div>
                                        </div>
                                        <?php
                                        if ($method == 'QRIS') {
                                            echo "
                                            <div class='mb-3'>
                                            <div class='payment__infoSubtitle'>
                                                <small style='font-style: italic;'>* Klik untuk memperbesar kode QR</small>
                                                <a class='fancybox' data-toggle='modal' data-target='#exampleModal'>
                                                    <img src=" . $qr_url . " style='width:100%;max-width:170px !important;cursor:zoom-in'>
                                                </a>
                                            </div>
                                        </div>
                                        ";
                                        };
                                        ?>

                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="card-footer text-muted">
                        <a href="<?php echo $cfg_baseurl ?>/history/deposit" class="btn btn-primary">Kembali</a>
                    </div>
                </div>

            </div>
        </div>

        <div class="modal fade" id="exampleModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
            <div class="modal-dialog" role="document">
                <div class="modal-content" style="align-items: center;">
                    <img src="<?php echo $qr_url; ?>" style="width: 450px;">
                </div>
            </div>
        </div>


    <?php
    }

    // RESPONSE EXAMPLE
    // {
    // "success": true,
    // "message": "",
    // "data": {
    // "reference": "DEV-T30280000007864GCQJK",
    // "merchant_ref": "DPO-9210313",
    // "payment_selection_type": "static",
    // "payment_method": "ALFAMART",
    // "payment_name": "Alfamart",
    // "customer_name": "sadiwantoro",
    // "customer_email": "sadiwantoro@yahoo.com",
    // "customer_phone": "082221584446",
    // "callback_url": "http://localhost/sosmed/tripay/callback.php",
    // "return_url": "http://localhost/sosmed/tripay/redirect.php",
    // "amount": 91250
    // "fee": 1250,
    // "is_customer_fee": 1,
    // "amount_received": 90000,
    // "pay_code": "324364698920072",
    // "pay_url": null,
    // "checkout_url": "https://payment.tripay.co.id/checkout/DEV-T30280000007864GCQJK",
    // "status": "UNPAID",
    // "expired_time": 1615714877,
    // "order_items": [
    // {
    // "sku": "Deposit",
    // "name": "Deposit Rp. 90000",
    // "price": 90000,
    // "quantity": 1,
    // "subtotal": 90000
    // }
    // ],
    // "instructions": [
    // {
    // "title": "Pembayaran via ALFAMART",
    // "steps": [
    // "Datang ke Alfamart",
    // "Sampaikan ke kasir ingin melakukan pembayaran Plasamall",
    // "Berikan kode bayar (324364698920072</b>) ke kasir",
    // "Bayar sesuai jumlah yang diinfokan oleh kasir",
    // "Simpan struk bukti pembayaran Anda"
    // ]
    // }
    // ]
    // }
    // }

    ?>
</body>
<script src="https://code.jquery.com/jquery-3.2.1.slim.min.js" integrity="sha384-KJ3o2DKtIkvYIK3UENzmM7KCkRr/rE9/Qpg6aAZGJwFDMVNA/GpGFF93hXpG5KkN" crossorigin="anonymous"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.12.9/umd/popper.min.js" integrity="sha384-ApNbgh9B+Y1QKtv3Rn7W3mgPxhU9K/ScQsAP7hUibX39j7fakFPskvXusvfa0b4Q" crossorigin="anonymous"></script>
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/js/bootstrap.min.js" integrity="sha384-JZR6Spejh4U02d8jOt6vLEHfe/JQGiRRSQQxSfFWpi1MquVdAyjUar5+76PVCmYl" crossorigin="anonymous"></script>
<script>
    function copy(idField) {
        var copyText = document.getElementById(idField);;

        var text = copyText.value;
        navigator.clipboard.writeText(text).then(function() {
            alert('Copying ' + text + ' to clipboard was successful!');
        }, function(err) {
            console.error('Could not copy text: ', err);
        });
    }
</script>

</html>