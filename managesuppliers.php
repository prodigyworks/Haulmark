<?php
	require_once("crud.php");
	
	class SupplierCrud extends Crud {
		
		/* Post header event. */
		public function postHeaderEvent() {
			createDocumentLink();
		}
		
		public function postScriptEvent() {
?>
			function editDocuments(node) {
				viewDocument(node, "addsupplierdocument.php", node, "supplierdocs", "supplierid");
			}
	
			/* Derived invoice address callback. */
			function fullInvoiceAddress(node) {
				var address = "";
				
				if ((node.invoiceaddress1) != "") {
					address = address + node.invoiceaddress1;
				} 
				
				if ((node.invoiceaddress2) != "") {
					if (address != "") {
						address = address + ", ";
					}
					
					address = address + node.invoiceaddress2;
				} 
				
				if ((node.invoiceaddress3) != "") {
					if (address != "") {
						address = address + ", ";
					}
					
					address = address + node.invoiceaddress3;
				} 
				
				if ((node.invoicecity) != "") {
					if (address != "") {
						address = address + ", ";
					}
					
					address = address + node.invoicecity;
				} 
				
				if ((node.invoicepostcode) != "") {
					if (address != "") {
						address = address + ", ";
					}
					
					address = address + node.invoicepostcode;
				} 
				
				if ((node.invoicecountry) != "") {
					if (address != "") {
						address = address + ", ";
					}
					
					address = address + node.invoicecountry;
				} 
				
				return address;
			}
	
			/* Derived delivery address callback. */
			function fullDeliveryAddress(node) {
				var address = "";
				
				if ((node.deliveryaddress1) != "") {
					address = address + node.deliveryaddress1;
				} 
				
				if ((node.deliveryaddress2) != "") {
					if (address != "") {
						address = address + ", ";
					}
					
					address = address + node.deliveryaddress2;
				} 
				
				if ((node.deliveryaddress3) != "") {
					if (address != "") {
						address = address + ", ";
					}
					
					address = address + node.deliveryaddress3;
				} 
				
				if ((node.deliverycity) != "") {
					if (address != "") {
						address = address + ", ";
					}
					
					address = address + node.deliverycity;
				} 
				
				if ((node.deliverypostcode) != "") {
					if (address != "") {
						address = address + ", ";
					}
					
					address = address + node.deliverypostcode;
				} 
				
				if ((node.deliverycountry) != "") {
					if (address != "") {
						address = address + ", ";
					}
					
					address = address + node.deliverycountry;
				} 
				
				return address;
			}
<?php			
		}
	}
	
	$crud = new SupplierCrud();
	$crud->dialogwidth = 650;
	$crud->document = array(
			'primaryidname'	 => 	"supplierid",
			'tablename'		 =>		"supplierdocs"
		);
	$crud->title = "Suppliers";
	$crud->table = "{$_SESSION['DB_PREFIX']}supplier";
	$crud->sql = "SELECT A.*, B.name AS deliverymethodname, D.name AS discountbandname
				  FROM  {$_SESSION['DB_PREFIX']}supplier A
				  LEFT OUTER JOIN  {$_SESSION['DB_PREFIX']}deliverymethod B
				  ON B.id = A.deliverymethodid
				  LEFT OUTER JOIN  {$_SESSION['DB_PREFIX']}discountband D
				  ON D.id = A.discountbandid
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
				'name'       => 'accountnumber',
				'length' 	 => 17,
				'label' 	 => 'Account Number'
			),			
			array(
				'name'       => 'name',
				'length' 	 => 70,
				'label' 	 => 'Name'
			),
			array(
				'name'       => 'firstname',
				'length' 	 => 15,
				'required'	 => false,
				'label' 	 => 'First Name'
			),			
			array(
				'name'       => 'lastname',
				'length' 	 => 15,
				'required'	 => false,
				'label' 	 => 'Last Name'
			),			
			array(
				'name'       => 'owner',
				'length' 	 => 25,
				'required'	 => false,
				'label' 	 => 'Owner'
			),			
			array(
				'name'       => 'imageid',
				'type'		 => 'IMAGE',
				'length' 	 => 64,
				'required'	 => false,
				'showInView' => false,
				'filter'	 => false,
				'label' 	 => 'Logo'
			),
			array(
				'name'       => 'invoiceaddress1',
				'length' 	 => 60,
				'required'	 => false,
				'showInView' => false,
				'label' 	 => 'Invoice Address 1'
			),
			array(
				'name'       => 'invoiceaddress2',
				'length' 	 => 60,
				'showInView' => false,
				'required'	 => false,
				'label' 	 => 'Invoice Address 2'
			),
			array(
				'name'       => 'invoiceaddress3',
				'length' 	 => 60,
				'showInView' => false,
				'required'	 => false,
				'required'	 => false,
				'label' 	 => 'Invoice Address 3'
			),
			array(
				'name'       => 'invoicecity',
				'length' 	 => 30,
				'required'	 => false,
				'showInView' => false,
				'label' 	 => 'Invoice City'
			),
			array(
				'name'       => 'invoicepostcode',
				'length' 	 => 10,
				'showInView' => false,
				'required'	 => false,
				'label' 	 => 'Invoice Post Code'
			),
			array(
				'name'       => 'invoicecountry',
				'length' 	 => 30,
				'showInView' => false,
				'required'	 => false,
				'label' 	 => 'Invoice Country'
			),
			array(
				'name'       => 'invoiceaddress',
				'length' 	 => 90,
				'editable'   => false,
				'required'	 => false,
				'bind'		 => false,
				'type'		 => 'DERIVED',
				'function'	 => 'fullInvoiceAddress',
				'label' 	 => 'Invoice Address'
			),
			array(
				'name'       => 'email1',
				'length' 	 => 40,
				'required'	 => false,
				'label' 	 => 'Invoice Email'
			),
			array(
				'name'       => 'telephone1',
				'length' 	 => 12,
				'required'	 => false,
				'label' 	 => 'Invoice Telephone'
			),
			array(
				'name'       => 'fax1',
				'length' 	 => 12,
				'required' 	 => false,
				'label' 	 => 'Invoice Fax'
			),
			array(
				'name'       => 'deliveryaddress1',
				'length' 	 => 60,
				'showInView' => false,
				'required' 	 => false,
				'label' 	 => 'Delivery Address 1'
			),
			array(
				'name'       => 'deliveryaddress2',
				'length' 	 => 60,
				'showInView' => false,
				'required' 	 => false,
				'label' 	 => 'Delivery Address 2'
			),
			array(
				'name'       => 'deliveryaddress3',
				'length' 	 => 60,
				'showInView' => false,
				'required' 	 => false,
				'label' 	 => 'Delivery Address 3'
			),
			array(
				'name'       => 'deliverycity',
				'length' 	 => 30,
				'showInView' => false,
				'required' 	 => false,
				'label' 	 => 'Delivery City'
			),
			array(
				'name'       => 'deliverypostcode',
				'length' 	 => 10,
				'showInView' => false,
				'required' 	 => false,
				'label' 	 => 'Delivery Post Code'
			),
			array(
				'name'       => 'deliverycountry',
				'length' 	 => 30,
				'showInView' => false,
				'required' 	 => false,
				'label' 	 => 'Delivery Country'
			),
			array(
				'name'       => 'deliveryaddress',
				'length' 	 => 90,
				'editable'   => false,
				'bind'		 => false,
				'type'		 => 'DERIVED',
				'function'	 => 'fullDeliveryAddress',
				'label' 	 => 'Delivery Address'
			),
			array(
				'name'       => 'email2',
				'length' 	 => 40,
				'required' 	 => false,
				'label' 	 => 'Delivery Email'
			),
			array(
				'name'       => 'telephone2',
				'length' 	 => 12,
				'required'	 => false,
				'label' 	 => 'Delivery Telephone'
			)
		);
		
	$crud->run();
?>
