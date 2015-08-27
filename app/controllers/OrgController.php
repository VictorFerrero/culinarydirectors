<?php

// id | name | address | city | state | zip | phone | email | phone2 | profileJSON
class OrgController{
		
		
//		public $arrOrgInfo; // keep org info stored in associative array
		private $orgModel;
		// TODO: use ID to select this from db
	public function __construct() {
		$this->orgModel = new OrgModel(); // TODO: 
	 }
	 
	 public function __destruct() {
		 // ensure that the OrgModel destructor gets called to properly
		 // close the database connection
		 $this->orgModel = null;
	 }
	 	 
	 public function createOrg() {
		$arrValues = array();
		$arrValues['name'] = $_REQUEST['name'];
		$arrValues['address'] =  $_REQUEST['address'];
	    $arrValues['city'] =  $_REQUEST['city'];
		$arrValues['state'] =  $_REQUEST['state'];
		$arrValues['zip'] =  $_REQUEST['zip'];
		$arrValues['phone'] =  $_REQUEST['phone'];
		$arrValues['email'] =  $_REQUEST['email'];
		$arrValues['phone2'] = $_REQUEST['phone2'];
		$arrValues['profileJSON'] = $_REQUEST['profileJSON'];
		
		$arrResult = $this->orgModel->createOrg($arrValues);
		return $arrResult;
	 }
	 
	 public function editOrg() {
		$arrValues = array();
		$arrValues['id'] = $_REQUEST['id'];
		$arrValues['name'] = $_REQUEST['name'];
		$arrValues['address'] =  $_REQUEST['address'];
	    $arrValues['city'] =  $_REQUEST['city'];
		$arrValues['state'] =  $_REQUEST['state'];
		$arrValues['zip'] =  $_REQUEST['zip'];
		$arrValues['phone'] =  $_REQUEST['phone'];
		$arrValues['email'] =  $_REQUEST['email'];
		$arrValues['phone2'] = $_REQUEST['phone2'];
		$arrValues['profileJSON'] = $_REQUEST['profileJSON'];
		$arrValues['id'] = $_REQUEST['id'];
		$arrResult = $this->orgModel->editOrg($arrValues);
		return $arrResult;
	 }
	 
	 public function deleteOrg() {
		 $id = $_REQUEST['id'];
		 $arrResult = $this->orgModel->deleteOrg($id);
		 return $arrResult;
	 }
	 
	 public function getOrgById() {
		$id = $_REQUEST['id'];
		$arrResult = $this->orgModel->getOrgById($id);
		return $arrResult;
	 }
}
?>
