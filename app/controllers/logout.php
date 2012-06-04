<?php 

class logout {
    public $html;
    public function __construct($options) {
        
        $options['auth_handler']->logout();
        
        header("Location: http://".$_SERVER['HTTP_HOST']."/index.php");
        exit;
        
    }
}

?>