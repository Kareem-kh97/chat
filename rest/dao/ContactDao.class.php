<?php
require_once __DIR__.'/BaseDao.class.php';

class ContactDao extends BaseDao{

  /**
  * constructor of dao class
  */
  public function __construct(){
    parent::__construct("contacts");
  }

  public function get_user_contacts($user_id){

    $query = "SELECT u.id, u.first_name, u.last_name, u.email, u.status
    FROM users u
    JOIN contacts c ON c.user_id = u.id
    WHERE u.id IN (SELECT cs.contact_id FROM contacts cs WHERE cs.user_id = :user_id)
    ";

    return $this->query($query, ['user_id' => $user_id]);
  }
  public function get_user_contact_messages($user_id, $contact_id){

    $query = "SELECT *
      FROM messages
      WHERE (sender_id = :user_id AND receiver_id = :contact_id) 
      OR (sender_id = :contact_id AND receiver_id = :user_id)
    ";

    return $this->query($query, ['user_id' => $user_id, 'contact_id' => $contact_id]);
  }

  public function get_by_id($id){
    return $this->query_unique('SELECT n.*, DATE_FORMAT(n.created, "%Y-%m-%d") created FROM notes n WHERE n.id = :id', ['id' => $id]);
  }
}

?>
