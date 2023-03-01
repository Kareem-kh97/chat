<?php
require_once __DIR__.'/BaseDao.class.php';

class UserDao extends BaseDao{

  /**
  * constructor of dao class
  */
  public function __construct(){
    parent::__construct("users");
  }

  public function get_user_by_email($email){
    return $this->query_unique("SELECT * FROM users WHERE email = :email", ['email' => $email]);
  }

  
  public function signup($entity){
    $query = "INSERT INTO users (";
    foreach ($entity as $column => $value) {
      $query .= $column . ", ";
    }
    $query = substr($query, 0, -2);
    $query .= ") VALUES (";
    foreach ($entity as $column => $value) {
      $query .= ":" . $column . ", ";
    }
    $query = substr($query, 0, -2);
    $query .= ")";
    return $this->query_entity($query, $entity);
  }
}
?>
