<?php
	require_once("AbstractBaseDataClass.php");
	
	class VehicleClass extends AbstractBaseDataClass {
	
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
					FROM {$_SESSION['DB_PREFIX']}vehicle
					WHERE id = $id";
	
			$result = mysql_query($sql);
	
			if (! $result) {
				throw new Exception("Cannot load vehicle - $sql");
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
		 * @return VehicleClass
		 */
		public function setRegistration($registration){
			$this->registration = $registration;
			return $this;
		}
	}
?>