<?php 

class ban {
    public $html;
    public function __construct($options) {

        if (!empty($options['bad_user_id'])) {
            $options['auth_handler']->banUser($options['bad_user_id']);
        }
        
        header("Location: http://".$_SERVER['HTTP_HOST']."/index.php");
        exit;
        
    }
}

?>