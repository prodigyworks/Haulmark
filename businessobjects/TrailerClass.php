<?php
	require_once("AbstractBaseDataClass.php");
	
	class TrailerClass extends AbstractBaseDataClass {
		
		/** @property **/
		private $id = null;
		/** @property **/
		private $registration = null;
		
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
	    	$this->registration = $resultset['registration'];
    	}
	    
	    /**
	     * Load message.
	     * @param $id ID
	     * @throws Exception
	     */
	    public function load($id) {
	    	$sql = "SELECT *
	    			FROM {$_SESSION['DB_PREFIX']}trailer
	    			WHERE id = $id";
	    	
	    	$result = mysql_query($sql);
	    	
	    	if (! $result) {
	    		 throw new Exception("Cannot load trailer - $sql");
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
		 * registration
		 * @return string
		 */
		public function getRegistration(){
			return $this->registration;
		}
	
		/**
		 * registration
		 * @param string $registration
		 * @return TrailerClass
		 */
		public function setRegistration($registration){
			$this->registration = $registration;
			return $this;
		}
	}
?>