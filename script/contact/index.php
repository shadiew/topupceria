<?php
session_start();
require("../lib/mainconfig.php");

/* CHECK USER SESSION */
if (isset($_SESSION['user'])) {
	$sess_username = $_SESSION['user']['username'];
	$check_user = mysqli_query($db, "SELECT * FROM users WHERE username = '$sess_username'");
	$data_user = mysqli_fetch_assoc($check_user);
	if (mysqli_num_rows($check_user) == 0) {
		header("Location: ".$cfg_baseurl."/logout/");
	} else if ($data_user['status'] == "Suspended") {
		header("Location: ".$cfg_baseurl."/logout/");
	}
}

$title = "Contact";
include("../lib/header.php");
?>

<div class="page  has-sidebar-left height-full">
    <header class="blue accent-3 relative">
        <div class="container-fluid text-white">
            <div class="row p-t-b-10 ">
                <div class="col">
                    <h4>
                        <i class="icon-support"></i>
                        Contact
                    </h4>
                </div>
            </div>
        </div>
    </header>
    <div class="container-fluid animatedParent animateOnce">
        <div class="tab-content my-3" id="v-pills-tabContent">
            <div class="tab-pane animated fadeInUpShort show active" id="v-pills-buyers" role="tabpanel" aria-labelledby="v-pills-buyers-tab">
                <div class="row">
				<?php
				$check_staff = mysqli_query($db, "SELECT * FROM staff ORDER BY level ASC");
				while ($data_staff = mysqli_fetch_assoc($check_staff)) {
				?>
                    <div class="col-md-3 my-3">
                        <div class="card no-b">
                            <div class="card-body text-center p-5">
                                <div class="avatar avatar-xl mb-3">
                                    <img src="../assets/img/dummy/u1.png" alt="User Image">
                                </div>
                                <div>
                                    <h6 class="p-t-10"><?php echo $data_staff['name']; ?></h6>
                                    <?php echo $data_staff['level']; ?> <br /><br />
                                </div>
									Contact: <br />
									<ul class="social social list-inline">
										<li class="list-inline-item"><a href="https://www.facebook.com/<?php echo $data_staff['facebook']; ?>" class="facebook" target="_blank"><i class="icon-facebook"></i></a></li>
										<li class="list-inline-item"><a href="https://www.instagram.com/<?php echo $data_staff['instagram']; ?>" class="instagram" target="_blank"><i class="icon-instagram"></i></a></li>
										<li class="list-inline-item"><a href="https://api.whatsapp.com/send?phone=<?php echo $data_staff['nomor']; ?>" class="whatsapp" target="_blank"><i class="icon-whatsapp"></i></a></li>
									</ul>
							</div>
                        </div>
                    </div>
					<?php
					}
					?>
                </div>
            </div>
        </div>
    </div>
</div>
<?php
include("../lib/footer.php");
?>