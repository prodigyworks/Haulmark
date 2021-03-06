<?php
	require_once('system-db.php');
	require_once('businessobjects/MessageClass.php');
	require_once('businessobjects/UserClass.php');
	
	if(!isset($_SESSION)) {
		session_start();
	}
	
	if (! isAuthenticated() && ! endsWith($_SERVER['PHP_SELF'], "/system-login.php")) {
		header("location: system-login.php?session=" . urlencode(base64_encode($_SERVER['PHP_SELF'] . "?" . $_SERVER['QUERY_STRING'] )));
		exit();
	}
	
	function showBreadCrumb() {
		BreadCrumbManager::showBreadcrumbTrail();
	}
?>
<?php 
	//Include database connection details
	require_once('system-config.php');
	require_once("confirmdialog.php");
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
<head>
<title>Truck-Net</title>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<meta http-equiv="X-UA-Compatible" content="IE=Edge" />
<link rel="shortcut icon" href="favicon.ico">

<link href="css/style-20012017.css" rel="stylesheet" type="text/css" />
<link href="css/jquery-ui-1.10.3.custom.css" rel="stylesheet" type="text/css" />
<link href="css/dcmegamenu.css" rel="stylesheet" type="text/css" />
<link href="css/skins/white.css" rel="stylesheet" type="text/css" />


<script src="js/jquery-1.8.0.min.js" type="text/javascript"></script>
<script src="js/jquery.min.js" type="text/javascript"></script>
<script src="js/jquery-ui.min.js" type="text/javascript"></script>
<script src='js/jquery.hoverIntent.minified.js' type='text/javascript'></script>
<script src='js/jquery.dcmegamenu.1.3.3.js' type='text/javascript'></script>
<script src="js/prodigyworks-20012017.js" language="javascript" ></script>
<script src="js/businessobject-20161120.js" language="javascript" ></script>

<!--[if lt IE 7]>
<script type="text/javascript" src="js/ie_png.js"></script>
<script type="text/javascript">
	ie_png.fix('.png, .carousel-box .next img, .carousel-box .prev img');
</script>
<link href="css/ie6.css" rel="stylesheet" type="text/css" />
<![endif]-->
</head>
<body id="page1">
<?php
	createConfirmDialog("passworddialog", "Forgot password ?", "forgotPassword");
	
	if (isset($_POST['command'])) {
		$_POST['command']();
	}
?>
	<form method="post" id="commandForm" name="commandForm">
		<input type="hidden" id="command" name="command" />
		<input type="hidden" id="pk1" name="pk1" />
		<input type="hidden" id="pk2" name="pk2" />
		<input type="hidden" id="pk3" name="pk3" />
	</form>
	
	<TABLE style="BORDER-COLLAPSE: collapse" cellSpacing=0 cellPadding=0 width='100%' align=left >
		<TR>
			<TD>
				<div class="tail-top">
				<!-- header -->
				<?php 
					if (isAuthenticated()) {
				?>
					<div id="header" class='header1'>
<?php		
						if (($_SESSION['SESS_CUSTOMER_IMAGEID'] != null && $_SESSION['SESS_CUSTOMER_IMAGEID'] != 0) ||
							($_SESSION['SESS_SUPPLIER_IMAGEID'] != null && $_SESSION['SESS_SUPPLIER_IMAGEID'] != 0)) {
?>
						<div id="partnerimage">
							<label>In Partnership With:</label>
							<br>
								<img src="system-imageviewer.php?id=<?php echo $_SESSION['SESS_CUSTOMER_IMAGEID'] | $_SESSION['SESS_SUPPLIER_IMAGEID']; ?>" />
						</div>
<?php		
						}
?>
<?php		
						UserClass::auditAccess();
						$messagecount = MessageClass::getUnreadMessageCountForUser(getLoggedOnMemberID());
						$emailImage = "email.gif";
						
						if (isset($_SESSION['SESSION_MESSAGE_COUNT'])) {
							if ($_SESSION['SESSION_MESSAGE_COUNT'] < $messagecount) {
								$emailImage = "email_new.png";
							}
						}
?>
						<div id="toppanel">
							<div class="profileimage">
								<a class="messages" href="messages.php">
									<img src='images/<?php echo $emailImage; ?>'></img>&nbsp;
<?php 
									echo "($messagecount)";
?>
								</a>
<?php 
								if (getLoggedOnImageID() != null && getLoggedOnImageID() != 0) {
?>	  
									<img id="profileimage_img" src='system-imageviewer.php?id=<?php echo getLoggedOnImageID(); ?>' />
<?php 
								} else {
?>	  
									<img id="profileimage_img" src='images/noprofile.png'  />
<?php 
								}
?>									
								<div class='profileimageselector'>
									<img src='images/minimize.gif' />
								</div>
								<ul id="profileimageselectormenu" class="submenu">
									<li onclick='navigate("profile.php");'>&nbsp;&nbsp;<img src='images/edit.png' />&nbsp;Edit Profile&nbsp;&nbsp;</a></li>
									<li onclick='navigate("system-logout.php");'>&nbsp;&nbsp;<img src='images/logout2.png' />&nbsp;Log Out&nbsp;&nbsp;</a></li>
								</ul>
							</div>
						</div>
					</div>
				<?php		
					}
				?>
				<!-- content -->
					<div id="content">
						<div class="row-1">
							<div class="inside">
								<div class="container">
									<div class="menu2">
										<div>
											<?php
												if (isAuthenticated()) {
													showMenu();
												}
											?>
										</div>
									</div>
									<?php 
										if (isAuthenticated()) {
											
								    		if (isset($_GET['callee'])) {
												cache_function("showBreadCrumb", array("pageid" => $_SESSION['pageid'], "callee" => $_GET['callee']));
												
								    		} else {
												cache_function("showBreadCrumb", array("pageid" => $_SESSION['pageid']));
								    		}
										
											echo "<hr>\n";
										}
									?>
									<div class="content">
