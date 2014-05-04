<?php
require_once "Model.Base.php";
class StudentModel extends BaseModel {

 public $username;
 public $studentName;
 public $major;
 public $validStudent;
 public $class1;
 public $class2;
 public $class3;

 private $dataFileName = "Data.Students.xml";
 private $dataFile;
 public function StudentModel() {
 $this -> dataFile = simplexml_load_file($this -> dataFileName) or die("Failed loading " . $this -> dataFileName);
 }
public function Create($username) {
 $user = $this -> dataFile -> addChild("student");
 $user -> addChild("username", $username);
 $user -> addChild("major", "");
 $user -> addChild("name", "");
 $this -> username = $username;
 $this -> major = "";
 $this -> studentName = "";

  $classesTaken = $user -> addChild("classesTaken");
  $classTaken0 = $classesTaken -> addChild("classTaken");
  $classTaken1 = $classesTaken -> addChild("classTaken");
  $classTaken2 = $classesTaken -> addChild("classTaken");

 $this -> dataFile -> asXML($this -> dataFileName);
 $this -> validStudent = true;
 }
 public function Retrieve($username) {
 foreach ($this->dataFile->student as $student) {
 if ($student -> username == $username) {
 $this -> username = strval($student -> username);
 $this -> major = strval($student -> major);
 $this -> studentName = strval($student -> name);
 $this -> class1 = strval($student -> classesTaken -> classTaken[0]);
 $this -> class2 = strval($student -> classesTaken -> classTaken[1]);
 $this -> class3 = strval($student -> classesTaken -> classTaken[2]);
 $this -> validStudent = true;
 return;
 }
 }
 $this -> validStudent = false;
 }
 public function Save() {
 $returnvalue = "";
 foreach ($this->dataFile->student as $student) {
 if ($student -> username == $this -> username) {
   $student -> major = $this -> major;
   $student -> name = $this -> studentName;
   $student -> classesTaken -> classTaken[0] = $this -> class1;
   $student -> classesTaken -> classTaken[1] = $this -> class2;
   $student -> classesTaken -> classTaken[2] = $this -> class3;
 if ($this -> dataFile -> asXML($this -> dataFileName)) {
     $this -> validStudent = true;
     } else {
     $this -> validStudent = false;
 }
 return;
 }
 }
 $this -> validStudent = false;

 return $returnvalue;
 }
}
