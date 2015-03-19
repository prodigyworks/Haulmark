<?php
	require_once("crud.php");
	
	class InvoiceCrud extends Crud {
		
		public function postAddScriptEvent() {
?>
			$("#createdby").val("<?php echo getLoggedOnMemberID(); ?>");
			$("#invoicedate").val("<?php echo date("d/m/Y"); ?>");
			$("#nett").val("0.00");
			$("#vatrate").val("<?php echo getSiteConfigData()->vatrate; ?>");
			$("#vat").val("0.00");
			$("#gross").val("0.00");
<?php
		}
		
		public function postHeaderEvent() {
		}
		
		public function postScriptEvent() {
?>
			function invoicenumber(node) {
				return "IN-" + padZero(node.id, 6);
			}
			
			function calculate_gross() {
				var nett = parseFloat($("#nett").val());
				var vatrate = parseFloat($("#nett").val());
				var vat = nett * (vatrate / 100);
				var gross = nett + vat;
				
				$("#vat").val(new Number(vat).toFixed(2));
				$("#gross").val(new Number(gross).toFixed(2));
			}
			
			function formattedDate(date) {
			    var d = new Date(date || Date.now()),
			        month = '' + (d.getMonth() + 1),
			        day = '' + d.getDate(),
			        year = d.getFullYear();
			
			    if (month.length < 2) month = '0' + month;
			    if (day.length < 2) day = '0' + day;
			
			    return [day, month, year].join('/');
			}
			
			function customerid_onchange() {
				callAjax(
						"finddata.php", 
						{ 
							sql: "SELECT A.duedays " + 
								 "FROM <?php echo $_SESSION['DB_PREFIX'];?>customer A " +
								 "WHERE A.id = " + $("#customerid").val()
						},
						function(data) {
							if (data.length > 0) {
								var duedays = data[0].duedays;
								var datearr = $("#invoicedate").val().split("/");
								var date = new Date(datearr[2], datearr[1] - 1, datearr[0]);
								
								date.setTime(date.getTime() + (duedays * 60 * 60 * 24 * 1000));
								
								$("#duedate").val(formattedDate(date));
							}
						}
					);
			}
					
			function printInvoiceReport(id) {
				window.open("invoicereport.php?id=" + id);
			}
<?php			
		}
	}

	$crud = new InvoiceCrud();
	$crud->title = "Invoices";
	$crud->table = "{$_SESSION['DB_PREFIX']}invoice";
	$crud->dialogwidth = 500;
	
	$crud->sql = 
		   "SELECT A.*, B.fullname, 
		    C.name, C.accountcode, C.street, C.town, C.city, C.county, C.postcode, 
		    C.vatnumber, C.vatprefix, C.terms, C.duedays, C.settlementdiscount
			FROM {$_SESSION['DB_PREFIX']}invoice A 
			LEFT OUTER JOIN {$_SESSION['DB_PREFIX']}members B 
			ON B.member_id = A.createdby 
			LEFT OUTER JOIN {$_SESSION['DB_PREFIX']}customer C 
			ON C.id = A.customerid
			ORDER BY A.id DESC";			
	
	$crud->columns = array(
			array(
				'name'       => 'id',
				'length' 	 => 6,
				'pk'		 => true,
				'showInView' => false,
				'editable'	 => false,
				'filter'	 => false,
				'bind' 	 	 => false,
				'label' 	 => 'ID'
			),
			array(
				'name'       => 'invoicenumber',
				'length' 	 => 12,
				'type'		 => 'DERIVED',
				'function'   => 'invoicenumber',
				'sortcolumn' => 'A.id',
				'editable'	 => false,
				'bind' 		 => false,
				'label' 	 => 'Invoice'
			),
			array(
				'name'       => 'customerid',
				'length' 	 => 40,
				'onchange'	 => 'customerid_onchange',
				'type'       => 'DATACOMBO',
				'label' 	 => 'Customer',
				'table'		 => 'customer',
				'table_id'	 => 'id',
				'alias'		 => 'name',
				'table_name' => 'name'
			),
			array(
				'name'       => 'invoicedate',
				'datatype'	 => 'date',
				'length' 	 => 12,
				'label' 	 => 'Invoice Date'
			),
			array(
				'name'       => 'duedate',
				'datatype'	 => 'date',
				'length' 	 => 12,
				'label' 	 => 'Due Date'
			),
			array(
				'name'       => 'customerreference',
				'length' 	 => 32,
				'label' 	 => 'Customer Reference'
			),
			array(
				'name'       => 'createdby',
				'length' 	 => 23,
				'type'       => 'DATACOMBO',
				'label' 	 => 'Created By',
				'table'		 => 'members',
				'table_id'	 => 'member_id',
				'alias'		 => 'fullname',
				'table_name' => 'fullname'
			),
			array(
				'name'       => 'nett',
				'length' 	 => 12,
				'readonly'	 => true,
				'onchange'	 => 'calculate_gross',
				'align'		 => 'right',
				'label' 	 => 'Nett'
			),
			array(
				'name'       => 'vatrate',
				'length' 	 => 12,
				'onchange'	 => 'calculate_gross',
				'align'		 => 'right',
				'label' 	 => 'VAT %'
			),
			array(
				'name'       => 'vat',
				'length' 	 => 12,
				'readonly'	 => true,
				'align'		 => 'right',
				'label' 	 => 'VAT'
			),
			array(
				'name'       => 'gross',
				'length' 	 => 12,
				'readonly'	 => true,
				'align'		 => 'right',
				'label' 	 => 'Gross'
			)

		);
		
	$crud->subapplications = array(
			array(
				'title'		  => 'Items',
				'imageurl'	  => 'images/invoice.png',
				'application' => 'manageinvoiceitems.php'
			),
			array(
				'title'		  => 'Print',
				'imageurl'	  => 'images/print.png',
				'script' 	  => 'printInvoiceReport'
			)
		);
		
	$crud->run();
?>
