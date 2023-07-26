<?php
if ($_POST['category'] == 'Instagram Custom Comments') { ?>
	<div class="form-group">
		<label class="col-md-2 control-label">Target</label>
		<div class="col-md-10">
			<input type="text" name="link" class="form-control" placeholder="Link/Target">
		</div>
	</div>
	<div class="form-group">
		<label class="col-md-2 control-label">Comment Data</label>
		<div class="col-md-10">
			<textarea class="form-control" name="comments" rows="5" id="comments" placeholder="Pisahkan tiap baris komentar dengan enter"></textarea>
		</div>
	</div>
	<div class="form-group">
		<label class="col-md-2 control-label">Quantity</label>
		<div class="col-md-10">
			<input type="number" name="quantity" class="form-control" placeholder="Quantity" onkeyup="get_total(this.value).value;">
		</div>
	</div>
	<input type="hidden" id="rate" value="0">
	<div class="form-group">
		<label class="col-md-2 control-label">Price</label>
		<div class="col-md-10">
			<div class="input-group"><span class="input-group-addon">Rp </span>
				<input type="number" class="form-control" id="total" value="0" disabled>
			</div>
		</div>
	</div>
<?php } else  
if ($_POST['category'] == 'Instagram Comments Likes') { ?>
	<div class="form-group">
		<label class="col-md-2 control-label">Username</label>
		<div class="col-md-10">
			<input type="text" name="custom_link" class="form-control" placeholder="Username">
		</div>
	</div>
	<div class="form-group">
		<label class="col-md-2 control-label">Target</label>
		<div class="col-md-10">
			<input type="text" name="link" class="form-control" placeholder="Link/Target">
		</div>
	</div>
	<div class="form-group">
		<label class="col-md-2 control-label">Quantity</label>
		<div class="col-md-10">
			<input type="number" name="quantity" class="form-control" placeholder="Quantity" onkeyup="get_total(this.value).value;">
		</div>
	</div>
	<input type="hidden" id="rate" value="0">
	<div class="form-group">
		<label class="col-md-2 control-label">Price</label>
		<div class="col-md-10">
			<div class="input-group"><span class="input-group-addon">Rp </span>
				<input type="number" class="form-control" id="total" value="0" disabled>
			</div>
		</div>
	</div>
<?php } else { ?>
	<div class="form-group">
		<label class="col-md-2 control-label">Target</label>
		<div class="col-md-10">
			<input type="text" name="link" class="form-control" placeholder="Link/Target">
		</div>
	</div>
	<div class="form-group">
		<label class="col-md-2 control-label">Quantity</label>
		<div class="col-md-10">
			<input type="number" name="quantity" id="quantity" class="form-control" placeholder="Quantity" onkeyup="get_total(this.value).value;" onkeypress="get_total(this.value).value;">
		</div>
	</div>
	<input type="hidden" id="rate" value="0">
	<div class="form-group">
		<label class="col-md-2 control-label">Price</label>
		<div class="col-md-10">
			<div class="input-group"><span class="input-group-addon">Rp </span>
				<input type="number" class="form-control" id="total" value="0" disabled>
			</div>
		</div>
	</div>
<?php } ?>