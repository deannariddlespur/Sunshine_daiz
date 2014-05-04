<?php
require_once "Model.Base.php";
class UserModel extends BaseModel {
    
 public $password;
 public $username;
 public $validUser;

 private $dataFileName = "Data.Users.xml";
 private $dataFile;
 public function UserModel() {
 $this -> dataFile = simplexml_load_file($this -> dataFileName) or die("Failed loading " . $this -> dataFileName);
}
 public function Retrieve($username) {
 foreach ($this->dataFile->user as $user) {
 if ($user -> username == $username) {
 $this -> password = $user -> password;
 $this -> username = $user -> username;
 $this -> validUser = true;
 return;
}
}
 $this -> validUser = false;
}
 public function Create($username, $password) {
 $user = $this -> dataFile -> addChild("user");
 $user -> addChild("username", $username);
 $user -> addChild("password", $password);

 $this -> dataFile -> asXML($this -> dataFileName);
 $this -> validUser = true;
}
}
