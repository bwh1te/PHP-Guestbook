<?php
/**
 * DbAdapter could be an implementation of adapter pattern, 
 * but actually handle only mysql connection.
 *
 * @package guestbook
 * @author Alexander Belyaev
 **/
class DbAdapter {
    
    private $connection;
    
    private $dbtype;
    private $user;
    private $password;
    private $host;
    private $dbname;

    /**
     * DbAdapter
     *
     * @param array Hashmap of parameters
     * @return void
     **/
    public function __construct(array $params) {
        
        $this->user = $params['user'];
        $this->password = $params['password'];
        $this->host = $params['host'];
        $this->dbname = $params['dbname'];
        
        if (isset($params['dbtype'])) {
            $this->dbtype = $params['dbtype'];
        } else {
            $this->dbtype = 'mysql';
        }

    }
    
    
    public function buildMysqlConnectionString() {
        return 'mysql:host='.$this->host.';dbname='.$this->dbname;        
    }

    /**
     * Try to connect to DB with predefined parameters
     *
     * @return object PDO connection
     **/
    public function getConnection() {
        if (is_null($this->connection)){
            try {
                if ($this->dbtype == 'mysql') {
                    $this->connection = new PDO($this->buildMysqlConnectionString(), $this->user, $this->password);
                }
                return $this->connection;
            } catch (Exception $e) {
                echo "[Fail!] " . $e->getMessage() . "\n";
                return False;
            }
        }        
    }    

}

?>
