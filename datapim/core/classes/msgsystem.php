<?

class msgsystem{
	
	
	function __construct($steamid){
		if(is_numeric($steamid)){
			$this->mysqli = database::getInstance();
			$this->steamid = $steamid;
		}else{
			return false;	
		}
	}
	
	function fetchUpdates(){
		$this->updateOnlineState();
		$array['newMessages'] = $this->fetchMessages();
		$array['newTrades'] = $this->fetchTrades();
		return $array;
	}
	
	function updateOnlineState() {
		$stmt = $this->mysqli->prepare("UPDATE users SET online=1, lastupdate=NOW() WHERE steamid = ?");
		$stmt->bind_param("i",$this->steamid);
		$stmt->execute();
		$stmt->close();
	}
	
	function fetchMessages(){
		$query = $this->mysqli->query("SELECT id FROM user_messages WHERE to_steamid = '".$this->steamid."' AND status=1");
		if($query->num_rows){
			return $query->num_rows;	
		}else{
			return 0;	
		}
	}
	
	function fetchTrades(){
		$query = $this->mysqli->query("SELECT id FROM trades WHERE (from_steamid = '".$this->steamid."' AND from_state=1) OR (to_steamid = '".$this->steamid."' AND to_state=1)");
		if($query->num_rows){
			return $query->num_rows;	
		}else{
			return 0;	
		}		
	}


} 


?>