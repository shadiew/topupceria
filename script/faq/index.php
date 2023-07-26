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
	$email = $data_user['email'];
	if ($email == "") {
	header("Location: ".$cfg_baseurl."settings");
	}
	
	$title = "FAQ";
	include("../lib/header.php");
	?>

<div class="page has-sidebar-left height-full">
    <header class="blue accent-3 relative nav-sticky">
        <div class="container-fluid text-white">
            <div class="row p-t-b-10 ">
                <div class="col">
                    <h4>
                        <i class="icon icon-help_outline s-18"></i>
                        FAQ
                    </h4>
                </div>
            </div>
        </div>
    </header>

	<?php
		$faq_query = mysqli_query($db, "SELECT * FROM faq ORDER BY id ASC");
		$faq_id = 0;
		while ($data_faq = mysqli_fetch_assoc($faq_query)) { $faq_id++;
	?>
		<div class="container-fluid my-3 relative animatedParent animateOnce">
		<div class="col-md-12">
			<div class="white p-2 r-5">
				<div class="table-responsive">
					<div class="card b-0  m-2">
						<div class="card-body">
							<div data-toggle="collapse" data-target="#message<?php echo $faq_id; ?>" aria-expanded="<?php if($faq_id == 1){ ?>true<?php }else{ ?>false<?php } ?>" class="<?php if($faq_id != 1){ ?>collapsed<?php } ?>">
								<div class="media">
									<div class="media-body">
										<h5><i class="icon icon-help_outline s-16"></i> <?php echo $data_faq['question']; ?></h5>
										<div class="my-3 collapse<?php if($faq_id == 1){ ?>show<?php } ?>" id="message<?php echo $faq_id; ?>">
											<div>
												<p><?php echo $data_faq['answer']; ?></p>
											</div>
										</div>
									</div>
								</div>
							</div>
						</div>
					</div>
				</div>
			</div> 
        </div>
    </div>
	<?php } ?>
            
<?php
	include("../lib/footer.php");
} else {
	header("Location: ".$cfg_baseurl);
}