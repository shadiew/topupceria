<?php
require("../lib/mainconfig.php");

/* Required by deposit/index.php */

if (isset($_POST['paymeth'])) {
	$post_sid = mysqli_real_escape_string($db, $_POST['paymeth']);
	$check_paymeth = mysqli_query($db, "SELECT * FROM deposit_method WHERE id = '$post_sid' AND Active ='YES'");
	if (mysqli_num_rows($check_paymeth) == 1) {
		$data_service = mysqli_fetch_assoc($check_paymeth);
	?>
												<div class="alert alert-info alert-dismissible" role="alert">
													<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">×</span>
													</button>
													<i class="mdi mdi-information"></i>
													<b>Target:</b> <?php echo number_format($data_service['data']); ?><br />
													<b>Note:</b> <?php echo $data_service['note']; ?>
												</div>
	<?php
	} else {
	?>
												<div class="alert alert-icon alert-danger alert-dismissible fade in" role="alert">
													<button type="button" class="close" data-dismiss="alert" aria-label="Close">
														<span aria-hidden="true">&times;</span>
													</button>
													<i class="mdi mdi-danger"></i>
													<b>Error:</b> Method not found.
												</div>
	<?php
	}
} else {
?>
												<div class="alert alert-icon alert-danger alert-dismissible fade in" role="alert">
													<button type="button" class="close" data-dismiss="alert" aria-label="Close">
														<span aria-hidden="true">&times;</span>
													</button>
													<i class="mdi mdi-danger"></i>
													<b>Error:</b> Something went wrong.
												</div>
<?php
}