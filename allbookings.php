<?php
	require_once("bookingslib.php");
	
	$and = "";
	
	if (isset($_GET['date'])) {
		$date = convertStringToDate($_GET['date']);
		$and = "WHERE (DATE(A.startdatetime) = '$date' OR DATE(A.enddatetime) = '$date') ";
	}
	
	$crud = new BookingCrud();
	$crud->columns[2]['filter'] = true;
	$crud->sql = 
		   "SELECT A.*, B.registration AS trailername, C.name AS driversname, D.name AS customername, 
		    E.registration AS vehiclename, F.name AS vehicletypename, 
		    H.name AS statusname, I.fullname, J.name AS worktypename,
		    L.name AS nominalledgercodename,
		    (
		    	SELECT M.arrivaltime 
		    	FROM {$_SESSION['DB_PREFIX']}bookingleg M  
		    	WHERE M.bookingid = A.id
		    	ORDER BY M.id
		    	LIMIT 1
		    ) AS startlegdatetime
			FROM {$_SESSION['DB_PREFIX']}booking A 
			LEFT OUTER JOIN {$_SESSION['DB_PREFIX']}trailer B 
			ON B.id = A.trailerid 
			LEFT OUTER JOIN {$_SESSION['DB_PREFIX']}driver C 
			ON C.id = A.driverid 
			LEFT OUTER JOIN {$_SESSION['DB_PREFIX']}customer D 
			ON D.id = A.customerid 
			LEFT OUTER JOIN {$_SESSION['DB_PREFIX']}vehicle E 
			ON E.id = A.vehicleid 
			LEFT OUTER JOIN {$_SESSION['DB_PREFIX']}vehicletype F 
			ON F.id = A.vehicletypeid 
			LEFT OUTER JOIN {$_SESSION['DB_PREFIX']}bookingstatus H 
			ON H.id = A.statusid 
			LEFT OUTER JOIN {$_SESSION['DB_PREFIX']}members I 
			ON I.member_id = A.memberid 
			LEFT OUTER JOIN {$_SESSION['DB_PREFIX']}worktype J 
			ON J.id = A.worktypeid
			LEFT OUTER JOIN {$_SESSION['DB_PREFIX']}nominalledgercode L 
			ON L.id = A.nominalledgercodeid
			$and
			ORDER BY A.id DESC";
			
	$crud->subapplications = array(
			array(
				'title'		  => 'Map',
				'imageurl'	  => 'images/map.png',
				'script' 	  => 'showMap'
			),
			array(
				'id'		  => 'deliverynotebutton',
				'title'		  => 'Delivery Note',
				'imageurl'	  => 'images/print.png',
				'script' 	  => 'printDeliveryNote'
			)
		);
	
	$crud->allowAdd = false;
	$crud->allowRemove = isUserInRole("DIRECTORS");
	$crud->allowEdit = true;
	$crud->run();
?>