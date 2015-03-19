<?php
	require_once("crud.php");

	
	class BookingLegCrud extends Crud {
		
	    public function __construct() {
	        parent::__construct();
	        
			$this->title = "Booking Legs";
			$this->dialogWidth = 500;
			$this->table = "{$_SESSION['DB_PREFIX']}bookingleg";
			
			if (isset($_GET['id'])) {
				$bookingid = $_GET['id'];
				
				$this->sql = 
						"SELECT A.* FROM 
						{$_SESSION['DB_PREFIX']}bookingleg A
						WHERE A.bookingid = $bookingid
						ORDER BY A.id";
			}
			
			$this->columns = array(
					array(
						'name'       => 'bookingid',
						'editable'	 => false,
						'showInView' => false,
						'default'	 => isset($_GET['id']) ? $_GET['id'] : 0
					),
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
						'name'       => 'place',
						'length' 	 => 50,
						'label' 	 => 'Place'
					),
					array(
						'name'       => 'departuretime',
						'datatype'   => 'datetime',
						'length' 	 => 15,
						'label' 	 => 'Estimated Arrival Time'
					),
					array(
						'name'       => 'miles',
						'length' 	 => 10,
						'label' 	 => 'Distance (Miles)'
					)
				);
			$this->subapplications = array(
					array(
						'title'		  => 'Documents',
						'imageurl'	  => 'images/article.png',
						'script' 	  => 'editDocuments'
					)
				);
		}

		/* Post header event. */
		public function postHeaderEvent() {
			createDocumentLink();
		}
		
		public function postScriptEvent() {
?>
			function editDocuments(node) {
				viewDocument(node, "addbookinglegdocument.php", node, "bookinglegdocs", "bookinglegid");
			}
<?php
		}
	}
	
	$crud = new BookingLegCrud();
	$crud->run();
?>
