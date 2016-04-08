<?php
	require_once("crud.php");
	
	class ProductCrud extends Crud {
		
		/* Post header event. */
		public function postHeaderEvent() {
			createDocumentLink();
		}
		
		public function postEditScriptEvent() {
?>
			$("#groupcode").css("font-style", "");
			$("#groupcode").css("color", "");
			$("#productcode").css("font-style", "");
			$("#productcode").css("color", "");
<?php
		}
		
		public function postAddScriptEvent() {
?>
			$("#groupcode").val("Auto Generated");
			$("#groupcode").css("font-style", "italic");
			$("#groupcode").css("color", "#888888");
			$("#productcode").val("Auto Generated");
			$("#productcode").css("font-style", "italic");
			$("#productcode").css("color", "#888888");
<?php
		}		
		
		public function postInsertEvent() {
			$id = mysql_insert_id();
			$groupcode = getSiteConfigData()->productgroupprefix . $id;
			$productcode = getSiteConfigData()->productcodeprefix . $id;
			$sql = "UPDATE {$_SESSION['DB_PREFIX']}product SET 
					groupcode = '$groupcode', 
					productcode = '$productcode'
					WHERE id = $id";
			
			if (! mysql_query($sql)) {
				logError($sql . " - " . mysql_error(), false);
			}
		}
		
		public function postScriptEvent() {
?>
			function editDocuments(node) {
				viewDocument(node, "addproductdocument.php", node, "productdocs", "productid");
			}
	
<?php			
		}
	}
	
	$crud = new ProductCrud();
	$crud->dialogwidth = 800;
	$crud->title = "Products";
	$crud->table = "{$_SESSION['DB_PREFIX']}product";
	$crud->sql = "SELECT A.*, B.name AS suppliername
				  FROM  {$_SESSION['DB_PREFIX']}product A
				  LEFT OUTER JOIN {$_SESSION['DB_PREFIX']}supplier B
				  ON B.id = A.supplierid
				  ORDER BY A.productcode";
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
				'name'       => 'groupcode',
				'length' 	 => 20,
				'readonly'	 => true,
				'label' 	 => 'Group Code'
			),			
			array(
				'name'       => 'productcode',
				'readonly'	 => true,
				'length' 	 => 20,
				'label' 	 => 'Product Code'
			),
			array(
				'name'       => 'description',
				'length' 	 => 90,
				'label' 	 => 'Description'
			),			
			array(
				'name'       => 'imageid',
				'type'		 => 'IMAGE',
				'required'   => false,
				'length' 	 => 35,
				'showInView' => false,
				'label' 	 => 'Image'
			),			
			array(
				'name'       => 'supplierid',
				'type'       => 'DATACOMBO',
				'length' 	 => 14,
				'required'	 => false,
				'label' 	 => 'Supplier',
				'table'		 => 'supplier',
				'table_id'	 => 'id',
				'alias'		 => 'suppliername',
				'table_name' => 'name'
			),
			array(
				'name'       => 'mainsupplierpartnumber',
				'required'   => false,
				'length' 	 => 20,
				'label' 	 => 'Main Supplier Part Number'
			),			
			array(
				'name'       => 'estimatedcost',
				'length' 	 => 12,
				'datatype'   => 'double',
				'required'   => false,
				'align'		 => 'right',
				'label' 	 => 'Estimated Cost'
			),			
			array(
				'name'       => 'rspnet',
				'length' 	 => 12,
				'datatype'   => 'double',
				'align'		 => 'right',
				'label' 	 => 'RSP net GBP / &pound;'
			),
			array(
				'name'       => 'weight',
				'length' 	 => 12,
				'required'   => false,
				'datatype'   => 'double',
				'align'		 => 'right',
				'label' 	 => 'Weight'
			)
		);

	$crud->subapplications = array(
			array(
				'title'		  => 'Documents',
				'imageurl'	  => 'images/document.gif',
				'script' 	  => 'editDocuments'
			),
			array(
				'title'		  => 'Price Breaks',
				'imageurl'	  => 'images/document.gif',
				'application' => 'managepricebreaks.php'
			)
		);
		
	$crud->run();
?>
