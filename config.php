<?php 
	// connect to database

//This file cannot be called directly, only included.
if (str_replace(DIRECTORY_SEPARATOR, "/", __FILE__) == $_SERVER['SCRIPT_FILENAME']) {
    exit;
}
	$conn = mysqli_connect("localhost", "root", "polawa", "interactive");

	if (!$conn) {
		die("Error connecting to database: " . mysqli_connect_error());
	}
    // define global constants
	define ('ROOT_PATH', realpath(dirname(__FILE__)));
	define('BASE_URL', 'http://localhost/interactive.pow');
        define('FILE_DIR', ROOT_PATH .'/static/');
	define('FILE_URL', BASE_URL .'/static/');
	define('IMAGE_DIR', FILE_DIR .'images/');
	define('IMAGE_URL', FILE_URL .'images/');
        define('RES_DIR_URL', BASE_URL .'/res/');
        define('RES_DIR', ROOT_PATH .'/res/');
        define('UPLOAD_TEMP', ROOT_PATH .'/temp/');
