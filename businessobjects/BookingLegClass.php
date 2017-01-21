<?php
	require_once("AbstractBaseDataClass.php");
	require_once("BookingClass.php");

	class BookingLegClass extends AbstractBaseDataClass {
		const VISITTYPE_COLLECTION = "C";
		const VISITTYPE_DELIVERY = "D";
		const VISITTYPE_BOTH = "B";
		
		/* Booking level properties. */
		private $id = null;
		private $bookingid = null;
		private $signatureid = null;
		private $pallets = null;
		private $status = null;
		private $damagedimageid = null;
		private $damagedtext = null;
		private $arrivaltime = null;
		private $departuretime = null;
		private $reference = null;
		private $phone = null;
		private $fromplace = null;
		private $timetaken = null;
		private $miles = null;
		private $place = null;
		private $visittype = null;
		
		/** @onetoone **/
		private $booking = null;
		
		/** 
		 * Constructor 
		 */
	    public function __construct() {
	    	start_db();
	    }
	    
	    /**
	     * Load booking data
	     * @param int $id Booking leg ID
	     * @throws Exception
	     */
	    public function load($id) {
	    	$sql = "SELECT A.*
			    	FROM {$_SESSION['DB_PREFIX']}bookingleg A
			    	WHERE A.id = $id";
	    		
	    	$result = mysql_query($sql);
	    		
	    	if (! $result) {
	    		throw new Exception("Cannot load booking leg $id - $sql - " . mysql_error());
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
	    	$this->bookingid = $resultset['bookingid'];
			$this->signatureid = $resultset['signatureid'];
			$this->pallets = $resultset['pallets'];
			$this->status = $resultset['status'];
			$this->damagedimageid = $resultset['damagedimageid'];
			$this->damagedtext = $resultset['damagedtext'];
			$this->place = $resultset['place'];
			$this->reference = $resultset['reference'];
			$this->phone = $resultset['phone'];
			$this->arrivaltime = $resultset['arrivaltime'];
			$this->departuretime = $resultset['departuretime'];
			$this->timetaken = $resultset['timetaken'];
			$this->miles = $resultset['miles'];
			$this->visittype = $resultset['visittype'];
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
	    				reference, 
	    				phone, 
	    				arrivaltime, 
	    				departuretime, 
	    				timetaken, 
	    				miles,
	    				visittype
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
	    				{$this->propertyStringValue($this->reference)}, 
	    				{$this->propertyStringValue($this->phone)}, 
	    				{$this->propertyDateValue($this->arrivaltime)}, 
	    				{$this->propertyDateValue($this->departuretime)}, 
	    				{$this->propertyDoubleValue($this->timetaken)}, 
	    				{$this->propertyDoubleValue($this->miles)},
	    				{$this->propertyStringValue($this->visittype)}
    				)";
	    				
	    	if (! mysql_query($sql)) {
	    		throw new Exception("Cannot insert booking leg - $sql:" . mysql_error());
	    	}
	    	
    		$this->id = mysql_insert_id();
	    }
	
		/**
		 * id
		 * @return int
		 */
		public function getId(){
			return $this->id;
		}

		/**
		 * bookingid
		 * @return int
		 */
		public function getBookingid(){
			return $this->bookingid;
		}

		/**
		 * booking
		 * @return BookingClass
		 */
		public function getBooking(){
			if ($this->booking == null) {
				$this->booking = new BookingClass();
				$this->booking->load($this->bookingid);
			}
			
			return $this->booking;
		}
		
		/**
		 * bookingid
		 * @param int $bookingid
		 * @return BookingLegClass
		 */
		public function setBookingid($bookingid){
			$this->bookingid = $bookingid;
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
		 * @return BookingLegClass
		 */
		public function setSignatureid($signatureid){
			$this->signatureid = $signatureid;
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
		 * @return BookingLegClass
		 */
		public function setPallets($pallets){
			$this->pallets = $pallets;
			return $this;
		}
	
		/**
		 * status
		 * @return string
		 */
		public function getStatus(){
			return $this->status;
		}
	
		/**
		 * status
		 * @param string $status
		 * @return BookingLegClass
		 */
		public function setStatus($status){
			$this->status = $status;
			return $this;
		}
	
		/**
		 * damagedimageid
		 * @return int
		 */
		public function getDamagedimageid(){
			return $this->damagedimageid;
		}
	
		/**
		 * damagedimageid
		 * @param int $damagedimageid
		 * @return BookingLegClass
		 */
		public function setDamagedimageid($damagedimageid){
			$this->damagedimageid = $damagedimageid;
			return $this;
		}
	
		/**
		 * damagedtext
		 * @return string
		 */
		public function getDamagedtext(){
			return $this->damagedtext;
		}
	
		/**
		 * damagedtext
		 * @param string $damagedtext
		 * @return BookingLegClass
		 */
		public function setDamagedtext($damagedtext){
			$this->damagedtext = $damagedtext;
			return $this;
		}
	
		/**
		 * arrivaltime
		 * @return DateTime
		 */
		public function getArrivaltime(){
			return $this->arrivaltime;
		}
	
		/**
		 * arrivaltime
		 * @param DateTime $arrivaltime
		 * @return BookingLegClass
		 */
		public function setArrivaltime($arrivaltime){
			$this->arrivaltime = $arrivaltime;
			return $this;
		}
	
		/**
		 * departuretime
		 * @return DateTime
		 */
		public function getDeparturetime(){
			return $this->departuretime;
		}
	
		/**
		 * departuretime
		 * @param DateTime $departuretime
		 * @return BookingLegClass
		 */
		public function setDeparturetime($departuretime){
			$this->departuretime = $departuretime;
			return $this;
		}
	
		/**
		 * reference
		 * @return string
		 */
		public function getReference(){
			return $this->reference;
		}
	
		/**
		 * reference
		 * @param string $reference
		 * @return BookingLegClass
		 */
		public function setReference($reference){
			$this->reference = $reference;
			return $this;
		}
	
		/**
		 * phone
		 * @return string
		 */
		public function getPhone(){
			return $this->phone;
		}
	
		/**
		 * phone
		 * @param string $phone
		 * @return BookingLegClass
		 */
		public function setPhone($phone){
			$this->phone = $phone;
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
		 * @return BookingLegClass
		 */
		public function setFromplace($fromplace){
			$this->fromplace = $fromplace;
			return $this;
		}
	
		/**
		 * timetaken
		 * @return float
		 */
		public function getTimetaken(){
			return $this->timetaken;
		}
	
		/**
		 * timetaken
		 * @param float $timetaken
		 * @return BookingLegClass
		 */
		public function setTimetaken($timetaken){
			$this->timetaken = $timetaken;
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
		 * @return BookingLegClass
		 */
		public function setMiles($miles){
			$this->miles = $miles;
			return $this;
		}
	
		/**
		 * place
		 * @return string
		 */
		public function getPlace(){
			return $this->place;
		}
	
		/**
		 * place
		 * @param string $place
		 * @return BookingLegClass
		 */
		public function setPlace($place){
			$this->place = $place;
			return $this;
		}
	
		/**
		 * visittype
		 * @return string
		 */
		public function getVisittype(){
			return $this->visittype;
		}
	
		/**
		 * visittype
		 * @param string $visittype
		 * @return BookingLegClass
		 */
		public function setVisittype($visittype){
			$this->visittype = $visittype;
			return $this;
		}
	
	} 
?>