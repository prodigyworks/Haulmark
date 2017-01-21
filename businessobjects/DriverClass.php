<?php
	require_once("AbstractBaseDataClass.php");
	
	class DriverClass extends AbstractBaseDataClass {
		
		/** @property **/
		private $id = null;
		/** @property **/
		private $name = null;
		
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
    	}
	    
	    /**
	     * Load message.
	     * @param $id ID
	     * @throws Exception
	     */
	    public function load($id) {
	    	$sql = "SELECT *
	    			FROM {$_SESSION['DB_PREFIX']}driver
	    			WHERE id = $id";
	    	
	    	$result = mysql_query($sql);
	    	
	    	if (! $result) {
	    		 throw new Exception("Cannot load driver - $sql");
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
		 * @return DriverClass
		 */
		public function setName($name){
			$this->name = $name;
			return $this;
		}
	}
?>