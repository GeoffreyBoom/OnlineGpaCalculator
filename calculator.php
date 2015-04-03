<?php
class calculator{
  static function calculateGpa(){
    $gpaArray = Database::getGpaArray();
    if(!$gpaArray){
      return 0;
    }
    $sumGrades = 0;
    $sumCredits = 0;
    foreach($gpaArray as $gpa){
      if($gpa){
        $sumGrades += $gpa->credit * calculator::getGpaByGrade($gpa->grade);
        $sumCredits+= $gpa->credit;
      }
    }
    return $sumGrades / $sumCredits;
  }
  static function getGpaByGrade($grade){
    switch($grade){
      case"a+":
      case"A+":
        return 4.3;
        break;
      case"a":
      case"A":
        return 4.0;
        break;
      case"a-":
      case"A-":
        return 3.7;
        break;   
      case"b+":
      case"B+":
        return 3.3;
        break;
      case"b":
      case"B":
        return 3.0;
        break;
      case"b-":
      case"B-":
        return 2.7;
        break;   
      case"c+":
      case"C+":
        return 2.3;
        break;
      case"c":
      case"C":
        return 2.0;
        break;
      case"c-":
      case"C-":
        return 1.7;
        break;   
      case"d+":
      case"D+":
        return 1.3;
        break;
      case"d":
      case"D":
        return 1.0;
        break;
      case"d-":
      case"D-":
        return 0.7;
        break;
      case"F":
      case"f":
        return 0;
        break;
    }
  }
}
?>
