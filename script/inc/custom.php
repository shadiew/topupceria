<?php
require("../lib/mainconfig.php");

if (isset($_POST['custom'])) {
	$post_sid = mysqli_real_escape_string($db, $_POST['custom']);
	$check_service = mysqli_query($db, "SELECT * FROM services WHERE sid = '$post_sid' AND status = 'Active'");
	if (mysqli_num_rows($check_service) == 1) {
		$data_service = mysqli_fetch_assoc($check_service);
		$servicename = $data_service['service'];
		$kata = 'Likes Komentar';
		$run = strpos($kata, $servicename);
		$nama_service = $data_service['service'];
		function RemoveSpecialChar($nama_service)
		{
			$result  = preg_replace('/[^a-zA-Z0-9_ -]/s', '', $nama_service);

			return $result;
		}
		if (preg_match_all('/^(?=.*Likes)(?=.*Komentar)/i', $data_service['service'])) {
?>
			<div class="form-group">
				<div class="form-group">
					<label class="control-label">Target / Link</label>
					<div>
						<input type="text" name="custom_link" class="form-control" placeholder="Link/Target">
					</div>
				</div>
				<div class="form-group">
					<label class="control-label">Username</label>
					<div>
						<input type="text" name="link" class="form-control" placeholder="Username">
					</div>
				</div>
				<div class="form-group">
					<label class="control-label">Quantity</label>
					<div>
						<input type="number" name="quantity" class="form-control" placeholder="Quantity" onkeyup="get_total(this.value).value;">
					</div>
				</div>
				<input type="hidden" id="rate" value="0">
				<div class="form-group">
					<label class="control-label">Price</label>
					<div>
						<div class="input-group"><span class="input-group-addon">Rp </span>
							<input type="number" class="form-control" id="total" value="0" disabled>
						</div>
					</div>
				</div>

			<?php } else	if (preg_match_all('/^(?=.*Mentions)(?=.*User)/i', $data_service['service'])) {
			?>
				<div class="form-group">
					<div class="form-group">
						<label class="control-label">Target / Link</label>
						<div>
							<input type="text" name="custom_link" class="form-control" placeholder="Link/Target">
						</div>
					</div>
					<div class="form-group">
						<label class="control-label">Username</label>
						<div>
							<input type="text" name="link" class="form-control" placeholder="Username">
						</div>
					</div>
					<div class="form-group">
						<label class="control-label">Quantity</label>
						<div>
							<input type="number" name="quantity" class="form-control" placeholder="Quantity" onkeyup="get_total(this.value).value;">
						</div>
					</div>
					<input type="hidden" id="rate" value="0">
					<div class="form-group">
						<label class="control-label">Price</label>
						<div>
							<div class="input-group"><span class="input-group-addon">Rp </span>
								<input type="number" class="form-control" id="total" value="0" disabled>
							</div>
						</div>
					</div>

				<?php } else if (preg_match_all('/^(?=.*Mentions)(?=.*Custom)/i', $data_service['service'])) { ?>
					<div class="form-group">
						<label class="control-label">Target</label>
						<div>
							<input type="text" name="link" class="form-control" placeholder="Link/Target">
						</div>
					</div>

					<div class="form-group">
						<label class="control-label">Comment Data</label>
						<div>
							<textarea class="form-control" name="custom_mentions" rows="5" id="comments" placeholder="Separate each comment line with enter" onkeyup="get_count(this.value).value;"></textarea>
						</div>
					</div>
					<div class="form-group">
						<label class="control-label">Quantity</label>
						<div>
							<div class="input-group"><span class="input-group-addon"></span>
								<input type="number" class="form-control" name="quantity" id="jumlah" readonly>
							</div>
						</div>
					</div>
					<input type="hidden" id="rate" value="0">
					<div class="form-group">
						<label class="control-label">Price</label>
						<div>
							<div class="input-group"><span class="input-group-addon">Rp </span>
								<input type="number" class="form-control" id="total" value="0" disabled>
							</div>
						</div>
					</div>

				<?php } else if (preg_match_all('/^(?=.*Comments)(?=.*Custom)/i', $data_service['service'])) { ?>
					<div class="form-group">
						<label class="control-label">Target</label>
						<div>
							<input type="text" name="link" class="form-control" placeholder="Link/Target">
						</div>
					</div>

					<div class="form-group">
						<label class="control-label">Comment Data</label>
						<div>
							<textarea class="form-control" name="comments" rows="5" id="comments" placeholder="Separate each comment line with enter" onkeyup="get_count(this.value).value;"></textarea>
						</div>
					</div>
					<div class="form-group">
						<label class="control-label">Quantity</label>
						<div>
							<div class="input-group"><span class="input-group-addon"></span>
								<input type="number" class="form-control" name="quantity" id="jumlah" readonly>
							</div>
						</div>
					</div>
					<input type="hidden" id="rate" value="0">
					<div class="form-group">
						<label class="control-label">Price</label>
						<div>
							<div class="input-group"><span class="input-group-addon">Rp </span>
								<input type="number" class="form-control" id="total" value="0" disabled>
							</div>
						</div>
					</div>

				<?php } else { ?>
					<div class="form-group">
						<label class="control-label">Target</label>
						<div>
							<input type="text" name="link" class="form-control" placeholder="Link/Target">
						</div>
					</div>

					<div class="form-group">
						<label class="control-label">Quantity</label>
						<div>
							<input type="number" name="quantity" class="form-control" placeholder="Quantity" onkeyup="get_total(this.value).value;">
						</div>
					</div>
					<input type="hidden" id="rate" value="0">
					<div class="form-group">
						<label class="control-label">Price</label>
						<div>
							<div class="input-group"><span class="input-group-addon">Rp </span>
								<input type="number" class="form-control" id="total" value="0" disabled>
							</div>
						</div>
					</div>
		<?php }
		}
	} ?>