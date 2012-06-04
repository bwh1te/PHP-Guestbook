<?php 

class newpost {
    public $html;
    public function __construct($options) {
        
        if (isset($_POST['ctext'])) {
            $db_adapter = new DbAdapter( unserialize(DB_SETTINGS) );
            $gb = new Guestbook( $db_adapter->getConnection() );
            if (!empty($options['user']) and !empty($options['user_id'])) {
                $user = $options['user'];
                $user_id = $options['user_id'];
            } else {
                $user = "Guest";
                $user_id = null;
            }
            $post = new Post( array( 'content'     => $_POST['ctext'],
                                     'author_name' => $user,
                                     'author_id'   => $user_id,
                                     'deleted'     => 0 ) );
            $gb->newPost(array($post)); 
        }

        header("Location: http://".$_SERVER['HTTP_HOST']."/index.php");
        exit;
        
    }
    
}

?>