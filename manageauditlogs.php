<?php
	require_once("crud.php");
	
	function clearAllErrors() {
		mysql_query("DELETE FROM {$_SESSION['DB_PREFIX']}errors");
	}
	
	class AuditCrud extends Crud {
		
		public function postScriptEvent() {
?>
			/* Full name callback. */
			function fullName(node) {
				if (node.firstname == null) {
					return "System Management Process";
				}
				
				return (node.firstname + " " + node.lastname);
			}
			
			function tableName(node) {
				if (node.tablename == "Q") {
					return "Quotations";
				}
				
				if (node.tablename == "I") {
					return "Invoices";
				}
				
				if (node.tablename == "C") {
					return "Cases";
				}
				
				return "";
			}
			
			function typeName(node) {
				if (node.type == "I") {
					return "Insert";
				}
				
				if (node.type == "U") {
					return "Update";
				}
				
				if (node.tablename == "D") {
					return "Delete";
				}
				
				return "";
			}
<?php			
		}
	}

	$crud = new AuditCrud();
	$crud->allowAdd = false;
	$crud->allowEdit = false;
	$crud->title = "Audit Logs";
	$crud->table = "{$_SESSION['DB_PREFIX']}caseauditlogs";
//	$crud->dialogwidth = 900;
	$crud->sql = 
			"SELECT A.*, C.fullname
			 FROM {$_SESSION['DB_PREFIX']}loginaudit A 
			 INNER JOIN {$_SESSION['DB_PREFIX']}members C 
			 ON C.member_id = A.memberid 
			 ORDER BY A.id DESC";
	
	$crud->columns = array(
			array(
				'name'       => 'id',
				'length' 	 => 6,
				'pk'		 => true,
				'showInView' => false,
				'filter'	 => false,
				'editable'	 => false,
				'bind' 	 	 => false,
				'label' 	 => 'ID'
			),
			array(
				'name'       => 'timeon',
				'datatype'	 => 'datetime',
				'length' 	 => 18,
				'label' 	 => 'Time On'
			),
			array(
				'name'       => 'timeoff',
				'datatype'	 => 'datetime',
				'length' 	 => 18,
				'label' 	 => 'Time Off'
			),
			array(
				'name'       => 'memberid',
				'type'       => 'DATACOMBO',
				'length' 	 => 30,
				'label' 	 => 'User',
				'table'		 => 'members',
				'table_id'	 => 'member_id',
				'alias'		 => 'fullname',
				'table_name' => 'fullname'
			)
		);
		
	$crud->run();
?>
