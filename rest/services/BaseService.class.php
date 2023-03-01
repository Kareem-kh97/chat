<?php
// we buidl basic CRUD operations
abstract class BaseService {
// it the business logic layer for our entities

  // in this class we can rap methods of 'dao'

// baseServices class where the CRUD operations can go up and ontop of the entity 
  protected $dao;
// this constructur accepts $dao parameter to be which is going to be initilized from 
// thie class which is going to extend this one
  public function __construct($dao){
    $this->dao = $dao;
  }

  public function get_all($user){
    return $this->dao->get_all();
  }

  public function get_by_id($user, $id){
    return $this->dao->get_by_id($id);
  }

  public function add($user, $entity){
    return $this->dao->add($entity);
  }

  public function update($user, $id, $entity){
    return $this->dao->update($id, $entity);
  }

  public function delete($user, $id){
    return $this->dao->delete($id);
  }
}
?>
