<?php

class GPA{
  function __construct($course, $credit, $grade){
    $this->course = $course;
    $this->credit = $credit;
    $this->grade  = $grade;
  }
  function toString(){
    return "$this->course, $this->credit, $this->grade";
  }
  function toNode(){
    $node = new DOMDocument();
    

    $node->loadHTML("<tr id=\"gpa$this->id\" class=\"gpadata\">
        <td class=\"course\">$this->course</td>
        <td class=\"credit\">$this->credit</td>
        <td class=\"grade\" >$this->grade </td>
        <input type=\"button\" value=\"remove\" name=\"remove\"></input>
      </tr>");
    return $node;
  }
  function addToDocument(){
    
  }
}
function loadDocument(){
  $document = new DOMDocument();
  $document->loadHTMLFile("gpapage.html");
  return $document;
}
function displayDocument($document){
  echo $document->saveHTML();
}
?>
<script>
function GPA(course, credit, grade){ 
  this.toString = function(){
    return this.course + "," + this.credit + "," + this.grade
  }
  this.toNode = function(){
    var node = document.createElement("tr");
    node.id = "gpa" + this.id;
    node.className = "gpadata";

    var course = document.createElement("td");
    var credit = document.createElement("td");
    var grade  = document.createElement("td");

    var button = document.createElement("input");
    button.type="button";
    button.value="remove";
    button.name = this.id;
    button.onclick= function() {removeGpa(this.name)};
    
    course.className = "course";
    credit.className = "credit";
    grade.className  = "grade";

    course.innerHTML = this.course;
    credit.innerHTML = this.credit;
    grade.innerHTML  = this.grade;

    node.appendChild(course);
    node.appendChild(credit);
    node.appendChild(grade);
    node.appendChild(button);

    return node;
  }
  this.parse = function(gpaNode){
    var coursevar = gpaNode.getElementsByClassName("course")[0].innerHTML;
    var creditvar = parseInt(gpaNode.getElementsByClassName("credit")[0].innerHTML);
    var gradevar = gpaNode.getElementsByClassName("grade")[0].innerHTML;
    return this.populate(coursevar,creditvar,gradevar);
  }
  this.populate = function(coursevar, creditvar, gradevar){ 
    if(arguments.length == 3){
      if(!this.verifyCourse(coursevar)){
        return false;
      }
      if(!this.verifyCredit(creditvar)){
        return false;
      }
      if(!this.verifyGrade(gradevar)){
        return false;
      }
      this.course=coursevar;
      this.credit=creditvar;
      this.grade=gradevar;
      return true;
    }
  }
  this.verifyCourse = function(course){
    if(course.length < 15){
      return course;
    }
    else{
      confirm(course + ": is too long");
      return false;
    }
  }

  this.verifyCredit = function(credit){
    if(credit > 0 && credit < 10){
      return credit;
    }
    else{
      confirm(credit + ": is not valid");
      return false;
    }
  }
  
  this.verifyGrade = function(grade){
    var potentialGrades = /^(([a-d]|[A-D])(\+|\-)?|F|f)$/;
    if(potentialGrades.test(grade)){
      return potentialGrades.exec(grade);
    }
    else{
      confirm(grade + ": is not valid");
      return false;
    }
  }
  this.validate = function(){
    return (this.verifyCourse(this.course) && this.verifyCredit(this.credit) && this.verifyGrade(this.grade));
  }
  if(arguments.length == 3){
    this.id = GPA.numGPA++;
    this.populate(course,credit,grade);
  }
}

GPA.numGPA = 0;
  
//retrieves GPA data and adds it to the Data Field
function addGpaData(){
  if(gpaData = getNewGpaData()){
    insertGpaData(gpaData.toNode());
  }
}

//retrieves GPA data from the gpaForm and returns a GPA object
function getNewGpaData(){
  var form = getFormField();
  var gpaData= new GPA(
        form.course.value, 
        form.credits.value, 
        form.grade.value);
  if(!gpaData.validate()){
    return false;
  }
  form.course.value = form.credits.value = form.grade.value = "";
  return gpaData;
}

//appends a dom object representing the gpa object as a child to the DataField
function insertGpaData(gpaObject){
  var gpaData = getDataField();
  gpadata.appendChild(gpaObject);
  displayGpa();
}

//Gets current data from the Document, calculates the gpa, and displays it.
function displayGpa(){
  var currdata = (getCurrentGpaData());
  var gpa = calculateGpa(currdata);  
  getDisplayField().innerHTML = gpa.toString().slice(0,4);
}

//gets the current GPA data and returns it as an array of GPA objects
function getCurrentGpaData(){
  var gpaData = [];
  var columns = [];
  var rows = getDataField().getElementsByClassName("gpadata");
  for(var i = 0; i < rows.length; i++){
    row = rows[i];
    if (row != ""){
      var data = new GPA();
      data.parse(row)
      gpaData.push(data);
    }
  }
  return gpaData;
}

//takes in GPA objects and calculates the gpa.
function calculateGpa(gpaData){
  var gradepointsum = 0;
  var creditsum = 0;
  for(var data = 0; data < gpaData.length; data++){
    gradepointsum += gpaData[data].credit * getGradeValue(gpaData[data].grade);
    creditsum += gpaData[data].credit;
  }
  if(creditsum != 0){
    return gradepointsum / creditsum;
  }
  else{
    return 0;
  }
}

//removes all GPA data and resets the display field
function removeAll(){
  var data = getDataField().getElementsByClassName("gpadata"); 
  var num = data.length;
  for(var i = 0; i<num; i++){
    getDataField().removeChild(data[0]);
  }
  displayGpa();
}

function removeGpa(id){
  var gpaToRemove = document.getElementById("gpa" + id);
  getDataField().removeChild(gpaToRemove);
  displayGpa();
}

function getDataField(){
  return document.getElementById("gpadata");
}
function getDisplayField(){
  return document.getElementById("gpafield");
}
function getFormField(){
  return document.getElementById("gpaform");
}

function getGradeValue(grade){
  switch(grade){
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
</script>
