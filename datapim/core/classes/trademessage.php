<?
class trademessage{
	private $from_steamid;
	private $creation_date;
	private $message;
	
	function getFromSteamId() {
		return $this->from_steamid;	
	}
	
	function getCreationDate() {
		return $this->creation_date;	
	}
	
	function getMessage() {
		return $this->message;	
	}
	
	function __construct($from_steamid, $message, $creation_date) {
		$this->from_steamid = $from_steamid;
		$this->creation_date = $creation_date;
		$this->message = $message;
	}
	
	static function getTradeMessages($trade_id) {
		global $mysqli;
		
		if(!is_numeric($trade_id)) {
			return false;	
		}
		
		$stmt = $mysqli->prepare("SELECT from_steamid, message, creation_date FROM trade_messages as tm WHERE tm.trade_id=? ORDER BY creation_date ASC");	
		$stmt->bind_param("i",$trade_id);
		$stmt->execute();
		$stmt->bind_result($from_steamid, $message, $creation_date);
		while($stmt->fetch()) {
			$messages[] = new trademessage($from_steamid, $message, $creation_date);
		}
		return $messages;
	}
	
	function getHtmlRenderString() {
		$returnHtml='';
		return $returnHtml;
	}
}
?>
