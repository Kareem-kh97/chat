<?php
// this class provides methods for interacting woth the created `users` table, to fetch a user by email, and insert a new user record
// it utilizes the functionallity provided by `BaseDao` class for executing database queries.
require_once __DIR__.'/BaseDao.class.php';
// 
class UserDao extends BaseDao{

  /**
  * constructor of dao class
  */
  // the class has a construcor that calls the parent constructur from `BaseDao` class
  // os it set the table-name for UserDao to `users`
  public function __construct(){
    parent::__construct("users");
  }
// queries the database to fetch us a user record based on the provided email.
// the query_unique is called from basedao to execute the query and return a single result. 
  public function get_user_by_email($email){
    return $this->query_unique("SELECT * FROM users WHERE email = :email", ['email' => $email]);
  }
// ---------------in short------------------------------
  // insertion a new user to the database
  // we are interaction with the `$entity` array
  // ggenerates the column names and placholder for the values in the query.

  // ---------------------details--------------------------------
  // the `signup` method in UserDao is responsible for inserting a new user record into the "Users" table
  // -the method recieves an `$entity` parameters, which an array contains user's data
  // -it starts constructing an sql query to perform insertion, starts with inser INTO followed by table-name "Users"
  // -the method iterates over the `$entity` array using `foreach` loop. For each key-value pair in the array, it appends the column name to the query string.
  // -after appeanding all column names the method removes the extra comma and space at the end of the query string using substr function with -2 as the length parameter.
  // -The method then appends the VALUES clause to the query string.
  // -It iterates over the $entity array again to append placeholders for the values using ":" followed by the column name.
  // -Finally, the method calls the query_entity method from the BaseDao class, passing the constructed query string and the $entity array as parameters
  // -The result of the query execution is returned by the query_entity method, and the signup method returns the same result.
  // -In summary, the signup method dynamically constructs an SQL query for inserting a new user by iterating over the $entity array and then executes the query using the query_entity method.
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
