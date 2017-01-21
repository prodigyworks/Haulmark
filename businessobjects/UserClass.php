<?php
	require_once("AbstractBaseDataClass.php");
	require_once("CustomerClass.php");

	class UserClass extends AbstractBaseDataClass {
		
		/* Booking level properties. */
		
		/** @property **/
		private $memberid = null;
		/** @property **/
		private $firstname = null;
		/** @property **/
		private $lastname = null;
		/** @property **/
		private $fullname = null;
		/** @property **/
		private $login = null;
		/** @property **/
		private $passwd = null;
		/** @property **/
		private $email = null;
		/** @property **/
		private $title = null;
		/** @property **/
		private $imageid = null;
		/** @property @datetime **/
		private $createddate = null;
		/** @property **/
		private $timeoutperiod = null;
		/** @property **/
		private $customerid = null;
		/** @property **/
		private $supplierid = null;
		/** @property **/
		private $driverid = null;
		/** @property **/
		private $prorataholidayentitlement = null;
		/** @property **/
		private $holidayentitlement = null;
		/** @property **/
		private $officeid = null;
		/** @property **/
		private $description = null;
		/** @property @datetime **/
		private $lastaccessdate = null;
		/** @property @datetime **/
		private $startdate = null;
		/** @property **/
		private $status = null;
		/** @property **/
		private $postcode = null;
		/** @property **/
		private $systemuser = null;
		/** @property **/
		private $accepted = null;
		/** @property **/
		private $website = null;
		/** @property **/
		private $guid = null;
		/** @property **/
		private $mobile = null;
		/** @property **/
		private $landline = null;
		/** @property **/
		private $fax = null;
		/** @property @datetime **/
		private $dateofbirth = null;
		/** @property **/
		private $notes = null;
		/** @property **/
		private $address = null;
		/** @property **/
		private $loginauditid = null;
		/** @property **/
		private $postcode_lat = null;
		/** @property **/
		private $postcode_lng = null;

		/** @onetoone **/
		private $customer = null;
		
		/**
		 * Constructor 
		 */
	    public function __construct() {
	    	start_db();
	    }
	    
	    /** 
	     * Load from resultset 
	     */
	    public function loadFromResultset($resultset) {
			$this->memberid = $resultset['member_id'];
			$this->firstname = $resultset['firstname'];
			$this->lastname = $resultset['lastname'];
			$this->fullname = $resultset['fullname'];
			$this->login = $resultset['login'];
			$this->passwd = $resultset['passwd'];
			$this->email = $resultset['email'];
			$this->title = $resultset['title'];
			$this->imageid = $resultset['imageid'];
			$this->createddate = $resultset['createddate'];
			$this->timeoutperiod = $resultset['timeoutperiod'];
			$this->customerid = $resultset['customerid'];
			$this->supplierid = $resultset['supplierid'];
			$this->driverid = $resultset['driverid'];
			$this->prorataholidayentitlement = $resultset['prorataholidayentitlement'];
			$this->holidayentitlement = $resultset['holidayentitlement'];
			$this->officeid = $resultset['officeid'];
			$this->description = $resultset['description'];
			$this->lastaccessdate = $resultset['lastaccessdate'];
			$this->startdate = $resultset['startdate'];
			$this->status = $resultset['status'];
			$this->postcode = $resultset['postcode'];
			$this->systemuser = $resultset['systemuser'];
			$this->accepted = $resultset['accepted'];
			$this->website = $resultset['website'];
			$this->guid = $resultset['guid'];
			$this->mobile = $resultset['mobile'];
			$this->landline = $resultset['landline'];
			$this->fax = $resultset['fax'];
			$this->dateofbirth = $resultset['dateofbirth'];
			$this->notes = $resultset['notes'];
			$this->address = $resultset['address'];
			$this->loginauditid = $resultset['loginauditid'];
			$this->postcode_lat = $resultset['postcode_lat'];
			$this->postcode_lng = $resultset['postcode_lng'];
	    }
	    
	    /**
	     * Load user data
	     * @param unknown_type $id Booking ID
	     * @throws Exception
	     */
		public function load($id) {
			$this->memberid = $id;
			
			$sql = "SELECT A.* 
					FROM {$_SESSION['DB_PREFIX']}members A
					WHERE A.member_id = $id";
			
			$result = mysql_query($sql);
			
			if (! $result) {
				throw new Exception("Cannot load user $id - $sql - " . mysql_error());
			}
			
			while (($resultset = mysql_fetch_assoc($result))) {
				$this->loadFromResultset($resultset);
			}
			
			return $this;
		}
		
		/**
		 * Is the user a customer user.
		 * @return boolean 
		 */
		public function isCustomerUser() {
			return $this->customerid != null && $this->customerid != 0;
		}
		
		/**
		 * Flag the user within the audit table for login timeout access.
		 */
		public static function auditAccess() {
			$memberid = getLoggedOnMemberID();
			$auditid = $_SESSION['SESS_LOGIN_AUDIT'];

			$sql = "UPDATE {$_SESSION['DB_PREFIX']}members SET 
					lastaccessdate = NOW(), 
					metamodifieddate = NOW(), 
					metamodifieduserid = $memberid
					WHERE member_id = $memberid";
			$result = mysql_query($sql);
			
			$sql = "UPDATE {$_SESSION['DB_PREFIX']}loginaudit SET 
					timeoff = NOW(), 
					metamodifieddate = NOW(), 
					metamodifieduserid = $memberid
					WHERE id = $auditid";
			$result = mysql_query($sql);
		}
		
		/**
		 * memberid
		 * @return int
		 */
		public function getMemberid(){
			return $this->memberid;
		}
	
		/**
		 * firstname
		 * @return string
		 */
		public function getFirstname(){
			return $this->firstname;
		}
	
		/**
		 * firstname
		 * @param string $firstname
		 * @return UserClass
		 */
		public function setFirstname($firstname){
			$this->firstname = $firstname;
			return $this;
		}
	
		/**
		 * lastname
		 * @return string
		 */
		public function getLastname(){
			return $this->lastname;
		}
	
		/**
		 * lastname
		 * @param string $lastname
		 * @return UserClass
		 */
		public function setLastname($lastname){
			$this->lastname = $lastname;
			return $this;
		}
	
		/**
		 * fullname
		 * @return string
		 */
		public function getFullname(){
			return $this->fullname;
		}
	
		/**
		 * fullname
		 * @param string $fullname
		 * @return UserClass
		 */
		public function setFullname($fullname){
			$this->fullname = $fullname;
			return $this;
		}
	
		/**
		 * login
		 * @return string
		 */
		public function getLogin(){
			return $this->login;
		}
	
		/**
		 * login
		 * @param string $login
		 * @return UserClass
		 */
		public function setLogin($login){
			$this->login = $login;
			return $this;
		}
	
		/**
		 * passwd
		 * @return string
		 */
		public function getPasswd(){
			return $this->passwd;
		}
	
		/**
		 * passwd
		 * @param string $passwd
		 * @return UserClass
		 */
		public function setPasswd($passwd){
			$this->passwd = $passwd;
			return $this;
		}
	
		/**
		 * email
		 * @return string
		 */
		public function getEmail(){
			return $this->email;
		}
	
		/**
		 * email
		 * @param string $email
		 * @return UserClass
		 */
		public function setEmail($email){
			$this->email = $email;
			return $this;
		}
	
		/**
		 * title
		 * @return string
		 */
		public function getTitle(){
			return $this->title;
		}
	
		/**
		 * title
		 * @param string $title
		 * @return UserClass
		 */
		public function setTitle($title){
			$this->title = $title;
			return $this;
		}
	
		/**
		 * imageid
		 * @return int
		 */
		public function getImageid(){
			return $this->imageid;
		}
	
		/**
		 * imageid
		 * @param int $imageid
		 * @return UserClass
		 */
		public function setImageid($imageid){
			$this->imageid = $imageid;
			return $this;
		}
	
		/**
		 * createddate
		 * @return DateTime
		 */
		public function getCreateddate(){
			return $this->createddate;
		}
	
		/**
		 * createddate
		 * @param DateTime $createddate
		 * @return UserClass
		 */
		public function setCreateddate($createddate){
			$this->createddate = $createddate;
			return $this;
		}
	
		/**
		 * timeoutperiod
		 * @return int
		 */
		public function getTimeoutperiod(){
			return $this->timeoutperiod;
		}
	
		/**
		 * timeoutperiod
		 * @param int $timeoutperiod
		 * @return UserClass
		 */
		public function setTimeoutperiod($timeoutperiod){
			$this->timeoutperiod = $timeoutperiod;
			return $this;
		}
	
		/**
		 * customerid
		 * @return int
		 */
		public function getCustomerid(){
			return $this->customerid;
		}
	
		/**
		 * customerid
		 * @param int $customerid
		 * @return UserClass
		 */
		public function setCustomerid($customerid){
			$this->customerid = $customerid;
			return $this;
		}
	
		/**
		 * supplierid
		 * @return int
		 */
		public function getSupplierid(){
			return $this->supplierid;
		}
	
		/**
		 * supplierid
		 * @param int $supplierid
		 * @return UserClass
		 */
		public function setSupplierid($supplierid){
			$this->supplierid = $supplierid;
			return $this;
		}
	
		/**
		 * driverid
		 * @return int
		 */
		public function getDriverid(){
			return $this->driverid;
		}
	
		/**
		 * driverid
		 * @param int $driverid
		 * @return UserClass
		 */
		public function setDriverid($driverid){
			$this->driverid = $driverid;
			return $this;
		}
	
		/**
		 * prorataholidayentitlement
		 * @return int
		 */
		public function getProrataholidayentitlement(){
			return $this->prorataholidayentitlement;
		}
	
		/**
		 * prorataholidayentitlement
		 * @param int $prorataholidayentitlement
		 * @return UserClass
		 */
		public function setProrataholidayentitlement($prorataholidayentitlement){
			$this->prorataholidayentitlement = $prorataholidayentitlement;
			return $this;
		}
	
		/**
		 * holidayentitlement
		 * @return int
		 */
		public function getHolidayentitlement(){
			return $this->holidayentitlement;
		}
	
		/**
		 * holidayentitlement
		 * @param int $holidayentitlement
		 * @return UserClass
		 */
		public function setHolidayentitlement($holidayentitlement){
			$this->holidayentitlement = $holidayentitlement;
			return $this;
		}
	
		/**
		 * officeid
		 * @return int
		 */
		public function getOfficeid(){
			return $this->officeid;
		}
	
		/**
		 * officeid
		 * @param int $officeid
		 * @return UserClass
		 */
		public function setOfficeid($officeid){
			$this->officeid = $officeid;
			return $this;
		}
	
		/**
		 * description
		 * @return string
		 */
		public function getDescription(){
			return $this->description;
		}
	
		/**
		 * description
		 * @param string $description
		 * @return UserClass
		 */
		public function setDescription($description){
			$this->description = $description;
			return $this;
		}
	
		/**
		 * lastaccessdate
		 * @return DateTime
		 */
		public function getLastaccessdate(){
			return $this->lastaccessdate;
		}
	
		/**
		 * lastaccessdate
		 * @param DateTime $lastaccessdate
		 * @return UserClass
		 */
		public function setLastaccessdate($lastaccessdate){
			$this->lastaccessdate = $lastaccessdate;
			return $this;
		}
	
		/**
		 * startdate
		 * @return DateTime
		 */
		public function getStartdate(){
			return $this->startdate;
		}
	
		/**
		 * startdate
		 * @param DateTime $startdate
		 * @return UserClass
		 */
		public function setStartdate($startdate){
			$this->startdate = $startdate;
			return $this;
		}
	
		/**
		 * status
		 * @return string
		 */
		public function getStatus(){
			return $this->status;
		}
	
		/**
		 * status
		 * @param string $status
		 * @return UserClass
		 */
		public function setStatus($status){
			$this->status = $status;
			return $this;
		}
	
		/**
		 * postcode
		 * @return string
		 */
		public function getPostcode(){
			return $this->postcode;
		}
	
		/**
		 * postcode
		 * @param string $postcode
		 * @return UserClass
		 */
		public function setPostcode($postcode){
			$this->postcode = $postcode;
			return $this;
		}
	
		/**
		 * systemuser
		 * @return string
		 */
		public function getSystemuser(){
			return $this->systemuser;
		}
	
		/**
		 * systemuser
		 * @param string $systemuser
		 * @return UserClass
		 */
		public function setSystemuser($systemuser){
			$this->systemuser = $systemuser;
			return $this;
		}
	
		/**
		 * accepted
		 * @return string
		 */
		public function getAccepted(){
			return $this->accepted;
		}
	
		/**
		 * accepted
		 * @param string $accepted
		 * @return UserClass
		 */
		public function setAccepted($accepted){
			$this->accepted = $accepted;
			return $this;
		}
	
		/**
		 * website
		 * @return string
		 */
		public function getWebsite(){
			return $this->website;
		}
	
		/**
		 * website
		 * @param string $website
		 * @return UserClass
		 */
		public function setWebsite($website){
			$this->website = $website;
			return $this;
		}
	
		/**
		 * guid
		 * @return string
		 */
		public function getGuid(){
			return $this->guid;
		}
	
		/**
		 * guid
		 * @param string $guid
		 * @return UserClass
		 */
		public function setGuid($guid){
			$this->guid = $guid;
			return $this;
		}
	
		/**
		 * mobile
		 * @return string
		 */
		public function getMobile(){
			return $this->mobile;
		}
	
		/**
		 * mobile
		 * @param string $mobile
		 * @return UserClass
		 */
		public function setMobile($mobile){
			$this->mobile = $mobile;
			return $this;
		}
	
		/**
		 * landline
		 * @return string
		 */
		public function getLandline(){
			return $this->landline;
		}
	
		/**
		 * landline
		 * @param string $landline
		 * @return UserClass
		 */
		public function setLandline($landline){
			$this->landline = $landline;
			return $this;
		}
	
		/**
		 * fax
		 * @return string
		 */
		public function getFax(){
			return $this->fax;
		}
	
		/**
		 * fax
		 * @param string $fax
		 * @return UserClass
		 */
		public function setFax($fax){
			$this->fax = $fax;
			return $this;
		}
	
		/**
		 * dateofbirth
		 * @return DateTime
		 */
		public function getDateofbirth(){
			return $this->dateofbirth;
		}
	
		/**
		 * dateofbirth
		 * @param DateTime $dateofbirth
		 * @return UserClass
		 */
		public function setDateofbirth($dateofbirth){
			$this->dateofbirth = $dateofbirth;
			return $this;
		}
	
		/**
		 * notes
		 * @return string
		 */
		public function getNotes(){
			return $this->notes;
		}
	
		/**
		 * notes
		 * @param string $notes
		 * @return UserClass
		 */
		public function setNotes($notes){
			$this->notes = $notes;
			return $this;
		}
	
		/**
		 * address
		 * @return string
		 */
		public function getAddress(){
			return $this->address;
		}
	
		/**
		 * address
		 * @param string $address
		 * @return UserClass
		 */
		public function setAddress($address){
			$this->address = $address;
			return $this;
		}
	
		/**
		 * loginauditid
		 * @return int
		 */
		public function getLoginauditid(){
			return $this->loginauditid;
		}
	
		/**
		 * loginauditid
		 * @param int $loginauditid
		 * @return UserClass
		 */
		public function setLoginauditid($loginauditid){
			$this->loginauditid = $loginauditid;
			return $this;
		}
	
		/**
		 * postcode_lat
		 * @return float
		 */
		public function getPostcode_lat(){
			return $this->postcode_lat;
		}
	
		/**
		 * postcode_lat
		 * @param float $postcode_lat
		 * @return UserClass
		 */
		public function setPostcode_lat($postcode_lat){
			$this->postcode_lat = $postcode_lat;
			return $this;
		}
	
		/**
		 * postcode_lng
		 * @return float
		 */
		public function getPostcode_lng(){
			return $this->postcode_lng;
		}
	
		/**
		 * postcode_lng
		 * @param float $postcode_lng
		 * @return UserClass
		 */
		public function setPostcode_lng($postcode_lng){
			$this->postcode_lng = $postcode_lng;
			return $this;
		}
	
		/**
		 * customer
		 * @return CustomerClass
		 */
		public function getCustomer(){
			if ($this->customer == null) {
				$this->customer = new CustomerClass();
				$this->customer->load($this->customerid);
			}
			
			return $this->customer;
		}
	
		/**
		 * customer
		 * @param CustomerClass $customer
		 * @return UserClass
		 */
		public function setCustomer($customer){
			$this->customer = $customer;
			return $this;
		}
	
	} 
?>