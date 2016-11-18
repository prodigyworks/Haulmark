<?php
	include("system-header.php");
	
	$id = $_GET['id'];
	$sql = "SELECT A.id
			FROM {$_SESSION['DB_PREFIX']}invoice A 
			WHERE A.id = $id";
	$result = mysql_query($sql);
	
	if ($result) {
		while (($member = mysql_fetch_assoc($result))) {
			echo "<h1>The invoice reference is INV-" . sprintf("%06d", $id) . "</h1><br><br>";
		}
	}
?>	
<?php
	include("system-footer.php");
?>