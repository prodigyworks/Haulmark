<?php
	require_once("system-db.php");
	require_once("podemail.php");
	require_once("crud.php");
	
	function emailPOD() {
		try {
			PODEmail($_POST['podid'], isset($_POST['podemailaddress']) ? $_POST['podemailaddress'] : null);
							
		} catch (Exception $e) {
			throw $e;
		}
	}

	class CustomerCrud extends Crud {
		
		public function postInsertEvent($id) {
			$this->updateBooking($id);
		}
		
		public function postUpdateEvent($id) {
			$this->updateBooking($id);
		}
		
		public function updateBooking($id) {
			if ($_POST['bookingid'] != "" && $_POST['bookingid'] != "0") {
				$sql = "UPDATE {$_SESSION['DB_PREFIX']}booking SET 
						podsent = 'Y'
						WHERE id = {$_POST['bookingid']}";

				if (! mysql_query($sql)) {
					logError("$sql - " . mysql_error(), false);
					throw new Exception(mysql_error());
				}
			}
		}
		
		/* Post header event. */
		public function postHeaderEvent() {
			createDocumentLink();
?>
			<div id="emailDialog" class="modal">
				<label>Email Address</label><br>
				<input type="text" id="customer_email" style="width:400px"/>
			</div>
<?php
		}

		public function postScriptEvent() {
?>
			var currentID = null;

			$(document).ready(
					function() {
						$("#emailDialog").dialog({
								autoOpen: false,
								modal: true,
								width: 460,
								title: "Email POD",
								buttons: {
									"Send": function() {
										pwAlert("Sending POD to customer");

										post("editform", "emailPOD", "submitframe",
												{
													podid: currentID,
													podemailaddress: $("#customer_email").val()
												}
											);

										$(this).dialog("close");
									},
									"Close": function() {
										$(this).dialog("close");
									}
								}
							});
					}
				);

			function bookingReference(node) {
				if (node.bookingid == null || node.bookingid == "" || node.bookingid == 0) {
					return "";
				}
				
				return "<?php echo getSiteConfigData()->bookingprefix; ?>" + padZero(node.bookingid, 6);
			}
			
			function emailCustomerPOD(id) {
				currentID = id;
				$("#emailDialog").dialog("open");
			}

			function emailPOD(id) {
				pwAlert("Sending POD to customer");
				
				post("editform", "emailPOD", "submitframe", 
						{ 
							podid: id
						}
					);
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
	$crud->dialogwidth = 620;
	$crud->title = "Customers";
	$crud->allowAdd = ! isUserInRole("CUSTOMER");
	$crud->allowRemove = false;
	$crud->allowEdit = false;
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
				'name'       => 'bookingref',
				'function'   => 'bookingReference',
				'sortcolumn' => 'A.bookingid',
				'type'		 => 'DERIVED',
				'length' 	 => 16,
				'editable'	 => false,
				'bind' 	 	 => false,
				'filter'	 => false,
				'label' 	 => 'Booking Number'
			),
			array(
				'name'       => 'bookingid',
				'type'       => 'DATACOMBO',
				'length' 	 => 30,
				'label' 	 => 'Booking',
				'required'	 => false,
				'showInView' => false,
				'table'		 => 'booking',
				'table_id'	 => 'id',
				'alias' 	 => 'bookingid',
				'where'		 => " WHERE A.customerid = $customerid ",
				'table_name' => 'id'
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
		
	if (isUserInRole("CUSTOMER")) {
		$crud->subapplications = array(
				array(
					'title'		  => 'Email',
					'imageurl'	  => 'images/email.gif',
					'script' 	  => 'emailCustomerPOD'
				)
			);

	} else {
		$crud->subapplications = array(
			array(
				'title'		  => 'Email',
				'imageurl'	  => 'images/email.gif',
				'script' 	  => 'emailPOD'
			)
		);
	}

	$crud->messages = array(
			array('id'		  => 'podid'),
			array('id'		  => 'podemailaddress')
		);

	$crud->run();
?>
