<?
class cosmetic{
	public $steamid;
	public $defindex;
	public $item_name;
	public $item_type_name;
	public $item_rarity;
	public $image_url;
	public $image_fast;
	public $price_euro;
	public $price_keys;
	
	private $quantity = 0;
	private $tradeQuantity = 0;
	private $onWishlist = false;
	
	function __construct($defindex) {
		$this->defindex = $defindex;
	}
	
	function getPriceKeys() {
		if(!empty($this->price_keys)){
			$retVal = $this->price_keys;
			if(strlen($retVal)>4){
				$retVal = substr($retVal, 0, 4);	
			}
			return $retVal;
		}
		return '?';
	}
	
	function getPriceEuro() {
		if(!empty($this->price_euro)){
			return $this->price_euro;
		}
		return '?';
	}
	
	function getTradeQuantity() {
		return $this->tradeQuantity;	
	}
	
	function setTradeQuantity($tQ) {
		$this->tradeQuantity = $tQ;
	}
	
	function incrementTradeQuantity() {
		if($this->tradeQuantity < $this->quantity) {
			$this->tradeQuantity++;
		}
	}
	
	function decrementTradeQuantity() {
		if($this->tradeQuantity > 0) {
			$this->tradeQuantity--;	
		}
	}
	
	function getQuantity() {
		return $this->quantity;	
	}
	
	function setQuantity($q) {
		$this->quantity = $q;
	}
	
	function incrementQuantity() {
		$this->quantity++;
	}
	
	function decrementQuantity() {
		$this->quantity--;	
	}
	
	function setOnWishlist() {
		$this->onWishlist=true;
	}
	
	static function getDefaultCosmeticSelectionString() {
		return "cosmetic.defindex, cosmetic.item_name, cosmetic.item_type_name, cosmetic.item_rarity, cosmetic.image_url, cosmetic.image_fast, cosmetic.item_set";	
	}
	
	static function getCosmeticsByDefindexes($defindexes) {
		$cosmetics = array();
		$mysqli = database::getInstance();
		
		if(!is_array($defindexes) || count($defindexes)===0) {
			return $cosmetics;	
		}
		$result = $mysqli->query("SELECT ".cosmetic::getDefaultCosmeticSelectionString().", t3.price_keys, t3.price_euro FROM cosmetics as cosmetic LEFT OUTER JOIN cosmetic_prices AS t3 ON cosmetic.defindex = t3.defindex WHERE cosmetic.defindex IN(".implode(',',$defindexes).") ORDER BY cosmetic.lastupdate DESC, cosmetic.item_name ASC;");
		if($result) {
			while($cosm = $result->fetch_assoc()){
				if(!array_key_exists($cosm['defindex'],$cosmetics)){
					$cosmetics[$cosm['defindex']] = new cosmetic($cosm['defindex']);
				}
				$cosmetics[$cosm['defindex']]->item_name = $cosm['item_name'];
				$cosmetics[$cosm['defindex']]->item_type_name = $cosm['item_type_name'];
				$cosmetics[$cosm['defindex']]->item_rarity = $cosm['item_rarity'];
				$cosmetics[$cosm['defindex']]->image_url = $cosm['image_url'];
				$cosmetics[$cosm['defindex']]->image_fast = $cosm['image_fast'];
				$cosmetics[$cosm['defindex']]->item_set = $cosm['item_set'];
				$cosmetics[$cosm['defindex']]->price_euro = $cosm['price_euro'];
				$cosmetics[$cosm['defindex']]->price_keys = $cosm['price_keys'];
			}
		}
		return $cosmetics;
		
	}
	
	static function getAllCosmetics($filter, $pointedQuery) {
		$cosmetics = array();
		$mysqli = database::getInstance();
		
		if(empty($pointedQuery)) {
			$pointedQuery = new pointedquery();
		}
		if(!isset($filter) || empty($filter)) {
			$filter = '';	
		}
		
		//FILTER BOUWEN!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!
		$result = $mysqli->query("SELECT ".cosmetic::getDefaultCosmeticSelectionString().", t3.price_keys, t3.price_euro FROM cosmetics as cosmetic LEFT OUTER JOIN cosmetic_prices AS t3 ON cosmetic.defindex = t3.defindex ".$filter." ORDER BY cosmetic.lastupdate DESC, cosmetic.item_name ASC LIMIT ".$pointedQuery->getQueryLocation().",".$pointedQuery->getPageSize().";");
		
		if($result) {
			while($cosm = $result->fetch_assoc()){
				if(!array_key_exists($cosm['defindex'],$cosmetics)){
					$cosmetics[$cosm['defindex']] = new cosmetic($cosm['defindex']);
				}
				$cosmetics[$cosm['defindex']]->item_name = $cosm['item_name'];
				$cosmetics[$cosm['defindex']]->item_type_name = $cosm['item_type_name'];
				$cosmetics[$cosm['defindex']]->item_rarity = $cosm['item_rarity'];
				$cosmetics[$cosm['defindex']]->image_url = $cosm['image_url'];
				$cosmetics[$cosm['defindex']]->image_fast = $cosm['image_fast'];
				$cosmetics[$cosm['defindex']]->item_set = $cosm['item_set'];
				$cosmetics[$cosm['defindex']]->price_euro = $cosm['price_euro'];
				$cosmetics[$cosm['defindex']]->price_keys = $cosm['price_keys'];
			}
			
			$user = user::getInstance();
			if($user->getValidated()) {
				$cosmetics=playerinventory::mergeCurrentUserInventory($cosmetics);
				$cosmetics=playerwishlist::mergeCurrentUserWishlist($cosmetics);
			}
		}
		
		$pointedQuery->incrementQueryLocation();
		$pointedQuery->setObjects($cosmetics);
		
		return $pointedQuery;
	}
	
