<?php
class GPA{
  function __construct($course, $credit, $grade){
    $this->id = Database::getGpaArraySize();
    $this->course = $course;
    $this->credit = $credit;
    $this->grade  = $grade;
  }
  function toString(){
    return "$this->course, $this->credit, $this->grade";
  }
  function toNode(){
    $doc = document::loadDocument();
    $node = $doc->createElement("tr"); $node->setAttribute("class", "gpadata"); $node->setAttribute("id", "gpa$this->id");
    $course = $doc->createElement("td", "$this->course"); $course->setAttribute("class", "course"); 
    $credit = $doc->createElement("td", "$this->credit"); $credit->setAttribute("class", "credit"); 
    $grade  = $doc->createElement("td", "$this->grade" ); $grade ->setAttribute("class", "grade" );
    $buttonpan = $doc->createElement("td");
    $button = $doc->createElement("input"); 
    $button->setAttribute("type",  "checkbox"); $button->setAttribute("name", "checkbox");
    $button->setAttribute("value", "$this->id");   
    $buttonpan->appendChild($button);
    $node->appendChild($course); $node->appendChild($credit); $node->appendChild($grade);
    $node->appendChild($buttonpan);
    return $node;
  }
  static function verifyCourse($course){
    if(strlen($course) < 15){
      document::courseError(false);
      return $course;
    }
    else{
      document::courseError(true);
      return false;
    }
  }

  static function verifyCredit($credit){
    if( (int)$credit > 0 && (int)$credit < 10){
      document::creditError(false);
      return (int) $credit;
    }
    else{
      document::creditError(true);
      return false;
    }
  }
  
  static function verifyGrade($grade){
    $potentialGrades = "/^(([a-d]|[A-D])(\+|\-)?|F|f)$/";
    if(preg_match($potentialGrades, $grade, $matches)){
      document::gradeError(false);
      return $matches[0];
    }
    else{
      document::gradeError(true);
      return false;
    }
  } 
}
?>
