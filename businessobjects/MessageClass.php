<?php
	require_once("AbstractBaseDataClass.php");
	require_once("UserClass.php");
	
	class MessageClass extends AbstractBaseDataClass {
		
		/** @property **/
		private $id = null;
		/** @property **/
		private $fromMemberID = null;
		/** @property **/
		private $toMemberID = null;
		/** @property **/
		private $subject = null;
		/** @property **/
		private $message = null;
		/** @property **/
		private $status = null;
		/** @property **/
		private $deleted = null;
		/** @property **/
		private $action = null;
		/** @property @datetime **/
		private $createddate = null;
		/** @property **/
		private $replied = null;

		/** @onetoone **/
		private $fromMember = null;
		/** @onetoone **/
		private $toMember = null;
		
		/**
		 * Constructor 
		 */
	    public function __construct() {
	    	start_db();
	    }

	    /**
	     * Get message count.
	     */
	    public static function getUnreadMessageCountForUser($userid) {
	    	$messagecount = 0;
	    	$sql = "SELECT COUNT(*) AS messagecount 
	    			FROM {$_SESSION['DB_PREFIX']}messages
	    			WHERE to_member_id = $userid
	    			AND status = 'N'
	    			AND (deleted != 'Y' AND deleted IS NOT NULL)";
	    	
	    	$result = mysql_query($sql);
	    	
	    	if (! $result) {
	    		throw new Exception("Cannot load messages");
	    	}
	    	
	    	while (($resultset = mysql_fetch_assoc($result))) {
	    		$messagecount = $resultset['messagecount'];
	    	}
	    	
	    	return $messagecount;
	    }
	    
	    /** 
	     * Load from resultset 
	     */
	    public function loadFromResultset($resultset) {
			$this->id = $resultset['id'];
	    	$this->fromMemberID = $resultset['from_member_id'];
			$this->toMemberID = $resultset['to_member_id'];
			$this->subject = $resultset['subject'];
			$this->message = $resultset['message'];
			$this->status = $resultset['status'];
			$this->deleted = $resultset['deleted'];
			$this->action = $resultset['action'];
			$this->createddate = $resultset['createddate'];
			$this->replied = $resultset['replied'];
	    }
	    
	    /**
	     * Load message.
	     * @param $id ID
	     * @throws Exception
	     */
	    public function load($id) {
	    	$sql = "SELECT 
	    			id, from_member_id, to_member_id, subject, status,
	    			deleted, action, createddate, replied,
	    			message
	    			FROM {$_SESSION['DB_PREFIX']}messages
	    			WHERE id = $id";
	    	
	    	$result = mysql_query($sql);
	    	
	    	if (! $result) {
	    		throw new Exception("Cannot load message - $sql");
	    	}
	    	
	    	while (($resultset = mysql_fetch_assoc($result))) {
		    	$this->loadFromResultset($resultset);
	    	}
	    }
	    
	    /**
	     * Mark as replied.
	     * @param $messages Message array.
	     * @throws Exception
	     */
	    public static function markSelectedMessagesAsReplied($messages) {
	    	$memberid = getLoggedOnMemberID();
	    	$inSQL = ArrayToInClause($messages);
	    	
			$sql = "UPDATE {$_SESSION['DB_PREFIX']}messages SET 
					replied = 'Y', 
					metamodifieddate = NOW(), 
					metamodifieduserid = $memberid 
					WHERE id IN ($inSQL)
					AND to_member_id = $memberid";
					
			if (! mysql_query($sql)) {
				throw new Exception("Cannot delete selected messages");
			}
	    }
	    
	    /**
	     * Delete selected messages.
	     * @param $messages Message array.
	     * @throws Exception
	     */
	    public static function deleteSelectedMessages($messages) {
	    	$memberid = getLoggedOnMemberID();
	    	$inSQL = ArrayToInClause($messages);
	    	
			$sql = "UPDATE {$_SESSION['DB_PREFIX']}messages SET 
					deleted = 'Y', 
					metamodifieddate = NOW(), 
					metamodifieduserid = $memberid 
					WHERE id IN ($inSQL)
					AND to_member_id = $memberid";
					
			if (! mysql_query($sql)) {
				throw new Exception("Cannot delete selected messages");
			}
	    }
	    
	    /**
	     * Mark selected messages as read.
	     * @param $messages Message array.
	     * @throws Exception
	     */
	    public static function markSelectedMessagesAsRead($messages) {
	    	$memberid = getLoggedOnMemberID();
	    	$inSQL = ArrayToInClause($messages);
	    	
			$sql = "UPDATE {$_SESSION['DB_PREFIX']}messages SET 
					status = 'R', 
					metamodifieddate = NOW(), 
					metamodifieduserid = $memberid 
					WHERE id IN ($inSQL)
					AND to_member_id = $memberid";
	    	
			if (! mysql_query($sql)) {
				throw new Exception("Cannot delete selected messages");
			}
        }
	    
	    /**
	     * Mark selected messages as unread.
	     * @param $messages Message array.
	     * @throws Exception
	     */
        public static function markSelectedMessagesAsUnread($messages) {
	    	$memberid = getLoggedOnMemberID();
	    	$inSQL = ArrayToInClause($messages);
	    	
			$sql = "UPDATE {$_SESSION['DB_PREFIX']}messages SET 
					status = 'N', 
					metamodifieddate = NOW(), 
					metamodifieduserid = $memberid 
					WHERE id IN ($inSQL)
					AND to_member_id = $memberid";
					
			if (! mysql_query($sql)) {
				throw new Exception("Cannot delete selected messages");
			}
	    }
	    
		/**
		 * Get the status image name.
		 */
		public function getStatusImageName() {
			if ($this->replied == "Y") {
				return "<img src='images/replied.png' />";
				
			} else if ($this->status == "N") {
				return "<img src='images/unread.png' />";
				
			} else if ($this->status == "R") {
				return "<img src='images/read.png' />";
			}
			
			return "";
		}
				/**
		 * id
		 * @return int
		 */
		public function getId(){
			return $this->id;
		}
	
/**
		 * fromMemberID
		 * @return int
		 */
		public function getFromMemberID(){
			return $this->fromMemberID;
		}
	
		/**
		 * fromMemberID
		 * @param int $fromMemberID
		 * @return MessageClass
		 */
		public function setFromMemberID($fromMemberID){
			$this->fromMemberID = $fromMemberID;
			return $this;
		}
	
		/**
		 * toMemberID
		 * @return int
		 */
		public function getToMemberID(){
			return $this->toMemberID;
		}
	
		/**
		 * toMemberID
		 * @param int $toMemberID
		 * @return MessageClass
		 */
		public function setToMemberID($toMemberID){
			$this->toMemberID = $toMemberID;
			return $this;
		}
	
		/**
		 * subject
		 * @return string
		 */
		public function getSubject(){
			return $this->subject;
		}
	
		/**
		 * subject
		 * @param string $subject
		 * @return MessageClass
		 */
		public function setSubject($subject){
			$this->subject = $subject;
			return $this;
		}
	
		/**
		 * message
		 * @return string
		 */
		public function getMessage(){
			return $this->message;
		}
	
		/**
		 * message
		 * @param string $message
		 * @return MessageClass
		 */
		public function setMessage($message){
			$this->message = $message;
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
		 * @return MessageClass
		 */
		public function setStatus($status){
			$this->status = $status;
			return $this;
		}
	
		/**
		 * deleted
		 * @return string
		 */
		public function getDeleted(){
			return $this->deleted;
		}
	
		/**
		 * deleted
		 * @param string $deleted
		 * @return MessageClass
		 */
		public function setDeleted($deleted){
			$this->deleted = $deleted;
			return $this;
		}
	
		/**
		 * action
		 * @return string
		 */
		public function getAction(){
			return $this->action;
		}
	
		/**
		 * action
		 * @param string $action
		 * @return MessageClass
		 */
		public function setAction($action){
			$this->action = $action;
			return $this;
		}
	
		/**
		 * createddate
		 * @return DateTime
		 */
		public function getCreateddate(){
			return $this->createddate;
		}
	
		/**
		 * createddate
		 * @param DateTime $createddate
		 * @return MessageClass
		 */
		public function setCreateddate($createddate){
			$this->createddate = $createddate;
			return $this;
		}
	
		/**
		 * replied
		 * @return string
		 */
		public function getReplied(){
			return $this->replied;
		}
	
		/**
		 * replied
		 * @param string $replied
		 * @return MessageClass
		 */
		public function setReplied($replied){
			$this->replied = $replied;
			return $this;
		}
	
		/**
		 * fromMember
		 * @return UserClass
		 */
		public function getFromMember(){
			if ($this->fromMember == null) {
				$this->fromMember = new UserClass();
				$this->fromMember->load($this->fromMemberID);
			}
				
			return $this->fromMember;
		}
	
		/**
		 * fromMember
		 * @param UserClass $fromMember
		 * @return MessageClass
		 */
		public function setFromMember($fromMember){
			$this->fromMember = $fromMember;
			return $this;
		}
	
		/**
		 * toMember
		 * @return UserClass
		 */
		public function getToMember(){
			if ($this->toMember == null) {
				$this->toMember = new UserClass();
				$this->toMember->load($this->toMemberID);
			}
			
			return $this->toMember;
		}
	
		/**
		 * toMember
		 * @param UserClass $toMember
		 * @return MessageClass
		 */
		public function setToMember($toMember){
			$this->toMember = $toMember;
			return $this;
		}
	
	}
?>