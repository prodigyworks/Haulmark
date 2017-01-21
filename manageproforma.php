<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);
	require_once("crud.php");
	
	function acceptProForma() {
		$id = $_POST['proformaid'];
		$sql = "UPDATE {$_SESSION['DB_PREFIX']}proforma SET
				status = 1,
				converteddatetime = NOW()
				WHERE id = $id";
		$result = mysql_query($sql);
			
		if (! $result) {
			logError($sql . " - " . mysql_error());
		}
	}
	
	function undo() {
		$id = $_POST['proformaid'];
		$sql = "UPDATE {$_SESSION['DB_PREFIX']}proforma SET
				status = 0,
				converteddatetime = null
				WHERE id = $id";
		$result = mysql_query($sql);
			
		if (! $result) {
			logError($sql . " - " . mysql_error());
		}
	}
	
	class ProFormaCrud extends Crud {
		
		/* Post header event. */
		public function postHeaderEvent() {
			createConfirmDialog("confirmacceptdialog", "Confirm acceptance ?", "confirmaccept");
			createConfirmDialog("confirmundodialog", "Confirm undo ?", "confirmundo");
			createConfirmDialog("confirmRemoveDialog", "Confirm removal ?", "confirmRemoval");
		}
		
		public function afterInsertRow() {
?>
			var status = rowData['status'];

			if (status == "1") {
				$(this).jqGrid('setRowData', rowid, false, { color: '#0000FF' });
		   	}
<?php
		}
		
		public function postUpdateEvent($proformaid) {
			$items = json_decode($_POST['item_serial'], true);
			$memberid = getLoggedOnMemberID();
			
			$qry = "DELETE FROM {$_SESSION['DB_PREFIX']}proformaitem 
					WHERE proformaid = $proformaid";
			
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
				
				$qry = "INSERT INTO {$_SESSION['DB_PREFIX']}proformaitem 
						(proformaid, description, quantity, priceeach, vatrate, vat, linetotal, 
						productid, metacreateddate, metacreateduserid, metamodifieddate, metamodifieduserid) 
						VALUES 
						($proformaid, '$description', '$qty', '$unitprice', $vatrate, '$vat', $linetotal, 
						'$productid', NOW(), $memberid , NOW(), $memberid)";
				
				$result = mysql_query($qry);
				
				if (! $result) {
					logError($qry . " - " . mysql_error());
				}
			}
		}
		
		public function postInsertEvent($proformaid) {
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
				
				$qry = "INSERT INTO {$_SESSION['DB_PREFIX']}proformaitem 
						(proformaid, quantity, priceeach, vatrate, vat, linetotal, 
						productid, description, metacreateddate, metacreateduserid, metamodifieddate, metamodifieduserid) 
						VALUES 
						($proformaid, '$qty', '$unitprice', $vatrate, '$vat', $linetotal, 
						'$productid', '$description', NOW(), $memberid , NOW(), $memberid)";
				
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
							sql: "SELECT A.*, B.discount " + 
								 "FROM <?php echo $_SESSION['DB_PREFIX'];?>supplier A " + 
								 "LEFT OUTER JOIN <?php echo $_SESSION['DB_PREFIX'];?>discountband B " + 
								 "ON B.id = A.discountbandid " + 
								 "WHERE A.id = " + $("#supplierid").val()
						},
						function(data) {
							if (data.length > 0) {
								var node = data[0];
								var invoiceaddress = "";
								var deliveryaddress = "";
								
								if (node.deliveryaddress1 != "") deliveryaddress += node.deliveryaddress1+ "\n";
								if (node.deliveryaddress2!= "") deliveryaddress += node.deliveryaddress2+ "\n";
								if (node.deliveryaddress3!= "") deliveryaddress += node.deliveryaddress3+ "\n";
								if (node.deliverycity!= "") deliveryaddress += node.deliverycity+ "\n";
								if (node.deliverypostcode!= "") deliveryaddress += node.deliverypostcode+ "\n";
								
								if (node.invoiceaddress1!= "") invoiceaddress += node.invoiceaddress1+ "\n";
								if (node.invoiceaddress2!= "") invoiceaddress += node.invoiceaddress2+ "\n";
								if (node.invoiceaddress3!= "") invoiceaddress += node.invoiceaddress3+ "\n";
								if (node.invoicecity!= "") invoiceaddress += node.invoicecity+ "\n";
								if (node.invoicepostcode!= "") invoiceaddress += node.invoicepostcode+ "\n";
								
								if (deliveryaddress == "") {
									deliveryaddress = invoiceaddress;
								}
								
								$("#accountcode").val(node.accountnumber);
								$("#invoiceaddress").val(invoiceaddress);
								$("#deliveryaddress").val(deliveryaddress);
							}
						},
						false
					);
			
			callAjax(
					"finddata.php", 
					{ 
						sql: "SELECT A.* " + 
							 "FROM <?php echo $_SESSION['DB_PREFIX'];?>proformaitem A " + 
							 "WHERE A.proformaid = " + currentCrudID + " " + 
							 "ORDER BY A.id"
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
			include("quoteform.php");
		}
		
		public function postScriptEvent() {
?>
			var currentID = 0;
			var currentItem = -1;
			var itemArray = [];
			
			function supplierid_onchange() {
				callAjax(
						"finddata.php", 
						{ 
							sql: "SELECT A.*, B.discount FROM <?php echo $_SESSION['DB_PREFIX'];?>supplier A LEFT OUTER JOIN <?php echo $_SESSION['DB_PREFIX'];?>discountband B ON B.id = A.discountbandid WHERE A.id = " + $("#supplierid").val()
						},
						function(data) {
							if (data.length > 0) {
								var node = data[0];
								var invoiceaddress = "";
								var deliveryaddress = "";
								
								if (node.deliveryaddress1 != "") deliveryaddress += node.deliveryaddress1+ "\n";
								if (node.deliveryaddress2!= "") deliveryaddress += node.deliveryaddress2+ "\n";
								if (node.deliveryaddress3!= "") deliveryaddress += node.deliveryaddress3+ "\n";
								if (node.deliverycity!= "") deliveryaddress += node.deliverycity+ "\n";
								if (node.deliverypostcode!= "") deliveryaddress += node.deliverypostcode+ "\n";
								
								if (node.invoiceaddress1!= "") invoiceaddress += node.invoiceaddress1+ "\n";
								if (node.invoiceaddress2!= "") invoiceaddress += node.invoiceaddress2+ "\n";
								if (node.invoiceaddress3!= "") invoiceaddress += node.invoiceaddress3+ "\n";
								if (node.invoicecity!= "") invoiceaddress += node.invoicecity+ "\n";
								if (node.invoicepostcode!= "") invoiceaddress += node.invoicepostcode+ "\n";
								
								if (deliveryaddress == "") {
									deliveryaddress = invoiceaddress;
								}
								
								$("#accountcode").val(node.accountnumber);
								$("#invoiceaddress").val(invoiceaddress);
								$("#deliveryaddress").val(deliveryaddress);
								$("#discount").val(new Number(node.discount).toFixed(2));
							}
						},
						false
					);
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
							sql: "SELECT A.rspnet, A.productcode, A.description, B.priceeach, B.qtyfrom, B.qtyto " +
								 "FROM <?php echo $_SESSION['DB_PREFIX'];?>product A " +
								 "LEFT OUTER JOIN <?php echo $_SESSION['DB_PREFIX'];?>pricebreak B " +
								 "ON B.productid = A.id " +
								 "WHERE A.id = " + $("#item_productid").val()
						},
						function(data) {
							var i;
							
							for (i = 0; i < data.length; i++) {
								var node = data[i];
								
								if (i == 0) {
									/* Default to unit price. */
									$("#item_unitprice").val(new Number(node.rspnet).toFixed(2)).trigger("change");
								}
								
								$("#item_productcode").val(node.productcode);
								$("#item_productdesc").val(node.description);
								
								if (node.qtyfrom != null) {
									var qty = parseInt($("#item_quantity").val());
									
									if (node.qtyfrom <= qty && node.qtyto >= qty) {
										/* Use price break. */
										$("#item_unitprice").val(new Number(node.priceeach).toFixed(2)).trigger("change");
									}
								}
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
			
			function printQuote(id) {
				window.open("quotereport.php?id=" + id);
			}
			
			function printRemittance(id) {
				window.open("remittancereport.php?id=" + id);
			}
			
			function populateTable(data) {
				var total = 0;
				var html = "<TABLE width='100%' class='grid list'><THEAD><?php createHeader(); ?></THEAD>";
				
				if (data != null) {
    				data.sort(
    						function(a, b) {
    						    if(a.description < b.description) return -1;
    						    if(a.description > b.description) return 1;
    						    
    						    return 0;
    						}
    					);
				}
										
				$("#item_serial").val(JSON.stringify(data));
											
				if (data != null) {
					for (var i = 0; i < data.length; i++) {
						var node = data[i];
						
						if (node.description != null) {
							html += "<TR>";
							html += "<TD><img src='images/edit.png'  title='Edit item' onclick='editItem(" + i + ")' />&nbsp;<img src='images/delete.png'  title='Remove item' onclick='removeItem(" + i + ")' /></TD>";
							html += "<TD>" + node.description + "</TD>";
							html += "<TD align=right>" + new Number(node.quantity).toFixed(0) + "</TD>";
							html += "<TD align=right>" + new Number(node.priceeach).toFixed(2) + "</TD>";
							html += "<TD align=right>" + new Number(node.vatrate).toFixed(2) + "</TD>";
							html += "<TD align=right>" + new Number(node.vat).toFixed(2) + "</TD>";
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
			
			function saveQuoteItem() {
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
						description: $("#item_productdesc").val()
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
				$("#item_productdesc").val(node.description);
				$("#item_quantity").val(node.quantity);
				$("#item_vat").val(node.vat);
				$("#item_vatrate").val(node.vatrate);
				$("#item_unitprice").val(node.priceeach);
				$("#item_linetotal").val(node.linetotal);
				
				$('#invoiceitemdialog').dialog('open');				
			}
			
			function addQuoteItem() {
				currentItem = -1;
				
				$("#item_itemid").val("0");
				$("#item_productid").val("0");
				$("#item_productdesc").val("");
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
						$("#supplierid").change(supplierid_onchange);
						
						$("#invoiceitemdialog").dialog({
								modal: true,
								autoOpen: false,
								show:"fade",
								closeOnEscape: true,
								width: 690,
								hide:"fade",
								title:"Quote Item",
								open: function(event, ui){
									
								},
								buttons: {
									"Save": function() {
										if (saveQuoteItem()) {
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
				return "<?php echo getSiteConfigData()->poprefix; ?>" + padZero(node.id, 6);
			}
			
			function accept(id) {
				currentID = id;
				
				$("#confirmacceptdialog .confirmdialogbody").html("You are about to flag this purchase order as paid.<br>Are you sure ?");
				$("#confirmacceptdialog").dialog("open");
			}
			
			function undo(id) {
				currentID = id;
				
				$("#confirmundodialog .confirmdialogbody").html("You are about to flag this purchase order as unpaid.<br>Are you sure ?");
				$("#confirmundodialog").dialog("open");
			}
			
			function confirmaccept() {
				post("editform", "acceptProForma", "submitframe", 
						{ 
							proformaid: currentID
						}
					);
					
				$("#confirmacceptdialog").dialog("close");
			}
			
			function confirmundo() {
				post("editform", "undo", "submitframe", 
						{ 
							proformaid: currentID
						}
					);
					
				$("#confirmundodialog").dialog("close");
			}
			
			function checkStatus(node) {
				if (node.status == 0) {
					$("#acceptbutton").show();
					$("#undobutton").hide();
					
				} else {
					$("#acceptbutton").hide();
					$("#undobutton").show();
				}
			}
	
<?php			
		}
	}
	
	$crud = new ProFormaCrud();
	$crud->dialogwidth = 840;
	$crud->title = "Purchase Orders";
	$crud->document = array(
			'primaryidname'	 => 	"proformaid",
			'tablename'		 =>		"proformadocs"
		);
	$crud->onClickCallback = "checkStatus";
	$crud->table = "{$_SESSION['DB_PREFIX']}proforma";
	$crud->sql = "SELECT A.*, B.name AS suppliername, C.fullname AS takenbyname
				  FROM  {$_SESSION['DB_PREFIX']}proforma A
				  INNER JOIN  {$_SESSION['DB_PREFIX']}supplier B
				  ON B.id = A.supplierid
				  INNER JOIN  {$_SESSION['DB_PREFIX']}members C
				  ON C.member_id = A.takenbyid
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
				'label' 	 => 'P/O Number'
			),
			array(
				'name'       => 'supplierid',
				'type'       => 'DATACOMBO',
				'length' 	 => 60,
				'label' 	 => 'Supplier',
				'table'		 => 'supplier',
				'required'	 => true,
				'table_id'	 => 'id',
				'alias'		 => 'suppliername',
				'table_name' => 'name'
			),
			array(
				'name'       => 'revision',
				'length' 	 => 10,
				'readonly'	 => true,
				'label' 	 => 'Revision'
			),			
			array(
				'name'       => 'orderdate',
				'length' 	 => 12,
				'datatype'   => 'date',
				'label' 	 => 'P/O Date'
			),
			array(
				'name'       => 'converteddatetime',
				'length' 	 => 16,
				'bind'		 => false,
				'edit'		 => false,
				'datatype'   => 'date',
				'label' 	 => 'Paid Date'
			),
			array(
				'name'       => 'yourordernumber',
				'length' 	 => 20,
				'label' 	 => 'Your Order Number'
			),			
			array(
				'name'       => 'status',
				'bind'       => false,
				'type'		 => 'CHECKBOX',
				'length' 	 => 10,
				'label' 	 => 'Paid'
			),			
			array(
				'name'       => 'takenbyid',
				'type'       => 'DATACOMBO',
				'length' 	 => 18,
				'label' 	 => 'Taken By',
				'table'		 => 'members',
				'required'	 => true,
				'table_id'	 => 'member_id',
				'alias'		 => 'takenbyname',
				'table_name' => 'fullname'
			),
			array(
				'name'       => 'discount',
				'length' 	 => 12,
				'align'		 => 'right',
				'label' 	 => 'Discount %'
			),	
			array(
				'name'       => 'total',
				'length' 	 => 12,
				'align'		 => 'right',
				'label' 	 => 'Total'
			)	
		);

	$crud->subapplications = array(
			array(
				'title'		  => 'Paid',
				'id'		  => 'acceptbutton',
				'imageurl'	  => 'images/accept.png',
				'script' 	  => 'accept'
			),
			array(
				'title'		  => 'Unpaid',
				'id'		  => 'undobutton',
				'imageurl'	  => 'images/invalid.png',
				'script' 	  => 'undo'
			),
			array(
				'title'		  => 'Print',
				'imageurl'	  => 'images/print.png',
				'script' 	  => 'printQuote'
			)
		);
		
	$crud->messages = array(
			array('id'		  => 'proformaid')
		);
		
	$crud->run();
?>
