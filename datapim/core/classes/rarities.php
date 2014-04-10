<?
class rarities{
	private $rarities;
	
	public function getRarities() {
		return $this->rarities;	
	}
	
	public function getRarity($valve_value) {
		return $this->rarities[$valve_value];	
	}
	
	function __construct() {
		$this->getAllRarities();
	}
	
	public static function getInstance(){
		if(!isset($_SESSION['rarity'])){
			$c = __CLASS__;
			$_SESSION['rarity'] = new $c;
		}
	
		return $_SESSION['rarity'];
	}
	
	private function getAllRarities() {
		$mysqli = database::getInstance();
		
		$this->rarities = array();
		
		$result = $mysqli->query("SELECT name, valve_value, color FROM cosmetic_rarities ORDER BY valve_value ASC");
		while($r = $result->fetch_assoc()){
			if(!array_key_exists($r['valve_value'],$this->rarities)){
				$this->rarities[$r['valve_value']] = new rarity($r['valve_value']);
			}
			$this->rarities[$r['valve_value']]->name = $r['name'];
			$this->rarities[$r['valve_value']]->color = $r['color'];
		}	
	}
}
?>
