<?php

class Model {
    
    public function __construct( array $params = array() ) {
        $properties = get_class_vars(get_class($this));
        foreach ($properties as $key => $value) {
            if (isset($params[$key])) {
                $this->$key = $params[$key];
            }            
        }
    }

}

?>