<?php
	require_once("AbstractBaseDataClass.php");
	
	class BookingStatusClass extends AbstractBaseDataClass {
		
		/** @property **/
		private $id = null;
		/** @property **/
		private $name = null;
		/** @property **/
		private $bgcolour = null;
		/** @property **/
		private $fgcolour = null;
		/** @property **/
		private $sequence = null;
		
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
	    	$this->name = $resultset['name'];
	    	$this->sequence = $resultset['sequence'];
	    	$this->bgcolour = $resultset['bgcolour'];
	    	$this->fgcolour = $resultset['fgcolour'];
    	}
	    
	    /**
	     * Load booking status.
	     * @param $id ID
	     * @throws Exception
	     */
	    public function load($id) {
	    	$sql = "SELECT *
	    			FROM {$_SESSION['DB_PREFIX']}bookingstatus
	    			WHERE id = $id";
	    	
	    	$result = mysql_query($sql);
	    	
	    	if (! $result) {
	    		 throw new Exception("Cannot load bookingstatus - $sql");
	    	}
	    	
	    	while (($resultset = mysql_fetch_assoc($result))) {
	    		 $this->loadFromResultset($resultset);
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
		 * name
		 * @return string
		 */
		public function getName(){
			return $this->name;
		}
	
		/**
		 * name
		 * @param string $name
		 * @return BookingStatusClass
		 */
		public function setName($name){
			$this->name = $name;
			return $this;
		}
		
		/**
		 * id
		 * @param unkown $id
		 * @return BookingStatusClass
		 */
		public function setId($id){
			$this->id = $id;
			return $this;
		}
	
		/**
		 * bgcolour
		 * @return string
		 */
		public function getBgcolour(){
			return $this->bgcolour;
		}
	
		/**
		 * bgcolour
		 * @param string $bgcolour
		 * @return BookingStatusClass
		 */
		public function setBgcolour($bgcolour){
			$this->bgcolour = $bgcolour;
			return $this;
		}
	
		/**
		 * fgcolour
		 * @return string
		 */
		public function getFgcolour(){
			return $this->fgcolour;
		}
	
		/**
		 * fgcolour
		 * @param string $fgcolour
		 * @return BookingStatusClass
		 */
		public function setFgcolour($fgcolour){
			$this->fgcolour = $fgcolour;
			return $this;
		}
	
		/**
		 * sequence
		 * @return int
		 */
		public function getSequence(){
			return $this->sequence;
		}
	
		/**
		 * sequence
		 * @param int $sequence
		 * @return BookingStatusClass
		 */
		public function setSequence($sequence){
			$this->sequence = $sequence;
			return $this;
		}
	
	}
?>