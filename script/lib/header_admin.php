<?php
if (isset($_SESSION['user'])) {
        $email = $data_user['email'];
	$hp = $data_user['nohp'];
	$nama = $data_user['name'];
	
	$check_settings = mysqli_query($db, "SELECT * FROM settings WHERE id = '1'");
    $data_settings = mysqli_fetch_assoc($check_settings);
	if ($email == "") {
		header("Location: ".$cfg_baseurl."/settings/");
	}
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta http-equiv="Content-Language" content="en">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="description" content="<?php echo $data_settings['web_description']; ?>">
    <meta name="keywords" content="<?php echo $data_settings['seo_keywords']; ?>">
    <title><?php echo $data_settings['web_title']; ?></title>
    <link rel="icon" href="<?php echo $data_settings['link_fav']; ?>" type="image/x-icon">

    <!--HEADER TAG-->
    <?php echo $data_settings['seo_meta']; ?>
    <!--HEADER TAG END-->

    <!--GTAG TAG-->
    <?php echo $data_settings['seo_analytics']; ?>
    <!--GTAG TAG END-->

    <!-- CSS -->
    <link rel="stylesheet" href="<?php echo $cfg_baseurl; ?>/assets/css/app.css">
    <link rel="stylesheet" href="<?php echo $cfg_baseurl; ?>/css/style.css">
    <meta name="theme-color" content="#127AFB" />

    <!--EMBED CHAT TAG-->
    <?php echo $data_settings['seo_chat']; ?>
    <!--EMBED CHAT TAG END-->
</head>
<body class="light">
<div id="app">
<aside class="main-sidebar fixed offcanvas shadow">
    <section class="sidebar">
        <div class="w-80px mt-3 mb-3 ml-3">
            <a href="<?= $cfg_baseurl ?>" class="navbar-brand">
            <img src="<?php echo $data_settings['link_logo_dark']; ?>" alt="<?php echo $data_settings['web_name']; ?>" class="tsg-header-logo">
            </a>
        </div>
        <div class="relative">
            <a data-toggle="collapse" href="#userSettingsCollapse" role="button" aria-expanded="false"
               aria-controls="userSettingsCollapse" class="btn-fab btn-fab-sm fab-right fab-top btn-primary shadow1 ">
                <i class="icon icon-cogs"></i>
            </a>
            <div class="user-panel p-3 light mb-2">
                <div>
                    <div class="float-left image">
                        <img class="user_avatar" src="<?php echo $cfg_baseurl; ?>/assets/img/dummy/u1.png" alt="User Image">
                    </div>
                    <div class="float-left info">
                        <h6 class="font-weight-light mt-2 mb-1"><?php echo $data_user['username']; ?></h6>
                        <a href="#"><i class="icon-circle text-primary blink"></i> Online</a>
                    </div>
                </div>
                <div class="clearfix"></div>
                <div class="collapse multi-collapse" id="userSettingsCollapse">
                    <div class="list-group mt-3 shadow">
                        <a href="<?php echo $cfg_baseurl; ?>/settings/" class="list-group-item list-group-item-action"><i
                                class="mr-2 icon-cogs text-yellow"></i>Settings</a>
                        <a href="<?php echo $cfg_baseurl; ?>/logout/" class="list-group-item list-group-item-action">
								<i class="mr-2 icon-umbrella text-blue"></i>Log Out</a>
                    </div>
                </div>
            </div>
        </div>
        <ul class="sidebar-menu">
            <!-- ADMIN MENU -->
            <?php
			if ($data_user['level'] == "Developers") {
			?>

            <li class="treeview"><a href="<?php echo $cfg_baseurl; ?>/admin">
                <i class="icon icon-home2 s-18"></i><span> Admin Dashboard</span> 
            </a>
            </li>
            <li class="treeview"><a href="#"><i class="icon icon-settings2 s-18"></i>Settings<i class="icon icon-angle-left s-18 pull-right"></i></a>
                <ul class="treeview-menu">
                    <li><a href="<?php echo $cfg_baseurl; ?>/admin/settings/general.php"><i class="icon icon-settings2"></i>General</a></li>
                    <li><a href="<?php echo $cfg_baseurl; ?>/admin/settings/seo.php"><i class="icon icon-settings2"></i>SEO</a></li>
                    <li><a href="<?php echo $cfg_baseurl; ?>/admin/settings/pages.php"><i class="icon icon-settings2"></i>Pages</a></li>
                    <li><a href="<?php echo $cfg_baseurl; ?>/admin/settings/payment.php"><i class="icon icon-settings2"></i>Payment</a></li>
                    <li><a href="<?php echo $cfg_baseurl; ?>/admin/depo/"><i class="icon icon-settings2"></i>Manual Payment</a></li>
                    <li><a href="<?php echo $cfg_baseurl; ?>/admin/settings/instructions.php"><i class="icon icon-settings2"></i>Instructions</a></li>
                </ul>
            </li>
            <li class="treeview"><a href="#"><i class="icon icon-box-filled s-18"></i>Packages<i class="icon icon-angle-left s-18 pull-right"></i></a>
                <ul class="treeview-menu">
                    <li><a href="<?php echo $cfg_baseurl; ?>/admin/api/"><i class="icon icon-box"></i>Providers</a></li>
                    <li><a href="<?php echo $cfg_baseurl; ?>/admin/category/"><i class="icon icon-box"></i>Categories</a></li>
                    <li><a href="<?php echo $cfg_baseurl; ?>/admin/service/"><i class="icon icon-box"></i>Services</a></li>
                    <li><a href="<?php echo $cfg_baseurl; ?>/admin/news/"><i class="icon icon-box"></i>Updates</a></li>
                    <li><a href="<?php echo $cfg_baseurl; ?>/admin/delete/"><i class="icon icon-box"></i>Delete All Services</a></li>
                    <li><a href="<?php echo $cfg_baseurl; ?>/admin/grab/"><i class="icon icon-box"></i>Grab Services(Bulk)</a></li>
                </ul>
            </li>
            <li class="treeview"><a href="#"><i class="icon icon-user-o s-18"></i>Users<i class="icon icon-angle-left s-18 pull-right"></i></a>
                <ul class="treeview-menu">
                    <li><a href="<?php echo $cfg_baseurl; ?>/admin/user/"><i class="icon icon-user-o"></i>Manage Users</a></li>
                    <li><a href="<?php echo $cfg_baseurl; ?>/admin/user/add.php"><i class="icon icon-user-o"></i>Add New User</a></li>
                </ul>
            </li>
            <li class="treeview"><a href="#"><i class="icon icon-shopping-cart2 s-18"></i>Orders<i class="icon icon-angle-left s-18 pull-right"></i></a>
                <ul class="treeview-menu">
                    <li><a href="<?php echo $cfg_baseurl; ?>/admin/order/"><i class="icon icon-shopping-cart2"></i>Manage Orders</a></li>
                </ul>
            </li>
            <li class="treeview"><a href="#"><i class="icon icon-credit-card s-18"></i>Deposits<i class="icon icon-angle-left s-18 pull-right"></i></a>
                <ul class="treeview-menu">
                    <li><a href="<?php echo $cfg_baseurl; ?>/admin/deposit/"><i class="icon icon-credit-card"></i>Manage Deposits</a></li>
                    <li><a href="<?php echo $cfg_baseurl; ?>/admin/transfer/"><i class="icon icon-credit-card"></i>Add Funds</a></li>
                </ul>
            </li>
            <li class="treeview"><a href="#"><i class="icon icon-chat_bubble_outline s-18"></i>Tickets<i class="icon icon-angle-left s-18 pull-right"></i></a>
                <ul class="treeview-menu">
                    <li><a href="<?php echo $cfg_baseurl; ?>/admin/tickets/"><i class="icon icon-chat_bubble_outline"></i>Manage Tickets</a></li>
                </ul>
            </li>
            <li class="treeview"><a href="#"><i class="icon icon-help_outline s-18"></i>FAQ<i class="icon icon-angle-left s-18 pull-right"></i></a>
                <ul class="treeview-menu">
                    <li><a href="<?php echo $cfg_baseurl; ?>/admin/faq/"><i class="icon icon-help_outline"></i>Manage FAQ</a></li>
                </ul>
            </li>
            <li class="treeview"><a href="#"><i class="icon icon-assignment_ind s-18"></i>Staff<i class="icon icon-angle-left s-18 pull-right"></i></a>
                <ul class="treeview-menu">
                    <li><a href="<?php echo $cfg_baseurl; ?>/admin/staff/"><i class="icon icon-assignment_ind"></i>Manage Staff</a></li>
                </ul>
            </li>

            <li class="header light mt-3"><strong>User</strong></li>

            <li class="treeview"><a href="<?php echo $cfg_baseurl; ?>">
                <i class="icon icon-home2 s-18"></i><span> User Dashboard</span> 
            </a>
            </li>

			<?php
			}
			?>
            </ul>
    </section>
</aside>
<!--Sidebar End-->
<div class="has-sidebar-left">
    <div class="pos-f-t">
    <div class="collapse" id="navbarToggleExternalContent">
        <div class="bg-dark pt-2 pb-2 pl-4 pr-2">
            <div class="search-bar">
                <input class="transparent s-24 text-white b-0 font-weight-lighter w-128 height-50" type="text"
                       placeholder="start typing...">
            </div>
            <a href="#" data-toggle="collapse" data-target="#navbarToggleExternalContent" aria-expanded="false"
               aria-label="Toggle navigation" class="paper-nav-toggle paper-nav-white active "><i></i></a>
        </div>
    </div>
</div>
    <div class="sticky">
        <div class="navbar navbar-expand navbar-dark d-flex justify-content-between bd-navbar blue accent-3">
            <div class="relative">
                <a href="#" data-toggle="offcanvas" class="paper-nav-toggle pp-nav-toggle">
                    <i></i>
                </a>
            </div>
            <!--Top Menu Start -->
<div class="navbar-custom-menu p-t-10">
    <ul class="nav navbar-nav">
        <!-- User Account-->
        <li class="dropdown custom-dropdown user user-menu">
            <a href="#" class="nav-link" data-toggle="dropdown">
                <i class="icon-more_vert "></i>
            </a>
            <div class="dropdown-menu p-4">
                <div class="row box justify-content-between my-4">
                    <div class="col"><a href="<?php echo $cfg_baseurl; ?>/order/">
                        <i class="icon-shopping-cart blue accent-2 avatar  r-5"></i>
                        <div class="pt-1">New Order</div>
                    </a></div>
                    <div class="col"><a href="<?php echo $cfg_baseurl; ?>/paypal/">
                        <i class="icon-money red avatar lighten-2 r-5"></i>
                        <div class="pt-1">New Deposit</div>
                    </a></div>
                    <div class="col">
                        <a href="<?php echo $cfg_baseurl; ?>/services/">
                            <i class="icon-tags orange lighten-1 avatar  r-5"></i>
                            <div class="pt-1">Services</div>
                    </a></div>
                    <div class="col">
                        <a href="<?php echo $cfg_baseurl; ?>/history/order/">
                            <i class="icon-shopping-cart indigo lighten-2 avatar  r-5"></i>
                            <div class="pt-1 tsg-quick-menu-pre">My Orders</div>
                    </a></div>
                    <div class="col"><a href="<?php echo $cfg_baseurl; ?>/history/deposit/">
                        <i class="icon-credit-card light-green lighten-1 avatar  r-5"></i>
                        <div class="pt-1">My Deposits</div>
                    </a></div>
                    <div class="col">
                        <a href="<?php echo $cfg_baseurl; ?>/tickets/">
                            <i class="icon-email green lighten-1 avatar  r-5 accent white-text"></i>
                            <div class="pt-1">My Tickets</div>
                    </a></div>
                </div>
            </div>
        </li>
    </ul>
</div>
        </div>
    </div>
</div>