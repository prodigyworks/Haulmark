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
	$crud->dialogwidth = 950;
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
				'label' 	 => 'Street'
			),
			array(
				'name'       => 'town',
				'length' 	 => 30,
				'showInView' => false,
				'label' 	 => 'Town'
			),
			array(
				'name'       => 'city',
				'length' 	 => 30,
				'showInView' => false,
				'label' 	 => 'City'
			),
			array(
				'name'       => 'county',
				'length' 	 => 30,
				'showInView' => false,
				'label' 	 => 'County'
			),
			array(
				'name'       => 'postcode',
				'length' 	 => 10,
				'showInView' => false,
				'label' 	 => 'Post Code'
			),
			array(
				'name'       => 'address',
				'length' 	 => 70,
				'editable'   => false,
				'bind'		 => false,
				'type'		 => 'DERIVED',
				'function'	 => 'fullAddress',
				'label' 	 => 'Address'
			),
			array(
				'name'       => 'email',
				'length' 	 => 70,
				'label' 	 => 'Email'
			),
			array(
				'name'       => 'telephone',
				'length' 	 => 12,
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
				'label' 	 => 'Contact 1'
			),			
			array(
				'name'       => 'contact2',
				'length' 	 => 15,
				'required'	 => false,
				'label' 	 => 'Contact 2'
			),			
			array(
				'name'       => 'nominalledgercode',
				'length' 	 => 15,
				'label' 	 => 'Nominal Ledger Code'
			),			
			array(
				'name'       => 'collectionpoint',
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
				'datatype'	 => 'integer',
				'label' 	 => 'Due Days'
			),			
			array(
				'name'       => 'creditlimit',
				'length' 	 => 15,
				'datatype'	 => 'double',
				'label' 	 => 'Credit Limit'
			),			
			array(
				'name'       => 'settlementdiscount',
				'length' 	 => 17,
				'datatype'	 => 'double',
				'label' 	 => 'Settlement Discount'
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
				'required'	 => trues,
				'table_id'	 => 'id',
				'alias'		 => 'accountstatusname',
				'table_name' => 'name'
			),
			array(
				'name'       => 'taxcodeid',
				'type'       => 'DATACOMBO',
				'length' 	 => 10,
				'label' 	 => 'Tax Code',
				'table'		 => 'taxcode',
				'required'	 => trues,
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
			)
		);
		
	$crud->run();
?>
