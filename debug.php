<?php

class Debug{
  static $debug = true;
  static function message($message){
    if(Debug::$debug) print $message."<br>";
  }
}


if(Debug::$debug){
  error_reporting(-1);
  ini_set('display_errors', 'On');
}
?>
