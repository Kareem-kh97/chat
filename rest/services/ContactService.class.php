<?php
require_once __DIR__ . '/BaseService.class.php';
require_once __DIR__ . '/../dao/ContactDao.class.php';
require_once __DIR__ . '/../dao/UserDao.class.php';

class ContactService extends BaseService
{

  private $user_dao;

  public function __construct()
  {
    parent::__construct(new ContactDao());
    $this->user_dao = new UserDao();
  }
  //retrive me contacts of a specific user
  public function get_user_contacts($user)
  {

    // Flight::json(["message" => "Hi from service ".implode(" ",$user)], 404);
    return $this->dao->get_user_contacts($user['id']);
  }
  //retrive me all messages between me and the other contact
  public function get_user_contact_messages($user, $contactId)
  {
    // Flight::json(["message" => "Hi from service ".implode(" ",$user)], 404);
    return $this->dao->get_user_contact_messages($user['id'], $contactId);
  }
  
  public function add_friend($user, $contactEmail)
  {
    // Flight::json(["message" => "Hi from service ".implode(" ",$user)], 404);
    return $this->dao->add_friend($user['id'], $contactEmail);
  }
}
