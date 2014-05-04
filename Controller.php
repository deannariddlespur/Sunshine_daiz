<?php

require_once "Model.User.php";
require_once "Model.Student.php";
require_once "Model.AccessLog.php";
require_once "Model.Classes.php";

$accessLogger = new AccessLogModel($_SERVER["REMOTE_ADDR"]);

switch ($_POST["action"]) {

 case "authenticate" :
 $theUser = new UserModel();
 $theUser -> Retrieve($_POST["userNameTextBox"]);

 if ($theUser -> validUser) {
 if ($theUser -> password == $_POST["passwordTextBox"]) {
    $accessLogger -> LogEntry($_POST["userNameTextBox"], $_POST["passwordTextBox"], "login", "success");
 require "View.Profile.html";
  } else {
  $accessLogger -> LogEntry($_POST["userNameTextBox"], $_POST["passwordTextBox"], "login", "fail");
   print "";
  }
  } else {
 if ($_REQUEST["conditionalCreateCheckBox"] == "1") {
 $theUser -> Create($_POST["userNameTextBox"], $_POST["passwordTextBox"]);
 if ($theUser -> validUser) {
 $accessLogger -> LogEntry($_POST["userNameTextBox"], $_POST["passwordTextBox"], "accountcreate", "success");
 require "View.Profile.html";
 } else {
 $accessLogger -> LogEntry($_POST["userNameTextBox"], $_POST["passwordTextBox"], "accountcreate", "fail");
  }
  } else {
  print "";
  }
  }
  break;

  case "loadProfile" :
  $theStudent = new StudentModel();
  $theStudent -> Retrieve($_POST["user"]);
  if ($theStudent -> validStudent) {
  $result -> data -> type = "student";
  $result -> data -> username = strval($theStudent -> username);
  $result -> data -> name = strval($theStudent -> studentName);
  $result -> data -> major = strval($theStudent -> major);
  } else {
  $theStudent -> Create($_POST["user"]);
  if ($theStudent -> validStudent) {
  $result -> data -> type = "student";
  $result -> data -> username = strval($theStudent -> username);
  $result -> data -> name = "";
  $result -> data -> major = "";
  } else {
  $result -> data -> username = "Invalid Student Detected.";
  }
  }
  echo json_encode($result);
  break;

  case "profile" :
  $username = $_POST["userNameTextBox"];
  $theStudent = new StudentModel();
  $theStudent -> Retrieve($username);
  if ($theStudent -> validStudent) {
  $saveRequired = false;
  if ($theStudent -> username != $username) {
  $theStudent -> username = $username;
  $saveRequired = true;
  }
  if ($theStudent -> studentName != $_POST["nameTextBox"]) {
  $theStudent -> studentName = $_POST["nameTextBox"];
  $saveRequired = true;
  }
  if ($theStudent -> major != $_POST["majorTextBox"]) {
  $theStudent -> major = $_POST["majorTextBox"];
  $saveRequired = true;
  }
  if ($saveRequired) {
  $theStudent -> Save();
  if ($theStudent -> validStudent == true) {
  require "View.ClassReg.html";
  } else {
  print "";
  }
  } else {
  require "View.ClassReg.html";
  }
  } else {
  echo "";
  }
  break;
  case "loadClassReg" :
  $username = $_POST["user"];
  $theStudent = new StudentModel();
  $theStudent -> Retrieve($username);

  $theClasses = new ClassesModel();
  $theClassList = $theClasses -> GetClassList();

  foreach ($theClassList as $class) {
   $full = (intval($class["actual"]) >= intval($class["limit"]));
   $registered = ($class["number"] == $theStudent -> class1 || $class["number"] == $theStudent -> class2 || $class["number"] == $theStudent -> class3);

 print "<div class='summaryBlock'><input ";
    if ($full && ($registered == false))
    print "disabled='disabled' ";
 
    if ($registered)
    print "checked='checked' ";
    print "type='checkbox' id='classCheckBox' name='classCheckBox' value='" . $class["number"] . "'/>";
    print "&nbsp;" . $class["number"] . "&nbsp;";
    print $class["name"];
    if ($full)
    print " (FULL)";
    print "</div>";
 }
   break;
   case "classReg" :
   $Student = new StudentModel();
   $Student -> Retrieve($_POST["user"]);
   $registeredClasses = explode("|", $_POST["classes"]);
   if ($Student -> class1 == $registeredClasses[0] && $Student -> class2 == $registeredClasses[1] && $Student -> class3 == $registeredClasses[2]) {
   require "View.Summary.html";
   return;
  }
  $Student -> class1 = $registeredClasses[0];
  $Student -> class2 = $registeredClasses[1];
  $Student -> class3 = $registeredClasses[2];
  $Student -> Save();
  if ($Student -> validStudent == false) {
// save has failed
  $accessLogger -> LogEntry($_POST["user"], "n/a", "register", "fail");
   print "";
   return;
  }
  $accessLogger -> LogEntry($_POST["user"], "n/a", "register", "success");
  $Classes = new ClassesModel();
  if ($Classes -> UpdateClassStudentCounts()) {
  require "View.Summary.html";
 } else {
   print "";
 }
  break;
  case "loadSummary" :
  $Student = new StudentModel();
  $classModel = new ClassesModel();
  $Student -> Retrieve($_POST["user"]);
  if ($Student -> validStudent) {
  $response -> message = "";
  $response -> Student = $Student;
  $response -> Class1Name = "";
  if ($Student -> class1 != "") {
  $classModel -> Retrieve($Student -> class1);
  $response -> Class1Name = strval($classModel -> className);
}
  $response -> Class2Name = "";
  if ($Student -> class2 != "") {
  $classModel -> Retrieve($Student -> class2);
  $response -> Class2Name = strval($classModel -> className);
  }
  $response -> Class3Name = "";
  if ($Student -> class3 != "") {
  $classModel -> Retrieve($Student -> class3);
  $response -> Class3Name = strval($classModel -> className);
  }
  } else {
  $response -> message = "Invalid student record detected.";
  }
  print json_encode($response);
  break;
  case "logout" :
  $accessLogger -> LogEntry($_POST["user"], "n/a", "logout", "success");
  require "View.Login.html";
  break;
  case "login" :
  default :
  require "View.Login.html";
  break;
}
?>