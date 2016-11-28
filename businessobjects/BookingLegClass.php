<?php
	require_once("AbstractBaseDataClass.php");
	
	class BookingLegClass extends AbstractBaseDataClass {
		
		/* Booking level properties. */
		public $id = null;
		public $bookingid = null;
		public $signatureid = null;
		public $pallets = null;
		public $status = null;
		public $damagedimageid = null;
		public $damagedtext = null;
		public $arrivaltime = null;
		public $departuretime = null;
		public $reference = null;
		public $phone = null;
		public $fromplace = null;
		public $timetaken = null;
		public $miles = null;
		public $place = null;
		
		/** 
		 * Constructor 
		 */
	    public function __construct() {
	    	start_db();
	    }
	    
	    /** 
	     * Load from resultset 
	     */
	    public function loadFromResultset($resultset) {
			$this->id = $resultset['id'];
	    	$this->bookingid = $resultset['bookingid'];
			$this->signatureid = $resultset['signatureid'];
			$this->pallets = $resultset['pallets'];
			$this->status = $resultset['status'];
			$this->damagedimageid = $resultset['damagedimageid'];
			$this->damagedtext = $resultset['damagedtext'];
			$this->place = $resultset['place'];
			$this->place_lat = $resultset['place_lat'];
			$this->place_lng = $resultset['place_lng'];
			$this->reference = $resultset['reference'];
			$this->phone = $resultset['phone'];
			$this->arrivaltime = $resultset['arrivaltime'];
			$this->departuretime = $resultset['departuretime'];
			$this->timetaken = $resultset['timetaken'];
			$this->miles = $resultset['miles'];
	    }
	    
	    /**
	     * Insert new row.
	     */
	    public function insert() {
	    	if ($this->bookingid == null) {
	    		throw new Exception("Not associated with a booking.");
	    	}
	    	
	    	$sql = "INSERT INTO {$_SESSION['DB_PREFIX']}bookingleg
	    			(
	    				bookingid, 
	    				signatureid, 
	    				pallets, 
	    				status, 
	    				damagedimageid, 
	    				damagedtext, 
	    				place, 
	    				place_lat, 
	    				place_lng,
	    				reference, 
	    				phone, 
	    				arrivaltime, 
	    				departuretime, 
	    				timetaken, 
	    				miles
	    			)
	    			VALUES
	    			(
	    				{$this->propertyIntValue($this->bookingid)}, 
	    				{$this->propertyIntValue($this->signatureid)}, 
	    				{$this->propertyIntValue($this->pallets)}, 
	    				{$this->propertyStringValue($this->status)}, 
	    				{$this->propertyIntValue($this->damagedimageid)}, 
	    				{$this->propertyStringValue($this->damagedtext)}, 
	    				{$this->propertyStringValue($this->place)}, 
	    				{$this->propertyIntValue($this->place_lat)}, 
	    				{$this->propertyIntValue($this->place_lng)},
	    				{$this->propertyStringValue($this->reference)}, 
	    				{$this->propertyStringValue($this->phone)}, 
	    				{$this->propertyDateValue($this->arrivaltime)}, 
	    				{$this->propertyDateValue($this->departuretime)}, 
	    				{$this->propertyDoubleValue($this->timetaken)}, 
	    				{$this->propertyDoubleValue($this->miles)}
	    			)";
	    				
	    	if (! mysql_query($sql)) {
	    		throw new Exception("Cannot insert booking leg - $sql:" . mysql_error());
	    	}
	    	
    		$this->id = mysql_insert_id();
	    }
	} 
?>