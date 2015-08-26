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
		$arrValues['sender'] = $_REQUEST['sender'];
		$arrValues['receiver'] = $_REQUEST['receiver'];
		$arrValues['message'] = $_REQUEST['message'];
		
		$arrResult = $this->feedModel->addMessage($arrValues);
		return $arrResult;
	}
	
	public function deleteMessageById() {
		$arrValues = array();
		$arrValues['id'] = $_REQUEST['id']; // id of thing we want to delete
		$arrValues['where_clause'] = "id=:id"; // where clause specifying what condition is to delete
		$arrResult = $this->feedModel->deleteMessage($arrValues);
		return $arrResult;
	}
	
	// -1 means the message is TO everyone
	public function getMessagesBySenderId() {
		$arrValues = array();
		$arrValues['id'] = $_REQUEST['senderId'];
		$arrValues['where_clause'] = "sender=:id";
		$arrResult = $this->feedModel->getMessages($arrValues);
		
		$arrMessages = $arrResult['data'];
		return $arrResult;
	}
	
	public function getMessagesByReceiverId() {
		$arrValues = array();
		$arrValues['id'] = $_REQUEST['receiverId'];
		$arrValues['where_clause'] = "receiver=:id";
		$arrResult = $this->feedModel->getMessages($arrValues);
		
		$arrMessages = $arrResult['data'];
		return $arrResult;
	}
	
	public function getMessagesById() {
		$arrValues = array();
		$arrValues['id'] = $_REQUEST['id'];
		$arrValues['where_clause'] = "id=:id";
		$arrResult = $this->feedModel->getMessages($arrValues);
		
		$arrMessages = $arrResult['data'];
		return $arrResult;
	}
}
?>
