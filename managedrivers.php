<?php
	require_once("crud.php");
	
	class AddressCrud extends Crud {
			
		/* Post header event. */
		public function postHeaderEvent() {
			createDocumentLink();
		}
		
		public function postScriptEvent() {
?>
			function editDocuments(node) {
				viewDocument(node, "adddriverdocument.php", node, "driverdocs", "driverid");
			}
			
			function holidayentitlement_onchange() {
				var startDateStr = $("#startdate").val();
				var lastWorkingDateStr = "";
				
				if (isDate(startDateStr)) {
					var startDate = new Date(startDateStr.substring(6, 10), (parseFloat(startDateStr.substring(3, 5)) - 1), startDateStr.substring(0, 2));
					var lastWorkingDate = null;
					
					if (isDate(lastWorkingDateStr)) {
						lastWorkingDate = new Date(lastWorkingDateStr.substring(6, 10), (parseFloat(lastWorkingDateStr.substring(3, 5)) - 1), lastWorkingDateStr.substring(0, 2));
					}
					
					if (startDate.getFullYear() == <?php echo date("Y"); ?>) {
						var week = getWeek(startDate);
						var prorataHolidayEntitlement = 0;
						
						if (lastWorkingDate != null) {
							var weeks = parseInt(daysBetween(startDate, lastWorkingDate) / 7);
							prorataHolidayEntitlement = ($("#holidayentitlement").val() / 52) * (weeks);
							
						} else {
							prorataHolidayEntitlement = ($("#holidayentitlement").val() / 52) * (52 - week);
						}
						
						$("#prorataholidayentitlement").val(parseInt(prorataHolidayEntitlement));

					} else {
						$("#prorataholidayentitlement").val($("#holidayentitlement").val());
					}
				}
			}
			
			function daysBetween(first, second) {
			    // Copy date parts of the timestamps, discarding the time parts.
			    var one = new Date(first.getFullYear(), first.getMonth(), first.getDate());
			    var two = new Date(second.getFullYear(), second.getMonth(), second.getDate());
			
			    // Do the math.
			    var millisecondsPerDay = 1000 * 60 * 60 * 24;
			    var millisBetween = two.getTime() - one.getTime();
			    var days = millisBetween / millisecondsPerDay;
			
			    // Round down.
			    return Math.floor(days);
			}
			
			function getWeek(date) {
				var onejan = new Date(date.getFullYear(),0,1);
				
				return Math.ceil((((date - onejan) / 86400000) + onejan.getDay()+1)/7);
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
				'name'       => 'usualvehicleid',
				'type'       => 'DATACOMBO',
				'length' 	 => 10,
				'label' 	 => 'Usual Vehicle',
				'table'		 => 'vehicle',
				'required'	 => trues,
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
				'required'	 => true,
				'table_id'	 => 'id',
				'alias'		 => 'trailername',
				'table_name' => 'registration'
			),
			array(
				'name'       => 'startdate',
				'datatype'	 => 'date',
				'length' 	 => 12,
				'onchange'	 => 'holidayentitlement_onchange',
				'label' 	 => 'Start Date'
			),
			array(
				'name'       => 'holidayentitlement',
				'length' 	 => 10,
				'onchange'	 => 'holidayentitlement_onchange',
				'align' 	 => 'center',
				'label' 	 => 'Entitlement'
			),
			array(
				'name'       => 'prorataholidayentitlement',
				'length' 	 => 10,
				'readonly'	 => true,
				'align' 	 => 'center',
				'label' 	 => 'Entitlement (Pro Rata)'
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
		
	$crud->subapplications = array(
			array(
				'title'		  => 'Documents',
				'imageurl'	  => 'images/document.gif',
				'script' 	  => 'editDocuments'
			)
		);
		
	$crud->run();
?>
