<?php
	require_once("crud.php");
	
	class CustomerCrud extends Crud {
		
		public function postScriptEvent() {
?>
			function rateCard(node) {
				callAjax(
						"finddata.php",
						{
							sql: "SELECT documentid " +
							"FROM <?php echo $_SESSION['DB_PREFIX'];?>customer  " +
							"WHERE id = " + node
						},
						function(data) {
							if (data.length == 1) {
								window.open("viewdocuments.php?id=" + data[0].documentid);
							}
						},
						false
					);
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
	$crud->document = array(
			'primaryidname'	 => 	"customerid",
			'tablename'		 =>		"customerdocs"
		);
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
				'length' 	 => 80,
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
				'length' 	 => 40,
				'datatype'	 => 'email',
				'label' 	 => 'Accounts Email'
			),
			array(
				'name'       => 'email2',
				'length' 	 => 40,
				'required'	 => false,
				'datatype'	 => 'email',
				'showInView' => false,
				'label' 	 => 'Email 2'
			),
			array(
				'name'       => 'telephone',
				'length' 	 => 17,
				'required'	 => false,
				'datatype'	 => 'tel',
				'label' 	 => 'Telephone'
			),
			array(
				'name'       => 'telephone2',
				'length' 	 => 12,
				'required'	 => false,
				'showInView' => false,
				'datatype'	 => 'tel',
				'label' 	 => 'Telephone 2'
			),
			array(
				'name'       => 'fax',
				'length' 	 => 12,
				'showInView' => false,
				'required'	 => false,
				'label' 	 => 'Fax'
			),
			array(
				'name'       => 'contact1',
				'length' 	 => 25,
				'required'	 => false,
				'showInView' => false,
				'label' 	 => 'Operations Contact'
			),			
			array(
				'name'       => 'contact2',
				'length' 	 => 25,
				'showInView' => false,
				'required'	 => false,
				'label' 	 => 'Invoicing Contact'
			),			
			array(
				'name'       => 'podfolder',
				'length' 	 => 40,
				'required'	 => false,
				'showInView' => false,
				'label' 	 => 'POD Folder'
			),
			array(
				'name'       => 'nominalledgercode',
				'length' 	 => 15,
				'required'	 => false,
				'showInView' => false,
				'label' 	 => 'Nominal Ledger Code'
			),			
			array(
				'name'       => 'collectionpoint',
				'required'	 => false,
				'showInView' => false,
				'length' 	 => 50,
				'type'		 => 'GEOLOCATION',
				'label' 	 => 'Collection Point'
			),			
			array(
				'name'       => 'deliverypoint',
				'length' 	 => 50,
				'type'		 => 'GEOLOCATION',
				'required'	 => false,
				'showInView' => false,
				'label' 	 => 'Delivery Point'
			),			
			array(
				'name'       => 'selfbilledinvoices',
				'length' 	 => 20,
				'required'	 => false,
				'showInView' => false,
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
				'name'       => 'mobileautoinvoice',
				'length' 	 => 20,
				'required'	 => false,
				'showInView' => false,
				'label' 	 => 'Auto Invoice (Mobile)',
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
				'showInView' => false,
				'label' 	 => 'Due Days'
			),			
			array(
				'name'       => 'creditlimit',
				'length' 	 => 15,
				'required'	 => false,
				'showInView' => false,
				'datatype'	 => 'float',
				'label' 	 => 'Credit Limit'
			),			
			array(
				'name'       => 'standardratepermile',
				'length' 	 => 17,
				'datatype'	 => 'float',
				'showInView' => false,
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
				'showInView' => false,
				'table_id'	 => 'id',
				'alias'		 => 'accountstatusname',
				'table_name' => 'name'
			),
			array(
				'name'       => 'sagecustomerref',
				'required'	 => false,
				'showInView' => false,
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
				'showInView' => false,
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
			),
			array(
				'name'		 => 'documentid',
				'type'		 => 'FILE',
				'label'		 => 'Rate Card',
				'length'	 => 100,
				'required'	 => false,
				'showInView' => false
			),
			array(
				'name'       => 'imageid',
				'type'		 => 'IMAGE',
				'length' 	 => 64,
				'required'	 => false,
				'showInView' => false,
				'filter'	 => false,
				'label' 	 => 'Logo'
			)
		);

	$crud->subapplications = array(
			array(
				'title'		  => 'Contacts',
				'imageurl'	  => 'images/user.png',
				'application' => 'managecustomercontacts.php'
			),
			array(
				'title'		  => 'POD',
				'imageurl'	  => 'images/document.gif',
				'application' => 'managecustomerpod.php'
			),
			array(
				'title'		  => 'Invoices',
				'imageurl'	  => 'images/document.gif',
				'application' => 'invoices.php'
			),
			array(
				'title'		  => 'Rate Card',
				'imageurl'	  => 'images/document.gif',
				'script' 	  => 'rateCard'
			)
		);
		
	$crud->run();
?>
