<?php
	require_once("system-mobileheader.php");
?>	
	<center>
<?php 
	$qry = "SELECT A.*, B.pagename, B.label
			FROM {$_SESSION['DB_PREFIX']}pagenavigation A
			INNER JOIN {$_SESSION['DB_PREFIX']}pages B
			ON B.pageid = A.childpageid
			WHERE A.pagetype = 'X'
			ORDER BY A.sequence";
	$result = mysql_query($qry);
	
	if (! $result) {
		logError($qry . " = " . mysql_error());
	}
	
	while (($member = mysql_fetch_assoc($result))) {
?>
		<div class='mobilemenu' onclick='navigate("<?php echo $member['pagename']; ?>")'>
			<?php echo $member['label']; ?>
		</div>	
<?php		
	}
?>
	</center>
	
<?php
	require_once("system-mobilefooter.php");
?>