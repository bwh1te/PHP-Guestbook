<?php

require_once 'utils/configreader.php';
require_once 'classes/dbadapter.php';
require_once 'classes/modeldriver.php';
require_once 'classes/guestbook.php';
require_once 'models/model.php';
require_once 'models/post.php';
require_once 'models/user.php';

$test_data = 'test_data.txt';
$rows_per_post = 5;
$db_adapter = new DbAdapter( readConfigFromFile('config/dbconfig.txt') );

if (file_exists($test_data)) {
    echo "[Ok!] File with test data exists.\n";
} else exit ("[Fail!] File ".$test_data." wasn't found!\n");

// Check if DB exists
$conn = $db_adapter->getConnection() or exit("[Fail!] Can't connect to DB. Check config/dbconfig.txt\n");
$conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

$md = new Modeldriver( $conn );

// Create USERS table if not exists
$sql_create_table = <<<SQL
CREATE TABLE `users` (
`id` INT NOT NULL AUTO_INCREMENT PRIMARY KEY ,
`name` TEXT NOT NULL ,
`password` TEXT NOT NULL ,
`blocked` INT NOT NULL DEFAULT  '0'
) ENGINE = MYISAM CHARACTER SET utf8 COLLATE utf8_unicode_ci;
SQL;

try {
    $recs = $md->getRecords('User');
} catch (PDOException $e) {
    echo "Table USERS wasn't found and will be created!\n";    
    try {
        $conn->beginTransaction();
        $result = $conn->exec($sql_create_table);
        $conn->commit();
        echo "[Ok!] Table was successfully created!\n";
    } catch (Exception $e) {
        $conn->rollBack();
        echo "[Fail!] " . $e->getMessage();
    }
}

// Fill the USERS table
$sql_insert_data[] = array( 'name' => 'root',  'password' => md5('root'),  'blocked' => 0);
$sql_insert_data[] = array( 'name' => 'user1', 'password' => md5('user1'), 'blocked' => 0);
$sql_insert_data[] = array( 'name' => 'user2', 'password' => md5('user2'), 'blocked' => 0);
$sql_insert_data[] = array( 'name' => 'user3', 'password' => md5('user3'), 'blocked' => 0);

foreach ($sql_insert_data as $entry) {
    try {
        $conn->beginTransaction();
        foreach ($entry as &$field) {
            $field = $conn->quote($field);                    
        }        
        $sql  = "insert into users";
        $sql .= " (".implode(",", array_keys($entry)).") ";
        $sql .= "values";
        $sql .= " (".implode( ",", $entry ).")"; 
        $result = $conn->exec($sql);
        $conn->commit();
    } catch (Exception $e) {
        $conn->rollBack();
        echo "[Fail!] " . $e->getMessage();
    }    
    
}

// Create POSTS table if not exists
$sql_create_table = <<<SQL
CREATE TABLE `posts` (
`id` INT NOT NULL AUTO_INCREMENT PRIMARY KEY ,
`content` TEXT DEFAULT NULL ,
`author_name` TEXT DEFAULT NULL ,
`author_id` INT NULL DEFAULT NULL ,
`deleted` INT NOT NULL DEFAULT  '0',
`date` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE = MYISAM CHARACTER SET utf8 COLLATE utf8_unicode_ci;
SQL;

try {
    $recs = $md->getRecords('Post');
} catch (PDOException $e) {
    echo "Table POSTS wasn't found and will be created!\n";    
    try {
        $conn->beginTransaction();
        $result = $conn->exec($sql_create_table);
        $conn->commit();
        echo "[Ok!] Table was successfully created!\n";
    } catch (Exception $e) {
        $conn->rollBack();
        echo "[Fail!] " . $e->getMessage();
    }
}



// Get list of USERS
$md = new Modeldriver( $conn );
$users = array();
foreach ($md->getRecords('User') as $user) {
    $users[$user->name] = $user->id;
}
$users['Guest'] = null;

// Read TEST_DATA file line by line
// and add data to POSTS by random users
print "DB will be filled with test data, text rows per one post: ".$rows_per_post."\n";
$handle = fopen($test_data, 'r');    
if ($handle) {
    $row = 1; $text = '';
    $success = 0; $failed = 0;
    while (!feof($handle)) {
        $text .= fgets($handle, 4096);
        $row ++ ;
        if ($row > $rows_per_post) {
            $rand_user = array_rand($users); 
            $post = new Post( array( 'content'     => $text,
                                     'author_name' => $rand_user,
                                     'author_id'   => $users[$rand_user] ) );
            if ($md->newRecords(array($post))->getStatus() == True) { 
                $success ++ ;
            } else {
                $failed ++ ;                    
            }
            $row = 1; $text = '';                
        }
    }
    print "Total: ".($success + $failed)." records tried to insert.\n";
    print "Successfull: ".$success." Failed: ".$failed."\n";
}


?>