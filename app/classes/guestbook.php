<?php 
/**
 * Wrapper for Modeldriver class
 *
 * @package guestbook
 * @author Alexander Belyaev
 **/
class Guestbook extends Modeldriver {
    
    public $posts_per_page = POSTS_PER_PAGE;
    public $output_order = ORDER_BY_ASC;
    
    /**
     * Get all posts from Guestbook
     *
     * @return array Array of non-deleted Post objects
     **/
    public function getPosts() {
        return $this->getRecords('Post', array('deleted' => 0), $order_by_asc = ORDER_BY_ASC);        
    }
    
    /**
     * Get total Guestbook pages count
     *
     * @return integer Pages count
     **/
    public function getPagesCount() {
        if ($this->total_records % POSTS_PER_PAGE > 0){
            return (int) ($this->total_records / POSTS_PER_PAGE) + 1;
        } else {
            return (int) $this->total_records / POSTS_PER_PAGE;
        }
    }
    
    /**
     * Get posts from one page
     *
     * @param string Number of page
     * @return array Array of objects from current page
     **/
    public function getPostsFromPage($page_number) {
        if ( is_numeric($page_number) ) {
            $page_number = (int) $page_number;
        } else {
            $page_number = 0;            
        }        
        
        if ($this->posts_per_page > 0) {
            return $this->getRecords( 'Post', 
                                      array('deleted' => 0,
                                            'offset'  => $this->posts_per_page * ($page_number-1), 
                                            'limit'   => $this->posts_per_page), 
                                      $order_by_asc = ORDER_BY_ASC );
        } else {
            return $this->getPosts();
        }        
    }
    
    /**
     * Wrapper for newRecords in Modeldriver
     *
     * @param array Array of objects
     * @return void
     **/
    public function newPost(array $post_object = array()) {
        $this->newRecords($post_object);
    }
    
    /**
     * Deletes $post_id in the will of $user_id
     *
     * @param string Post id - what delete
     * @param string User id - who delete
     * @return boolean Status if insertion new record
     **/
    public function deletePostBy($post_id, $user_id) {
        $post_to_delete = $this->getRecords( 'Post', 
                                             array('id' => $post_id) );         
        if (($user_id == $post_to_delete[0]->author_id) or ($user_id == "1")) {
            $sql = "update posts set deleted = 1 where id = ".$post_id.";";            
            try {  
                $this->db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);                             
                $this->db->beginTransaction();
                $result = $this->db->exec($sql);                    
                $this->db->commit();
                return True;
            } catch (Exception $e) {
                $this->db->rollBack();
                //echo "Failed: " . $e->getMessage();
                return False;
            }
        }
    
    }
    
    
}

?>