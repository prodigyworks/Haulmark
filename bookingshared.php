<?php
	require_once("system-db.php");
	
	function getJourneyDescription($bookingid) {
		$sql ="SELECT A.place, A.id
			   FROM {$_SESSION['DB_PREFIX']}bookingleg A
			   WHERE A.bookingid = $bookingid
			   ORDER BY A.id";
		$itemresult = mysql_query($sql);
		
		$place = "";
		
		//Check whether the query was successful or not
		if($itemresult) {
			while (($itemmember = mysql_fetch_assoc($itemresult))) {
				if ($place != "") {
					$place = "$place -> " . str_replace(", United Kingdom", "", $itemmember['place']);
					
				} else {
					$place = str_replace(", United Kingdom", "", $itemmember['place']);
				}				
			}
			
		} else {
			logError($sql . " - " . mysql_error());
		}
		
		return $place;
	}		
?>