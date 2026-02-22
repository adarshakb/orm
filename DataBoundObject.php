<?php
require_once 'Database.php';

/**
 * Data Bound Object ORM
 *
 * ORM (Object relation mapping) base class. Extend this class per table.
 */
abstract class DataBoundObject {
    protected $ID; // Stores primary/composite keys
    protected $AutoIncrementFeild; // Auto-increment field name
    protected $strTableName; // Table name
    protected $arRelationMap; // table_field => class_property
    protected $blForDeletion;
    protected $blIsLoaded;
    protected $arModifiedRelations;

    abstract protected function DefineTableName();
    abstract protected function DefineRelationMap();
    abstract protected function DefineID();
    abstract protected function DefineAutoIncrementField();

    protected function loadIDs() {
        if (count($this->ID) > 0) {
            foreach ($this->ID as $key => $clause) {
                $field = $clause['field'];
                if (isset($this->arRelationMap[$field])) {
                    $member = $this->arRelationMap[$field];
                    if (property_exists($this, $member)) {
                        $this->ID[$key]['value'] = $this->{$member};
                    }
                }
            }
        }
    }

    protected function isIDDefined() {
        if (count($this->ID) > 0) {
            foreach ($this->ID as $clause) {
                if (!isset($clause['value']) || $clause['value'] === null) {
                    return false;
                }
            }
        }
        return true;
    }

    public function __construct(array $idVals = array()) {
        $this->strTableName = $this->DefineTableName();
        $this->arRelationMap = $this->DefineRelationMap();
        $this->blIsLoaded = false;
        $this->blForDeletion = false;
        $this->ID = array();

        $tmp = $this->DefineID();
        for ($i = 0; $i < count($tmp); $i++) {
            $this->ID[$i]['field'] = $tmp[$i];
        }
        for ($j = 0; $j < count($idVals); $j++) {
            $this->ID[$j]['value'] = $idVals[$j];
        }

        $this->AutoIncrementFeild = $this->DefineAutoIncrementField();
        $this->arModifiedRelations = array();

        if ($this->isIDDefined()) {
            $this->Load(true);
        }
    }

    public function markForDeletion() {
        $this->blForDeletion = true;
    }

    public function __destruct() {
        if ($this->isIDDefined() && $this->blForDeletion === true) {
            $strQuery = 'DELETE FROM ' . $this->strTableName . ' WHERE ';
            $params = array();
            foreach ($this->ID as $clause) {
                $strQuery .= $clause['field'] . ' = ? AND ';
                $params[] = $clause['value'];
            }
            $strQuery = substr($strQuery, 0, strlen($strQuery) - 4);
            $args = array_merge(array($strQuery), $params);
            $result = call_user_func_array(array('Database', 'updateQuery'), $args);

            if ($result != true) {
                throw new Exception('Deletion of the line failed!');
            }
        }
    }

    public function Load($fromConstructor = false) {
        if (!$this->isIDDefined()) {
            throw new Exception('Complete IDs are not defined');
        }

        $strQuery = ' SELECT ';
        foreach ($this->arRelationMap as $key => $value) {
            $strQuery .= $key . ',';
        }
        $strQuery = substr($strQuery, 0, strlen($strQuery) - 1);
        $strQuery .= ' FROM ' . $this->strTableName . ' WHERE ';

        $params = array();
        foreach ($this->ID as $clause) {
            $field = $clause['field'];
            $strQuery .= $field . ' = ? AND ';
            if ($fromConstructor) {
                $params[] = $clause['value'];
            } else {
                $member = $this->arRelationMap[$field];
                $params[] = $this->{$member};
            }
        }
        $strQuery = substr($strQuery, 0, strlen($strQuery) - 4);

        $args = array_merge(array($strQuery), $params);
        $result = call_user_func_array(array('Database', 'query'), $args);
        $row = $result->fetch();

        if (!$row) {
            throw new Exception(' Could not load the required ID/IDs as row not found ');
        }

        foreach ($row as $key => $value) {
            if (isset($this->arRelationMap[$key])) {
                $strMember = $this->arRelationMap[$key];
                if (property_exists($this, $strMember)) {
                    $this->{$strMember} = $value;
                }
            }
        }

        $this->blIsLoaded = true;
    }

