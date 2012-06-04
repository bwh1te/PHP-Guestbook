<?php
require_once 'utils/configreader.php';
require_once 'utils/autoloader.php';
require_once 'utils/pagestoshow.php';

require_once 'models/model.php';
require_once 'models/post.php';
require_once 'models/user.php';

require_once 'classes/dbadapter.php';
require_once 'classes/page.php';
require_once 'classes/modeldriver.php';
require_once 'classes/guestbook.php';
require_once 'classes/auth.php';

// LOAD SETTINGS. To change settings - edit config/settings.txt, 
// config/err_settings.txt and config/dbconfig.txt
$err_settings = readConfigFromFile('config/err_settings.txt');
$settings     = readConfigFromFile('config/settings.txt');
error_reporting( $err_settings['error_reporting'] );
ini_set( 'display_errors', $err_settings['display_errors'] );
foreach ($settings as $key => $value) define(strtoupper($key), $value);
define( "DB_SETTINGS", serialize(readConfigFromFile('config/dbconfig.txt')) ); 

$user_name = (isset($_POST['ulogin'])) ? $_POST['ulogin'] : null;
$user_pass = (isset($_POST['upass']))  ? $_POST['upass']  : null;

$db_adapter = new DbAdapter( unserialize(DB_SETTINGS) );
$auth = new Auth( $user_name, $user_pass, $db_adapter->getConnection() );

// GET PARAMETERS FROM HTTP-REQUEST
$options = $_GET;   $controller_name = '';
$options['user']    = $auth->getAuthorizedUser();
$options['user_id'] = $auth->getAuthorizedUserId();
$options['auth_handler'] = $auth;

if (isset($_GET['action'])) {    
    $controller_name = $_GET['action'];
    unset($options['action']);    
} else {
	$controller_name = 'showpost';
}

// SET UP CONTROLLER. Controller name = action from GET-string
try {
    $ctrl = new $controller_name($options);
} catch (Exception $e) {
    $ctrl = new not_found(); // Почему не ловится исключение?
}

// FINALLY RETURN PROCESSED HTML
header("Content-type: text/html");
echo($ctrl->html); 


?>