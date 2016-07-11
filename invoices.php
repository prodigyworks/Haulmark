<?php
	require_once("crud.php");
	require_once("invoiceemail.php");
	
	function emailInvoice() {
		try {
			invoiceEmail($_POST['invoiceid']);
							
		} catch (Exception $e) {
			throw $e;
		}
	}

	class InvoiceCrud extends Crud {

		/* Post header event. */
		public function postHeaderEvent() {
			createConfirmDialog("confirmRemoveDialog", "Confirm removal ?", "confirmRemoval");
			createDocumentLink();
		}

		public function afterInsertRow() {
			?>
			var status = rowData['status'];

			if (status == "1") {
				$(this).jqGrid('setRowData', rowid, false, { color: '#0000FF' });
			}
			<?php
		}

		public function postUpdateEvent($invoiceid) {
			$items = json_decode($_POST['item_serial'], true);
			$memberid = getLoggedOnMemberID();

			$qry = "DELETE FROM {$_SESSION['DB_PREFIX']}invoiceitem
						WHERE invoiceid = $invoiceid";

			$result = mysql_query($qry);

			if (! $result) {
				logError($qry . " - " . mysql_error());
			}

			foreach ($items as $k=>$item) {
				$qty = $item['quantity'];
				$vatrate = $item['vatrate'];
				$linetotal = $item['linetotal'];
				$vat = $item['vat'];
				$unitprice = $item['priceeach'];
				$description = mysql_escape_string($item['description']);
				$productid = $item['productid'];

				$qry = "INSERT INTO {$_SESSION['DB_PREFIX']}invoiceitem
							(invoiceid, description, quantity, priceeach, vatrate, vat, linetotal,
							productid, metacreateddate, metacreateduserid, metamodifieddate, metamodifieduserid)
							VALUES
							($invoiceid, '$description', '$qty', '$unitprice', $vatrate, '$vat', $linetotal,
							'$productid', NOW(), $memberid , NOW(), $memberid)";

				$result = mysql_query($qry);

				if (! $result) {
					logError($qry . " - " . mysql_error());
				}
			
				$qry = "UPDATE {$_SESSION['DB_PREFIX']}booking SET
						statusid = 8
						WHERE id = $productid";

				$result = mysql_query($qry);

				if (! $result) {
					logError($qry . " - " . mysql_error());
				}
			}
		}

		public function postInsertEvent() {
			$invoiceid = mysql_insert_id();
			$items = json_decode($_POST['item_serial'], true);
			$memberid = getLoggedOnMemberID();

			foreach ($items as $k=>$item) {
				$qty = $item['quantity'];
				$vatrate = $item['vatrate'];
				$linetotal = $item['linetotal'];
				$vat = $item['vat'];
				$unitprice = $item['priceeach'];
				$productid = $item['productid'];
				$description = mysql_escape_string($item['description']);

				$qry = "INSERT INTO {$_SESSION['DB_PREFIX']}invoiceitem
							(invoiceid, quantity, priceeach, vatrate, vat, linetotal,
							productid, description, metacreateddate, metacreateduserid, metamodifieddate, metamodifieduserid)
							VALUES
							($invoiceid, '$qty', '$unitprice', $vatrate, '$vat', $linetotal,
							'$productid', '$description', NOW(), $memberid , NOW(), $memberid)";

				$result = mysql_query($qry);

				if (! $result) {
					logError($qry . " - " . mysql_error());
				}

				$qry = "UPDATE {$_SESSION['DB_PREFIX']}booking SET
						statusid = 8
						WHERE id = $productid";

				$result = mysql_query($qry);

				if (! $result) {
					logError($qry . " - " . mysql_error());
				}
			}

		}

		public function postAddScriptEvent() {
			?>
			$("#revision").val("1");
			$("#discount").val("0.00");
			$("#total").val("0.00");
			$("#orderdate").val("<?php echo date("d/m/Y"); ?>");
			$("#takenbyid").val("<?php echo getLoggedOnMemberID(); ?>");
			itemArray = [];

			populateTable();
<?php
		}

		public function postEditScriptEvent() {
?>
			$("#revision").val(parseInt($("#revision").val()) + 1);

			callAjax(
					"finddata.php",
					{
						sql: "SELECT A.* FROM <?php echo $_SESSION['DB_PREFIX'];?>customer A WHERE A.id = " + $("#customerid").val()
					},
					function(data) {
						if (data.length > 0) {
							var node = data[0];
							var invoiceaddress = "";
							var deliveryaddress = "";

							if (node.street != "" && node.city != null) deliveryaddress += node.street + "\n";
							if (node.address2 != "" && node.address2 != null) deliveryaddress += node.address2+ "\n";
							if (node.town != "" && node.town != null) deliveryaddress += node.town+ "\n";
							if (node.city != "" && node.city != null) deliveryaddress += node.city+ "\n";
							if (node.county != "" && node.county != null) deliveryaddress += node.county+ "\n";
							if (node.postcode != "" && node.postcode != null) deliveryaddress += node.postcode+ "\n";

							invoiceaddress = deliveryaddress;

							$("#accountcode").val(node.accountcode);
							$("#invoiceaddress").val(invoiceaddress);
							$("#deliveryaddress").val(deliveryaddress);
						}
					},
					false
			);

			callAjax(
					"finddata.php",
					{
						sql: "SELECT A.*, B.id AS proddesc, B.legsummary AS description " + 
							 "FROM <?php echo $_SESSION['DB_PREFIX'];?>invoiceitem A  " +
							 "INNER JOIN <?php echo $_SESSION['DB_PREFIX'];?>booking B  " +
							 "ON B.id = A.productid  " +
							 "WHERE A.invoiceid = " + currentCrudID + " " +
							 "ORDER BY B.id"
					},
					function(data) {
						itemArray = data;

						populateTable(data);
					},
					false
				);
			<?php
		}

		public function editScreenSetup() {
			include("invoiceform.php");
		}

		public function postScriptEvent() {
			?>
			var currentID = 0;
			var currentItem = -1;
			var itemArray = [];

			function customerid_onchange() {
				callAjax(
						"finddata.php",
						{
							sql: "SELECT A.* FROM <?php echo $_SESSION['DB_PREFIX'];?>customer A WHERE A.id = " + $("#customerid").val()
						},
						function(data) {
							if (data.length > 0) {
								var node = data[0];
								var invoiceaddress = "";
								var deliveryaddress = "";

								if (node.street != "" && node.city != null) deliveryaddress += node.street + "\n";
								if (node.address2 != "" && node.address2 != null) deliveryaddress += node.address2+ "\n";
								if (node.town != "" && node.town != null) deliveryaddress += node.town+ "\n";
								if (node.city != "" && node.city != null) deliveryaddress += node.city+ "\n";
								if (node.county != "" && node.county != null) deliveryaddress += node.county+ "\n";
								if (node.postcode != "" && node.postcode != null) deliveryaddress += node.postcode+ "\n";

								invoiceaddress = deliveryaddress;

								$("#accountcode").val(node.accountcode);
								$("#invoiceaddress").val(invoiceaddress);
								$("#deliveryaddress").val(deliveryaddress);
								
								reloadBookingCombo();
							}
						},
						false
				);
			}
			
			function reloadBookingCombo() {
				$.ajax({
						url: "createbookingcombo.php",
						dataType: 'html',
						async: false,
						data: { 
							customerid: $("#customerid").val()
						},
						type: "POST",
						error: function(jqXHR, textStatus, errorThrown) {
							pwAlert("ERROR :" + errorThrown);
						},
						success: function(data) {
							$("#item_productid").html(data);
						}
					});
			}

			function total_onchange() {
				calculate_total();
			}

			function calculate_total() {
				var total;
				var discount;

				discount = parseFloat($("#discount").val());

				total = parseFloat($("#total").val());

				if (total < 0) {
					total = 0;
				}

				total -= (total * (discount) / 100);

				$("#discount").val(new Number(discount).toFixed(2));
				$("#total").val(new Number(total).toFixed(2));
			}

			function productid_onchange() {
				callAjax(
						"finddata.php",
						{
							sql: "SELECT id, charge, legsummary " +
								 "FROM <?php echo $_SESSION['DB_PREFIX'];?>booking A " +
								 "WHERE A.id = " + $("#item_productid").val()
						},
						function(data) {
							var i;

							for (i = 0; i < data.length; i++) {
								var node = data[i];

								$("#item_productdesc").val(padZero(node.id, 6));
								$("#item_description").val(node.legsummary);
								$("#item_unitprice").val(node.charge);
								$("#item_quantity").val("1");

								qty_onchange();
							}
						}
				);
			}

			function qty_onchange(node) {
				var qty = parseInt($("#item_quantity").val());
				var unitprice = parseFloat($("#item_unitprice").val());
				var vatrate = parseFloat($("#item_vatrate").val());

				if (isNaN(unitprice)) {
					unitprice = 0;
				}

				if (isNaN(vatrate)) {
					vatrate = 0;
				}

				if (isNaN(qty)) {
					qty = 0;
				}

				var total = parseFloat(qty * unitprice);
				var vat = total * (vatrate / 100);

				total += vat;

				$("#item_vatrate").val(new Number(vatrate).toFixed(2));
				$("#item_vat").val(new Number(vat).toFixed(2));
				$("#item_unitprice").val(new Number(unitprice).toFixed(2));
				$("#item_quantity").val(new Number(qty).toFixed(0));
				$("#item_linetotal").val(new Number(total).toFixed(2));
			}
			
			function emailInvoice(id) {
				post("editform", "emailInvoice", "submitframe", 
						{ 
							invoiceid: id 
						}
					);
					
				pwAlert("Sending email to customer");
			}

			function printInvoice(id) {
				window.open("invoicereport.php?id=" + id);
			}

			function populateTable(data) {
				var total = 0;
				var html = "<TABLE width='100%' class='grid list'><THEAD><?php createHeader(); ?></THEAD>";

				$("#item_serial").val(JSON.stringify(data));

				if (data != null) {
					for (var i = 0; i < data.length; i++) {
						var node = data[i];

						if (node.description != null) {
							html += "<TR>";
								html += "<TD><img src='images/edit.png'  title='Edit item' onclick='editItem(" + i + ")' />&nbsp;<img src='images/delete.png'  title='Remove item' onclick='removeItem(" + i + ")' /></TD>";
								html += "<TD><?php echo getSiteConfigData()->bookingprefix; ?>" + padZero(node.proddesc, 6) + "</TD>";
								html += "<TD align=left>" + node.description + "</TD>";
								html += "<TD align=right>" + new Number(node.linetotal).toFixed(2) + "</TD>";
								html += "</TR>\n";

							total += parseFloat(node.linetotal);
						}
					}
				}

				$("#total").val(new Number(total).toFixed(2));

				calculate_total();

				html = html + "</TABLE>";

				$("#divtable").html(html);
			}

			function saveInvoiceItem() {
				if (! verifyStandardForm("#invoiceitemform")) {
					pwAlert("Invalid form");
					return false;
				}

				var item = {
						id: $("#item_id").val(),
						quantity: $("#item_quantity").val(),
						priceeach: $("#item_unitprice").val(),
						vatrate: $("#item_vatrate").val(),
						vat: $("#item_vat").val(),
						linetotal: $("#item_linetotal").val(),
						productid: $("#item_productid").val(),
						proddesc: $("#item_productdesc").val(),
						description: $("#item_description").val()
					};

				if (currentItem == -1) {
					itemArray.push(item);

				} else {
					itemArray[currentItem] = item;
				}

				populateTable(itemArray);

				return true;
			}

			function removeItem(id) {
				currentItem = id;

				$("#confirmRemoveDialog .confirmdialogbody").html("You are about to approve this item.<br>Are you sure ?");
				$("#confirmRemoveDialog").dialog("open");
			}

			function confirmRemoval() {
				var newItemArray = [];
				var i;

				$("#confirmRemoveDialog").dialog("close");

				for (i = 0; i < itemArray.length; i++) {
					if (currentItem != i) {
						newItemArray.push(itemArray[i]);
					}
				}

				itemArray = newItemArray;

				populateTable(itemArray);
			}

			function editItem(id) {
				currentItem = id;
				var node = itemArray[id];

				$("#item_itemid").val(node.id);
				$("#item_productid").val(node.productid).trigger("change");
				$("#item_description").val(node.description);
				$("#item_productdesc").val(node.proddesc);
				$("#item_quantity").val(node.quantity);
				$("#item_vat").val(node.vat);
				$("#item_vatrate").val(node.vatrate);
				$("#item_unitprice").val(node.priceeach);
				$("#item_linetotal").val(node.linetotal);

				$('#invoiceitemdialog').dialog('open');
			}

			function addInvoiceItem() {
				currentItem = -1;
				
				reloadBookingCombo();

				$("#item_itemid").val("0");
				$("#item_productid").val("0");
				$("#item_productdesc").val("");
				$("#item_description").val("");
				$("#item_quantity").val("1");
				$("#item_vatrate").val("<?php echo getSiteConfigData()->vatrate; ?>");
				$("#item_vat").val("0.00");
				$("#item_unitprice").val("0.00");
				$("#item_linetotal").val("0.00");

				$('#invoiceitemdialog').dialog('open');

			}

			function validateForm() {
				return true;
			}

			$(document).ready(
					function() {
						$("#item_productid").change(productid_onchange);
						$("#customerid").change(customerid_onchange);

						$("#invoiceitemdialog").dialog({
								modal: true,
								autoOpen: false,
								show:"fade",
								closeOnEscape: true,
								width: 690,
								hide:"fade",
								title:"Invoice Item",
								open: function(event, ui){

								},
								buttons: {
									"Save": function() {
										if (saveInvoiceItem()) {
											$(this).dialog("close");

										}
									},
									Cancel: function() {
										$(this).dialog("close");
									}
								}
							});
					}
				);


			function bookingReference(node) {
				return "<?php echo "INV-"; ?>" + padZero(node.id, 6);
			}


			function checkStatus(node) {
			}

			function editDocuments(node) {
				viewDocument(node, "addinvoicedocument.php", node, "invoicedocs", "invoiceid");
			}

			function exportInvoices() {
				window.open("exportinvoices.php");
				
				refresh();
			}

			<?php
		}
	}
	
	$where = "";
	
	$crud = new InvoiceCrud();
	
	if (isset($_GET['id'])) {
		$where = "WHERE B.id = " . $_GET['id'];
	}
	
	if (isUserInRole("CUSTOMER")) {
		$where = "WHERE B.id = " . getLoggedOnCustomerID();
	}
		
	$crud->allowRemove = ! isUserInRole("CUSTOMER");
	$crud->allowAdd = ! isUserInRole("CUSTOMER");
	$crud->allowView = ! isUserInRole("CUSTOMER");
	$crud->allowEdit = ! isUserInRole("CUSTOMER");
	$crud->dialogwidth = 840;
	$crud->title = "Invoices";
	$crud->onClickCallback = "checkStatus";
	$crud->table = "{$_SESSION['DB_PREFIX']}invoice";
	$crud->sql = "SELECT A.*, B.name AS customername, C.fullname AS takenbyname
				  FROM  {$_SESSION['DB_PREFIX']}invoice A
				  INNER JOIN  {$_SESSION['DB_PREFIX']}customer B
				  ON B.id = A.customerid
				  INNER JOIN  {$_SESSION['DB_PREFIX']}members C
				  ON C.member_id = A.takenbyid
				  $where
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
			'pk'		 => true,
			'label' 	 => 'ID'
		),
		array(
			'name'       => 'bookingref',
			'function'   => 'bookingReference',
			'sortcolumn' => 'A.id',
			'type'		 => 'DERIVED',
			'length' 	 => 17,
			'editable'	 => false,
			'bind' 	 	 => false,
			'filter'	 => false,
			'label' 	 => 'Invoice Number'
		),
		array(
			'name'       => 'customerid',
			'type'       => 'DATACOMBO',
			'length' 	 => 60,
			'label' 	 => 'Customer',
			'table'		 => 'customer',
			'required'	 => true,
			'table_id'	 => 'id',
			'alias'		 => 'customername',
			'table_name' => 'name'
		),
		array(
			'name'       => 'revision',
			'length' 	 => 10,
			'readonly'	 => true,
			'role'		 => 
				array(
					'ADMIN', 
					'ALLEGRO'
				),
			'label' 	 => 'Revision'
		),
		array(
			'name'       => 'orderdate',
			'length' 	 => 12,
			'datatype'   => 'date',
			'label' 	 => 'Invoice Date'
		),
		array(
			'name'       => 'yourordernumber',
			'length' 	 => 20,
			'label' 	 => 'Your Order Number'
		),
		array(
			'name'       => 'exported',
			'length' 	 => 10,
			'role'		 => 
				array(
					'ADMIN', 
					'ALLEGRO'
				),
			'label' 	 => 'Exported',
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
			'name'       => 'emailed',
			'length' 	 => 10,
			'label' 	 => 'Emailed',
			'type'       => 'COMBO',
			'role'		 => 
				array(
					'ADMIN', 
					'ALLEGRO'
				),
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
			'name'       => 'emaileddate',
			'datatype'	 => 'datetime',
			'length' 	 => 18,
			'role'		 => 
				array(
					'ADMIN', 
					'ALLEGRO'
				),
			'label' 	 => 'Emailed Date'
		),
		array(
			'name'       => 'emailfailedreason',
			'length' 	 => 58,
			'role'		 => 
				array(
					'ADMIN', 
					'ALLEGRO'
				),
			'label' 	 => 'Emailed Failure Reason'
		),
		array(
			'name'       => 'total',
			'length' 	 => 12,
			'align'		 => 'right',
			'label' 	 => 'Total'
		),
		array(
			'name'       => 'takenbyid',
			'type'       => 'DATACOMBO',
			'length' 	 => 18,
			'label' 	 => 'Taken By',
			'table'		 => 'members',
			'role'		 => 
				array(
					'ADMIN', 
					'ALLEGRO'
				),
			'required'	 => true,
			'table_id'	 => 'member_id',
			'alias'		 => 'takenbyname',
			'table_name' => 'fullname'
		)
	);

	if (! isUserInRole("CUSTOMER")) {
		$crud->subapplications = array(
			array(
				'title'		  => 'Documents',
				'imageurl'	  => 'images/document.gif',
				'script' 	  => 'editDocuments'
			),
			array(
				'title'		  => 'Print',
				'imageurl'	  => 'images/print.png',
				'script' 	  => 'printInvoice'
			),
			array(
				'title'		  => 'Email',
				'imageurl'	  => 'images/email.gif',
				'script' 	  => 'emailInvoice'
			)
		);
		
		$crud->applications = array(
				array(
					'title'		  => 'Export',
					'imageurl'	  => 'images/document.gif',
					'script' 	  => 'exportInvoices'
				)
			);
		
	} else {
		$crud->subapplications = array(
			array(
				'title'		  => 'Print',
				'imageurl'	  => 'images/print.png',
				'script' 	  => 'printInvoice'
			)
		);
	}

	$crud->messages = array(
		array('id'		  => 'invoiceid')
	);

	$crud->run();
?>
