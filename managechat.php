<?php
	require_once("crud.php");
	
	function clearAllChat() {
		mysql_query("DELETE FROM {$_SESSION['DB_PREFIX']}chat");
	}
	
	class ChatCrud extends Crud {
		
		public function postScriptEvent() {
?>
			function clearChat(id) {
				post("editform", "clearAllChat", "submitframe", {});
			}
<?php			
		}
	}

	$crud = new ChatCrud();
	$crud->allowAdd = false;
	$crud->title = "Chat";
	$crud->allowRemove = true;
	$crud->table = "{$_SESSION['DB_PREFIX']}chat";
	$crud->dialogwidth = 900;
	$crud->sql = 
			"SELECT A.*, C.fullname 
			 FROM {$_SESSION['DB_PREFIX']}chat A 
			 LEFT OUTER JOIN {$_SESSION['DB_PREFIX']}members C 
			 ON C.member_id = A.memberid 
			 ORDER BY A.id DESC";
	
	$crud->columns = array(
			array(
				'name'       => 'id',
				'length' 	 => 6,
				'pk'		 => true,
				'showInView' => false,
				'editable'	 => false,
				'bind' 	 	 => false,
				'label' 	 => 'ID'
			),
			array(
				'name'       => 'createddate',
				'length' 	 => 20,
				'bind'		 => false,
				'label' 	 => 'Created Date'
			),
			array(
				'name'       => 'memberid',
				'datatype'	 => 'user',
				'length' 	 => 20,
				'showInView' => false,
				'label' 	 => 'User'
			),
			array(
				'name'       => 'fullname',
				'length' 	 => 30,
				'editable'	 => false,
				'bind'		 => false,
				'label' 	 => 'User'
			),
			array(
				'name'       => 'status',
				'length' 	 => 20,
				'label' 	 => 'Status',
				'type'       => 'COMBO',
				'options'    => array(
						array(
							'value'		=> 'N',
							'text'		=> 'New'
						),
						array(
							'value'		=> 'C',
							'text'		=> 'Complete'
						),
						array(
							'value'		=> 'X',
							'text'		=> 'Cancelled'
						)
					)
			),
			array(
				'name'       => 'message',
				'type'		 => 'TEXTAREA',
				'length' 	 => 100,
				'label' 	 => 'Message'
			)
		);
	$crud->applications = array(
			array(
				'title'		  => 'Clear',
				'imageurl'	  => 'images/minimize.gif',
				'script' 	  => 'clearChat'
			)
		);
		
	$crud->run();
?>
