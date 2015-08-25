<?php
class FeedController
{
	private $feedModel;
	
	// id | to | from | message
	public function __construct() {
		$this->feedModel = new FeedModel();
	}
	
	public function __destruct() {
		$this->feedModel = null;
	}
	
	public function addMessage() {
		$arrValues = array();
		$arrValues['to'] = $_REQUEST['to'];
		$arrValues['from'] = $_REQUEST['from'];
		$arrValues['message'] = $_REQUEST['message'];
		
		$arrResult = $this->feedModel->addMessage($arrValues);
	}
	
	public function deleteMessageById() {
		$arrValues = array();
		$arrValues['id'] = $_REQUEST['id']; // id of thing we want to delete
		$arrValues['where_clause'] = "id=:id"; // where clause specifying what condition is to delete
		$arrResult = $this->feedModel->deleteMessage($arrValues);
	}
	
	// -1 means the message is TO everyone
	public function getMessagesByToId() {
		$arrValues = array();
		$arrValues['id'] = $_REQUEST['toId']
		$arrValues['where_clause'] = "to=:id";
		$arrResult = $this->feedModel->getMessages($arrValues);
		
		$arrMessages = $arrResult['data'];
	}
	
	public function getMessagesByFromId() {
		$arrValues = array();
		$arrValues['id'] = $_REQUEST['fromId']
		$arrValues['where_clause'] = "from=:id";
		$arrResult = $this->feedModel->getMessages($arrValues);
		
		$arrMessages = $arrResult['data'];
	}
	
	public function getMessagesById() {
		$arrValues = array();
		$arrValues['id'] = $_REQUEST['id']
		$arrValues['where_clause'] = "id=:id";
		$arrResult = $this->feedModel->getMessages($arrValues);
		
		$arrMessages = $arrResult['data'];
	}
}
?>
