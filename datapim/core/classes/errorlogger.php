<?

class errorlogger{
	
	public static function logError($msg,$bt){
			$mysqli = database::getInstance();
			$mysqli->query("INSERT INTO sys_error_log (error_msg,datetime,`file`,`line`,`function`,`class`,backtrace) VALUES ('".$msg."','".date("Y-m-d H:m:s")."','".$bt[1]['file']."','".$bt[1]['line']."','".$bt[1]['function']."','".$bt[1]['class']."','".serialize($bt)."')");
	}
	
} 

?>