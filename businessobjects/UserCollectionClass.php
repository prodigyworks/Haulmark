<?php
	require_once("AbstractBaseDataClass.php");
	require_once("UserClass.php");
	
	class UserCollectionClass extends AbstractBaseDataClass {
		
		/** @onetomany **/
		private $users = array();
		
		/**
		 * Constructor 
		 */
	    public function __construct() {
	    	start_db();
	    }
	    
	    /**
	     * Load users available for messaging.
	     */
	    public function loadAvailbleForMessaging() {
	    	if (isUserInRole("ALLEGRO")) {
	    		return $this->loadAll();
	    	}
	    	
			$sql = "SELECT *
					FROM {$_SESSION['DB_PREFIX']}members
					WHERE member_id IN 
					(
						SELECT memberid 
						FROM {$_SESSION['DB_PREFIX']}userroles
						WHERE roleid = 'EXTERNALMESSAGING'
					)
					ORDER BY fullname";
			$result = mysql_query($sql);
			
			if (! $result) {
				throw new Exception("Cannot load users - $sql - " . mysql_error());
			}
			
			/* Clear legs array. */
			$this->users = array();
			
			while (($resultset = mysql_fetch_assoc($result))) {
				/* Create new leg. */
				$user = new UserClass();
				$user->loadFromResultset($resultset);
				
				/* Add leg to the array. */
				$this->users[] = $user;
			}
	    }
		
	    /**
	     * Load all users
	     */
		public function loadAll() {
			$sql = "SELECT *
					FROM {$_SESSION['DB_PREFIX']}members
					ORDER BY fullname";
			$result = mysql_query($sql);
			
			if (! $result) {
				throw new Exception("Cannot load users - $sql - " . mysql_error());
			}
			
			/* Clear legs array. */
			$this->users = array();
			
			while (($resultset = mysql_fetch_assoc($result))) {
				/* Create new leg. */
				$user = new UserClass();
				$user->loadFromResultset($resultset);
				
				/* Add leg to the array. */
				$this->users[] = $user;
			}
			
			return $this;
		}
	
		/**
		 * users
		 * @return UserClass
		 */
		public function getUsers(){
			return $this->users;
		}
	
		/**
		 * users
		 * @param UserClass $users
		 * @return UserCollectionClass
		 */
		public function setUsers($users){
			$this->users = $users;
			return $this;
		}
	}
?>