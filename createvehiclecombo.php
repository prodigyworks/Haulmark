<?php
	//Include database connection details
	require_once('system-db.php');
	
	start_db();
	
	$vehicletypeid = $_POST['vehicletypeid'];
	$groupage = false;
	
	$sql = "SELECT groupage 
			FROM {$_SESSION['DB_PREFIX']}vehicletype
			WHERE id = $vehicletypeid";
			
	$result = mysql_query($sql);
	
	if (! $result) {
		logError("$sql - " . mysql_error());
	}
	
	while (($member = mysql_fetch_assoc($result))) {
		if ($member['groupage'] == "Y") {
			$groupage = true;
		}
	}

	if ($vehicletypeid == 0 || $groupage) {
		$where = "WHERE active = 'Y'";
		
	} else {
		$where = "WHERE vehicletypeid = $vehicletypeid AND active = 'Y'";
	}
	
	ob_start();
	createComboOptions("id", "registration", "{$_SESSION['DB_PREFIX']}vehicle", $where);
	$combo = ob_get_clean();
	
	$array = array(
			'data'	 	=> $combo,
			'groupage'	=> $groupage
		);
	
	echo json_encode($array);
?>