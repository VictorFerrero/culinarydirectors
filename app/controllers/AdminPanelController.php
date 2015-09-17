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

	public function lists(){
		$admin_id = $_REQUEST['admin_id'];
		$res = $this->adminPanelModel->getLists($admin_id);
		return $res;
	}

	public function upsert(){
		$type = $_REQUEST['type'];
		$action = $_REQUEST['action'];
		$res = $this->adminPanelModel->upsert($type, $action);
		return $res;
	}

	public function delete(){

	}

}
?>