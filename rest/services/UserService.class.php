<?php
require_once __DIR__.'/BaseService.class.php';
require_once __DIR__.'/../dao/UserDao.class.php';

class UserService extends BaseService{
//defines define constructur 
//that uses UserDao object which provide access to db methods
  public function __construct(){
    parent::__construct(new UserDao());
  }

  // $this: is responsible for the current instance of the class. allows me to access the object properties or methods within its own class.
  //for creating a new user account  
  // 1.we defined a signup methodd that accept an $entity as a parameter
  // 2.within this method we are callin the signup() method from the `dao` object which responsible for performing the actual logic to signup a user.
  // 3.the result of the signup method of the dao object is then returned from the signup method of the current class.

public function signup($entity){
    return $this->dao->signup($entity);
  }

}
?>
