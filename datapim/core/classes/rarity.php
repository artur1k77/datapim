<?
class rarity{
	public $valve_value;
	public $name;
	public $color;
	
	function __construct($valve_value) {
		$this->valve_value = $valve_value;
	}
	
	function getFormattedRarityImage() {
		$info = '<div class="rarity_info">';
			$info.= $this->name;
		$info.= '</div>';
		
		return $info;
	}
	
	static function getRaritiesByIds($ids) {
		$rarities = array();
		$mysqli = database::getInstance();
		
		if(!is_array($ids) || count($ids)===0) {
			return $rarities;	
		}
		$result = $mysqli->query("SELECT name, valve_value, color FROM cosmetic_rarities WHERE valve_value IN(".implode(',',$ids).") ORDER BY valve_value ASC");		
		while($r = $result->fetch_assoc()){
			if(!array_key_exists($r['valve_value'],$rarities)){
				$rarities[$r['valve_value']] = new rarity($r['valve_value']);
			}
			$rarities[$r['valve_value']]->name = $r['name'];
			$rarities[$r['valve_value']]->color = $r['color'];	
		}
		return $rarities;
		
	}
	
	function getHtmlRenderString($regular=true) {
		if(!$regular){
			return $this->getMaTRenderString('large');
		}
		$html = '<div class="rarity" style="background-color: '.$this->color.';" id="'.$this->valve_value.'" >';
		$html .= $this->getFormattedRarityImage();
		$html .= '</div>';
		
		return $html;
	}
	
	function getMaTRenderString($large=false) {
		if($this->valve_value>0) {
			$html = '<div class="rarity mat '.$large.'" style="background-color: '.$this->color.';" id="'.$this->valve_value.'" >';
			$html .= '<div class="rarity_info">';
				$html.= 'Any ';
			$html.= '</div>';
			$html .= '</div>';
		}
		
		return $html;
	}
}
?>
