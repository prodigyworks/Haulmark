<?php
	require_once("AbstractBaseDataClass.php");
	require_once("BookingLegClass.php");
	require_once("CustomerClass.php");
	require_once("DriverClass.php");
	require_once("TrailerClass.php");
	require_once("VehicleClass.php");
	require_once("BookingStatusClass.php");
	
	class BookingClass extends AbstractBaseDataClass {
		
		/* Booking level properties. */
		
		/** @property **/
		private $id = null;
		/** @property **/
		private $vehicleid = null;
		/** @property **/
		private $driverid = null;
		/** @property **/
		private $trailerid = null;
		/** @property **/
		private $customerid = null;
		/** @property **/
		private $signatureid = null;
		/** @property **/
		private $loadtypeid = null;
		/** @property **/
		private $vehicletypeid = null;
		/** @property **/
		private $worktypeid = null;
		/** @property **/
		private $pallets = null;
		/** @property **/
		private $memberid = null;
		/** @property @datetime **/
		private $startdatetime = null;
		/** @property @datetime **/
		private $enddatetime = null;
		/** @property **/
		private $rate = null;
		/** @property **/
		private $charge = null;
		/** @property **/
		private $weight = null;
		/** @property **/
		private $miles = null;
		/** @property **/
		private $vehiclecostoverhead = null;
		/** @property **/
		private $allegrodayrate = null;
		/** @property **/
		private $agencydayrate = null;
		/** @property **/
		private $wages = null;
		/** @property **/
		private $fuelcostoverhead = null;
		/** @property **/
		private $maintenanceoverhead = null;
		/** @property **/
		private $profitmargin = null;
		/** @property **/
		private $customercostpermile = null;
		/** @property **/
		private $bookingtype = null;
		/** @property **/
		private $postedtosage = null;
		/** @property **/
		private $statusid = null;
		/** @property **/
		private $nominalledgercodeid = null;
		/** @property **/
		private $legsummary = null;
		/** @property **/
		private $duration = null;
		/** @property **/
		private $ordernumber = null;
		/** @property **/
		private $ordernumber2 = null;
		/** @property **/
		private $drivername = null;
		/** @property **/
		private $driverphone = null;
		/** @property **/
		private $fromplace = null;
		/** @property **/
		private $toplace = null;
		/** @property **/
		private $fromplace_lat = null;
		/** @property **/
		private $fromplace_lng = null;
		/** @property **/
		private $fromplace_phone = null;
		/** @property **/
		private $fromplace_ref = null;
		/** @property **/
		private $toplace_lat = null;
		/** @property **/
		private $toplace_lng = null;
		/** @property **/
		private $toplace_phone = null;
		/** @property **/
		private $toplace_ref = null;
		/** @property **/
		private $startvisittype = null;
		/** @property **/
		private $endvisittype = null;
		/** @property **/
		private $notes = null;
		/** @property **/
		private $agencyvehicleregistration = null;
		/** @property **/
		private $fixedprice = null;
		/** @property **/
		private $podsent = null;
		/** @property **/
		private $invoiced = null;
		/** @property **/
		private $confirmed = null;
		/** @property @datetime **/
		private $metacreateddate = null;
		/** @property @datetime **/
		private $metamodifieddate = null;
		/** @property **/
		private $metamodifieduserid = null;
		/** @property **/
		private $metacreateduserid = null;
		
		/** @property (meta) **/
		private $originalstatusid = null;
		/** @property (meta) **/
		private $originalcharge = null;
		
		/** @onetoone **/
		private $customer = null;
		/** @onetoone **/
		private $driver = null;
		/** @onetoone **/
		private $trailer = null;
		/** @onetoone **/
		private $vehicle = null;
		/** @onetoone **/
		private $status = null;
		
		/** @onetomany **/
		private $legs = array();
		
		/**
		 * Constructor 
		 */
	    public function __construct() {
	    	start_db();
	    }

	    /**
	     * Get formatted ID.
	     */
	    public function getFormattedID() {
			return getSiteConfigData()->bookingprefix . sprintf("%06d", $this->id);
	    }
	    
	    /**
	     * Get the utilisation percentage.
	     */
	    public function getUtilisationPercentage() {
	    	if ($this->legs == null) {
	    		$this->loadLegs();
	    	}
	    	
			$totalhours = (strtotime($this->enddatetime) - strtotime($this->startdatetime));
			$nonutilisationhours = 0;
			$lastpoint = $this->startdatetime;
			$visittype = $this->startvisittype;
			
			foreach ($this->getLegs() AS $leg) {
				/* Accumulate the none utilisation hours
				 * when neither a collection or delivery
				 * has occurred.
				 */
				if ($visittype == null || $visittype == "") {
					$nonutilisationhours += (strtotime($leg->getArrivaltime()) - strtotime($lastpoint));
				}
				
				$lastpoint = $leg->getDeparturetime();
				$visittype = $leg->getVisittype();
			}

			/* Check the return to time against last leg. */
			if ($this->getEndvisittype() == null || $this->getEndvisittype() == "") {
				$nonutilisationhours += (strtotime($this->enddatetime) - strtotime($lastpoint));
			}
			
			return number_format(100 - ($nonutilisationhours * (100 / $totalhours)), 0);
	    }
	    
	    /**
	     * Clear legs
	     * @throws Exception
	     */
	    public function clearLegs() {
			$sql = "DELETE FROM {$_SESSION['DB_PREFIX']}bookingleg 
					WHERE bookingid = {$this->id}";
			$result = mysql_query($sql);
			
			if (! $result) {
				throw new Exception("Cannot clear legs for booking {$this->id} - $sql - " . mysql_error());
			}
			
			$this->legs = array();
	    }

	    /**
	     * Load booking data
	     * @param int $id Booking ID
	     * @throws Exception
	     */
		public function load($id) {
			$sql = "SELECT A.* 
					FROM {$_SESSION['DB_PREFIX']}booking A
					WHERE A.id = $id";
			
			$result = mysql_query($sql);
			
			if (! $result) {
				throw new Exception("Cannot load booking $id - $sql - " . mysql_error());
			}
			
			while (($resultset = mysql_fetch_assoc($result))) {
				$this->loadFromResultset($resultset);
			}
			
			return $this;
		}
		
		/**
		 * Load from resultset
		 * @param array $resultset
		 */
		public function loadFromResultset($resultset) {
			$this->id = $resultset['id'];
			$this->vehicleid = $resultset['vehicleid'];
			$this->driverid = $resultset['driverid'];
			$this->trailerid = $resultset['trailerid'];
			$this->customerid = $resultset['customerid'];
			$this->signatureid = $resultset['signatureid'];
			$this->loadtypeid = $resultset['loadtypeid'];
			$this->vehicletypeid = $resultset['vehicletypeid'];
			$this->worktypeid = $resultset['worktypeid'];
			$this->pallets = $resultset['pallets'];
			$this->memberid = $resultset['memberid'];
			$this->startdatetime = $resultset['startdatetime'];
			$this->enddatetime = $resultset['enddatetime'];
			$this->rate = $resultset['rate'];
			$this->charge = $resultset['charge'];
			$this->weight = $resultset['weight'];
			$this->miles = $resultset['miles'];
			$this->vehiclecostoverhead = $resultset['vehiclecostoverhead'];
			$this->allegrodayrate = $resultset['allegrodayrate'];
			$this->agencydayrate = $resultset['agencydayrate'];
			$this->wages = $resultset['wages'];
			$this->fuelcostoverhead = $resultset['fuelcostoverhead'];
			$this->maintenanceoverhead = $resultset['maintenanceoverhead'];
			$this->profitmargin = $resultset['profitmargin'];
			$this->customercostpermile = $resultset['customercostpermile'];
			$this->bookingtype = $resultset['bookingtype'];
			$this->postedtosage = $resultset['postedtosage'];
			$this->statusid = $resultset['statusid'];
			$this->nominalledgercodeid = $resultset['nominalledgercodeid'];
			$this->legsummary = $resultset['legsummary'];
			$this->duration = $resultset['duration'];
			$this->ordernumber = $resultset['ordernumber'];
			$this->ordernumber2 = $resultset['ordernumber2'];
			$this->drivername = $resultset['drivername'];
			$this->driverphone = $resultset['driverphone'];
			$this->fromplace = $resultset['fromplace'];
			$this->toplace = $resultset['toplace'];
			$this->fromplace_lat = $resultset['fromplace_lat'];
			$this->fromplace_lng = $resultset['fromplace_lng'];
			$this->fromplace_phone = $resultset['fromplace_phone'];
			$this->fromplace_ref = $resultset['fromplace_ref'];
			$this->toplace_lat = $resultset['toplace_lat'];
			$this->toplace_lng = $resultset['toplace_lng'];
			$this->toplace_phone = $resultset['toplace_phone'];
			$this->toplace_ref = $resultset['toplace_ref'];
			$this->startvisittype = $resultset['startvisittype'];
			$this->endvisittype = $resultset['endvisittype'];
			$this->notes = $resultset['notes'];
			$this->agencyvehicleregistration = $resultset['agencyvehicleregistration'];
			$this->fixedprice = $resultset['fixedprice'];
			$this->podsent = $resultset['podsent'];
			$this->invoiced = $resultset['invoiced'];
			$this->confirmed = $resultset['confirmed'];
			$this->metacreateddate = $resultset['metacreateddate'];
			$this->metamodifieddate = $resultset['metamodifieddate'];
			$this->metamodifieduserid = $resultset['metamodifieduserid'];
			$this->metacreateduserid = $resultset['metacreateduserid'];
			
			/* Resultset comparison. */
			$this->originalstatusid = $resultset['statusid'];
			$this->originalcharge = $resultset['charge'];
		}
		
		/**
		 * Insert new row.
		 * @throws Exception
		 */
		public function insert() {
			$memberid = getLoggedOnMemberID();
			
			$this->refreshLegSummary();
			
	    	$sql = "INSERT INTO {$_SESSION['DB_PREFIX']}booking
	    			(
						vehicleid,
						driverid,
						trailerid,
						customerid,
						signatureid,
						loadtypeid,
						vehicletypeid,
						worktypeid,
						pallets,
						memberid,
						startdatetime,
						enddatetime,
						rate,
						charge,
						weight,
						miles,
						vehiclecostoverhead,
						allegrodayrate,
						agencydayrate,
						wages,
						fuelcostoverhead,
						maintenanceoverhead,
						profitmargin,
						customercostpermile,
						bookingtype,
						postedtosage,
						statusid,
						nominalledgercodeid,
						legsummary,
						duration,
						ordernumber,
						ordernumber2,
						drivername,
						driverphone,
						fromplace,
						toplace,
						fromplace_lat,
						fromplace_lng,
						fromplace_phone,
						fromplace_ref,
						toplace_lat,
						toplace_lng,
						toplace_phone,
						toplace_ref,
						startvisittype,
						endvisittype,
						notes,
						agencyvehicleregistration,
						fixedprice,
						podsent,
						invoiced,
						confirmed,
						metacreateddate,
						metamodifieddate,
						metamodifieduserid,
						metacreateduserid
	    			)
	    			VALUES
	    			(
						{$this->propertyIntValue($this->vehicleid)},
						{$this->propertyIntValue($this->driverid)},
						{$this->propertyIntValue($this->trailerid)},
						{$this->propertyIntValue($this->customerid)},
						{$this->propertyIntValue($this->signatureid)},
						{$this->propertyIntValue($this->loadtypeid)},
						{$this->propertyIntValue($this->vehicletypeid)},
						{$this->propertyIntValue($this->worktypeid)},
						{$this->propertyIntValue($this->pallets)},
						{$this->propertyIntValue($this->memberid)},
						{$this->propertyDateValue($this->startdatetime)},
						{$this->propertyDateValue($this->enddatetime)},
						{$this->propertyDoubleValue($this->rate)},
						{$this->propertyDoubleValue($this->charge)},
						{$this->propertyDoubleValue($this->weight)},
						{$this->propertyDoubleValue($this->miles)},
						{$this->propertyDoubleValue($this->vehiclecostoverhead)},
						{$this->propertyDoubleValue($this->allegrodayrate)},
						{$this->propertyDoubleValue($this->agencydayrate)},
						{$this->propertyDoubleValue($this->wages)},
						{$this->propertyDoubleValue($this->fuelcostoverhead)},
						{$this->propertyDoubleValue($this->maintenanceoverhead)},
						{$this->propertyDoubleValue($this->profitmargin)},
						{$this->propertyDoubleValue($this->customercostpermile)},
						{$this->propertyStringValue($this->bookingtype)},
						{$this->propertyStringValue($this->postedtosage)},
						{$this->propertyIntValue($this->statusid)},
						{$this->propertyIntValue($this->nominalledgercodeid)},
						{$this->propertyStringValue($this->legsummary)},
						{$this->propertyDoubleValue($this->duration)},
						{$this->propertyStringValue($this->ordernumber)},
						{$this->propertyStringValue($this->ordernumber2)},
						{$this->propertyStringValue($this->drivername)},
						{$this->propertyStringValue($this->driverphone)},
						{$this->propertyStringValue($this->fromplace)},
						{$this->propertyStringValue($this->toplace)},
						{$this->propertyDoubleValue($this->fromplace_lat)},
						{$this->propertyDoubleValue($this->fromplace_lng)},
						{$this->propertyStringValue($this->fromplace_phone)},
						{$this->propertyStringValue($this->fromplace_ref)},
						{$this->propertyDoubleValue($this->toplace_lat)},
						{$this->propertyDoubleValue($this->toplace_lng)},
						{$this->propertyStringValue($this->toplace_phone)},
						{$this->propertyStringValue($this->toplace_ref)},
						{$this->propertyStringValue($this->startvisittype)},
						{$this->propertyStringValue($this->endvisittype)},
						{$this->propertyStringValue($this->notes)},
						{$this->propertyStringValue($this->agencyvehicleregistration)},
						{$this->propertyStringValue($this->fixedprice)},
						{$this->propertyStringValue($this->podsent)},
						{$this->propertyStringValue($this->invoiced)},
						{$this->propertyStringValue($this->confirmed)},
						{$this->propertyDateValue($this->metacreateddate)},
						{$this->propertyDateValue($this->metamodifieddate)},
						{$this->propertyIntValue($this->metamodifieduserid)},
						{$this->propertyIntValue($this->metacreateduserid)}
    				)";
	    				
	    	if (! mysql_query($sql)) {
	    		throw new Exception("Cannot insert booking - $sql:");
	    	}
	    	
    		$this->id = mysql_insert_id();
		}
		
		/**
		 * Update booking date.
		 * @throws Exception
		 */
		public function update() {
			$memberid = getLoggedOnMemberID();
			
			$this->refreshLegSummary();
			
			if ($this->statusid == 4 && $this->originalstatusid < 4) {
				/* Scheduled. */
				sendCustomerMessage(
						$this->customerid, 
						"Delivery scheduled", 
						getSiteConfigData()->deliveryconfirmationmessage
					);
				
			} else if (($this->statusid == 7 && $this->originalstatusid < 7 && $this->charge != 0) ||
					   ($this->statusid == 7 && $this->originalcharge == 0 && $this->charge != 0)) {
				/* Completed. */
				$sql = "SELECT A.selfbilledinvoices, A.taxcodeid
						FROM {$_SESSION['DB_PREFIX']}customer A 
						WHERE A.id = {$this->customerid}";
				$result = mysql_query($sql);
			
				//Check whether the query was successful or not
				if($result) {
					while (($resultset = mysql_fetch_assoc($result))) {
						$selfbilledinvoices = $resultset['selfbilledinvoices'];
						
						if ($selfbilledinvoices == "N") {
							/* Set to invoiced. */
							$this->statusid = 8;
							
							$sql = "INSERT INTO {$_SESSION['DB_PREFIX']}invoice
									(
										customerid, revision, orderdate, yourordernumber,
										paid, exported, status, takenbyid, deliverycharge,
										discount, total, downloaded
									)
									VALUES
									(
										{$this->customerid}, 1, CURDATE(), '{$this->legsummary}',
										'N', 'N', 0, 1, 0,
										0, {$this->charge}, 'N'
									)";

							if (! mysql_query($sql)) {
								throw new Exception("Error inserting invoice: $sql");
							}
							
							$invoiceid = mysql_insert_id();
							$vatrate = getSiteConfigData()->vatrate;
							$vat = $this->charge * ($vatrate / 100);
							$linetotal = $this->charge;
							
							$sql = "INSERT INTO {$_SESSION['DB_PREFIX']}invoiceitem	
									(
										invoiceid, productid, description, priceeach,
										quantity, linetotal, vat, vatrate, nominalledgercodeid
									)
									VALUES
									(
										$invoiceid, {$this->id}, '{$this->legsummary}', {$this->charge},
										1, $linetotal, $vat, $vatrate, '{$this->nominalledgercodeid}'
									)";
										
							if (! mysql_query($sql)) {
								throw new Exception("Error inserting invoice: $sql");
							}
							
//							try {
//								invoiceEmail($invoiceid);
//								
//							} catch (Exception $e) {
//								/* Ignore */
//							}
						}
					}
				}
			}
						
	    	$sql = "UPDATE {$_SESSION['DB_PREFIX']}booking SET
					vehicleid = {$this->propertyIntValue($this->vehicleid)},
					driverid = {$this->propertyIntValue($this->driverid)},
					trailerid = {$this->propertyIntValue($this->trailerid)},
					customerid = {$this->propertyIntValue($this->customerid)},
					signatureid = {$this->propertyIntValue($this->signatureid)},
					loadtypeid = {$this->propertyIntValue($this->loadtypeid)},
					vehicletypeid = {$this->propertyIntValue($this->vehicletypeid)},
					worktypeid = {$this->propertyIntValue($this->worktypeid)},
					pallets = {$this->propertyIntValue($this->pallets)},
					memberid = {$this->propertyIntValue($this->memberid)},
					startdatetime = {$this->propertyDateValue($this->startdatetime)},
					enddatetime = {$this->propertyDateValue($this->enddatetime)},
					rate = {$this->propertyDoubleValue($this->rate)},
					charge = {$this->propertyDoubleValue($this->charge)},
					weight = {$this->propertyDoubleValue($this->weight)},
					miles = {$this->propertyDoubleValue($this->miles)},
					vehiclecostoverhead = {$this->propertyDoubleValue($this->vehiclecostoverhead)},
					allegrodayrate = {$this->propertyDoubleValue($this->allegrodayrate)},
					agencydayrate = {$this->propertyDoubleValue($this->agencydayrate)},
					wages = {$this->propertyDoubleValue($this->wages)},
					fuelcostoverhead = {$this->propertyDoubleValue($this->fuelcostoverhead)},
					maintenanceoverhead = {$this->propertyDoubleValue($this->maintenanceoverhead)},
					profitmargin = {$this->propertyDoubleValue($this->profitmargin)},
					customercostpermile = {$this->propertyDoubleValue($this->customercostpermile)},
					bookingtype = {$this->propertyStringValue($this->bookingtype)},
					postedtosage = {$this->propertyStringValue($this->postedtosage)},
					statusid = {$this->propertyIntValue($this->statusid)},
					nominalledgercodeid = {$this->propertyIntValue($this->nominalledgercodeid)},
					legsummary = {$this->propertyStringValue($this->legsummary)},
					duration = {$this->propertyDoubleValue($this->duration)},
					ordernumber = {$this->propertyStringValue($this->ordernumber)},
					ordernumber2 = {$this->propertyStringValue($this->ordernumber2)},
					drivername = {$this->propertyStringValue($this->drivername)},
					driverphone = {$this->propertyStringValue($this->driverphone)},
					fromplace = {$this->propertyStringValue($this->fromplace)},
					toplace = {$this->propertyStringValue($this->toplace)},
					fromplace_lat = {$this->propertyDoubleValue($this->fromplace_lat)},
					fromplace_lng = {$this->propertyDoubleValue($this->fromplace_lng)},
					fromplace_phone = {$this->propertyStringValue($this->fromplace_phone)},
					fromplace_ref = {$this->propertyStringValue($this->fromplace_ref)},
					toplace_lat = {$this->propertyDoubleValue($this->toplace_lat)},
					toplace_lng = {$this->propertyDoubleValue($this->toplace_lng)},
					toplace_phone = {$this->propertyStringValue($this->toplace_phone)},
					toplace_ref = {$this->propertyStringValue($this->toplace_ref)},
					startvisittype = {$this->propertyStringValue($this->startvisittype)},
					endvisittype = {$this->propertyStringValue($this->endvisittype)},
					notes = {$this->propertyStringValue($this->notes)},
					agencyvehicleregistration = {$this->propertyStringValue($this->agencyvehicleregistration)},
					fixedprice = {$this->propertyStringValue($this->fixedprice)},
					podsent = {$this->propertyStringValue($this->podsent)},
					invoiced = {$this->propertyStringValue($this->invoiced)},
					confirmed = {$this->propertyStringValue($this->confirmed)},
					metacreateddate = NOW(),
					metamodifieddate = NOW(),
					metamodifieduserid = $memberid,
					metacreateduserid = $memberid
					WHERE id = {$this->id}";
	    				
	    	if (! mysql_query($sql)) {
	    		throw new Exception("Cannot update booking - $sql:" . mysql_error());
	    	}
	    	
	    	/* Re-apply comparisons. */
			$this->originalstatusid = $this->statusid;
			$this->originalcharge = $this->originalcharge;
		}

		/**
		 * Initialise costs from base data.
		 * @throws Exception
		 */
		public function initialiseCostsFromBaseData() {
			$this->allegrodayrate = 0;
			$this->agencydayrate = 0;
			$this->vehiclecostoverhead = 0;
			$this->fuelcostoverhead = 0;
			$this->maintenanceoverhead = 0;
			$this->customercostpermile = 0;
			$this->wages = 0;
			$this->rate = 0;
			$this->charge = 0;
			$this->worktypeid = getSiteConfigData()->defaultworktype;
			
			$sql = "SELECT A.* 
					FROM {$_SESSION['DB_PREFIX']}vehicletype A 
					WHERE A.id = {$this->vehicletypeid}";
			
			$result = mysql_query($sql);
			
			if ($result) {
				while (($resultset = mysql_fetch_assoc($result))) {
					$this->allegrodayrate = $resultset['allegrodayrate'];
					$this->agencydayrate = $resultset['agencydayrate'];
					$this->vehiclecostoverhead = $resultset['vehiclecostpermile'];
					$this->fuelcostoverhead = $resultset['fuelcostpermile'];
					$this->maintenanceoverhead = $resultset['overheadcostpermile'];
				}
			}
			
			$sql = "SELECT A.* 
					FROM {$_SESSION['DB_PREFIX']}customer A 
					WHERE A.id = {$this->customerid}";
			
			$result = mysql_query($sql);
			
			if ($result) {
				while (($resultset = mysql_fetch_assoc($result))) {
					$this->customercostpermile = $resultset['customercostpermile'];
					
					if ($this->customercostpermile == null) {
						$this->customercostpermile = 0;
					}
				}
			}
			
			$this->wages = ($this->duration * $this->allegrodayrate) * (1 + (getSiteConfigData()->defaultwagesmargin / 100));
			
			if ($this->customercostpermile != 0) {
				$this->rate = $this->customercostpermile * $this->miles;
				
			} else {
				$this->rate = $this->wages + (($this->vehiclecostoverhead + $this->fuelcostoverhead + $this->maintenanceoverhead) * $this->miles);
			}
			
			$this->charge = $this->rate * (1 + (getSiteConfigData()->defaultprofitmargin / 100));
			$this->nominalledgercodeid = 0;
			
			$sql = "SELECT nominalledgercodeid 
				    FROM {$_SESSION['DB_PREFIX']}worktype 
				    WHERE id = {$this->worktypeid}";
		
			$result = mysql_query($sql);
			
			if (! $result) {
				throw new Exception($sql);
			}
			
			while (($resultset = mysql_fetch_assoc($result))) {
				$this->nominalledgercodeid = $resultset['nominalledgercodeid']; 
			}			
		}
		
		/**
		 * Load legs. 
		 */
		public function loadLegs() {
			/* Do not load if booking not loaded. */
			if ($this->id == null) {
				throw new Exception("No booking loaded");
			}
			
			$sql = "SELECT A.* 
					FROM {$_SESSION['DB_PREFIX']}bookingleg A
					WHERE A.bookingid = {$this->id}
					ORDER BY A.id";
			
			$result = mysql_query($sql);
			
			if (! $result) {
				throw new Exception("Cannot load booking $id - $sql - " . mysql_error());
			}
			
			/* Clear legs array. */
			$this->legs = array();
			
			while (($resultset = mysql_fetch_assoc($result))) {
				/* Create new leg. */
				$leg = new BookingLegClass();
				$leg->loadFromResultset($resultset);

				/* Add leg to the array. */
				$this->legs[] = $leg;
			}
		}

		/**
		 * Add a booking leg.
		 * @param BookingLegClass $leg
		 */
		public function addLeg($leg) {
			/* Add to booking leg. */
			$leg->setBookingid($this->id);
			$leg->insert();
			
			$this->legs[] = $leg;
		}
		
		/**
		 * Refresh the leg summary.
		 */
		private function refreshLegSummary() {
			$this->legsummary = "";
			
			if ($this->startvisittype != "" && $this->startvisittype != null) {
				$this->legsummary = str_replace(", United Kingdom", "", $this->fromplace);
			}
			
			foreach ($this->getLegs() as $leg) {
				if ($this->legsummary != "") {
					$this->legsummary = "{$this->legsummary} -> " . str_replace(", United Kingdom", "", $leg->getPlace());
					
				} else {
					$this->legsummary = str_replace(", United Kingdom", "", $leg->getPlace());
				}				
			}
			
			if ($this->endvisittype != "" && $this->endvisittype != null) {
				if ($this->legsummary != "") {
					$this->legsummary = "{$this->legsummary} -> " . str_replace(", United Kingdom", "", $this->toplace);
					
				} else {
					$this->legsummary = str_replace(", United Kingdom", "", $this->toplace);
				}				
			}
		}
		
		/**
		 * id
		 * @return int
		 */
		public function getId(){
			return $this->id;
		}

		/**
		 * Vehicle
		 * @return VehicleClass
		 */
		public function getVehicle(){
			if ($this->vehicle == null) {
				$this->vehicle = new VehicleClass();
				$this->vehicle->load($this->getVehicleid());
			}
		
			return $this->vehicle;
		}
		
		/**
		 * vehicleid
		 * @return int
		 */
		public function getVehicleid(){
			return $this->vehicleid;
		}
	
		/**
		 * vehicleid
		 * @param int $vehicleid
		 * @return BookingClass
		 */
		public function setVehicleid($vehicleid){
			$this->vehicleid = $vehicleid;
			return $this;
		}

		/**
		 * Driver
		 * @return DriverClass
		 */
		public function getDriver(){
			if ($this->driver == null) {
				$this->driver = new DriverClass();
				$this->driver->load($this->getDriverid());
			}
			
			return $this->driver;
		}
		
		/**
		 * driverid
		 * @return int
		 */
		public function getDriverid(){
			return $this->driverid;
		}
	
		/**
		 * driverid
		 * @param int $driverid
		 * @return BookingClass
		 */
		public function setDriverid($driverid){
			$this->driverid = $driverid;
			return $this;
		}
	
		/**
		 * Trailer
		 * @return TrailerClass
		 */
		public function getTrailer(){
			if ($this->trailer == null) {
				$this->trailer = new TrailerClass();
				$this->trailer->load($this->getTrailerid());
			}
				
			return $this->trailer;
		}
		
		/**
		 * trailerid
		 * @return int
		 */
		public function getTrailerid(){
			return $this->trailerid;
		}
	
		/**
		 * trailerid
		 * @param int $trailerid
		 * @return BookingClass
		 */
		public function setTrailerid($trailerid){
			$this->trailerid = $trailerid;
			return $this;
		}

		/**
		 * customerid
		 * @return int
		 */
		public function getCustomerid(){
			return $this->customerid;
		}

		/**
		 * customer
		 * @return CustomerClass
		 */
		public function getCustomer(){
			if ($this->customer == null) {
				$this->customer = new CustomerClass();
				$this->customer->load($this->getCustomerid());
			}
				
			return $this->customer;
		}
		
		/**
		 * customerid
		 * @param int $customerid
		 * @return BookingClass
		 */
		public function setCustomerid($customerid){
			$this->customerid = $customerid;
			return $this;
		}
	
		/**
		 * signatureid
		 * @return int
		 */
		public function getSignatureid(){
			return $this->signatureid;
		}
	
		/**
		 * signatureid
		 * @param int $signatureid
		 * @return BookingClass
		 */
		public function setSignatureid($signatureid){
			$this->signatureid = $signatureid;
			return $this;
		}
	
		/**
		 * loadtypeid
		 * @return int
		 */
		public function getLoadtypeid(){
			return $this->loadtypeid;
		}
	
		/**
		 * loadtypeid
		 * @param int $loadtypeid
		 * @return BookingClass
		 */
		public function setLoadtypeid($loadtypeid){
			$this->loadtypeid = $loadtypeid;
			return $this;
		}
	
		/**
		 * vehicletypeid
		 * @return int
		 */
		public function getVehicletypeid(){
			return $this->vehicletypeid;
		}
	
		/**
		 * vehicletypeid
		 * @param int $vehicletypeid
		 * @return BookingClass
		 */
		public function setVehicletypeid($vehicletypeid){
			$this->vehicletypeid = $vehicletypeid;
			return $this;
		}
	
		/**
		 * worktypeid
		 * @return int
		 */
		public function getWorktypeid(){
			return $this->worktypeid;
		}
	
		/**
		 * worktypeid
		 * @param int $worktypeid
		 * @return BookingClass
		 */
		public function setWorktypeid($worktypeid){
			$this->worktypeid = $worktypeid;
			return $this;
		}
	
		/**
		 * pallets
		 * @return int
		 */
		public function getPallets(){
			return $this->pallets;
		}
	
		/**
		 * pallets
		 * @param int $pallets
		 * @return BookingClass
		 */
		public function setPallets($pallets){
			$this->pallets = $pallets;
			return $this;
		}
	
		/**
		 * memberid
		 * @return int
		 */
		public function getMemberid(){
			return $this->memberid;
		}
	
		/**
		 * memberid
		 * @param int $memberid
		 * @return BookingClass
		 */
		public function setMemberid($memberid){
			$this->memberid = $memberid;
			return $this;
		}
	
		/**
		 * startdatetime
		 * @return DateTime
		 */
		public function getStartdatetime(){
			return $this->startdatetime;
		}
	
		/**
		 * startdatetime
		 * @param DateTime $startdatetime
		 * @return BookingClass
		 */
		public function setStartdatetime($startdatetime){
			$this->startdatetime = $startdatetime;
			return $this;
		}
	
		/**
		 * enddatetime
		 * @return DateTime
		 */
		public function getEnddatetime(){
			return $this->enddatetime;
		}
	
		/**
		 * enddatetime
		 * @param DateTime $enddatetime
		 * @return BookingClass
		 */
		public function setEnddatetime($enddatetime){
			$this->enddatetime = $enddatetime;
			return $this;
		}
	
		/**
		 * rate
		 * @return float
		 */
		public function getRate(){
			return $this->rate;
		}
	
		/**
		 * rate
		 * @param float $rate
		 * @return BookingClass
		 */
		public function setRate($rate){
			$this->rate = $rate;
			return $this;
		}
	
		/**
		 * charge
		 * @return float
		 */
		public function getCharge(){
			return $this->charge;
		}
	
		/**
		 * charge
		 * @param float $charge
		 * @return BookingClass
		 */
		public function setCharge($charge){
			$this->charge = $charge;
			return $this;
		}
	
		/**
		 * weight
		 * @return float
		 */
		public function getWeight(){
			return $this->weight;
		}
	
		/**
		 * weight
		 * @param float $weight
		 * @return BookingClass
		 */
		public function setWeight($weight){
			$this->weight = $weight;
			return $this;
		}
	
		/**
		 * miles
		 * @return float
		 */
		public function getMiles(){
			return $this->miles;
		}
	
		/**
		 * miles
		 * @param float $miles
		 * @return BookingClass
		 */
		public function setMiles($miles){
			$this->miles = $miles;
			return $this;
		}
	
		/**
		 * vehiclecostoverhead
		 * @return float
		 */
		public function getVehiclecostoverhead(){
			return $this->vehiclecostoverhead;
		}
	
		/**
		 * vehiclecostoverhead
		 * @param float $vehiclecostoverhead
		 * @return BookingClass
		 */
		public function setVehiclecostoverhead($vehiclecostoverhead){
			$this->vehiclecostoverhead = $vehiclecostoverhead;
			return $this;
		}
	
		/**
		 * allegrodayrate
		 * @return float
		 */
		public function getAllegrodayrate(){
			return $this->allegrodayrate;
		}
	
		/**
		 * allegrodayrate
		 * @param float $allegrodayrate
		 * @return BookingClass
		 */
		public function setAllegrodayrate($allegrodayrate){
			$this->allegrodayrate = $allegrodayrate;
			return $this;
		}
	
		/**
		 * agencydayrate
		 * @return float
		 */
		public function getAgencydayrate(){
			return $this->agencydayrate;
		}
	
		/**
		 * agencydayrate
		 * @param float $agencydayrate
		 * @return BookingClass
		 */
		public function setAgencydayrate($agencydayrate){
			$this->agencydayrate = $agencydayrate;
			return $this;
		}
	
		/**
		 * wages
		 * @return float
		 */
		public function getWages(){
			return $this->wages;
		}
	
		/**
		 * wages
		 * @param float $wages
		 * @return BookingClass
		 */
		public function setWages($wages){
			$this->wages = $wages;
			return $this;
		}
	
		/**
		 * fuelcostoverhead
		 * @return float
		 */
		public function getFuelcostoverhead(){
			return $this->fuelcostoverhead;
		}
	
		/**
		 * fuelcostoverhead
		 * @param float $fuelcostoverhead
		 * @return BookingClass
		 */
		public function setFuelcostoverhead($fuelcostoverhead){
			$this->fuelcostoverhead = $fuelcostoverhead;
			return $this;
		}
	
		/**
		 * maintenanceoverhead
		 * @return float
		 */
		public function getMaintenanceoverhead(){
			return $this->maintenanceoverhead;
		}
	
		/**
		 * maintenanceoverhead
		 * @param float $maintenanceoverhead
		 * @return BookingClass
		 */
		public function setMaintenanceoverhead($maintenanceoverhead){
			$this->maintenanceoverhead = $maintenanceoverhead;
			return $this;
		}
	
		/**
		 * profitmargin
		 * @return float
		 */
		public function getProfitmargin(){
			return $this->profitmargin;
		}
	
		/**
		 * profitmargin
		 * @param float $profitmargin
		 * @return BookingClass
		 */
		public function setProfitmargin($profitmargin){
			$this->profitmargin = $profitmargin;
			return $this;
		}
	
		/**
		 * customercostpermile
		 * @return float
		 */
		public function getCustomercostpermile(){
			return $this->customercostpermile;
		}
	
		/**
		 * customercostpermile
		 * @param float $customercostpermile
		 * @return BookingClass
		 */
		public function setCustomercostpermile($customercostpermile){
			$this->customercostpermile = $customercostpermile;
			return $this;
		}
	
		/**
		 * bookingtype
		 * @return string
		 */
		public function getBookingtype(){
			return $this->bookingtype;
		}
	
		/**
		 * bookingtype
		 * @param string $bookingtype
		 * @return BookingClass
		 */
		public function setBookingtype($bookingtype){
			$this->bookingtype = $bookingtype;
			return $this;
		}
	
		/**
		 * postedtosage
		 * @return string
		 */
		public function getPostedtosage(){
			return $this->postedtosage;
		}
	
		/**
		 * postedtosage
		 * @param string $postedtosage
		 * @return BookingClass
		 */
		public function setPostedtosage($postedtosage){
			$this->postedtosage = $postedtosage;
			return $this;
		}

		/**
		 * statusid
		 * @return int
		 */
		public function getStatus(){
			if ($this->status == null) {
				$this->status = new BookingStatusClass();
				$this->status->load($this->statusid);
			}
			
			return $this->status;
		}

		/**
		 * statusid
		 * @return int
		 */
		public function getStatusid(){
			return $this->statusid;
		}
		
		/**
		 * statusid
		 * @param int $statusid
		 * @return BookingClass
		 */
		public function setStatusid($statusid){
			$this->statusid = $statusid;
			return $this;
		}
	
		/**
		 * nominalledgercodeid
		 * @return int
		 */
		public function getNominalledgercodeid(){
			return $this->nominalledgercodeid;
		}
	
		/**
		 * nominalledgercodeid
		 * @param int $nominalledgercodeid
		 * @return BookingClass
		 */
		public function setNominalledgercodeid($nominalledgercodeid){
			$this->nominalledgercodeid = $nominalledgercodeid;
			return $this;
		}
	
		/**
		 * legsummary
		 * @return string
		 */
		public function getLegsummary(){
			return $this->legsummary;
		}
	
		/**
		 * legsummary
		 * @param string $legsummary
		 * @return BookingClass
		 */
		public function setLegsummary($legsummary){
			$this->legsummary = $legsummary;
			return $this;
		}
	
		/**
		 * duration
		 * @return float
		 */
		public function getDuration(){
			return $this->duration;
		}
	
		/**
		 * duration
		 * @param float $duration
		 * @return BookingClass
		 */
		public function setDuration($duration){
			$this->duration = $duration;
			return $this;
		}
	
		/**
		 * ordernumber
		 * @return string
		 */
		public function getOrdernumber(){
			return $this->ordernumber;
		}
	
		/**
		 * ordernumber
		 * @param string $ordernumber
		 * @return BookingClass
		 */
		public function setOrdernumber($ordernumber){
			$this->ordernumber = $ordernumber;
			return $this;
		}
	
		/**
		 * ordernumber2
		 * @return string
		 */
		public function getOrdernumber2(){
			return $this->ordernumber2;
		}
	
		/**
		 * ordernumber2
		 * @param string $ordernumber2
		 * @return BookingClass
		 */
		public function setOrdernumber2($ordernumber2){
			$this->ordernumber2 = $ordernumber2;
			return $this;
		}
	
		/**
		 * drivername
		 * @return string
		 */
		public function getDrivername(){
			return $this->drivername;
		}
	
		/**
		 * drivername
		 * @param string $drivername
		 * @return BookingClass
		 */
		public function setDrivername($drivername){
			$this->drivername = $drivername;
			return $this;
		}
	
		/**
		 * driverphone
		 * @return string
		 */
		public function getDriverphone(){
			return $this->driverphone;
		}
	
		/**
		 * driverphone
		 * @param string $driverphone
		 * @return BookingClass
		 */
		public function setDriverphone($driverphone){
			$this->driverphone = $driverphone;
			return $this;
		}
	
		/**
		 * fromplace
		 * @return string
		 */
		public function getFromplace(){
			return $this->fromplace;
		}
	
		/**
		 * fromplace
		 * @param string $fromplace
		 * @return BookingClass
		 */
		public function setFromplace($fromplace){
			$this->fromplace = $fromplace;
			return $this;
		}
	
		/**
		 * toplace
		 * @return string
		 */
		public function getToplace(){
			return $this->toplace;
		}
	
		/**
		 * toplace
		 * @param string $toplace
		 * @return BookingClass
		 */
		public function setToplace($toplace){
			$this->toplace = $toplace;
			return $this;
		}
	
		/**
		 * fromplace_lat
		 * @return float
		 */
		public function getFromplace_lat(){
			return $this->fromplace_lat;
		}
	
		/**
		 * fromplace_lat
		 * @param float $fromplace_lat
		 * @return BookingClass
		 */
		public function setFromplace_lat($fromplace_lat){
			$this->fromplace_lat = $fromplace_lat;
			return $this;
		}
	
		/**
		 * fromplace_lng
		 * @return float
		 */
		public function getFromplace_lng(){
			return $this->fromplace_lng;
		}
	
		/**
		 * fromplace_lng
		 * @param float $fromplace_lng
		 * @return BookingClass
		 */
		public function setFromplace_lng($fromplace_lng){
			$this->fromplace_lng = $fromplace_lng;
			return $this;
		}
	
		/**
		 * fromplace_phone
		 * @return string
		 */
		public function getFromplace_phone(){
			return $this->fromplace_phone;
		}
	
		/**
		 * fromplace_phone
		 * @param string $fromplace_phone
		 * @return BookingClass
		 */
		public function setFromplace_phone($fromplace_phone){
			$this->fromplace_phone = $fromplace_phone;
			return $this;
		}
	
		/**
		 * fromplace_ref
		 * @return string
		 */
		public function getFromPlaceReference(){
			return $this->fromplace_ref;
		}
	
		/**
		 * fromplace_ref
		 * @param string $fromplace_ref
		 * @return BookingClass
		 */
		public function setFromPlaceReference($fromplace_ref){
			$this->fromplace_ref = $fromplace_ref;
			return $this;
		}
	
		/**
		 * toplace_lat
		 * @return float
		 */
		public function getToplace_lat(){
			return $this->toplace_lat;
		}
	
		/**
		 * toplace_lat
		 * @param float $toplace_lat
		 * @return BookingClass
		 */
		public function setToplace_lat($toplace_lat){
			$this->toplace_lat = $toplace_lat;
			return $this;
		}
	
		/**
		 * toplace_lng
		 * @return float
		 */
		public function getToplace_lng(){
			return $this->toplace_lng;
		}
	
		/**
		 * toplace_lng
		 * @param float $toplace_lng
		 * @return BookingClass
		 */
		public function setToplace_lng($toplace_lng){
			$this->toplace_lng = $toplace_lng;
			return $this;
		}
	
		/**
		 * toplace_phone
		 * @return string
		 */
		public function getToplace_phone(){
			return $this->toplace_phone;
		}
	
		/**
		 * toplace_phone
		 * @param string $toplace_phone
		 * @return BookingClass
		 */
		public function setToplace_phone($toplace_phone){
			$this->toplace_phone = $toplace_phone;
			return $this;
		}
	
		/**
		 * toplace_ref
		 * @return string
		 */
		public function getToplace_ref(){
			return $this->toplace_ref;
		}
	
		/**
		 * toplace_ref
		 * @param string $toplace_ref
		 * @return BookingClass
		 */
		public function setToplace_ref($toplace_ref){
			$this->toplace_ref = $toplace_ref;
			return $this;
		}
	
		/**
		 * startvisittype
		 * @return string
		 */
		public function getStartvisittype(){
			return $this->startvisittype;
		}
	
		/**
		 * startvisittype
		 * @param string $startvisittype
		 * @return BookingClass
		 */
		public function setStartvisittype($startvisittype){
			$this->startvisittype = $startvisittype;
			return $this;
		}
	
		/**
		 * endvisittype
		 * @return string
		 */
		public function getEndvisittype(){
			return $this->endvisittype;
		}
	
		/**
		 * endvisittype
		 * @param string $endvisittype
		 * @return BookingClass
		 */
		public function setEndvisittype($endvisittype){
			$this->endvisittype = $endvisittype;
			return $this;
		}
	
		/**
		 * notes
		 * @return string
		 */
		public function getNotes(){
			return $this->notes;
		}
	
		/**
		 * notes
		 * @param string $notes
		 * @return BookingClass
		 */
		public function setNotes($notes){
			$this->notes = $notes;
			return $this;
		}
	
		/**
		 * agencyvehicleregistration
		 * @return string
		 */
		public function getAgencyvehicleregistration(){
			return $this->agencyvehicleregistration;
		}
	
		/**
		 * agencyvehicleregistration
		 * @param string $agencyvehicleregistration
		 * @return BookingClass
		 */
		public function setAgencyvehicleregistration($agencyvehicleregistration){
			$this->agencyvehicleregistration = $agencyvehicleregistration;
			return $this;
		}
	
		/**
		 * fixedprice
		 * @return string
		 */
		public function getFixedprice(){
			return $this->fixedprice;
		}
	
		/**
		 * fixedprice
		 * @param string $fixedprice
		 * @return BookingClass
		 */
		public function setFixedprice($fixedprice){
			$this->fixedprice = $fixedprice;
			return $this;
		}
	
		/**
		 * podsent
		 * @return string
		 */
		public function getPodsent(){
			return $this->podsent;
		}
	
		/**
		 * podsent
		 * @param string $podsent
		 * @return BookingClass
		 */
		public function setPodsent($podsent){
			$this->podsent = $podsent;
			return $this;
		}
	
		/**
		 * invoiced
		 * @return string
		 */
		public function getInvoiced(){
			return $this->invoiced;
		}
	
		/**
		 * invoiced
		 * @param string $invoiced
		 * @return BookingClass
		 */
		public function setInvoiced($invoiced){
			$this->invoiced = $invoiced;
			return $this;
		}
	
		/**
		 * confirmed
		 * @return string
		 */
		public function getConfirmed(){
			return $this->confirmed;
		}
	
		/**
		 * confirmed
		 * @param string $confirmed
		 * @return BookingClass
		 */
		public function setConfirmed($confirmed){
			$this->confirmed = $confirmed;
			return $this;
		}
	
		/**
		 * metacreateddate
		 * @return DateTime
		 */
		public function getMetacreateddate(){
			return $this->metacreateddate;
		}
	
		/**
		 * metacreateddate
		 * @param DateTime $metacreateddate
		 * @return BookingClass
		 */
		public function setMetacreateddate($metacreateddate){
			$this->metacreateddate = $metacreateddate;
			return $this;
		}
	
		/**
		 * metamodifieddate
		 * @return DateTime
		 */
		public function getMetamodifieddate(){
			return $this->metamodifieddate;
		}
	
		/**
		 * metamodifieddate
		 * @param DateTime $metamodifieddate
		 * @return BookingClass
		 */
		public function setMetamodifieddate($metamodifieddate){
			$this->metamodifieddate = $metamodifieddate;
			return $this;
		}
	
		/**
		 * metamodifieduserid
		 * @return DateTime
		 */
		public function getMetamodifieduserid(){
			return $this->metamodifieduserid;
		}
	
		/**
		 * metamodifieduserid
		 * @param DateTime $metamodifieduserid
		 * @return BookingClass
		 */
		public function setMetamodifieduserid($metamodifieduserid){
			$this->metamodifieduserid = $metamodifieduserid;
			return $this;
		}
	
		/**
		 * metacreateduserid
		 * @return DateTime
		 */
		public function getMetacreateduserid(){
			return $this->metacreateduserid;
		}
	
		/**
		 * metacreateduserid
		 * @param DateTime $metacreateduserid
		 * @return BookingClass
		 */
		public function setMetacreateduserid($metacreateduserid){
			$this->metacreateduserid = $metacreateduserid;
			return $this;
		}
	
		/**
		 * originalstatusid
		 * @return int
		 */
		public function getOriginalstatusid(){
			return $this->originalstatusid;
		}
	
		/**
		 * originalstatusid
		 * @param int $originalstatusid
		 * @return BookingClass
		 */
		public function setOriginalstatusid($originalstatusid){
			$this->originalstatusid = $originalstatusid;
			return $this;
		}
	
		/**
		 * originalcharge
		 * @return float
		 */
		public function getOriginalcharge(){
			return $this->originalcharge;
		}
	
		/**
		 * originalcharge
		 * @param float $originalcharge
		 * @return BookingClass
		 */
		public function setOriginalcharge($originalcharge){
			$this->originalcharge = $originalcharge;
			return $this;
		}
	
		/**
		 * legs
		 * @return BookingLegClass
		 */
		public function getLegs(){
			return $this->legs;
		}
	
		/**
		 * legs
		 * @param BookingLegClass $legs
		 * @return BookingClass
		 */
		public function setLegs($legs){
			$this->legs = $legs;
			return $this;
		}
	
	} 
?>