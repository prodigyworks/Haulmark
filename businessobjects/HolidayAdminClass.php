<?php
	class HolidayAdminClass {
		
		/** @property @date **/
		private $start;
		/** @property @date **/
		private $end;
		
		/**
		 * Constructor 
		 */
	    public function __construct() {
	    	start_db();
	    	
			$year = date("Y");
			$month = sprintf("%02d", getSiteConfigData()->holidaycutoffmonth);
			$day = sprintf("%02d", getSiteConfigData()->holidaycutoffday);
			$currentYearMay = strtotime("$year-$month-$day");
			$strCurrentYearMay = date("Y-m-d", $currentYearMay);
			$now = mktime();
			
			if ($currentYearMay > $now) {
			    $dt = new DateTime($strCurrentYearMay);
			    $dt->modify('-1 year');
			    
			    $this->start = $dt->format('Y-m-d'); 
			    $this->end = $strCurrentYearMay; 
				
			} else {
			    $dt = new DateTime($strCurrentYearMay);
			    $dt->modify('+1 year');
			    $end = $dt->format('Y-m-d'); 
			    
			    $this->start = $strCurrentYearMay; 
			    $this->end = $dt->format('Y-m-d'); 
			}
	    	
	    }
		
		/**
		 * start
		 * @return DateTime
		 */
		public function getStart(){
			return $this->start;
		}
	
		/**
		 * start
		 * @param DateTime $start
		 * @return HolidayAdminClass
		 */
		public function setStart($start){
			$this->start = $start;
			return $this;
		}
	
		/**
		 * end
		 * @return DateTime
		 */
		public function getEnd(){
			return $this->end;
		}
	
		/**
		 * end
		 * @param DateTime $end
		 * @return HolidayAdminClass
		 */
		public function setEnd($end){
			$this->end = $end;
			return $this;
		}
	}
?>