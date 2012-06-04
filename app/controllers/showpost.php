<?php 

class showpost {
    public $html;
    public function __construct($options) {
        
        $db_adapter = new DbAdapter( unserialize(DB_SETTINGS) );
        $gb = new Guestbook( $db_adapter->getConnection() );
        
        $gb->posts_per_page = POSTS_PER_PAGE;
        if ( !isset($options['page']) ) {
            $options['page'] = 1;
        }
        $data = $gb->getPostsFromPage( $options['page'] );
        //$data = $gb->getPosts();
        
        $page = new Page( 'templates/showpost.html', array('posts'         => $data, 
                                                           'pages_count'   => $gb->getPagesCount(), 
                                                           'current_page'  => $options['page'], 
                                                           'pages_to_show' => pagesToShow( $options['page'], $gb->getPagesCount(), PAGINATOR_LINKS_COUNT ) ,
                                                           'user'          => $options['user'],
                                                           'user_id'       => $options['user_id'] ) ); 
        $page->title = 'Страница: '.$options['user'];
        $page->addStylesheet('templates/css/main.css');
        $this->html = $page->render();
    }
    
}

?>