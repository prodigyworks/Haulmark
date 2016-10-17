<?php
	//Include database connection details
	require_once('system-db.php');
	
	start_db();
	
	$customerid = $_POST['customerid'];
	$existing = $_POST['existing'];
	$products = array();
	
	for ($i = 0; $i < count($existing); $i++) {
		$item = $existing[$i];
		
		array_push($products, $item['productid']);
	}
	
	$notin = ArrayToInClause($products);
	
	if (isset($_POST['currentbooking']) && $_POST['currentbooking'] != null) {
		$currentbooking = $_POST['currentbooking'];
		
		createBookingComboOptions("WHERE A.customerid = $customerid AND ((A.statusid = 7 AND A.id NOT IN ($notin)) OR A.id = $currentbooking)");
		
	} else {
		createBookingComboOptions("WHERE A.customerid = $customerid AND A.statusid = 7 AND A.id NOT IN ($notin)");
	}
	
?>