<?php
require_once __DIR__ . '/BaseService.class.php';
require_once __DIR__ . '/../dao/MessageDao.class.php';
require_once __DIR__ . '/../dao/UserDao.class.php';

class MessageService extends BaseService
{

  private $user_dao;
// we call parent with the dao we wish
  public function __construct()
  {
    parent::__construct(new MessageDao());
    $this->user_dao = new UserDao();
  }
// getting me all messages exchanged between mme and the other contact
  public function get_user_contact_messages($user, $contactId)
  {
    // Flight::json(["message" => "Hi from service ".implode(" ",$user)], 404);
    return $this->dao->get_user_contact_messages($user['id'], $contactId);
  }
  //it is for data interaction (user infor, name, id)
  public function sendmessage($messageData){
    return $this->dao->sendmessage($messageData);
  }

  public function softdelete($messageId){
    
    // Flight::json(["message" => "Deleting the message " . $messageId . " (service)"], 404);
    return $this->dao->softdelete($messageId);
  }

  public function updatetext($message, $user){
    // Flight::json(["message" => "It works (service)"], 404);
    $userId = $user['id'];
    return $this->dao->updatetext($message, $userId);
  }
}
