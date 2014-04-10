<?

class runcronjobs{
	
	function __construct(){
		$this->loadCronfiles();
	}
	
	function loadCronfiles(){
		if(CRONJOBS_PATH){
			$files = scandir(CRONJOBS_PATH);
			if(is_array($files) && count($files)>0){
				unset($files[0],$files[1]); // unset . en .. directory dingen
				$i=0;
				foreach($files as $file){
					if(preg_match('/cronjob-/',$file)){ // hence de !==
						$this->runCronjob($file);
						$i++;
					}
				}
				if($i==0){ // lieve melding terug als er geen files zijn
					utils::throwExcption('No cronjobs found, nothing runned !!!');
				}
			}			
		}else{
			utils::throwExcption('No cronjob path defined !!!');
		}
	}
	
	function runCronjob($file){
		if(file_exists(CRONJOBS_PATH.$file)){
			ob_start();
			require_once(CRONJOBS_PATH.$file);
			$this->config = $config;
			//print_r($this->config);
			$this->cron_output = ob_get_clean();
			echo 'Cronjob runned: '.$file.'<br>';
			echo $this->cron_output;
		}else{
			utils::throwExcption('Cronjob file not found !!!');
		}
	}
	
	public static function checkTime($config){
			if($_GET['runall']=='yes'){ // gore fix om ze allemaal te runnen.
				$runall = true;
			}
			if($runall){ return true; } // alles runnen GEVAARLIJK :P
			if(isset($config['cron']['hour']) && isset($config['cron']['minute'])){
				
				list($hour,$minute) = explode(":",date("H:i"));
				if($config['cron']['hour']=='*'){$config['cron']['hour']=$hour;}
				if($config['cron']['minute']=='*'){$config['cron']['minute']=$minute;}
				
				if($config['cron']['hour'] == date("H") && $config['cron']['minute'] == date("i")){
					return true;	
				}else{
					return false;	
				}
			}else{
				return false;	
			}
	}
	
	function output(){
		echo $this->cron_output;
	}
	
}

?>