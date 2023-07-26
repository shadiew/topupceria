<?php
require("../lib/mainconfig.php");

if (isset($_POST['service'])) {
	$post_sid = mysqli_real_escape_string($db, $_POST['service']);
	$check_service = mysqli_query($db, "SELECT * FROM services WHERE sid = '$post_sid' AND status IN ('Active','normal')");
	if (mysqli_num_rows($check_service) == 1) {
		$data_service = mysqli_fetch_assoc($check_service);
?>
		<div class="alert alert-info alert-dismissible" role="alert">
			<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">×</span>
			</button>
			<i class="mdi mdi-information"></i>
			<b>Min. Order:</b> <?php echo number_format($data_service['min']); ?><br />
			<b>Max. Order:</b> <?php echo number_format($data_service['max']); ?><br />
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
			<b>Error:</b> Service not found.
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
