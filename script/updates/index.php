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
	
	$title = "News & Updates";
	include("../lib/header.php");
	?>

<div class="page has-sidebar-left height-full">
    <header class="blue accent-3 relative nav-sticky">
        <div class="container-fluid text-white">
            <div class="row p-t-b-10 ">
                <div class="col">
                    <h4>
                        <i class="icon icon-newspaper-o s-18"></i>
                        News & Updates
                    </h4>
                </div>
            </div>
        </div>
    </header>
    <div class="container-fluid my-3 relative animatedParent animateOnce">
        <div class="tab-content pb-3" id="v-pills-tabContent">
            <!-- NEWS AND UPDATES -->
            <div class="tab-pane animated fadeInUpShort show active" id="dashboard">
                <div class="row">
                    <div class="col-md-12">
                        <div class="white p-5 r-5">
						<div class="table-responsive">
                            <div class="card-title">
                                <h5> News & Updates</h5>
                            </div>
                            <table class="table table-bordered">
                                    <thead>
					                	<tr>
                                        <th width="15%">Date</th>
                                        <th width="10%">Category</th>
						                <th>Content</th>
					                	</tr>
					                </thead>
									<tbody>
										<?php
										// start paging config
										$query_list = "SELECT * FROM news ORDER BY id DESC"; // edit
										$records_per_page = 5; // edit

										$starting_position = 0;
										if(isset($_GET["page_no"])) {
											$starting_position = ($_GET["page_no"]-1) * $records_per_page;
										}
										$new_query = $query_list." LIMIT $starting_position, $records_per_page";
										$new_query = mysqli_query($db, $new_query);
										// end paging config
												$no = 1;
												while ($data_news = mysqli_fetch_assoc($new_query)) {
													if($data_news['status'] == "INFO") {
														$label = "info";
														$label2 = "INFO";
													} else if($data_news['status'] == "NEW SERVICE") {
														$label = "success";
														$label2 = "NEW SERVICE";
													} else if($data_news['status'] == "SERVICE") {
														$label = "success";
														$label2 = "SERVICE";														
													} else if($data_news['status'] == "MAINTENANCE") {
														$label = "danger";
														$label2 = "MAINTENANCE";																										
													} else if($data_news['status'] == "UPDATE") {
														$label = "warning";
														$label2 = "UPDATE";						
													}
										?>
										<tr>
										    <td align="center"><?php echo $data_news['date']; ?> <?php echo $data_news['time']; ?></td></td>
											<td align="center"><label class="badge badge-<?php echo $label; ?>"><?php echo $data_news['status']; ?></label></td>
											<td><?php echo nl2br($data_news['content']); ?></td>
										</tr>
									    <?php
										$no++;
										}
										?>
									</tbody>
                           </table>
                        </div>
                </div> </div>
                </div>
            </div>
			<!-- Service Updates -->
            <div class="tab-pane animated fadeInUpShort show active" id="updates">
                <div class="row my-3">
                    <div class="col-md-12">
                        <div class="white p-5 r-5">
                        	<div class="table-responsive">
                            <div class="card-title">
                                <h5>Service Updates</h5>
                            </div>
                            <table class="table table-bordered">
                                    <thead>
                                    <tr>
                                        <th width="15%">Date</th>
                                        <th width="70%">Service</th>
						                <th>Status</th>
                                    </tr>
									</thead>
									<tbody>
                                    <?php
													$limit = 15; // EDIT LIMIT FOR NUMBER OF UPDATES
													$check_news = mysqli_query($db, "SELECT * FROM history_update ORDER BY id DESC LIMIT $limit");
													$no = 1;
													while ($data_news = mysqli_fetch_assoc($check_news)) {
													?>
											<tr>
											    <td scope="row"><?php echo $data_news['date']; ?></td>
												<td><b><?php echo $data_news['sid']; ?></b> - <?php echo $data_news['service']; ?> - $ <?php echo number_format($data_news['rate'],4); ?>/1k</td>
												<td><?php echo $data_news['status']; ?></td>
											</tr>
													<?php
													$no++;
													}
													?>
                                    </tbody>
                           </table>
                        </div>
                    </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
            
<?php
	include("../lib/footer.php");
} else {
	header("Location: ".$cfg_baseurl);
}