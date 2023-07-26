<?php
require("../lib/mainconfig.php");

if (isset($_POST['category'])) {
	$post_cat = mysqli_real_escape_string($db, $_POST['category']);
	$check_service = mysqli_query($db, "SELECT * FROM services WHERE category = '$post_cat' AND status IN ('Active','normal') ORDER BY sid ASC");
?>
	<option value="0">Select one...</option>
	<?php
	while ($data_service = mysqli_fetch_assoc($check_service)) {
	?>
		<option value="<?php echo $data_service['sid']; ?>"><?php echo $data_service['service']; ?> -- <?php echo rupiah($data_service['price']); ?> per 1000</option>
	<?php
	}
} else {
	?>
	<option value="0">Error.</option>
<?php
}
