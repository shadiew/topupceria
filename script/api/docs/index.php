<?php
session_start();
require("../../lib/mainconfig.php");

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
}

$title = "API Doc";
include("../../lib/header.php");
?>
<div class="page has-sidebar-left height-full">
    <header class="blue accent-3 relative nav-sticky">
        <div class="container-fluid text-white">
            <div class="row p-t-b-10 ">
                <div class="col">
                    <h4>
                        <i class="icon-random"></i>
                        API Doc
                    </h4>
                </div>
            </div>
            <div class="row">
                <ul class="nav responsive-tab nav-material nav-material-white" id="v-pills-tab">
                    <li>
                        <a class="nav-link active" id="v-pills-1-tab" data-toggle="pill" href="#doc">
                            <i class="icon icon-home2"></i>API Doc</a>
                    </li>
                    <li>
                        <a class="nav-link" id="v-pills-2-tab" data-toggle="pill" href="#add"><i class="icon icon-add_shopping_cart mb-3"></i>Add Order</a>
                    </li>
                    <li>
                        <a class="nav-link" id="v-pills-3-tab" data-toggle="pill" href="#status"><i class="icon icon-signal"></i>Order Status</a>
                    </li>
                </ul>
            </div>
        </div>
    </header>
    <div class="container-fluid relative animatedParent animateOnce">
        <div class="tab-content pb-3" id="v-pills-tabContent">
            <!--Today Tab Start-->
            <div class="tab-pane animated fadeInUpShort show active" id="doc">
                <div class="row my-3">
                    <div class="col-md-12">
                        <div class="white p-5 r-5">
                            <div class="card-title">
                                <h5> API Doc</h5>
                            </div>
                            <table class="table table-bordered">
								<tbody>
                                    <tr>
										<td>HTTP Method</td>
										<td>POST</td>
									</tr>
									<tr>
										<td>Response format</td>
										<td>JSON</td>
									</tr>
                                    <tr>
										<td>API URL</td>
										<td><?php echo $cfg_baseurl; ?>/api/v1/</td>
									</tr>
                                    <tr>
										<td>API KEY</td>
										<td><?php echo $data_user['api_key']; ?></td>
									</tr>
									<!-- <tr>
										<td>Example PHP Code</td>
										<td><a href="<?php echo $cfg_baseurl; ?>/api_example.php" target="blank">Example</a></td>
									</tr> -->
								</tbody>
                           </table>
                        </div>
                    </div>
                </div>
            </div>
            <!--Today Tab End-->
            <!--Yesterday Tab Start-->
            <div class="tab-pane animated fadeInUpShort" id="add">
                <div class="row my-3">
                    <div class="col-md-12">
                        <div class="white p-5 r-5">
                            <div class="card-title">
                                <h5>Add Order</h5>
                            </div>
                            <table class="table table-bordered">
								<thead>
									<tr>
										<th>Parameters</th>
										<th>Description</th>
									</tr>
								</thead>
								<tbody>
                                    <tr>
										<td>key</td>
										<td>Your API key</td>
									</tr>
									<tr>
										<td>action</td>
										<td>add</td>
									</tr>
									<tr>
										<td>service</td>
										<td>Service ID <a href="<?php echo $cfg_baseurl; ?>/services">Check at price list</a></td>
									</tr>
									<tr>
										<td>link</td>
										<td>Link to page</td>
									</tr>
									<tr>
										<td>quantity</td>
										<td>Needed quantity</td>
									</tr>
								</tbody>
                           </table>
						   <b>Example Response</b><br />
<pre>
IF ORDER SUCCESS

{
  "order":"12345"
}

IF ORDER FAIL

{
  "error":"Incorrect request"
}
</pre>
                        </div>
                    </div>
                </div>
            </div>
            <!--Yesterday Tab Start-->
            <!--Yesterday Tab Start-->
            <div class="tab-pane animated fadeInUpShort" id="status">
                <div class="row my-3">
                    <div class="col-md-12">
                        <div class="white p-5 r-5">
                            <div class="card-title">
                                <h5>Order Status</h5>
                            </div>
                            <table class="table table-bordered">
								<thead>
                                    <tr>
										<th>Parameters</th>
										<th>Description</th>
									</tr>
								</thead>
								<tbody>
                                    <tr>
										<td>key</td>
										<td>Your API key</td>
									</tr>
									<tr>
										<td>action</td>
										<td>status</td>
									</tr>
									<tr>
										<td>order</td>
										<td>Your order id</td>
									</tr>
								</tbody>
                           </table>
						   <b>Example Response</b><br />
                            <pre>
                            IF CHECK STATUS SUCCESS

                            {
                            "charge":"10000",
                            "start_count":"123",
                            "status":"Success",
                            "remains":"0"
                            }

                            IF CHECK STATUS FAIL

                            {
                            "error":"Incorrect request"
                            }
                            </pre>
                        </div>
                    </div>
                </div>
            </div>
            <!--Yesterday Tab Start-->
        </div>
    </div>
</div>
<?php
include("../../lib/footer.php");
?>