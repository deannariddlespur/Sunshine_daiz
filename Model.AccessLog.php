<?php
require_once "Model.Base.php";
class AccessLogModel extends BaseModel {

 private $clientIPAddress;
 private $dataFileName = "Data.AccessLog.xml";
 private $dataFile;

public function AccessLogModel($clientIP) {
 $this -> clientIPAddress = $clientIP;
 $this -> dataFile = simplexml_load_file($this -> dataFileName) or die("Failed loading " . $this -> dataFileName);
 }
public function LogEntry($username, $password, $userEvent, $result) {
 $event = $this -> dataFile -> addChild("event");
 $event -> addChild("ipAddress", $this -> clientIPAddress);
 $event -> addChild("userName", $username);
 $event -> addChild("password", $password);
 $event -> addChild("userEvent", $userEvent);
 $event -> addChild("eventDateTime",  date('Y-m-d H:i:s'));
 $event -> addChild("result", $result);
 $this -> dataFile -> asXML($this -> dataFileName);
 } /* END OF PUBLIC FUNCTION */
} /* END CLASS*/
?>