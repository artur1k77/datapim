<?

class userinfo{
	
	function __construct($steamid=false){
		if(is_numeric($steamid)){
			$this->steamid = $steamid;	
		}
		$this->mysqli = database::getInstance();
	}
	
	function getInfo(){
		if($this->steamid){
			$user = $this->mysqli->query("SELECT * FROM users WHERE steamid='{$this->steamid}'");
			if($user->num_rows){
				$array= $user->fetch_assoc();
				$this->info = json_decode(json_encode($array), FALSE);  // confirmeren aan object user
			}else{
				utils::throwExcption('No user info found in database !');
			}
		}
	}
	
	function getMultiInfo($steamids){
		if(count($steamids)>0){
			$user = $this->mysqli->query("SELECT * FROM users WHERE steamid IN (".implode(", ",array_keys($steamids)).")");
			if($user->num_rows){
				while($userinfo = $user->fetch_assoc()){
					$array[] = $userinfo; 
				}
				$this->info = json_decode(json_encode($array), FALSE);  // confirmeren aan object user
			}else{
				utils::throwExcption('No user info found in database !');
			}
		}
	}
	
}

?>