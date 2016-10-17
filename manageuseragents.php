<?php
	require_once("crud.php");
	
	function createUserAgent() {
		$useragent = mysql_escape_string($_SERVER['HTTP_USER_AGENT']);
		
		$sql = "INSERT INTO  {$_SESSION['DB_PREFIX']}useragent 
				(
					name, useragent
				)
				VALUES
				(
					'Auto Generated', '$useragent'
				)";
		
		if (! mysql_query($sql)) {
			logError($sql . " - " . mysql_error());
		}
	}
	
	class UserAgentCrud extends Crud {
		
		public function postScriptEvent() {
?>
			function createUserAgent() {
				post("editform", "createUserAgent", "submitframe", {});
			}
<?php			
		}
	}
	
	$crud = new UserAgentCrud();
	$crud->dialogwidth = 740;
	$crud->title = "User Agents";
	$crud->table = "{$_SESSION['DB_PREFIX']}useragent";
	$crud->sql = "SELECT A.* 
				  FROM  {$_SESSION['DB_PREFIX']}useragent A
				  ORDER BY A.name";
	$crud->columns = array(
			array(
				'name'       => 'id',
				'viewname'   => 'uniqueid',
				'length' 	 => 6,
				'showInView' => false,
				'filter'	 => false,
				'bind' 	 	 => false,
				'editable' 	 => false,
				'pk'		 => true,
				'label' 	 => 'ID'
			),
			array(
				'name'       => 'name',
				'length' 	 => 30,
				'label' 	 => 'Name'
			),			
			array(
				'name'       => 'width',
				'length' 	 => 12,
				'align'		 => 'right',
				'label' 	 => 'Width'
			),			
			array(
				'name'       => 'height',
				'length' 	 => 12,
				'align'		 => 'right',
				'label' 	 => 'Height'
			),			
			array(
				'name'       => 'useragent',
				'type'		 => 'BASICTEXTAREA',
				'length' 	 => 140,
				'unique'	 => true,
				'label' 	 => 'User Agent'
			)
		);
		
	$crud->applications = array(
			array(
				'title'		  => 'I am Mobile',
				'imageurl'	  => 'images/document.gif',
				'script' 	  => 'createUserAgent'
			)
		);
		
	$crud->run();
?>
