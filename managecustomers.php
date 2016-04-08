<?php
	require_once("crud.php");
	
	class CustomerCrud extends Crud {
		
		/* Post header event. */
		public function postHeaderEvent() {
			createDocumentLink();
		}
		
		public function postScriptEvent() {
?>
			function editDocuments(node) {
				viewDocument(node, "addcustomerdocument.php", node, "customerdocs", "customerid");
			}
	
			/* Derived address callback. */
			function fullAddress(node) {
				var address = "";
				
				if ((node.street) != "") {
					address = address + node.street;
				} 
				
				if ((node.address2) != "" && node.address2 != null) {
					if (address != "") {
						address = address + ", ";
					}
					
					address = address + node.address2;
				} 
				
				if ((node.town) != "") {
					if (address != "") {
						address = address + ", ";
					}
					
					address = address + node.town;
				} 
				
				if ((node.city) != "") {
					if (address != "") {
						address = address + ", ";
					}
					
					address = address + node.city;
				} 
				
				if ((node.county) != "") {
					if (address != "") {
						address = address + ", ";
					}
					
					address = address + node.county;
				} 
				
				if ((node.postcode) != "") {
					if (address != "") {
						address = address + ", ";
					}
					
					address = address + node.postcode;
				} 
				
				return address;
			}
<?php			
		}
	}
	
	$crud = new CustomerCrud();
	$crud->dialogwidth = 970;
	$crud->title = "Customers";
	$crud->table = "{$_SESSION['DB_PREFIX']}customer";
	$crud->sql = "SELECT A.*, B.name AS taxcodename, C.name AS accountstatusname
				  FROM  {$_SESSION['DB_PREFIX']}customer A
				  LEFT OUTER JOIN  {$_SESSION['DB_PREFIX']}taxcode B
				  ON B.id = A.taxcodeid
				  LEFT OUTER JOIN  {$_SESSION['DB_PREFIX']}accountstatus C
				  ON C.id = A.accountstatusid
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
				'name'       => 'accountcode',
				'length' 	 => 10,
				'label' 	 => 'Account Code'
			),			
			array(
				'name'       => 'name',
				'length' 	 => 40,
				'label' 	 => 'Name'
			),
			array(
				'name'       => 'street',
				'length' 	 => 60,
				'showInView' => false,
				'required'	 => false,
				'label' 	 => 'Street'
			),
			array(
				'name'       => 'address2',
				'length' 	 => 60,
				'showInView' => false,
				'required'	 => false,
				'label' 	 => 'Address 2'
			),
			array(
				'name'       => 'town',
				'length' 	 => 30,
				'showInView' => false,
				'required'	 => false,
				'label' 	 => 'Town'
			),
			array(
				'name'       => 'city',
				'length' 	 => 30,
				'required'	 => false,
				'showInView' => false,
				'label' 	 => 'City'
			),
			array(
				'name'       => 'county',
				'length' 	 => 30,
				'required'	 => false,
				'showInView' => false,
				'label' 	 => 'County'
			),
			array(
				'name'       => 'postcode',
				'length' 	 => 10,
				'required'	 => false,
				'showInView' => false,
				'label' 	 => 'Post Code'
			),
			array(
				'name'       => 'address',
				'length' 	 => 70,
				'editable'   => false,
				'required'	 => false,
				'bind'		 => false,
				'type'		 => 'DERIVED',
				'function'	 => 'fullAddress',
				'label' 	 => 'Address'
			),
			array(
				'name'       => 'email',
				'required'	 => false,
				'length' 	 => 70,
				'label' 	 => 'Email'
			),
			array(
				'name'       => 'email2',
				'length' 	 => 70,
				'required'	 => false,
				'label' 	 => 'Email'
			),
			array(
				'name'       => 'telephone',
				'length' 	 => 12,
				'required'	 => false,
				'label' 	 => 'Telephone'
			),
			array(
				'name'       => 'telephone2',
				'length' 	 => 12,
				'required'	 => false,
				'label' 	 => 'Telephone 2'
			),
			array(
				'name'       => 'fax',
				'length' 	 => 12,
				'required'	 => false,
				'label' 	 => 'Fax'
			),
			array(
				'name'       => 'contact1',
				'length' 	 => 15,
				'required'	 => false,
				'label' 	 => 'Contact 1'
			),			
			array(
				'name'       => 'contact2',
				'length' 	 => 15,
				'required'	 => false,
				'label' 	 => 'Contact 2'
			),			
			array(
				'name'       => 'podfolder',
				'length' 	 => 50,
				'required'	 => false,
				'label' 	 => 'POD Folder'
			),			
			array(
				'name'       => 'nominalledgercode',
				'length' 	 => 15,
				'required'	 => false,
				'label' 	 => 'Nominal Ledger Code'
			),			
			array(
				'name'       => 'collectionpoint',
				'required'	 => false,
				'length' 	 => 50,
				'type'		 => 'GEOLOCATION',
				'label' 	 => 'Collection Point'
			),			
			array(
				'name'       => 'deliverypoint',
				'length' 	 => 50,
				'type'		 => 'GEOLOCATION',
				'required'	 => false,
				'label' 	 => 'Delivery Point'
			),			
			array(
				'name'       => 'selfbilledinvoices',
				'length' 	 => 20,
				'required'	 => false,
				'label' 	 => 'Self Billed Invoices',
				'type'       => 'COMBO',
				'options'    => array(
						array(
							'value'		=> "N",
							'text'		=> "No"
						),
						array(
							'value'		=> "Y",
							'text'		=> "Yes"
						)
					)
			),
			array(
				'name'       => 'vatregistered',
				'length' 	 => 13,
				'label' 	 => 'VAT Registered',
				'required'	 => false,
				'type'       => 'COMBO',
				'options'    => array(
						array(
							'value'		=> "N",
							'text'		=> "No"
						),
						array(
							'value'		=> "Y",
							'text'		=> "Yes"
						)
					)
			),
			array(
				'name'       => 'duedays',
				'length' 	 => 10,
				'required'	 => false,
				'datatype'	 => 'integer',
				'label' 	 => 'Due Days'
			),			
			array(
				'name'       => 'creditlimit',
				'length' 	 => 15,
				'required'	 => false,
				'datatype'	 => 'double',
				'label' 	 => 'Credit Limit'
			),			
			array(
				'name'       => 'standardratepermile',
				'length' 	 => 17,
				'datatype'	 => 'double',
				'required'	 => false,
				'label' 	 => 'Standard Rate Per Mile'
			),			
			array(
				'name'       => 'accountstatusid',
				'type'       => 'DATACOMBO',
				'length' 	 => 10,
				'label' 	 => 'Account Status',
				'table'		 => 'accountstatus',
				'required'	 => false,
				'table_id'	 => 'id',
				'alias'		 => 'accountstatusname',
				'table_name' => 'name'
			),
			array(
				'name'       => 'sagecustomerref',
				'required'	 => false,
				'length' 	 => 40,
				'label' 	 => 'Sage Customer Reference'
			),			
			array(
				'name'       => 'taxcodeid',
				'type'       => 'DATACOMBO',
				'length' 	 => 10,
				'label' 	 => 'Tax Code',
				'required'	 => false,
				'table'		 => 'taxcode',
				'table_id'	 => 'id',
				'alias'		 => 'taxcodename',
				'table_name' => 'name'
			),
			array(
				'name'       => 'terms',
				'length' 	 => 50,
				'type'		 => 'TEXTAREA',
				'required'	 => false,
				'showInView' => false,
				'label' 	 => 'Terms'
			),
			array(
				'name'       => 'termsagreed',
				'length' 	 => 50,
				'type'		 => 'TEXTAREA',
				'showInView' => false,
				'required'	 => false,
				'label' 	 => 'Terms Agreed'
			),
			array(
				'name'       => 'notes',
				'length' 	 => 50,
				'type'		 => 'TEXTAREA',
				'showInView' => false,
				'required'	 => false,
				'label' 	 => 'Notes'
			)
		);

	$crud->subapplications = array(
			array(
				'title'		  => 'Documents',
				'imageurl'	  => 'images/document.gif',
				'script' 	  => 'editDocuments'
			),
			array(
				'title'		  => 'Contacts',
				'imageurl'	  => 'images/user.png',
				'application' => 'managecustomercontacts.php'
			),
			array(
				'title'		  => 'POD',
				'imageurl'	  => 'images/document.gif',
				'application' => 'managecustomerpod.php'
			)
		);
		
	$crud->run();
?>
