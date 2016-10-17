<?php
	require_once("crud.php");
	
	$crud = new Crud();
	$crud->title = "Trailers";
	$crud->table = "{$_SESSION['DB_PREFIX']}trailer";
	$crud->dialogwidth = 950;
	$crud->document = array(
			'primaryidname'	 => 	"trailerid",
			'tablename'		 =>		"trailerdocs"
		);
	$crud->sql = 
			"SELECT A.*, C.name AS drivername, D.name AS trailertypename " .
			"FROM {$_SESSION['DB_PREFIX']}trailer A " .
			"LEFT OUTER JOIN {$_SESSION['DB_PREFIX']}driver C " .
			"ON C.id = A.usualdriverid " .
			"LEFT OUTER JOIN {$_SESSION['DB_PREFIX']}trailertype D " .
			"ON D.id = A.trailertypeid " .
	"ORDER BY A.registration";
	
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
				'name'       => 'trailertypeid',
				'type'       => 'DATACOMBO',
				'length' 	 => 20,
				'label' 	 => 'Trailer Type',
				'table'		 => 'trailertype',
				'table_id'	 => 'id',
				'alias'		 => 'trailertypename',
				'table_name' => 'name'
			),
			array(
				'name'       => 'manufacturer',
				'length' 	 => 30,
				'required'	 => false,
				'label' 	 => 'Manufacturer'
			),
			array(
				'name'       => 'purchasedate',
				'length' 	 => 10,
				'datatype'	 => 'date',
				'required'	 => false,
				'label' 	 => 'Purchase Date'
			),
			array(
				'name'       => 'purchaseprice',
				'datatype'	 => 'float',
				'required'	 => false,
				'align'		 => 'right',
				'length' 	 => 12,
				'label' 	 => 'Purchase Price'
			),
			array(
				'name'       => 'mpg',
				'datatype'	 => 'float',
				'align'		 => 'right',
				'required'	 => false,
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
				'align'		 => 'right',
				'required'	 => false,
				'label' 	 => 'YS Tachometer'
			),
			array(
				'name'       => 'capacity',
				'datatype'	 => 'float',
				'align'		 => 'right',
				'required'	 => false,
				'length' 	 => 12,
				'label' 	 => 'Capacity'
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
				'name'       => 'usualdriverid',
				'type'       => 'DATACOMBO',
				'required'	 => false,
				'length' 	 => 30,
				'label' 	 => 'Usual Driver',
				'table'		 => 'driver',
				'table_id'	 => 'id',
				'alias'		 => 'drivername',
				'table_name' => 'name'
			),
			array(
				'name'       => 'notes',
				'length' 	 => 50,
				'type'		 => 'TEXTAREA',
				'showInView' => false,
				'label' 	 => 'Notes'
			)
		);
				
	$crud->run();
?>
