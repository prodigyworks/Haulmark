<?php
	require_once("crud.php");
	
	class BookingStatusCrud extends Crud {
		public function postScriptEvent() {
?>
<?php
		}
	}
	
	$crud = new BookingStatusCrud();
	$crud->title = "Booking Status";
	$crud->dialogwidth = 950;
	$crud->table = "{$_SESSION['DB_PREFIX']}bookingstatus";
	$crud->sql = "SELECT A.*, B.roleid
				  FROM {$_SESSION['DB_PREFIX']}bookingstatus A
				  LEFT OUTER JOIN {$_SESSION['DB_PREFIX']}roles B
				  ON B.roleid = A.workflowrole
				  ORDER BY A.id";
	
	$crud->columns = array(
			array(
				'name'       => 'id',
				'length' 	 => 6,
				'showInView' => false,
				'bind' 	 	 => false,
				'editable' 	 => false,
				'pk'		 => true,
				'label' 	 => 'ID'
			),
			array(
				'name'       => 'name',
				'length' 	 => 60,
				'label' 	 => 'Name'
			),
			array(
				'name'		 => 'workflowrole',
				'type'       => 'DATACOMBO',
				'length' 	 => 30,
				'label' 	 => 'Workflow Role',
				'table'		 => 'roles',
				'table_id'	 => 'roleid',
				'alias'		 => 'roleid',
				'table_name' => 'roleid'
			),
			array(
				'name'       => 'bgcolour',
				'length' 	 => 20,
				'label' 	 => 'Background Chart Colour'
			),
			array(
				'name'       => 'fgcolour',
				'length' 	 => 20,
				'label' 	 => 'Foreground Chart Colour'
			),
			array(
				'name'       => 'emailcontent',
				'length' 	 => 60,
				'type'		 => 'TEXTAREA',
				'showInView' => false,
				'label' 	 => 'Email Content'
			)
		);
		
	$crud->run();
	
?>
