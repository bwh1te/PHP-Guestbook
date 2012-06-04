<?php 

class login {
    public $html;
    public function __construct($options) {
        
        header("Location: http://".$_SERVER['HTTP_HOST']."/index.php");
        exit;
        
    }
    
}

?>