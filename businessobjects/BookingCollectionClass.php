<?php
	require_once("AbstractBaseDataClass.php");
	require_once("BookingClass.php");
	
	class BookingCollectionClass extends AbstractBaseDataClass {
	
		/** @onetomany **/
		private $bookings = array();
	
		/**
		 * Constructor
		 */
		public function __construct() {
			start_db();
		}
	
		/**
		 * Load overdue bookings
		 * @return BookingCollectionClass
		 */
		public function loadOverdueBookings() {
			$sql = "SELECT A.*
					FROM {$_SESSION['DB_PREFIX']}booking A
					WHERE A.statusid IN (4, 5, 6)
					AND A.enddatetime < NOW()
					AND A.enddatetime >= DATE_SUB(NOW(), INTERVAL 24 hour)
					ORDER BY A.id";
		
			return $this->populateBookings($sql);
		}
	
		/**
		 * Load late bookings
		 * @return BookingCollectionClass
		 */
		public function loadLateBookings() {
			$sql = "SELECT A.*
					FROM {$_SESSION['DB_PREFIX']}booking A
					WHERE A.statusid IN (1, 2, 3, 4, 5)
					AND A.startdatetime < NOW()
					AND A.startdatetime >= DATE_SUB(NOW(), INTERVAL 24 hour)
					ORDER BY A.id";
			
			return $this->populateBookings($sql);
		}
		
		/**
		 * Load all bookings
		 * @return BookingCollectionClass
		 */
		public function loadAll() {
			$sql = "SELECT *
					FROM {$_SESSION['DB_PREFIX']}bookings
					ORDER BY id";
	
			return $this->populateBookings($sql);
		}
	
		/**
		 * Load bookings for planning page.
		 * @param DateTime $startdate
		 * @param DateTime $enddate
		 * @return BookingCollectionClass
		 */
		public function loadPlanningPage($startdate, $enddate) {
			$sql = "SELECT *
					FROM {$_SESSION['DB_PREFIX']}booking
			   		WHERE (startdatetime < '$enddate' AND enddatetime > '$startdate')
			   		AND statusid > 1
					ORDER BY id";
			
			return $this->populateBookings($sql);
		}
		
		/**
		 * Populate bookings
		 * @param string $sql
		 * @throws Exception
		 * @return BookingCollectionClass
		 */
		private function populateBookings($sql) {
			$result = mysql_query($sql);
				
			if (! $result) {
				throw new Exception("Cannot load bookings - $sql - " . mysql_error());
			}
				
			/* Clear legs array. */
			$this->bookings = array();
				
			while (($resultset = mysql_fetch_assoc($result))) {
				/* Create new leg. */
				$booking = new BookingClass();
				$booking->loadFromResultset($resultset);
			
				/* Add leg to the array. */
				$this->bookings[] = $booking;
			}
				
			return $this;
		}
		
		/**
		 * bookings
		 * @return BookingClass
		 */
		public function getBookings(){
			return $this->bookings;
		}
	
		/**
		 * bookings
		 * @param BookingClass $bookings
		 * @return BookingCollectionClass
		 */
		public function setBookings($bookings){
			$this->bookings = $bookings;
			return $this;
		}
	}
?>