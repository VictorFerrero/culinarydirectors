<?php
class AdminPanelController
{
	private $adminPanelModel;
	
	public function __construct() {
		$this->adminPanelModel = new AdminPanelModel();
	}
	
	public function __destruct() {
		$this->adminPanelModel = null;
	}
	
	//login, lists, upsert, delete
	public function login() {
		$em = $_REQUEST['email'];
		$pw = $_REQUEST['pw'];
		$res = $this->adminPanelModel->login($em, $pw);
		return $res;
	}
}
?>