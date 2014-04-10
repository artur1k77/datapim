<?
class user{	
	public $steamid;
	public $profilename;
	public $profileurl;
	public $avatarsmall;
	public $avatarmedium;
	public $avatarlarge;
	
	private $validated;
	
	function getValidated() {
		if(!empty($this->validated)){
			return $this->validated;
		} else {
			if(isset($_COOKIE["sid"]) && isset($_COOKIE["dot2tok"])) {
				$this->getUserInfo();	
			}
			return $this->validated;	
		}
	}
	
	function getSteamId() {
		return $this->steamid;	
	}
	
	private function __construct() {
		$this->getUserInfo();
	}
	
	public static function getInstance(){
		if(empty($_SESSION['user'])){
			$c = __CLASS__;
			$_SESSION['user'] = new $c;
			playerinventory::refreshAPICosmetics($_SESSION['user']->getSteamId());	
		}
	
		return $_SESSION['user'];
	}
	
	function getUserInfo() {
		global $mysqli;
		$this->validated = false;
		if(isset($_COOKIE["sid"]) && isset($_COOKIE["dot2tok"])) {
			$providedSId = $_COOKIE["sid"];
			$providedToken = $_COOKIE["dot2tok"];
			
			$query = $mysqli->prepare("SELECT steamid, profilename, profileurl, avatarsmall, avatarmedium, avatarlarge, token FROM users WHERE steamid=?");
			$query->bind_param("i",$providedSId);
			$query->execute();
			$query->bind_result($steamid, $profname, $profurl, $avatsmall, $avatmedium, $avatlarge, $token);
			$query->fetch();
			
			if($providedToken === $token && !empty($providedToken)) {
				$this->steamid = $steamid;
				$this->profilename = $profname;
				$this->profileurl = $profurl;
				$this->avatarsmall = $avatsmall;
				$this->avatarmedium = $avatmedium;
				$this->avatarlarge = $avatlarge;
				$this->validated = true;
			} else {
				$this->validated = false;
			}
		} else {
			$this->validated = false;
		}
	}
	
	function generateToken($sId, $ts) {
		$salt = md5("dota2".sha1($ts)."essentials");
		return hash("sha512", $salt.$sId);
	}
	
	function logout() {
		global $mysqli;
		
		$query = $mysqli->prepare("UPDATE users SET token=null WHERE steamid=?");
		$query->bind_param("i", $this->steamid);
		$query->execute();
		
		setcookie("sid", '', time()-3600, '/', ".dota2essentials.com");
		setcookie("dot2tok", '', time()-3600, '/', ".dota2essentials.com");
		unset($_SESSION['user']);
	}
	
	function processAuth($sId) {
		global $mysqli;
		
		$timestamp = @date("Y-m-d H:i:s");
			
        // BUILD COOKIE.
		$token = $this->generateToken($sId, $timestamp);
			
		setcookie("sid", $sId, time()+(3600*24*14), '/', ".dota2essentials.com");
		setcookie("dot2tok", $token, time()+(3600*24*14), '/', ".dota2essentials.com");
		// END BUILD COOKIE
		
        // Fetch remaining userinfo from steam
        $steam = new steamapi('getUserInfo',false,$sId);
        $json_object= $steam->sendrequest();
        $json_decoded = json_decode($json_object);
		// Eerste player uit de array selecteren (om de een of andere vage reden geeft steam een array met players terug, de player die we willen hebben zit echter altijd in element 0).
		$player = $json_decoded->response->players[0];


		// Doe een upsert van de userinfo samen met de timestamp naar de database
		$query = $mysqli->prepare("INSERT INTO users (steamid, profilename, profileurl, avatarsmall, avatarmedium, avatarlarge, token, primaryclanid, loccountrycode, locstatecode, loccityid) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?) ON DUPLICATE KEY UPDATE profilename=?, profileurl=?, avatarsmall=?, avatarmedium=?, avatarlarge=?, token=?, primaryclanid=?, loccountrycode=?, locstatecode=?, loccityid=?");
			
		$query->bind_param("issssssssssssssssssss",$sId,$player->personaname,$player->profileurl,$player->avatar,$player->avatarmedium,$player->avatarfull,$token,$player->primaryclanid,$player->loccountrycode,$player->locstatecode,$player->loccityid,$player->personaname,$player->profileurl,$player->avatar,$player->avatarmedium,$player->avatarfull,$token,$player->primaryclanid,$player->loccountrycode,$player->locstatecode,$player->loccityid);
		$query->execute();
		if($query->error) {
			utils::throwExcption($query->error);
		} else {
			playerinventory::refreshAPICosmetics($sId);	
		}
	}
	
	function renderLoginHTML(){
		if($this->getValidated()){
			$html .= ''.$this->profilename.'&nbsp;';
			$html .= '<div class="commwrap"><a title="Trade Overview" href="/trade-overview/" style="display:block;"><div class="commnotify_trade"></div><img src="/template/img/tradeicon.png"></a></div>&nbsp;<div class="commwrap"><a title="Personal messages" href="/my-messages/" style="display:block;"><div class="commnotify_message"></div><img src="/template/img/msgicon.png"></a></div>&nbsp;';
      		$html .= '<a href="/user/'.$this->steamid.'"><img src="'.$this->avatarmedium.'" height="40px" ></a>';
		}else{
       		$html .= '<a href="/login/?url=/'.$_GET['page'].'/"><img id="sits" src="/template/img/sits_small.png" style="margin:10px 0 0 0;" ></a>';
		}
		return $html;
	}
	
	function saveUserSettings($array){
			if(is_array($array) && count($array)>0){
				foreach($array as $key=>$value){
					$sql .= " ".$key."='".$value."',";
					if(is_int($value)){
						$params .= "i";
					}else{
						$params .= "s";
					}
				}
				rtrim($sql, ","); // laatste komma eraf
				$stmt = $this->mysqli->prepare("UPDATE users SET $sql WHERE steamid = ?");
				call_user_func_array(array($stmt,'bind_param'),$array); 
				$stmt->bind_param("i",$this->steamid);
				$stmt->execute();
				echo $stmt->error;
				$stmt->close();
				if($stmt->affected_rows==2){
					return true;	
				}else{
					return false;	
				}
			}else{
				return 'No settings specified';	
			}
	}
}
?>
