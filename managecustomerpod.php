<?php
	require_once("system-db.php");
	require_once("crud.php");
	
	class CustomerCrud extends Crud {
		
		/* Post header event. */
		public function postHeaderEvent() {
			createDocumentLink();
		}
		
		public function postScriptEvent() {
?>
			function emailPOD(id) {
				post("editform", "emailPOD", "submitframe", 
						{ 
							podid: id 
						}
					);
					
				pwAlert("Sending POD to customer");
			}
			
			function fileName(node) {
				return "<a class='shortcut' href='viewdocuments.php?id=" + node.documentid + "'>" + node.filename + "</a>";
			}
<?php
		}
	}
	
	if (isset($_GET['id'])) {
		$customerid = $_GET['id'];
		
	} else {
		$customerid = getLoggedOnCustomerID();
	}
	
	$crud = new CustomerCrud();
	$crud->dialogwidth = 550;
	$crud->title = "Customers";
	$crud->allowAdd = ! isUserInRole("CUSTOMER");
	$crud->allowRemove = ! isUserInRole("CUSTOMER");
	$crud->allowEdit = ! isUserInRole("CUSTOMER");
	$crud->table = "{$_SESSION['DB_PREFIX']}customerpod";
	$crud->sql = "SELECT A.*, B.name, C.filename, C.size
				  FROM  {$_SESSION['DB_PREFIX']}customerpod A
				  INNER JOIN  {$_SESSION['DB_PREFIX']}customer B
				  ON B.id = A.customerid
				  INNER JOIN  {$_SESSION['DB_PREFIX']}documents C
				  ON C.id = A.documentid
				  WHERE A.customerid = $customerid
				  ORDER BY A.id DESC";
	$crud->columns = array(
			array(
				'name'       => 'id',
				'viewname'   => 'uniqueid',
				'length' 	 => 6,
				'showInView' => false,
				'filter'	 => false,
				'bind' 	 	 => false,
				'editable' 	 => false,
				'filter'	 => false,
				'pk'		 => true,
				'label' 	 => 'ID'
			),
			array(
				'name'       => 'customerid',
				'editable'	 => false,
				'filter'	 => false,
				'showInView' => false,
				'default'	 => isset($_GET['id']) ? $_GET['id'] : 0
			),
			array(
				'name'       => 'name',
				'length' 	 => 40,
				'editable'	 => false,
				'bind'		 => false,
				'filter'	 => false,
				'label' 	 => 'Customer'
			),
			array(
				'name'       => 'reference',
				'length' 	 => 40,
				'label' 	 => 'Reference'
			),
			array(
				'name'       => 'filedesc',
				'type'		 => 'DERIVED',
				'function'	 => 'fileName',
				'length' 	 => 40,
				'filter'	 => false,
				'editable'   => false,
				'bind'		 => false,
				'label' 	 => 'File'
			),
			array(
				'name'       => 'size',
				'length' 	 => 10,
				'align'		 => 'right',
				'filter'	 => false,
				'editable'	 => false,
				'bind'		 => false,
				'label' 	 => 'Size'
			),
			array(
				'name'       => 'poddate',
				'length' 	 => 15,
				'required'	 => false,
				'datatype'	 => 'date',
				'label' 	 => 'Date'
			),
			array(
				'name'       => 'documentid',
				'type'		 => 'FILE',
				'length' 	 => 75,
				'filter'	 => false,
				'showInView' => false,
				'label' 	 => 'Document'
			)
		);
		
	if (! isUserInRole("CUSTOMER")) {
		$crud->subapplications = array(
				array(
					'title'		  => 'Email',
					'imageurl'	  => 'images/email.gif',
					'script' 	  => 'emailPOD'
				)
			);
		

		$crud->messages = array(
			array('id'		  => 'podid')
		);
	}
		
	$crud->run();
?>
