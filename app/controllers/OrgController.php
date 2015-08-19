<?php

class OrgController{
		
		public $greekName;
		public $letters;
		public $address;
		public $phoneNumber;
		public $email;
		public $description;
		
		private $orgModel;
		// TODO: use ID to select this from db
	public function __construct($OrgId) {
		$this->orgModel = new OrgModel("MySQL", $user, $password); // TODO: 
		$arrResult = $orgModel->getOrgById($OrgId);
		$arrOrgInfo = $arrResult['data'];
		
		$this->greekName = $arrOrgInfo['greekName'];
		// TODO:
		// etc...   Dont know the schema yet
	 }
	 
	 public function __destruct() {
		 // ensure that the OrgModel destructor gets called to properly
		 // close the database connection
		 $this->orgModel = null;
	 }
}

?>
