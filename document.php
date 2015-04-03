class document{
  static $document;
  static function loadDocument(){
    if(isset(self::$document)){
      return self::$document;
    }
    else{
      self::$document = new DOMDocument();
      self::$document->loadHTMLFile("gpapage.html");
      return self::$document;
    }
  }
 
  static function addAllGPA(){
    $data = Database::getGpaArray();
    foreach($data as $gpa){
      document::addToGPA($gpa->toNode());
    }
    document::addToGPA(document::createRemoveButton());
  }
  static function createRemoveButton(){
    $doc = document::loadDocument();
    $removePanel  = $doc->createElement("tr");
    $removeAll    = $doc->createElement("td", "Remove All");
    $blank1       = $doc->createElement("td");
    $blank2       = $doc->createElement("td");
    $buttontd     = $doc->createElement("td");
    $button       = $doc->createElement("input"); 
    $button->setAttribute("type",  "checkbox"); 
    $button->setAttribute("value", "all"); $button->setAttribute("name", "checkbox");
    $buttontd->appendChild($button);
    $removePanel->appendChild($removeAll);
    $removePanel->appendChild($blank1);
    $removePanel->appendChild($blank2);
    $removePanel->appendChild($buttontd);
    return $removePanel;
  }

  static function displayDocument($document){
    echo self::$document->saveHTML();
  }
  
  static function addToGPA($gpa){
    $doc = document::loadDocument();
    $gpaTable = $doc->getElementById("gpadata");
    $gpaTable->appendChild($gpa);
  }
  static function setTotal($gpa){
    $doc = document::loadDocument();
    $gpaTotal = $doc->getElementById("gpafield");
    $gpaTotal->appendChild($doc->createElement("p", "$gpa"));
  }
  static function handleInput(){
    $gpa = null;
    if ($_SERVER["REQUEST_METHOD"] == "POST") {
      if(isset($_POST["Add"])){
        $course = $_POST["course"]; $credit = $_POST["credits"]; $grade = $_POST["grade"];
        if($course = GPA::verifyCourse($course)){
          if($credit = GPA::verifyCredit($credit)){
            if($grade = GPA::verifyGrade($grade)){
              $gpa = new GPA($course, $credit, $grade);
            }
          }
        }
      }
      else if(isset($_POST["Remove"])){
        foreach($_POST as $type=>$box){
          if($type=="checkbox"){
            if($box == "all"){
              Database::resetGpaArray();
            }
            else{
              Database::removeGpa((int)$box);
            }
          }
        }
      }
    }
    if($gpa){
      Database::addToGPA($gpa);
    }
  }
  static function gradeError($display){
    //<td class="error"><img src="img/dogeintensifies.gif">hello</td>    
    if($display){
      $error = document::createError("Grades must be between A-D or F");
    }
    else{
      $error = document::loadDocument()->createElement("td");
    }
    $field = document::loadDocument()->getElementById("errorfield");
    $field->appendChild($error);
  }
  static function courseError($display){

    if($display){
      $error = document::createError("Course name is too long");
    }
    else{
      $error = document::loadDocument()->createElement("td");
    }
    $field = document::loadDocument()->getElementById("errorfield");
    $field->appendChild($error);

  }
  static function creditError($display){
    if($display){
      $error = document::createError("Credits must be between 0-9");
    }
    else{
      $error = document::loadDocument()->createElement("td");
    }
    $field = document::loadDocument()->getElementById("errorfield");
    $field->appendChild($error);
  }
  static function createError($message){
    $doc = document::loadDocument();
    $column = $doc->createElement("td");    $column->setAttribute("class", "error");
    $doge   = $doc->createElement("img");   $doge->setAttribute("src", "img/dogeintensifies.gif"); 
    $paragraph = $doc->createElement("p", $message);
    $column->appendChild($doge);
    $column->appendChild($paragraph);
    return $column;
  }
}
