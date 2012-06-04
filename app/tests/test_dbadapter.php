<?php
require_once 'PHPUnit/Framework.php';

class DbAdapterTest extends PHPUnit_Framework_TestCase
{
    protected function setUp() {        
        include_once('../classes/dbadapter.php');
    }
    
    public function testBuildMysqlConnectionString() {
        $params = array('dbtype'   => 'mysql', 
                        'host'     => 'localhost', 
                        'dbname'   => 'db', 
                        'user'     => 'root', 
                        'password' => 'root');
        $db = new DbAdapter($params);        
        $this->assertEquals( 'mysql:host=localhost;dbname=db', $db->buildMysqlConnectionString() );
        unset($db);
    }
    

}
?>