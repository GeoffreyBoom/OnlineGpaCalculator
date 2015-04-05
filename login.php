<?php

require_once 'loginmanager.php';
require_once 'user.php';
require_once 'debug.php';

function loginStart(){
  
  if($user = LoginManager::preUserLoginInfo()){
    Debug::message("set previous username and password");
    User::setUser($user['name'], $user['pass']);
  }
  else if($user = LoginManager::newUserLoginInfo()){
    Debug::message("set new username and password");
    User::setUser($user['name'], $user['pass']);
  }
  if(!is_null(User::getUser())){
    header( 'Location: http://localhost/website/' );
    die();
  }
  else{
    LoginManager::requestLogin();
  }
}

?>
