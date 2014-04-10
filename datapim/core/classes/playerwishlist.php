<?
class playerwishlist{	
	public $pointedQuery;
	
	function __construct() {
		$this->pointedQuery = new pointedQuery();
		$this->pointedQuery->setQueryLocation(0);
		$this->pointedQuery->setPageSize(12);
	}
	
	public static function getInstance(){
		if(!isset($_SESSION['playerwishlist'])){
			$c = __CLASS__;
			$_SESSION['playerwishlist'] = new $c();
		}
		
		return $_SESSION['playerwishlist'];
	}
	
	function getPlayerWishlist() {
		return playerwishlist::getWishlist(NULL, $this->pointedQuery, user::getInstance()->steamid);
	}
	
	public static function getWishlist($filter, $pointedQuery, $steamid) {
		$cosmetics = array();
		$mysqli = database::getInstance();
		
		if(empty($pointedQuery)) {
			$pointedQuery = new pointedquery();
		}
		if(empty($steamid)){
			return false;	
		}
		
		$result = $mysqli->query("SELECT t1.defindex, cosmetic.item_name, cosmetic.item_type_name, cosmetic.item_rarity, cosmetic.image_url, cosmetic.image_fast, cosmetic.item_set, t3.price_keys, t3.price_euro FROM user_wishlist AS t1 INNER JOIN cosmetics AS cosmetic ON t1.defindex = cosmetic.defindex LEFT OUTER JOIN cosmetic_prices AS t3 ON cosmetic.defindex = t3.defindex WHERE t1.steamid=".$steamid.str_replace('WHERE', ' AND ', $filter)." ORDER BY t1.lastupdate DESC".$pointedQuery->createLimitString().";");
		
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
	
	public static function mergeCurrentUserWishlist($cosmetics) {
		if(!empty($cosmetics) && is_array($cosmetics)) {
			$user = user::getInstance();
			if($user->getValidated()) {
				$mysqli = database::getInstance();
				$userCosmeticResult = $mysqli->query("SELECT t1.defindex FROM user_wishlist as t1 WHERE t1.steamid=".$user->getSteamId().' AND t1.defindex IN ('.implode(',',array_keys($cosmetics)).')');
				if($userCosmeticResult) {
					while($cosm = $userCosmeticResult->fetch_assoc()){
						if(array_key_exists($cosm['defindex'],$cosmetics)){
							$cosmetics[$cosm['defindex']]->setOnWishlist();
						}
					}
				}
			}
		}
		return $cosmetics;
	}
	
	function addWishlistCosmetic($defIndex) {
		$mysqli = database::getInstance();
		
		$upsert = $mysqli->prepare("INSERT INTO user_wishlist (steamid, defindex) VALUES (?, ?);");
		$upsert->bind_param("ii",user::getInstance()->steamid, $defIndex);
		$upsert->execute();
		if($mysqli->sqlstate=="00000"){
			return true;
		} else {
			//echo $mysqli->error;
			if($mysqli->sqlstate=="23000") {
				return 'This cosmetic is already part of your wishlist.';	
			}
			return $mysqli->error;
		}
	}
	
	function removeWishlistCosmetic($defIndex) {
		$mysqli = database::getInstance();
		
		$upsert = $mysqli->prepare("DELETE FROM user_wishlist WHERE steamid=? AND defindex=?;");
		$upsert->bind_param("ii",user::getInstance()->steamid, $defIndex);
		$upsert->execute();
		if($mysqli->sqlstate=="00000"){
			return true;
		} else {
			//echo $mysqli->error;
			if($mysqli->sqlstate=="23000") {
				return 'This cosmetic is already part of your wishlist.';	
			}
			return $mysqli->error;
		}
	}
}
?>