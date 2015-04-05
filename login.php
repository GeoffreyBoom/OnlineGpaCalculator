<?php
error_reporting(-1);
ini_set('display_errors', 'On');

require_once 'loginmanager.php';
require_once 'user.php';

function loginStart(){
  if(isset(User::getUser())){
    //header( 'Location: http://localhost/website/' );
    //die();
  }
  if($user = LoginManager::preUserLoginInfo()){
    Debug::message("set previous username and password");
    setUser($user['name'], $user['pass']);
  }
  else if($user = LoginManager::newUserLoginInfo()){
    Debug::message("set new username and password");
    setUser($user['name'], $user['pass']);
  }
  else{
    LoginManager::requestLogin();
  }

}
loginStart();

?>
