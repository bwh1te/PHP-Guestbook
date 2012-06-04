<?php

function readConfigFromFile($file_name) {
    
    if (file_exists($file_name)) {
        $lines = file($file_name);
        $options = array();
        foreach ($lines as $line) {
            $line_list = explode("=", $line);
            $key = trim($line_list[0]);
            $value = trim($line_list[1]);
            $options[$key] = $value;
        }
        return $options;        
    } else {
        return null;
    }

}

?>