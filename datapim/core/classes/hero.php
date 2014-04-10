<?
class hero{
	public $valveid;
	public $name;
	public $ingamename;
	public $imgsmall;
	public $imgverysmall;
	public $imgfast;
	
	function __construct($valveid) {
		$this->valveid = $valveid;
	}
	
	static function getAllHeroes() {
		$mysqli = database::getInstance();
		
		$result = $mysqli->query("SELECT name, ingamename, valveid, imgsmall, imgverysmall, imgfast FROM heros ORDER BY name ASC");
		while($h = $result->fetch_assoc()){
			$hero = new hero($h['valveid']);
			$hero->name = $h['name'];
			$hero->ingamename = $h['ingamename'];
			$hero->imgsmall = $h['imgsmall'];
			$hero->imgverysmall = $h['imgverysmall'];
			$hero->imgfast = $h['imgfast'];
			$heroes[] = $hero;
		}	
		
		return $heroes;
	}
	
	function getFormattedHeroImage() {
		$info = '<div class="hero_info">';
			$info.= '<img class="hero_img" src="/media/heros/'.$this->imgfast.'"/>';
		$info.= '</div>';
		
		return $info;
	}
	
	function renderasHtml() {
		$html = '<div class="hero" id="'.$this->valveid.'" >';
		$html .= $this->getFormattedHeroImage();
		//echo detail div
		$html .= '<div class="hero_details '.$this->valveid.'">';
			$html .= '<div>'.$this->name.'</div>';
		$html .= '</div>';
		$html .= '</div>';
		
		return $html;
	}
}
?>
