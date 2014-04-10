<?

class utils{
	
	public static function	throwExcption($msg){
			try{
				$bt = debug_backtrace();
				errorlogger::logError($msg,$bt);
				throw new exception($msg);
			} catch (Exception $e) {
				if(DISPLAY_ERRORS){
    				echo '<b>Caught exception:</b> ',  $e->getMessage(), "\n";
				}
			}
	}

	
}