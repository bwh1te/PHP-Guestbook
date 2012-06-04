<?php
/**
 * Auth is a class for authorisation service.
 *
 * @package guestbook
 * @author Alexander Belyaev
 **/
class Auth extends Modeldriver {
    
    private $logged_in = False;
    private $user      = null;
    private $user_id   = null;
    private $password  = null;    

    /**
     * Auth
     *
     * @param string User login
     * @param string User password
     * @param object PDO 
     * @return void
     **/
    public function __construct($user, $password, $db_connection_handler) {
        parent::__construct($db_connection_handler);
        
        if ( !empty($user) and !empty($password) ) {
            $this->user = $user;
            $this->password = $password;            
        }
        
        if ( isset($_COOKIE['login']) ) {
            list($cookie_uname, $cookie_hash) = explode(',', $_COOKIE['login']);
            if ( $this->createTokenFor($cookie_uname) == $cookie_hash ) {                
                if ($cookie_uname == $this->user) {                    
                    $this->logged_in = True;
                } else {
                    if (empty($this->user)) {
                        $this->user = $cookie_uname;
                        $this->logged_in = True;
                    } else {
                        $this->logged_in = False;
                        $this->logout();
                    }
                }
            } else {
                $this->logged_in = False;
                $this->logout();
            }            
        }
        
        if (!$this->isLoggedIn()) {    
            $this->connectAndLogin();
        }        
             
    }
    
    
    /**
     * Try to authorize with predefined login and password
     *
     * @return boolean True in case of success
     **/
    public function connectAndLogin() {
        if ( !empty($this->user) and !empty($this->password) ) {            
            $data = $this->getRecords( 'User', array('name'     => $this->user, 
                                                     'password' => $this->hashFunction($this->password), 
                                                     'blocked'  => 0) );
            if ($this->total_records == 1) {
                $this->logged_in = True;
                setcookie('login', $this->user.','.$this->createTokenFor($this->user));
                return True;
                
            }            
        } 
        return False;
        
    }
    
    
    /**
     * Closes the session
     *
     * @return void
     **/
    public function logout() {
        $this->logged_in = False;
        setcookie('login', '', time()-3600);
    }
    
    
    /**
     * Checks if user is already logged in.
     *
     * @return boolean
     **/
    public function isLoggedIn() {
        return $this->logged_in;            
    }
    
    
    /**
     * Returns the username of authorized user.
     *
     * @return string username, if logged in
     **/
    public function getAuthorizedUser() {
        if ($this->isLoggedIn()) {
            return $this->user;
        } else {
            return null;
        }
    }
    

    /**
     * Returns the user id of authorized user.
     *
     * @return string user id, if logged in
     **/
    public function getAuthorizedUserId() {
        if ($this->isLoggedIn()) {
            if (empty($this->user_id)) {
                $user_record = $this->getRecords( 'User', array('name'     => $this->user,
                                                                'blocked'  => 0) );
                $this->user_id = $user_record[0]->id;       
                unset($user_record);
            }
            return $this->user_id;
        } else {
            return null;
        }
    }    
    
    
    /**
     * Ban user. Works only on root account
     *
     * @param string id of user to ban
     * @return boolean True if user successfully banned
     **/
    public function banUser($bad_user_id) {
        if ($this->getAuthorizedUserId() == 1) {
            $bad_user_record = $this->getRecords( 'User', array('id'       => $bad_user_id,
                                                                'blocked'  => 0) ); 
                                                                            
            if (($this->total_records == 1) and ($bad_user_id != 1)) {
                $sql = "update users set blocked = 1 where id = ".$bad_user_id.";";
                try {  
                    $this->db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
                    $this->db->beginTransaction();
                    $result = $this->db->exec($sql);                    
                    $this->db->commit();
                    return True;
                } catch (Exception $e) {
                    $this->db->rollBack();
                    echo "Failed: " . $e->getMessage();
                    return False;
                }                
                
            }

            
        }
    
    }


    private function createTokenFor( $source_string ) {
        $salt = 'iddqd';
        return $this->hashFunction($source_string.$salt);
    }
    
    
    private function hashFunction($source_string) {
        return md5($source_string);
    }
    
    
}

?>