<!-- Right Sidebar -->
<aside class="control-sidebar fixed white ">
    <div class="slimScroll">
        <div class="sidebar-header">
            <h4>Orders Status</h4>
            <a href="#" data-toggle="control-sidebar" class="paper-nav-toggle  active"><i></i></a>
        </div>
        <div class="table-responsive">
            <table id="recent-orders" class="table table-hover mb-0 ps-container ps-theme-default">
                <tbody>
				<?php
				// start paging config
				$query_order = mysqli_query($db, "SELECT * FROM orders WHERE user = '$sess_username' ORDER BY id DESC LIMIT 12"); // edit
				// end paging config
				while ($data_order = mysqli_fetch_assoc($query_order)) {
					if($data_order['status'] == "Pending") {
						$label = "warning";
					} else if($data_order['status'] == "Processing") {
						$label = "info";
					} else if($data_order['status'] == "In Progress") {
						$label = "info";
					} else if($data_order['status'] == "Error") {
						$label = "danger";
					} else if($data_order['status'] == "Canceled") {
						$label = "danger";
					} else if($data_order['status'] == "Partial") {
						$label = "danger";
					} else if($data_order['status'] == "Success") {
						$label = "success";
					} else if($data_order['status'] == "Completed") {
						$label = "success";
					}
					?>
                <tr>
                    <td>
                        <?php echo $data_order['oid']; ?>
                    </td>
                    <td>
                        <label class="badge badge-<?php echo $label; ?>"><?php echo $data_order['status']; ?></label>
                    </td>
                    <td>$ <?php echo $data_order['price']; ?></td>
                </tr>
				<?php
				}
				?>
                </tbody>
            </table>
        </div>
    </div>
</aside>
<!-- /.right-sidebar -->
<!-- Add the sidebar's background. This div must be placed
         immediately after the control sidebar -->
<div class="control-sidebar-bg shadow white fixed"></div>
</div>
<!--/#app -->
<script src="<?php echo $cfg_baseurl; ?>/assets/js/app.js"></script>
</body>
</html>