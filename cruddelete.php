<?php
	//Include database connection details
	require_once('system-db.php');
	
	start_db();
	
	function removeFromTable($id, $table, $pkname) {
		$qry = "DELETE FROM $table WHERE $pkname = $id";
		
		$result = mysql_query($qry);
		
		if (! $result) {
			logError($qry . " - " . mysql_error(), false);
		}
	}
	
	for ($i = 0; $i < count($_POST['table']); $i++) {
		removeFromTable(
				$_POST['id'], 
				$_POST['table'][$i], 
				$_POST['pkname'][$i]
			);
	}
		
	mysql_query("COMMIT");
?>