    public function populateData(array $row) {
        foreach ($row as $key => $value) {
            if (isset($this->arRelationMap[$key])) {
                $strMember = $this->arRelationMap[$key];
                if (property_exists($this, $strMember)) {
                    $this->{$strMember} = $value;
                }
            }
        }
        $this->blIsLoaded = true;
        $this->loadIDs();
    }

    public function insert($loadAfterInsert = false) {
        $strQuery = 'INSERT INTO ' . $this->strTableName . ' (';
        $dataToBeChanged = array();

        foreach ($this->arRelationMap as $key => $value) {
            $actualVal = $this->{$value};
            if (isset($actualVal) && array_key_exists($value, $this->arModifiedRelations)) {
                $strQuery .= $key . ',';
                $dataToBeChanged[] = $actualVal;
            }
        }

        $strQuery = substr($strQuery, 0, strlen($strQuery) - 1);
        $strQuery .= ') VALUES (';
        for ($i = 0; $i < count($dataToBeChanged); $i++) {
            $strQuery .= '?, ';
        }
        $strQuery = substr($strQuery, 0, strlen($strQuery) - 2);
        $strQuery .= ')';

        $args = array_merge(array($strQuery), $dataToBeChanged);
        $result = call_user_func_array(array('Database', 'updateQuery'), $args);

        if ($result === false) {
            throw new Exception('The insertion failed!');
        }

        foreach ($this->ID as $key => $clause) {
            $field = $clause['field'];
            if ($field == $this->AutoIncrementFeild) {
                $this->ID[$key]['value'] = Database::getLastInsertId();
                $member = $this->arRelationMap[$field];
                $this->{$member} = $this->ID[$key]['value'];
                break;
            } else {
                $member = $this->arRelationMap[$field];
                $this->ID[$key]['value'] = $this->{$member};
            }
        }

        if ($loadAfterInsert) {
            $this->Load();
        }

        return $result;
    }

    public function save($loadAfterSave = false) {
        $result = false;
        $dataToBeChanged = array();

        if (count($this->arModifiedRelations) > 0) {
            if (!$this->isIDDefined()) {
                throw new Exception('Didnot save as not all ID defined');
            }

            $strQuery = 'UPDATE ' . $this->strTableName . ' SET ';
            foreach ($this->arRelationMap as $key => $value) {
                if (array_key_exists($value, $this->arModifiedRelations)) {
                    $strQuery .= $key . ' = ? ,';
                    $dataToBeChanged[] = $this->{$value};
                }
            }
            $strQuery = substr($strQuery, 0, strlen($strQuery) - 1);
            $strQuery .= ' WHERE ';

            foreach ($this->ID as $clause) {
                $strQuery .= $clause['field'] . ' = ? AND ';
                $dataToBeChanged[] = $clause['value'];
            }
            $strQuery = substr($strQuery, 0, strlen($strQuery) - 4);

            $args = array_merge(array($strQuery), $dataToBeChanged);
            $result = call_user_func_array(array('Database', 'updateQuery'), $args);

            $this->arModifiedRelations = array();

            if ($loadAfterSave) {
                $this->Load();
            }
        }

        return $result;
    }

    public function __call($strFunction, $arArguments) {
        $strMethodType = substr($strFunction, 0, 3);
        $strMethodMember = substr($strFunction, 3);

        switch ($strMethodType) {
            case 'set':
                return $this->SetAccessor($strMethodMember, $arArguments[0]);
            case 'get':
                return $this->GetAccessor($strMethodMember);
            default:
                throw new Exception("Non existent method $strFunction call dude!");
        }
    }

    public function SetAccessor($strMember, $strNewValue) {
        if (!property_exists($this, $strMember)) {
            throw new Exception("Property $strMember doesnt exist!");
        }

        $this->{$strMember} = $strNewValue;
        $this->arModifiedRelations[$strMember] = true;
    }

    public function GetAccessor($strMember) {
        if ($this->blIsLoaded != true) {
            $this->Load();
        }

        if (!property_exists($this, $strMember)) {
            throw new Exception("Property $strMember doesnt exist!");
        }

        return $this->{$strMember};
    }
}
?>
