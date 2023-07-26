<?php
session_start();
require("../lib/mainconfig.php");
$check_settings = mysqli_query($db, "SELECT * FROM settings WHERE id = '1'");
$data_settings = mysqli_fetch_assoc($check_settings);
$msg_type = "nothing";

if (isset($_POST['login'])) {
  $post_username = htmlspecialchars(trim($_POST['username']));
  $post_password = htmlspecialchars(trim($_POST['password']));
  $ip = $_SERVER['REMOTE_ADDR'];
  if (empty($post_username) || empty($post_password)) {
    $msg_type = "error";
    $msg_content = "Please Fill In All Inputs.";
  } else {
    $check_user = mysqli_query($db, "SELECT * FROM users WHERE username = '$post_username'");
    if (mysqli_num_rows($check_user) == 0) {
      $msg_type = "error";
      $msg_content = "The username you entered is not registered.";
    } else {
      $data_user = mysqli_fetch_assoc($check_user);
      if (password_verify($post_password, $data_user['password'])) {
        $verified = true;
      } else {
        $verified = false;
      }

      if ($data_user['level'] == "Developers" && !$verified) {
        $ip = $_SERVER['REMOTE_ADDR'];
        $msg_type = "error";
        $msg_content = "The Password You Enter Is Wrong.";
      } else if (!$verified) {
        $msg_type = "error";
        $msg_content = "The Password You Enter Is Wrong!.";
      } else if ($data_user['status'] == "Suspended") {
        $msg_type = "error";
        $msg_content = "Account Suspended.";
      } else if ($data_user['status'] == "Not Active") {
        header("Location: " . $cfg_baseurl . "/login/verification.php");
      } else {
        $_SESSION['user'] = $data_user;
        header("Location: " . $cfg_baseurl);
      }
    }
  }
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8" />
    <title><?php echo $data_settings['web_title']; ?></title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="<?php echo $data_settings['web_description']; ?>" />
    <meta name="keywords" content="<?php echo $data_settings['seo_keywords']; ?>" />
    <meta content="Themesdesign" name="author" />
    <!-- favicon -->
    <link rel="icon" href="<?php echo $data_settings['link_fav']; ?>" type="image/x-icon">
    <!-- css -->
    <link href="css/bootstrap.min.css" rel="stylesheet" type="text/css" />
    <link href="css/materialdesignicons.min.css" rel="stylesheet" type="text/css" />
     <!-- flexslider slider -->
     <link rel="stylesheet" type="text/css" href="css/flexslider.css" />
    <!--Slider-->
    <link rel="stylesheet" type="text/css" href="css/owl.carousel.css" />
    <link rel="stylesheet" type="text/css" href="css/owl.theme.css" />
    <link rel="stylesheet" type="text/css" href="css/owl.transitions.css" />
    <!-- magnific pop-up -->
    <link rel="stylesheet" type="text/css" href="css/magnific-popup.css" />
    <link href="css/style.css" rel="stylesheet" type="text/css" />
    <?php echo $data_settings['seo_meta']; ?>
    <?php echo $data_settings['seo_analytics']; ?>
</head>

<body data-spy="scroll" data-target="#navbarCollapse">

    <!--Navbar Start-->
    <nav class="navbar navbar-expand-lg fixed-top navbar-custom sticky sticky-dark">
        <div class="container">
            <!-- LOGO -->
            <a class="navbar-brand logo text-uppercase" href="">
                <img src="<?php echo $data_settings['link_logo']; ?>" alt="" height="80">
            </a>
            <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarCollapse"
                aria-controls="navbarCollapse" aria-expanded="false" aria-label="Toggle navigation">
                <i class="mdi mdi-menu"></i>
            </button>
            <div class="collapse navbar-collapse" id="navbarCollapse">
                <ul class="navbar-nav ml-auto navbar-center" id="mySidenav">

                    <li class="nav-item">
                        <a href="#home" class="nav-link smoothlink">Home</a>
                    </li>
                    <li class="nav-item">
                        <a href="#services" class="nav-link smoothlink">Kenapa Kami?</a>
                    </li>
                    <li class="nav-item">
                        <a href="#pembayaran" class="nav-link smoothlink">Pembayaran</a>
                    </li>
                    <li class="nav-item">
                        <a href="#contact" class="nav-link smoothlink">Kontak</a>
                    </li>
                </ul>

                <ul class="navbar-nav navbar-center">
                    <li class="nav-item">
                        <a href="../login" class="nav-link">Masuk</a>
                    </li>
                    <li class="nav-item d-inline-block d-lg-none">
                        <a href="../login/register" class="nav-link">Daftar</a>
                    </li>
                </ul>
                <div class="navbar-button d-none d-lg-inline-block">
                    <a href="../login/register" class="btn btn-sm btn-soft-primary btn-round">Daftar</a>
                </div>
            </div>
        </div>
    </nav>
    <!-- Navbar End -->

    <!-- END HOME -->
    <section class="bg-home bg-light" id="home">
        <div class="home-center">
            <div class="home-desc-center">
                <div class="container">
                    <div class="row align-items-center">
                        <div class="col-lg-6">
                            <div class="home-content">
                                <p class="mb-0">Jasa Follower</p>
                                <img src="images/home-border.png" height="15" alt="">
                                <h1 class="home-title mt-4">Jual Follower & Subscribe</h1>
                                <p class="text-muted mt-4 f-20">Menyediakan layanan penambahan Follower, like, subscribe, views dan Share untuk sosial media seperti Youtube, facebook, instagram, twitter, Tiktok, dll.</p>
                                <div class="mt-4 pt-2">
                                    <a href="../download/app.apk" class="btn btn-primary mr-3">Download APPS</a>
                                    
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-5 offset-lg-1">
                            <div class="home-registration-form bg-white p-5 mt-4">
                                <h5 class="form-title mb-4 text-center font-weight-bold">Login Session</h5>
                                <?php 
          if ($msg_type == "error") {
        ?>
        <div class="alert alert-danger alert-dismissible" role="alert">
        <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">×</span>
        </button>
        <strong>Failed!</strong> <?php echo $msg_content; ?>
        </div>
        <?php
        }
        ?>
                                <form class="registration-form" method="POST">
                                    <label class="text-muted">Username</label>
                                    <input type="text" name="username" id="exampleInputName1" class="form-control mb-4 registration-input-box">
                                    <label class="text-muted">Password</label>
                                    <input type="password" name="password" id="exampleInputName2" class="form-control mb-4 registration-input-box">
                                    
                                    <button class="btn btn-primary w-100" value="Login" name="login" type="submit">Login Now</button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <!-- END HOME -->

    <!-- START SERVICES -->
    <section class="section bg-services" id="services">
        <div class="container">

            <div class="row">
                <div class="col-lg-12">
                    <div class="title-box text-center">
                        <h3 class="title-heading mt-4">Kenapa Kami? </h3>
                        <p class="text-muted f-17 mt-3">Ada banyak layanan yang menyediakan produk serupa, ini alasan anda <br>memilih kami sebagai penyedia layanan Sosmed</p>

                        <img src="images/home-border.png" height="15" class="mt-3" alt="">
                    </div>
                </div>
            </div>

            <div class="row mt-5 pt-4">
                <div class="col-lg-4">
                    <div class="services-box p-4 mt-4">
                        <div class="services-icon bg-soft-primary">
                            <i class="mdi mdi-history text-primary"></i>
                        </div>

                        <h5 class="mt-4">24 Jam Non-stop</h5>
                        <p class="text-muted mt-3">Dilengkapi dengan sistem pemesanan dan pembayaran otomatis 24 jam non-stop, bisa dipakai kapanpun dan dimanapun!.</p>

                        <div class="mt-3">
                            <a href="" class="text-primary f-16">Learn More <i class="mdi mdi-arrow-right ml-1"></i></a>
                        </div>

                    </div>
                </div>

                <div class="col-lg-4">
                    <div class="services-box p-4 mt-4">
                        <div class="services-icon bg-soft-primary">
                            <i class="mdi mdi-rocket text-primary"></i>
                        </div>

                        <h5 class="mt-4">Proses Tercepat</h5>
                        <p class="text-muted mt-3">Pesanan diproses dengan sistem otomatis, tanpa menunggu respon admin!</p>

                        <div class="mt-3">
                            <a href="" class="text-primary f-16">Learn More <i class="mdi mdi-arrow-right ml-1"></i></a>
                        </div>

                    </div>
                </div>

                <div class="col-lg-4">
                    <div class="services-box p-4 mt-4">
                        <div class="services-icon bg-soft-primary">
                            <i class="mdi mdi-lock text-primary"></i>
                        </div>

                        <h5 class="mt-4">Safe</h5>
                        <p class="text-muted mt-3">Diproses dengan metode LEGAL & proses hanya membutuhkan link akun/post (tanpa e-mail maupun password).</p>

                        <div class="mt-3">
                            <a href="" class="text-primary f-16">Learn More <i class="mdi mdi-arrow-right ml-1"></i></a>
                        </div>

                    </div>
                </div>
            </div>
        </div>
    </section>
    <!-- END SERVICES -->

    <!-- START CLIENT -->
    <section class="section bg-light bg-clients" id="pembayaran">
        <div class="container">
            <div class="row">
                <div class="col-lg-12">
                    <div class="title-box text-center">
                        <h3 class="title-heading mt-4">Pembayaran Otomatis</h3>
                        <p class="text-muted f-17 mt-3">Untuk kemudahan dan kenyamanan pembayaran, kami menyediakan beragam pembayaran otomatis <br>yang dapat anda gunakan sesuai kebutuhan anda.</p>

                        <img src="images/home-border.png" height="15" class="mt-3" alt="">
                    </div>
                </div>
            </div>

            <div class="row mt-5 pt-4">
                <div class="col-lg-3">
                    <div class="client-images mt-4">
                        <img src="images/clients/1.png" alt="logo-img" class="mx-auto img-fluid d-block">
                    </div>
                </div>
                <div class="col-lg-3">
                    <div class="client-images mt-4">
                        <img src="images/clients/alfamart.svg" alt="logo-img" class="mx-auto img-fluid d-block">
                    </div>
                </div>
                <div class="col-lg-3 ">
                    <div class="client-images mt-4">
                        <img src="images/clients/3.png" alt="logo-img" class="mx-auto img-fluid d-block">
                    </div>
                </div>
                <div class="col-lg-3">
                    <div class="client-images mt-4">
                        <img src="images/clients/4.png" alt="logo-img" class="mx-auto img-fluid d-block">
                    </div>
                </div>
                <div class="col-lg-3">
                    <div class="client-images mt-4">
                        <img src="images/clients/2.png" alt="logo-img" class="mx-auto img-fluid d-block">
                    </div>
                </div>
                <div class="col-lg-3">
                    <div class="client-images mt-4">
                        <img src="images/clients/bni.png" alt="logo-img" class="mx-auto img-fluid d-block">
                    </div>
                </div>
                <div class="col-lg-3">
                    <div class="client-images mt-4">
                        <img src="images/clients/bankpermata.png" alt="logo-img" class="mx-auto img-fluid d-block">
                    </div>
                </div>
                <div class="col-lg-3">
                    <div class="client-images mt-4">
                        <img src="images/clients/maybank.png" alt="logo-img" class="mx-auto img-fluid d-block">
                    </div>
                </div>
            </div>
        </div>
    </section>
    <!-- END CLIENT -->

    <!-- START CONTACT -->
    <section class="section" id="contact">
        <div class="container">
            <div class="row">
                <div class="col-lg-12">
                    <div class="title-box text-center">
                        <h3 class="title-heading mt-4">Selalu Terhubung!</h3>
                        <p class="text-muted f-17 mt-3">Jika kamu memiliki kendala atau pertanyaan seputar layanan dan produk kami<br>Silahkan hubungi kami dengan kontak yang telah kami sediakan</p>

                        <img src="images/home-border.png" height="15" class="mt-3" alt="">
                    </div>
                </div>
            </div>

            <div class="row mt-5 pt-4">
                <div class="col-lg-6">
                    <div class="mt-4 home-img text-center">
                        <div class="animation-2"></div>
                        <div class="animation-3"></div>
                        <img src="images/features/img-3.png" class="img-fluid" alt="">
                    </div>
                </div>

                <div class="col-lg-6">
                    <div class="custom-form mt-4">
                        <div id="message"></div>
                        <form method="post" action="php/contact.php" name="contact-form" id="contact-form">
                            <div class="row">
                                <div class="col-lg-6">
                                    <div class="form-group mt-3">
                                        <label class="contact-lable">Nama Depan</label>
                                        <input name="name" id="name" class="form-control" type="text">
                                    </div>
                                </div>

                                <div class="col-lg-6">
                                    <div class="form-group mt-3">
                                        <label class="contact-lable">Nama Belakang</label>
                                        <input name="name" id="lastname" class="form-control" type="text">
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-lg-12">
                                    <div class="form-group mt-3">
                                        <label class="contact-lable">Alamat Email</label>
                                        <input name="email" id="email" class="form-control" type="text">
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-lg-12">
                                    <div class="form-group mt-3">
                                        <label class="contact-lable">Subyek</label>
                                        <input name="subject" id="subject" class="form-control" type="text">
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-lg-12">
                                    <div class="form-group mt-3">
                                        <label class="contact-lable">Pesan Kamu</label>
                                        <textarea name="comments" id="comments" rows="5"
                                            class="form-control"></textarea>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-lg-12 mt-3 text-right">
                                    <input id="submit" name="send" class="submitBnt btn btn-primary btn-round"
                                        value="Send Message" type="submit">
                                    <div id="simple-msg"></div>
                                </div>
                            </div>
                        </form>
                    </div>

                </div>
            </div>

        </div>
    </section>
    <!-- END CONTACT -->

    <!-- START FOOTER -->
    <section class="section bg-light bg-footer pb-5">
        <div class="container">
            <div class="row">
                <div class="col-lg-4">
                    <div class="footer-info mt-4">
                        <img src="<?php echo $data_settings['link_logo']; ?>" alt="" height="60">
                        <p class="text-muted mt-4 mb-2">Penyedia Jasa Layanan Tambah follower Murah, aman, terbaik, dan terpercaya.</p>
                        <div class="team-social mt-4 pt-2">
                            <ul class="list-inline mb-0">
                                <li class="list-inline-item">
                                    <a href="" class="text-reset"><i class="mdi mdi-facebook"></i></a>
                                </li>
                                <li class="list-inline-item">
                                    <a href="" class="text-reset"><i class="mdi mdi-twitter"></i></a>
                                </li>
                                <li class="list-inline-item">
                                    <a href="" class="text-reset"><i class="mdi mdi-google"></i></a>
                                </li>
                                <li class="list-inline-item">
                                    <a href="" class="text-reset"><i class="mdi mdi-pinterest"></i></a>
                                </li>
                            </ul>
                        </div>

                    </div>
                </div>

                <div class="col-lg-4">
                    <div class="row  pl-0 md-lg-5">
                        <div class="col-lg-6">
                            <div class="mt-4">
                                <h5 class="f-20">Services</h5>
                                <ul class="list-unstyled footer-link mt-3">
                                    <li><a href="">Web Design</a></li>
                                    <li><a href="https://bozztool.com/download/apps-bozztool.apk">Download APP</a></li>
                                </ul>
                            </div>
                        </div>

                        <div class="col-lg-6">
                            <div class="mt-4">
                                <h5 class="f-20">Company</h5>
                                <ul class="list-unstyled footer-link mt-3">
                                    <li><a href="">Features</a></li>
                                    <li><a href="">Faq</a></li>
                                    <li><a href="#message">Contact us</a></li>
                                </ul>
                            </div>
                        </div>

                    </div>
                </div>

                <div class="col-lg-4">
                    <div class="mt-4">
                        <h5 class="f-20">Subscribe</h5>

                        <div class="subscribe mt-4 pt-1">
                            <form action="#">
                                <input placeholder="Enter Email" type="text">
                                <button type="submit" class="btn btn-primary"><i class="mdi mdi-send"></i></button>
                            </form>
                        </div>
                    </div>
                </div>

            </div>

            <hr class="my-5">

            <div class="row">
                <div class="col-12">
                    <div class="text-center">
                        <p class="text-muted mb-0">2020 © <?php echo $data_settings['web_copyright']; ?>.</p>
                    </div>
                </div>
            </div>


        </div>
    </section>
    <!-- END FOOTER -->

    <!-- javascript -->
    <script src="js/jquery.min.js"></script>
    <script src="js/bootstrap.bundle.min.js"></script>
    <script src="js/jquery.easing.min.js"></script>
    <script src="js/jquery.mb.YTPlayer.js"></script>
     <!--flex slider plugin-->
     <script src="js/jquery.flexslider-min.js"></script>
    <!-- Portfolio -->
    <script src="js/jquery.magnific-popup.min.js"></script>
    <!-- contact init -->
    <script src="js/contact.init.js"></script>
    <!-- counter init -->
    <script src="js/counter.init.js"></script>
    <!-- Owl Carousel -->
    <script src="js/owl.carousel.min.js"></script>
    <script src="js/app.js"></script>

</body>

</html>