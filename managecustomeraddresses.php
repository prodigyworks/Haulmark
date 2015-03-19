<?php
	require_once("crud.php");
	
	class AddressCrud extends Crud {
		
		public function postScriptEvent() {
?>
		/* Full name callback. */
	
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
	
	$crud = new AddressCrud();
	$crud->dialogwidth = 950;
	$crud->title = "Customers";
	$crud->table = "{$_SESSION['DB_PREFIX']}customer";
	$crud->sql = "SELECT A.* " .
				 "FROM  {$_SESSION['DB_PREFIX']}customer A " .
				 "ORDER BY A.name";
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
				'length' 	 => 40,
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
				'name'       => 'fax',
				'length' 	 => 12,
				'label' 	 => 'Fax'
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
