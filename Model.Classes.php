<?php

require_once "Model.Base.php";

class ClassesModel extends BaseModel {

    public $classNumber;
    public $className;
    public $classLimit;
    public $classActual;
    public $validClass;

    private $dataFileName = "Data.Classes.xml";
    private $dataFile;

    public function ClassesModel() {
        $this -> dataFile = simplexml_load_file($this -> dataFileName) or die("Failed loading " . $this -> dataFileName);
    }

    public function GetFormattedClassName($classname) {
        return str_replace("Ii", "II", str_replace("To", "to", str_replace("And", "and", str_replace("Php", "PHP", ucwords(strtolower($classname))))));
    }

    public function Retrieve($classnumber) {
        foreach ($this->dataFile->class as $class) {
            if ($class -> number == $classnumber) {
                $this -> classNumber = $class -> number;
                $this -> className = $this->GetFormattedClassName($class -> name);
                $this -> classLimit = $class -> limit;
                $this -> classActual = $class -> actual;
                $this -> validClass = true;
                return;
            }
        }
        $this -> validClass = false;
    }

    function GetClassList() {
        $returnvalue = array();
        $i = 0;
        
        foreach ($this->dataFile->class as $class) {
            $returnvalue[$i]["number"] = $class -> number;
            $returnvalue[$i]["name"] = $this->GetFormattedClassName($class -> name);
            $returnvalue[$i]["limit"] = $class -> limit;
            $returnvalue[$i]["actual"] = $class -> actual;
            $i++;
        }

        return $returnvalue;
    }

    public function UpdateClassStudentCounts() {

        // load the Student data file.
        $studentDataFile = simplexml_load_file("Data.Students.xml") or die("Failed loading Data.Students.xml.");

        // this circuit takes a live count from the Students data file of how many students are registered
        // for a given course, then stores that amount in the Actual field.  It is more efficient to simply
        // save a live count than to try to do a live running count as the user makes changes.
        foreach ($this->dataFile->class as $class) {
            $xpathQuery = "//classTaken[text() = '" . $class -> number . "']";
            $countresult = $studentDataFile -> xpath($xpathQuery);
            $class -> actual = count($countresult);
        }

        // save Classes data file.
        $this -> dataFile -> asXML($this -> dataFileName);

        return true;
    }

}
?>