<?php
	require_once("system-db.php");
	require_once("businessobjects/BookingClass.php");
	require_once("businessobjects/BookingLegClass.php");
	
	start_db();
	
	$booking = new BookingClass();
	$booking->load($_POST['id']);
	
	$booking->setStartdatetime(convertStringToDateTime($_POST['startdatetime'] . " " . $_POST['startdatetime_time']));
	$booking->setEnddatetime(convertStringToDateTime($_POST['enddatetime'] . " " . $_POST['enddatetime_time']));
	$booking->setCustomerid($_POST['customerid']);
	$booking->setStatusid($_POST['statusid']);
	$booking->setMemberid($_POST['memberid']);
	$booking->setDriverid($_POST['driverid']);
	$booking->setVehicleid($_POST['vehicleid']);
	$booking->setVehicletypeid($_POST['vehicletypeid']);
	$booking->setTrailerid($_POST['trailerid']);
	$booking->setDrivername($_POST['drivername']);
	$booking->setAgencyvehicleregistration($_POST['agencyvehicleregistration']);
	$booking->setDriverphone($_POST['driverphone']);
	$booking->setWorktypeid($_POST['worktypeid']);
	$booking->setNominalledgercodeid($_POST['nominalledgercodeid']);
	$booking->setLoadtypeid($_POST['loadtypeid']);
	$booking->setOrdernumber($_POST['ordernumber']);
	$booking->setOrdernumber2($_POST['ordernumber2']);
	$booking->setMiles($_POST['miles']);
	$booking->setDuration($_POST['duration']);
	$booking->setPallets($_POST['pallets']);
	$booking->setWeight($_POST['weight']);
	$booking->setRate($_POST['rate']);
	$booking->setCharge($_POST['charge']);
	$booking->setFixedprice($_POST['fixedprice']);
	$booking->setNotes($_POST['notes']);
	$booking->setFromplace($_POST['fromplace']);
	$booking->setToplace($_POST['toplace']);
	$booking->setStartvisittype($_POST['startvisittype']);
	$booking->setEndvisittype($_POST['endvisittype']);
	$booking->setToplace_phone($_POST['toplace_phone']);
	$booking->setToplace_ref($_POST['toplace_ref']);
	$booking->setFromplace_phone($_POST['fromplace_phone']);
	$booking->setFromPlaceReference($_POST['fromplace_ref']);
	
	/* Clear down the existing legs. */
	$booking->clearLegs();

	if ($_POST['legs'] != null) {
		for ($i = 0; $i < count($_POST['legs']); $i++) {
			$node = $_POST['legs'][$i];
			
			$leg = new BookingLegClass();
			$leg->setPlace($node['place']);
			$leg->setDeparturetime(convertStringToDate($node['departuredate']) . " " . $node['departuretime']);
			$leg->setArrivaltime(convertStringToDate($node['arrivaldate']) . " " . $node['arrivaltime']);
			$leg->setReference($node['reference']);
			$leg->setPhone($node['phone']);
			$leg->setVisittype($node['visittype']);

			$booking->addLeg($leg);
		}
	}

	$booking->update();
	
	mysql_query("COMMIT");
?>
