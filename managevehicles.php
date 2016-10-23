<?php
	require_once("crud.php");
	
	function disableVehicle() {
		$id = $_POST['vehicle_id'];
		
		$qry = "UPDATE {$_SESSION['DB_PREFIX']}vehicle SET active = 'N' WHERE id = $id";
		$result = mysql_query($qry);
	}
	
	function enableVehicle() {
		$id = $_POST['vehicle_id'];
		
		$qry = "UPDATE {$_SESSION['DB_PREFIX']}vehicle SET active = 'Y' WHERE id = $id";
		$result = mysql_query($qry);
	}
	
	class ContactCrud extends Crud {
		
		/* Post header event. */
		public function postHeaderEvent() {
			createConfirmDialog("disabledialog", "Disable Vehicle ?", "confirmDisableVehicle");
			createConfirmDialog("enabledialog", "Enable Vehicle ?", "confirmEnableVehicle");
		}
		
		public function postScriptEvent() {
?>
			
			function disableVehicle(id) {
				currentID = id;
				
				$("#disabledialog .confirmdialogbody").html("You are about to de-activate this vehicle.<br>Are you sure ?");
				$("#disabledialog").dialog("open");
			}
			
			function enableVehicle(id) {
				currentID = id;
				
				$("#enabledialog .confirmdialogbody").html("You are about to activate this vehicle.<br>Are you sure ?");
				$("#enabledialog").dialog("open");
			}

			function confirmDisableVehicle() {
				$("#disabledialog").dialog("close");

				post("editform", "disableVehicle", "submitframe", 
						{ 
							vehicle_id: currentID
						}
					);
			}
			
			function confirmEnableVehicle() {
				$("#enabledialog").dialog("close");
				
				post("editform", "enableVehicle", "submitframe", 
						{ 
							vehicle_id: currentID
						}
					);
			}
			
			function checkClick(node) {
				if (node.active != "Yes") {
					$("#enablebutton").show();
					$("#disablebutton").hide();
					
				} else {
					$("#disablebutton").show();
					$("#enablebutton").hide();
				}				
			}
<?php			
		}
	}

	$crud = new ContactCrud();
	$crud->title = "Vehicles";
	$crud->allowRemove = false;
	$crud->document = array(
			'primaryidname'	 => 	"vehicleid",
			'tablename'		 =>		"vehicledocs"
		);
	$crud->onClickCallback = "checkClick";
	$crud->table = "{$_SESSION['DB_PREFIX']}vehicle";
	$crud->dialogwidth = 970;
	$crud->sql = 
			"SELECT A.*, B.registration AS trailername, C.name AS drivername, D.name AS vehicletypename " .
			"FROM {$_SESSION['DB_PREFIX']}vehicle A " .
			"LEFT OUTER JOIN {$_SESSION['DB_PREFIX']}trailer B " .
			"ON B.id = A.usualtrailerid " .
			"LEFT OUTER JOIN {$_SESSION['DB_PREFIX']}driver C " .
			"ON C.id = A.usualdriverid " .
			"LEFT OUTER JOIN {$_SESSION['DB_PREFIX']}vehicletype D " .
			"ON D.id = A.vehicletypeid " .
			"ORDER BY A.registration";
	
	$crud->messages = array(
			array('id'		  => 'vehicle_id')
		);
	
	$crud->columns = array(
			array(
				'name'       => 'id',
				'length' 	 => 6,
				'pk'		 => true,
				'showInView' => false,
				'editable'	 => false,
				'bind' 	 	 => false,
				'filter'	 => false,
				'label' 	 => 'ID'
			),
			array(
				'name'       => 'registration',
				'length' 	 => 10,
				'label' 	 => 'Registration'
			),
			array(
				'name'       => 'description',
				'length' 	 => 60,
				'label' 	 => 'Description'
			),
			array(
				'name'       => 'vehicletypeid',
				'type'       => 'DATACOMBO',
				'length' 	 => 20,
				'label' 	 => 'Vehicle Type',
				'table'		 => 'vehicletype',
				'table_id'	 => 'id',
				'alias'		 => 'vehicletypename',
				'table_name' => 'name'
			),
			array(
				'name'       => 'manufacturer',
				'length' 	 => 30,
				'label' 	 => 'Manufacturer'
			),
			array(
				'name'       => 'purchasedate',
				'length' 	 => 10,
				'required'	 => false,
				'datatype'	 => 'date',
				'label' 	 => 'Purchase Date'
			),
			array(
				'name'       => 'purchaseprice',
				'datatype'	 => 'float',
				'align'		 => 'right',
				'required'	 => false,
				'length' 	 => 12,
				'label' 	 => 'Purchase Price'
			),
			array(
				'name'       => 'mpg',
				'datatype'	 => 'float',
				'required'	 => false,
				'align'		 => 'right',
				'length' 	 => 12,
				'label' 	 => 'MPG'
			),
			array(
				'name'       => 'presentprice',
				'datatype'	 => 'float',
				'align'		 => 'right',
				'required'	 => false,
				'length' 	 => 12,
				'label' 	 => 'Present Price'
			),
			array(
				'name'       => 'grossweight',
				'datatype'	 => 'float',
				'align'		 => 'right',
				'required'	 => false,
				'length' 	 => 12,
				'label' 	 => 'Gross Weight'
			),
			array(
				'name'       => 'ystachometer',
				'length' 	 => 10,
				'datatype'	 => 'integer',
				'required'	 => false,
				'align'		 => 'right',
				'label' 	 => 'YS Tachometer'
			),
			array(
				'name'       => 'capacity',
				'datatype'	 => 'float',
				'required'	 => false,
				'align'		 => 'right',
				'length' 	 => 12,
				'label' 	 => 'Capacity'
			),
			array(
				'name'       => 'maxpallets',
				'datatype'	 => 'integer',
				'align'		 => 'right',
				'required'	 => false,
				'length' 	 => 15,
				'label' 	 => 'Maximum Pallets'
			),
			array(
				'name'       => 'type',
				'length' 	 => 20,
				'label' 	 => 'VAT Applicable',
				'type'       => 'COMBO',
				'showInView' => false,
				'editable'   => false,
				'filter'     => false,
				'default'	 => 'Y',
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
			),
			array(
				'name'       => 'subcontractor',
				'length' 	 => 20,
				'label' 	 => 'Subcontractor',
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
				'name'       => 'usualdriverid',
				'type'       => 'DATACOMBO',
				'length' 	 => 30,
				'required'	 => false,
				'label' 	 => 'Usual Driver',
				'table'		 => 'driver',
				'table_id'	 => 'id',
				'alias'		 => 'drivername',
				'table_name' => 'name'
			),
			array(
				'name'       => 'usualtrailerid',
				'type'       => 'DATACOMBO',
				'required'	 => false,
				'length' 	 => 30,
				'label' 	 => 'Usual Trailer',
				'table'		 => 'trailer',
				'table_id'	 => 'id',
				'alias'		 => 'trailername',
				'table_name' => 'registration'
			),
			array(
				'name'       => 'active',
				'length' 	 => 10,
				'bind'		 => false,
				'editable'	 => false,
				'label' 	 => 'Active',
				'type'       => 'COMBO',
				'options'    => array(
						array(
							'value'		=> 'Y',
							'text'		=> 'Yes'
						),
						array(
							'value'		=> 'N',
							'text'		=> 'No'
						)
					)
			),
			array(
				'name'       => 'notes',
				'length' 	 => 50,
				'type'		 => 'TEXTAREA',
				'showInView' => false,
				'label' 	 => 'Notes'
			)
		);

	$crud->subapplications = array(
			array(
				'id'		  => 'disablebutton',
				'title'		  => 'De-activate',
				'imageurl'	  => 'images/delete.png',
				'script' 	  => 'disableVehicle'
			),
			array(
				'id'		  => 'enablebutton',
				'title'		  => 'Activate',
				'imageurl'	  => 'images/thumbs_up.gif',
				'script' 	  => 'enableVehicle'
			)
		);
				
	$crud->run();
?>
