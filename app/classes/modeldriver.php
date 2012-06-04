<?php
/**
 * Object-to-DB and DB-to-object translator.
 *
 * @package guestbook
 * @author Alexander Belyaev
 **/
class Modeldriver {
    
    protected $db;
    public $result_array;
    public $total_records;

    /**
     * Modeldriver
     *
     * @param object PDO connection
     * @return void
     **/
    public function __construct($db_connection_handler) {
        $this->db = $db_connection_handler;
    }
    
    /**
     * Try to authorize with predefined login and password
     *
     * @param string Class name
     * @return string Table name where class objects stores
     **/
    protected function getTableName($for_object) {
        return strtolower($for_object)."s";
        
    }
    
    /**
     * Count records returned by query before delimiting by limit keyword
     *
     * @param string SQL query
     * @return integer Count of records
     **/
    protected function countAll($counter) {
        $counter = str_replace("*", "count(*)", $counter);
        $result = $this->db->query($counter);
        if($result != null) {
            return (int) $result->fetchColumn();  
        } else {
            return 0;
        }
    } 

    /**
     * Get the objects from DB. Model must be defined in models/model_name.php
     *
     * @param string What class of objects we want to get
     * @param array Parameters of query like 'author' => 'foobar'
     * @param boolean Records order. Ascending by default.
     * @return array Array of objects or empty array
     **/
    public function getRecords($object_name, array $conditions = array(), $order_by_asc = True) {
        $object_name = ucfirst($object_name);
        $this->result_array = null;
        
        // Create SQL query on parameters from $conditions array
        $sql = "select * from ".$this->getTableName($object_name);
        if (!empty($conditions)) {
            $start_from = 0; $stop_at = 0;
            if(isset($conditions['offset'])) {
                $start_from = $conditions['offset'];
                unset($conditions['offset']); 
            }
            if(isset($conditions['limit'])) {
                $stop_at = $conditions['limit'];
                unset($conditions['limit']);
            }
            $has_parameters = 0;
            $last_key = end(array_keys($conditions));
            foreach ($conditions as $field => &$value) {
                if ($has_parameters == 0) $sql .= " where";
                $sql .= " ".$field." = '".$value."' ";
                if ($field != $last_key) $sql .= "and";
                $has_parameters += 1;
            }            
            // Count all records before limiting
            $this->total_records = $this->countAll($sql.";");
            $sql .= (!$order_by_asc) ? " order by id desc" : "" ;
            if ($start_from > 0 or $stop_at > 0) $sql .= " limit ".$start_from.",".$stop_at;
            $sql .= ";" ;
        }
        
        // Execute query
        //print $sql."\n";
        $result = $this->db->query($sql);
        if($result != null) {
            $this->result_array = $result->fetchAll();
        }
        
        // Generate object for each DB record
        $result_objects = array();
        foreach ($this->result_array as $entry) {
            $result_objects[] = new $object_name($entry);
            
        }
        
        return $result_objects;
        
    }
    
    /**
     * Saving objects in DB. Model must be defined in models/model_name.php
     *
     * @param array Array of objects
     * @return array Array of boolean results of saving objects
     **/
    public function newRecords(array $objects_to_save = array()) {
        $results = array();
        $this->result_array = null;
        
        foreach ($objects_to_save as $entry) {
            $object_name = get_class($entry);
            $properties = array_filter(get_object_vars($entry), function($x){return !empty($x);});
            if (!empty($properties)) {
                foreach ($properties as &$field) {
                    $field = $this->db->quote($field);                    
                }
                $sql  = "insert into ".$this->getTableName($object_name);
                $sql .= " (".implode(",", array_keys($properties)).") ";
                $sql .= "values";
                $sql .= " (".implode( ",", $properties ).")";

                try {  
                    $this->db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
                                 
                    $this->db->beginTransaction();
                    $result = $this->db->exec($sql);                    
                    $this->db->commit();
                    
                    $results[] = ($result > 0) ? True : False;
                } catch (Exception $e) {
                    $this->db->rollBack();
                    $results[] = False;
                    echo "Failed: " . $e->getMessage();
                }
               
            } else {
                $results[] = False;
            }
        }
        
        $this->result_array = $results;
        return $this;
        
        
    }

    /**
     * Visualize the output of newRecords function
     *
     * @return void But prints messages corresponding of newRecords results
     **/
    public function printStatus() {  
        foreach ($this->result_array as $result_item) {
            if (empty($result_item) or $result_item == False) {
                print "Query status: FAIL\n";
            } else {
                print "Query status: OK\n";
            }
        }
    }
    
    /**
     * Result of saving objects
     *
     * @return boolean True if all is ok.
     **/
    public function getStatus() {  
        return array_reduce($this->result_array, function($x, $y) {return (is_null($x) ? true : $x) && $y;});
    }
    

}

?>
