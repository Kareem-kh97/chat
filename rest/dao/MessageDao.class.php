<?php
require_once __DIR__ . '/BaseDao.class.php';

class MessageDao extends BaseDao
{

  /**
   * constructor of dao class
   */
  public function __construct()
  {
    parent::__construct("messages");
  }

  public function get_user_contact_messages($user_id, $contact_id)
  {

    $query = "SELECT *
      FROM messages
      WHERE (sender_id = :user_id AND receiver_id = :contact_id AND deleted_sender IS NULL) 
      OR (sender_id = :contact_id AND receiver_id = :user_id AND deleted_receiver IS NULL)
    ";

    return $this->query($query, ['user_id' => $user_id, 'contact_id' => $contact_id]);
  }

  public function sendmessage($messageData)
  {
    $query = "INSERT INTO messages (";
    foreach ($messageData as $column => $value) {
      $query .= $column . ", ";
    }
    $query = substr($query, 0, -2);
    $query .= ") VALUES (";
    foreach ($messageData as $column => $value) {
      $query .= ":" . $column . ", ";
    }
    $query = substr($query, 0, -2);
    $query .= ")";
    return $this->query_entity($query, $messageData);
  }

  public function softdelete($messageId)
  {
    $entity = [];
    $entity['messageId'] = $messageId;
    // Flight::json(["message" => "Deleting the message " . $messageId . " (dao)"], 404);
    $query = "UPDATE messages SET deleted_sender = now() WHERE id = :messageId";

    // $query = "UPDATE messages SET deleted_sender = now()  WHERE (id = :messageId)";

    $this->query_entity($query, $entity);
    Flight::json(["message" => "Deleted the message " . $messageId . " (dao)"], 200);
  }

  public function updatetext($text, $id)
  {
    $entity = [];
    $entity['text' . 'id'] = $text;
    $entity['id'] = $id;
    
    $query = "UPDATE messages SET text = :text WHERE id= :id";
    $this->query_entity($query, $entity);
    
    Flight::json(["text" => "Updated the text " . $text . " (dao)"], 200);
  }


//   public function updatetext($text, $id)
// {
//     $entity = [
//         'text' => $text
//     ];

//     $id_column = "id";
//     $query = "UPDATE messages SET ";
//     foreach($entity as $name => $value){
//         $query .= $name ."= :". $name. ", ";
//     }
//     $query = substr($query, 0, -2);
//     $query .= " WHERE ${id_column} = :id";

//     $this->update($id, $entity, $id_column);

//     Flight::json(["text" => "Updated the text " . $text . " (dao)"], 200);
// }


}

?>