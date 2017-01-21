<?php
	require_once("AbstractBaseDataClass.php");
	require_once("MessageClass.php");
	
	class MessageCollectionClass extends AbstractBaseDataClass {
		
		/** @onetomany **/
		private $messages = array();
		
		/**
		 * Constructor 
		 */
	    public function __construct() {
	    	start_db();
	    }

	    /**
	     * Load messages by user.
	     * @param $memberid User ID.
	     */
		public function loadInboxByUser($memberid) {
			$this->loadMessages(
				   "SELECT 
				   	A.id, A.replied, A.status, A.subject, A.message, 
				   	A.from_member_id, A.to_member_id, A.createddate, A.action
					FROM  {$_SESSION['DB_PREFIX']}messages A 
					WHERE A.to_member_id = $memberid 
					AND (A.deleted != 'Y' OR A.deleted IS NULL) 
					ORDER BY A.createddate DESC"
				);
		}

	    /**
	     * Load sent messages by user.
	     * @param $memberid User ID.
	     */
		public function loadSentMessagesByUser($memberid) {
			$this->loadMessages(
				   "SELECT A.id, A.replied, A.status, A.subject, A.message, A.from_member_id, A.to_member_id, 
				   	A.id, A.replied, A.status, A.subject, A.message, 
				   	A.from_member_id, A.to_member_id, A.createddate, A.action
					FROM  {$_SESSION['DB_PREFIX']}messages A 
					WHERE A.from_member_id = $memberid
					ORDER BY A.createddate DESC"
				);
		}

	    /**
	     * Load archived messages by user.
	     * @param $memberid User ID.
	     */
		public function loadArchivedMessagesByUser($memberid) {
			$this->loadMessages(
				   "SELECT A.id, A.replied, A.status, A.subject, A.message, A.from_member_id, A.to_member_id, 
				   	A.id, A.replied, A.status, A.subject, A.message, 
				   	A.from_member_id, A.to_member_id, A.createddate, A.action
					FROM  {$_SESSION['DB_PREFIX']}messages A 
					WHERE A.to_member_id = $memberid 
					AND A.deleted = 'Y' 
					ORDER BY A.createddate DESC"
				);
		}

		/**
		 * Load messages.
		 * @param $sql SQL
		 * @throws Exception Exception
		 */
		private function loadMessages($sql) {
			$result = mysql_query($sql);
			
			if (! $result) {
				throw new Exception("Cannot load messages - $sql - " . mysql_error());
			}
			
			/* Clear legs array. */
			$this->messages = array();
			
			while (($resultset = mysql_fetch_assoc($result))) {
				/* Create new leg. */
				$message = new MessageClass();
				$message->loadFromResultset($resultset);
				
				/* Add leg to the array. */
				$this->messages[] = $message;
			}
		}
	
		/**
		 * messages
		 * @return MessageClass
		 */
		public function getMessages(){
			return $this->messages;
		}
	
		/**
		 * messages
		 * @param MessageClass $messages
		 * @return MessageCollectionClass
		 */
		public function setMessages($messages){
			$this->messages = $messages;
			return $this;
		}

	}
?>