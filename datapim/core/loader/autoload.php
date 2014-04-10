<?
// config
require('/var/www/vhosts/dota2essentials.com/httpdocs/core/loader/config.php');


// error reporting
if(DISPLAY_ERRORS){
	error_reporting(E_ALL  & ~E_NOTICE);
	ini_set("display_errors", 1);
}

// start user sessions
session_start();


/* Auto class loader */
function __autoload($class_name) {

	$path = CLASS_PATH .  strtolower($class_name) . '.php';
	if(autoload_file($path)) return true;
	
	$directories = scandir(CLASS_PATH);
	foreach($directories as $dir){
		if(is_dir(CLASS_PATH.$dir)){
			$path = CLASS_PATH . $dir. "/".  strtolower($class_name) . '.php';
			if(autoload_file($path)) return true;
		}
	}
	return false;

}
function autoload_file($file){
	if(file_exists($file)){ require_once($file); return true; } else { return false; }
}

// Setting timezone
if(!ini_get('date.timezone')){
   date_default_timezone_set(PHP_TIMEZONE);
}


// database
require('/var/www/vhosts/dota2essentials.com/httpdocs/core/loader/dbconnect.php');


?>