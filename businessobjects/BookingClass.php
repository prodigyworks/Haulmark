<?php
	require_once("AbstractBaseDataClass.php");
	require_once("BookingLegClass.php");
	
	class BookingClass extends AbstractBaseDataClass {
		
		/* Booking level properties. */
		
		/** @property **/
		public $id = null;
		/** @property **/
		public $vehicleid = null;
		/** @property **/
		public $driverid = null;
		/** @property **/
		public $trailerid = null;
		/** @property **/
		public $customerid = null;
		/** @property **/
		public $signatureid = null;
		/** @property **/
		public $loadtypeid = null;
		/** @property **/
		public $vehicletypeid = null;
		/** @property **/
		public $worktypeid = null;
		/** @property **/
		public $pallets = null;
		/** @property **/
		public $memberid = null;
		/** @property @datetime **/
		public $startdatetime = null;
		/** @property @datetime **/
		public $enddatetime = null;
		/** @property **/
		public $rate = null;
		/** @property **/
		public $charge = null;
		/** @property **/
		public $weight = null;
		/** @property **/
		public $miles = null;
		/** @property **/
		public $vehiclecostoverhead = null;
		/** @property **/
		public $allegrodayrate = null;
		/** @property **/
		public $agencydayrate = null;
		/** @property **/
		public $wages = null;
		/** @property **/
		public $fuelcostoverhead = null;
		/** @property **/
		public $maintenanceoverhead = null;
		/** @property **/
		public $profitmargin = null;
		/** @property **/
		public $customercostpermile = null;
		/** @property **/
		public $bookingtype = null;
		/** @property **/
		public $postedtosage = null;
		/** @property **/
		public $statusid = null;
		/** @property **/
		public $nominalledgercodeid = null;
		/** @property **/
		public $legsummary = null;
		/** @property **/
		public $duration = null;
		/** @property **/
		public $ordernumber = null;
		/** @property **/
		public $ordernumber2 = null;
		/** @property **/
		public $drivername = null;
		/** @property **/
		public $driverphone = null;
		/** @property **/
		public $fromplace = null;
		/** @property **/
		public $toplace = null;
		/** @property **/
		public $fromplace_lat = null;
		/** @property **/
		public $fromplace_lng = null;
		/** @property **/
		public $fromplace_phone = null;
		/** @property **/
		public $fromplace_ref = null;
		/** @property **/
		public $toplace_lat = null;
		/** @property **/
		public $toplace_lng = null;
		/** @property **/
		public $toplace_phone = null;
		/** @property **/
		public $toplace_ref = null;
		/** @property **/
		public $notes = null;
		/** @property **/
		public $agencyvehicleregistration = null;
		/** @property **/
		public $fixedprice = null;
		/** @property **/
		public $podsent = null;
		/** @property **/
		public $invoiced = null;
		/** @property **/
		public $confirmed = null;
		/** @property @datetime **/
		public $metacreateddate = null;
		/** @property @datetime **/
		public $metamodifieddate = null;
		/** @property **/
		public $metamodifieduserid = null;
		/** @property **/
		public $metacreateduserid = null;
		
		/** @onetomany **/
		public $legs = array();
		
		/**
		 * Constructor 
		 */
	    public function __construct() {
	    	start_db();
	    }

	    /** 
	     * Load booking row. 
	     */
		public function load($id) {
			$this->id = $id;
			
			$sql = "SELECT A.* 
					FROM {$_SESSION['DB_PREFIX']}booking A
					WHERE A.id = $id";
			
			$result = mysql_query($sql);
			
			if (! $result) {
				throw new Exception("Cannot load booking $id - $sql - " . mysql_error());
			}
			
			while (($resultset = mysql_fetch_assoc($result))) {
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
					WHERE A.bookingid = {$this->id}";
			
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
			
			/* Refresh the leg summary description. */
			$this->refreshLegSummary();
		}
		
		/**
		 * Add a booking leg.
		 */
		public function addLeg($leg) {
			/* Add to booking leg. */
			$leg->bookingid = $this->id;
			$leg->insert();
			
			$this->legs[] = $leg;
			$this->refreshLegSummary();
			
			/* Update booking. */
			$this->update();
			
		}
		
		/**
		 * Refresh the leg summary.
		 */
		private function refreshLegSummary() {
			$this->legsummary = "";
			
			foreach ($this->legs as $leg) {
				if ($place != "") {
					$this->legsummary = "{$this->legsummary} -> " . str_replace(", United Kingdom", "", $leg->place);
					
				} else {
					$this->legsummary = str_replace(", United Kingdom", "", $leg->place);
				}				
			}
		}
	} 
?>