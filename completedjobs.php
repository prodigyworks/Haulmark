<?php
	require_once("bookingslib.php");
	
	$crud = new BookingCrud();
	$crud->sql = 
		   "SELECT A.*, B.registration AS trailername, C.name AS driversname, D.name AS customername, 
		    E.registration AS vehiclename, F.name AS vehicletypename, 
		    H.name AS statusname, I.fullname, J.name AS worktypename, K.name AS loadtypename,
		    L.name AS nominalledgercodename
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
			LEFT OUTER JOIN {$_SESSION['DB_PREFIX']}loadtype K 
			ON K.id = A.loadtypeid
			LEFT OUTER JOIN {$_SESSION['DB_PREFIX']}nominalledgercode L 
			ON L.id = A.nominalledgercodeid
			WHERE A.statusid = 7
			AND A.charge = 0
			ORDER BY A.id DESC";
	$crud->allowAdd = false;
	$crud->run();
?>