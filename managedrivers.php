<?php
	require_once("crud.php");
	
	class AddressCrud extends Crud {
		
		public function postScriptEvent() {
?>
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
				
				if ((node.addressextra) != "") {
					if (address != "") {
						address = address + ", ";
					}
					
					address = address + node.addressextra;
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
	
	$crud = new AddressCrud();
	$crud->dialogwidth = 980;
	$crud->title = "Drivers";
	$crud->document = array(
			'primaryidname'	 => 	"driverid",
			'tablename'		 =>		"driverdocs"
		);
	$crud->table = "{$_SESSION['DB_PREFIX']}driver";
	$crud->sql = "SELECT A.*, D.registration AS vehiclename, E.registration AS trailername 
				  FROM  {$_SESSION['DB_PREFIX']}driver A 
				  LEFT OUTER JOIN  {$_SESSION['DB_PREFIX']}vehicle D
				  ON D.id = A.usualvehicleid
				  LEFT OUTER JOIN  {$_SESSION['DB_PREFIX']}trailer E
				  ON E.id = A.usualtrailerid
				  ORDER BY A.code";
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
				'name'       => 'code',
				'length' 	 => 20,
				'label' 	 => 'Code'
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
				'name'       => 'addressextra',
				'length' 	 => 30,
				'showInView' => false,
				'label' 	 => 'Additional Address Line'
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
				'datatype'	 => 'email',
				'required'	 => false,
				'label' 	 => 'Email'
			),
			array(
				'name'       => 'telephone',
				'length' 	 => 12,
				'datatype'	 => 'tel',
				'label' 	 => 'Home Telephone'
			),
			array(
				'name'       => 'fax',
				'length' 	 => 12,
				'datatype'	 => 'tel',
				'required'	 => false,
				'label' 	 => 'Mobile'
			),
			array(
				'name'       => 'usualvehicleid',
				'type'       => 'DATACOMBO',
				'length' 	 => 10,
				'label' 	 => 'Usual Vehicle',
				'table'		 => 'vehicle',
				'required'	 => false,
				'table_id'	 => 'id',
				'alias'		 => 'vehiclename',
				'table_name' => 'registration'
			),
			array(
				'name'       => 'usualtrailerid',
				'type'       => 'DATACOMBO',
				'length' 	 => 10,
				'label' 	 => 'Usual Trailer',
				'table'		 => 'trailer',
				'required'	 => false,
				'table_id'	 => 'id',
				'alias'		 => 'trailername',
				'table_name' => 'registration'
			),
			array(
				'name'       => 'startdate',
				'datatype'	 => 'date',
				'length' 	 => 12,
				'required'	 => false,
				'label' 	 => 'Start Date'
			),
			array(
				'name'       => 'qualifications',
				'length' 	 => 50,
				'type'		 => 'TEXTAREA',
				'showInView' => false,
				'label' 	 => 'Qualifications / Restrictions'
			),
			array(
				'name'       => 'hazardousqualifications',
				'length' 	 => 20,
				'label' 	 => 'Hazardous Qualifications',
				'type'       => 'COMBO',
				'options'    => array(
						array(
							'value'		=> "Y",
							'text'		=> "Yes"
						),
						array(
							'value'		=> "N",
							'text'		=> "No"
						)
					)
			),
			array(
				'name'       => 'agencydriver',
				'length' 	 => 20,
				'label' 	 => 'Agency Driver',
				'type'       => 'COMBO',
				'options'    => array(
						array(
							'value'		=> "Y",
							'text'		=> "Yes"
						),
						array(
							'value'		=> "N",
							'text'		=> "No"
						)
					)
			),
			array(
				'name'       => 'hgvlicenceexpire',
				'length' 	 => 12,
				'datatype'	 => 'date',
				'label' 	 => 'HGV Licence Expires'
			),
			array(
				'name'       => 'type',
				'length' 	 => 20,
				'label' 	 => 'Type',
				'type'       => 'COMBO',
				'options'    => array(
						array(
							'value'		=> "N",
							'text'		=> "Normal"
						),
						array(
							'value'		=> "S",
							'text'		=> "Special"
						)
					)
			)
		);
		
	$crud->run();
?>
