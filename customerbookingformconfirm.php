<?php
	include("system-header.php");
	
	$id = $_GET['id'];
	$sql = "SELECT A.id
			FROM {$_SESSION['DB_PREFIX']}booking A 
			WHERE A.id = $id";
	$result = mysql_query($sql);
	
	if ($result) {
		while (($member = mysql_fetch_assoc($result))) {
			echo "<h1>Your booking reference is " . getBookingReference($id) . "</h1><br><br>";
		}
	}
?>	
<div>
<?php
	echo getSiteConfigData()->webbookingconfirmation;
?>	
</div>
<?php
	include("system-footer.php");
?>