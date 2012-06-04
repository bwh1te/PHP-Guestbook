<?php 

class delete {
    public $html;
    public function __construct($options) {
        
        if (!empty($options['post_id']) and !empty($options['user_id'])) {
            $db_adapter = new DbAdapter( unserialize(DB_SETTINGS) );
            $gb = new Guestbook( $db_adapter->getConnection() );
            $gb->deletePostBy($options['post_id'], $options['user_id']);            
        }
        
        header("Location: http://".$_SERVER['HTTP_HOST']."/index.php");
        exit;
        
    }
    
}

?>