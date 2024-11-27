<?php
ini_set('display_errors', '1');
ini_set('display_startup_errors', '1');
error_reporting(E_ALL);
// ------------------------------------------------------------------------
// get auth status
session_start([
  'cookie_lifetime' => 31536000, // 1 year
]);

// Include the config file
include 'auth/config.php';

//define global variable
define('LOGGED_IN', isset($_SESSION['user_id']));

// init function
function redirect($path='')
{
	header('Location: /'.$path);
	exit;
}

# ROUTING
// ------------------------------------------------------------------------
// For get URL PATH
$request = $_SERVER['REQUEST_URI'];
$route['/'] = 'page/index.php';
$route['/dashboard'] = 'page/sysinfo.php';


// middleware
if (!LOGGED_IN && $request != '/') {
	header('Location: /');
	die;
}


// goto page
if (!empty($route[$request])) {
	include $route[$request];
} else {
	echo '404';
}