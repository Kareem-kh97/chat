<?php
require_once __DIR__ . '/BaseDao.class.php';

class ContactDao extends BaseDao
{

  /**
   * constructor of dao class
   */
  public function __construct()
  {
    parent::__construct("contacts");
  }

  public function get_user_contacts($user_id)
  {

    $query = "SELECT u.id, u.first_name, u.last_name, u.email, u.status
    FROM users u
    JOIN contacts c ON c.contact_id = u.id
    WHERE c.user_id = :user_id";

    return $this->query($query, ['user_id' => $user_id]);
  }
  public function get_user_contact_messages($user_id, $contact_id)
  {

    $query = "SELECT *
      FROM messages
      WHERE (sender_id = :user_id AND receiver_id = :contact_id) 
      OR (sender_id = :contact_id AND receiver_id = :user_id)
    ";

    return $this->query($query, ['user_id' => $user_id, 'contact_id' => $contact_id]);
  }

  public function add_friend($user_id, $contact_email)
  {


    $query = "INSERT INTO contacts (user_id, contact_id)
    SELECT :user_id, (SELECT id FROM users where email = :contact_email)
    WHERE 
      NOT (
        select exists(Select * from contacts where user_id = :user_id and contact_id = (SELECT id FROM users where email = :contact_email)) as isExists
        ) AND
        (
        :user_id != (SELECT id FROM users where email = :contact_email)
        );
        INSERT INTO contacts (user_id, contact_id)
    SELECT (SELECT id FROM users where email = :contact_email), :user_id
    WHERE 
      NOT (
        select exists(Select * from contacts where user_id = (SELECT id FROM users where email = :contact_email) and contact_id = :user_id) as isExists
        ) AND
        (
        (SELECT id FROM users where email = :contact_email) != :user_id
        );
        ";

    $result = $this->query_entity($query, ['user_id' => $user_id, 'contact_email' => $contact_email['email']]);
    // Flight::json(["message" => "Friend Added!"], 200);
    // return $result['id'];
    if ($result['id'] == "0") {
      throw new Exception('Friend Adding Unsuccessful!');
    } else {
      return ["message" => "Friend Added!"];
    }
  }

  public function get_by_id($id)
  {
    return $this->query_unique('SELECT n.*, DATE_FORMAT(n.created, "%Y-%m-%d") created FROM notes n WHERE n.id = :id', ['id' => $id]);
  }
}

?>