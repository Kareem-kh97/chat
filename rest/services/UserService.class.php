<?php
require_once __DIR__.'/BaseService.class.php';
require_once __DIR__.'/../dao/UserDao.class.php';

class UserService extends BaseService{
//defines define constructur 
//uses UserDao object which provide access to db methods
  public function __construct(){
    parent::__construct(new UserDao());
  }
//for creating a new user account  
  public function signup($entity){
    return $this->dao->signup($entity);
  }

}
?>
