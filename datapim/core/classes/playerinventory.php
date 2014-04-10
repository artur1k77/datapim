<?
class playerinventory{	
	public static function getInventory($filter, $pointedQuery, $steamid) {
		$cosmetics = array();
		$mysqli = database::getInstance();
		
		if(empty($pointedQuery)) {
			$pointedQuery = new pointedquery();
		}
		if(empty($steamid)){
			return false;	
		}
		//ESCAPE!!!!!!!!!!!!!!!!!!!!!!!!!!!
		$result = $mysqli->query("SELECT t1.defindex, ".cosmetic::getDefaultCosmeticSelectionString().", t3.price_keys, t3.price_euro FROM user_cosmetics AS t1 INNER JOIN cosmetics AS cosmetic ON t1.defindex = cosmetic.defindex LEFT OUTER JOIN cosmetic_prices AS t3 ON cosmetic.defindex = t3.defindex WHERE t1.steamid=".$steamid.str_replace('WHERE', ' AND ', $filter)." ORDER BY t1.tradeQuantity DESC, cosmetic.item_name ASC".$pointedQuery->createLimitString().";");
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
				$cosmetics=playerwishlist::mergeCurrentUserWishlist($cosmetics);
				$cosmetics=playerinventory::mergeCurrentUserInventory($cosmetics);
			}
		}
		$pointedQuery->incrementQueryLocation();
		$pointedQuery->setObjects($cosmetics);
		
		return $pointedQuery;
	}
	
	public static function mergeCurrentUserInventory($cosmetics) {
		if(!empty($cosmetics) && is_array($cosmetics)) {
			$user = user::getInstance();
			if($user->getValidated()) {
				$mysqli = database::getInstance();
				$userCosmeticResult = $mysqli->query("SELECT t1.defindex, t1.quantity, t1.tradeQuantity FROM user_cosmetics as t1 WHERE t1.steamid=".$user->getSteamId().' AND t1.defindex IN ('.implode(',',array_keys($cosmetics)).')');
				if($userCosmeticResult) {
					while($cosm = $userCosmeticResult->fetch_assoc()){
						if(array_key_exists($cosm['defindex'],$cosmetics)){
							$cosmetics[$cosm['defindex']]->setQuantity($cosm['quantity']);
							$cosmetics[$cosm['defindex']]->setTradeQuantity($cosm['tradeQuantity']);
						}
					}
				}
			}
		}
		return $cosmetics;
	}
	
	static function compareInventoryWishlist($inventoryTargetSteamId, $wishlistTargetSteamId, $pointedQuery) {
		$user = user::getInstance();
		$cosmetics = array();
		$mysqli = database::getInstance();
		
		if(empty($pointedQuery)) {
			$pointedQuery = new pointedquery();
		}
		
		if($user->getValidated()) {
			$cosmeticResult = $mysqli->query("SELECT defindex, item_name, item_type_name, item_rarity, image_url, image_fast, item_set FROM cosmetics WHERE defindex IN (SELECT uC.defindex FROM user_cosmetics AS uC INNER JOIN user_wishlist AS uW ON uW.defindex=uC.defindex WHERE uC.steamid=$inventoryTargetSteamId AND uW.steamid=$wishlistTargetSteamId)".$pointedQuery->createLimitString().";");
			if($cosmeticResult && $cosmeticResult->num_rows>0) {
				while($cosm = $cosmeticResult->fetch_assoc()) {
					if(!array_key_exists($cosm['defindex'],$cosmetics)){
						$cosmetics[$cosm['defindex']] = new cosmetic($cosm['defindex']);
					}
					$cosmetics[$cosm['defindex']]->item_name = $cosm['item_name'];
					$cosmetics[$cosm['defindex']]->item_type_name = $cosm['item_type_name'];
					$cosmetics[$cosm['defindex']]->item_rarity = $cosm['item_rarity'];
					$cosmetics[$cosm['defindex']]->image_url = $cosm['image_url'];
					$cosmetics[$cosm['defindex']]->image_fast = $cosm['image_fast'];
					$cosmetics[$cosm['defindex']]->item_set = $cosm['item_set'];
				}
				$pointedQuery->incrementQueryLocation();
			} else {
				$pointedQuery->messages[]='No more found.';	
			}
			$pointedQuery->setObjects($cosmetics);
		}
		return $pointedQuery;
	}
	
	static function refreshAPICosmetics($steamid) {	
		if(empty($steamid)){
			return false;	
		}
		$mysqli = database::getInstance();
		
		//Query steamAPI om de cosmetics van de steamgebruiker op te halen;
		$steam = new steamapi('getPlayerCosmetics',false,$steamid);
		$output = $steam->sendrequest();
		$array = json_decode($output,true);
		$pC = array();
		
		if(!empty($array)) {
			//Voeg de gereturnde cosmetics to aan de playerCosmetics array met hun defindex als key.
			//Als de defindex al voorkomt in de playerCosmetics array roep dan zijn incrementQuantity functie aan.
			foreach($array['result']['items'] as $item){
				if(!array_key_exists($item['defindex'],$pC)){
					$pC[$item['defindex']] = new cosmetic($item['defindex']);
					$pC[$item['defindex']]->incrementQuantity();
				} else {
					$pC[$item['defindex']]->incrementQuantity();
				}
			}
			//Cosmetics van een player die wel in de database voorkomen maar niet meer in de steaminventory voorkomen verwijderen uit de database.
			//ESCAPE!!!!!!!!!!!!
			$result = $mysqli->query("DELETE FROM user_cosmetics WHERE defindex NOT IN(".implode(',',array_keys($pC)).") AND steamid=".$steamid.";");
			
			$result = $mysqli->query("SELECT defindex FROM cosmetics WHERE defindex IN(".implode(',',array_keys($pC)).') ORDER BY item_name ASC');
			while($cosmetic = $result->fetch_assoc()){
				$existingCosmeticIds[$cosmetic['defindex']] = true;
			}
			
			foreach($pC as $cosmetic){
				if($existingCosmeticIds[$cosmetic->defindex]) {
					$quantity = $cosmetic->getQuantity();
					$upsert = $mysqli->prepare("INSERT INTO user_cosmetics (steamid, defindex, quantity) VALUES (?, ?, ?) ON DUPLICATE KEY UPDATE quantity=?");
					$upsert->bind_param("iiii",$steamid, $cosmetic->defindex, $quantity, $quantity);
					$upsert->execute();
					echo $upsert->error;
				}
			}
		} else {
			echo '<b>Steam connection failure:</b> Steam cannot be reached at this moment your items might be out of date.';
			//utils::throwExcption('No player items found.');	
		}
	}
	
	static function toggleTrading($defindex) {
		$mysqli = database::getInstance();
		$steamid = user::getInstance()->getSteamId();
		$q = $mysqli->prepare("SELECT tradeQuantity FROM user_cosmetics WHERE defindex=? AND steamid=?");
		$q->bind_param("ii", $defindex, $steamid);
		$q->execute();
		$q->store_result();
		if($q->num_rows>0){
			$q->bind_result($tradeQuantity);
			$q->fetch();
			$newTradeQuantity = 1;
			if($tradeQuantity>0){
				$newTradeQuantity = 0;
			}
			$update = $mysqli->prepare("UPDATE user_cosmetics SET tradeQuantity=? WHERE defindex=? AND steamid=?");
			$update->bind_param("iii", $newTradeQuantity, $defindex, $steamid);
			$update->execute();
			$update->store_result();
			if($update->affected_rows>0){
				return $newTradeQuantity;
			}
		}
		return false;	
	}
	
	function savePlayerCosmeticChanges() {
		$mysqli = database::getInstance();
		
		foreach($this->playerCosmetics as $cosmetic){
			$quantity = $cosmetic->getQuantity();
			$tradeQuantity = $cosmetic->getTradeQuantity();
			$upsert = $mysqli->prepare("INSERT INTO user_cosmetics (steamid, defindex, quantity, tradeQuantity) VALUES (?, ?, ?, ?) ON DUPLICATE KEY UPDATE quantity=?, tradeQuantity=?");
			$upsert->bind_param("iiiiii",$this->steamid, $cosmetic->defindex, $quantity, $tradeQuantity, $quantity, $tradeQuantity);
			$upsert->execute();
		}
		if($mysqli->sqlstate=="00000"){
			echo 'Inventory succesfully saved.';
		} else {
			echo $mysqli->error;
		}
	}
}
?>