	function getFormattedCosmeticImage($showQuantities) {
		$info = '<div class="info">';
			$info.= '<div class="cosmetic_img_container rarity_'.$this->item_rarity.'">';
			$info.= '<img class="cosmetic_img" src="/media/cosmetics/'.$this->image_fast.'"/>';
			$info.= '</div>';
			//if($showQuantities) {
			$info.= '<div class="markers" '.($showQuantities?'':'style="display: none;"').'>';
				$info.= '<div class="tradeQuantity_marker '.$this->defindex.'" style="'.($this->getTradeQuantity()>=1?'':'display: none;').'">';
					$info.= '<img src="/template/img/trade_icon.jpg"/>';
					//$info.= '<span class="quantity '.$this->defindex.'">'.$this->getTradeQuantity().'</span>';
				$info.='</div>';
				$info.= '<div class="wishlist_marker '.$this->defindex.'" style="'.($this->onWishlist?'':'display: none;').'">';
					$info.= '<img src="/template/img/wishlist_icon.png"/>';
					//$info.= '<span class="quantity '.$this->defindex.'">'.$this->getTradeQuantity().'</span>';
				$info.='</div>';
				$info.= '<div class="quantity_marker '.$this->defindex.'" style="'.($this->getQuantity()>=1?'':'display: none;').'">';
					$info.= '<img src="/template/img/quantity_icon.jpg"/>';
					$info.= '<span class="quantity '.$this->defindex.'">x'.$this->getQuantity().'</span>';
				$info.='</div>';
			$info.='</div>';
			//}
		$info.= '</div>';
		
		return $info;
	}
	
	function renderasHtml($showQuantities) {
		if($this->image_url) {
			if($showQuantities) {
				echo '<div class="cosmetic" id="'.$this->defindex.'" >';
			} else {
				echo '<div class="cosmetic tiny" id="'.$this->defindex.'" >';
			}
			
			//echo detail div
			echo '<div class="detail_hover_wrap cosmetic_details '.$this->defindex.'">';
			echo '<div class="detail_hover_header">'.$this->item_name.'</div>';
			echo '<div>Type: '.$this->item_type_name.'</div>';
			echo '<div>Key Price: '.$this->price_keys.'</div>';
			echo '<div>Euro Price: '.$this->price_euro.'</div>';
			echo '</div>';
			echo $this->getFormattedCosmeticImage($showQuantities);
			echo '</div>';
		}
	}
	
	function getRarityCssClass() {
		return 'rarity_'.$this->item_rarity;	
	}
	
	function getHtmlRenderString($showQuantities) {
		$returnHtml='';
		if($this->image_url) {
			if($showQuantities) {
				$returnHtml.='<div class="cosmetic" id="'.$this->defindex.'" >';
			} else {
				$returnHtml.='<div class="cosmetic tiny" id="'.$this->defindex.'" >';
			}
			
			//echo detail div
			$returnHtml.='<div class="detail_hover_wrap cosmetic_details '.$this->defindex.'">';
			$returnHtml.='<div class="detail_hover_header">'.$this->item_name.'</div>';
			$returnHtml.='<div class="detail_content_wrap">';
			$returnHtml.=rarities::getInstance()->getRarity($this->item_rarity)->getHtmlRenderString();
			$returnHtml.='<div>Type: '.$this->item_type_name.'</div>';
			$returnHtml.='<div>'.$this->getPriceKeys().' keys, $ '.$this->getPriceEuro().'</div>';
			$returnHtml.='</div>';
			$returnHtml.='</div>';
			$returnHtml.=$this->getFormattedCosmeticImage($showQuantities);
			$returnHtml.='</div>';
		}
		return $returnHtml;
	}
}
?>
