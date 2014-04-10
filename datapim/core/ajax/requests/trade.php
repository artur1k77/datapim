<?
//$this->request;
//$playerinventory = playerinventory::getInstance('');

$reqKind = $this->request['reqKind'];

if($reqKind === 'toggleTrading') {
	echo playerinventory::toggleTrading($this->request['defIndex']);
}
if($reqKind === 'refreshInventory') {
	playerinventory:refreshAPICosmetics();
	/*$playerinventory->shownCosmetics=0;
	foreach($playerinventory->getCosmetics() as $cosmetic) {
		$cosmetic->renderasHtml(true);
	}*/
}
if($reqKind === 'saveChanges') {
	$playerinventory->savePlayerCosmeticChanges();
}
if($reqKind === 'compareWishlistInventory') {
	$targetInventory = $this->request['iTarget'];
	$targetWishlist = $this->request['wTarget'];
	
	$queryLoc = $this->request['queryLoc'];
	$pageSize = $this->request['pageSize'];
	
	$pointedQuery = new pointedquery();
	$pointedQuery->setQueryLocation($queryLoc);
	$pointedQuery->setPageSize($pageSize);
	
	if($targetInventory === 'current_user') {
		if(user::getInstance()->getValidated()) {
			$targetInventoryId = user::getInstance()->getSteamId();
		} else {
			$targetInventoryId = false;
			$pointedQuery->messages[] = 'NOT LOGGED IN';
		}
	} else {
		$targetInventoryId = $targetInventory;
	}
	if($targetWishlist === 'current_user') {
		if(user::getInstance()->getValidated()) {
			$targetWishlistId = user::getInstance()->getSteamId();
		} else {
			$targetWishlistId = false;
			$pointedQuery->messages[] = 'NOT LOGGED IN';
		}
	} else {
		$targetWishlistId = $targetWishlist;
	}
	
	if($targetWishlistId!=false && $targetInventoryId!=false) {
		$pointedResults = playerinventory::compareInventoryWishlist($targetInventoryId, $targetWishlistId, $pointedQuery);
		$pointedResults->renderAsJson(false);
	} else {
		$pointedQuery->renderAsJson(false);
	}
}

if($reqKind === 'addTrade') {
	$user = user::getInstance();
	if($user->getValidated()){
		$trade = new makeatrade($user->steamid,$this->request['his_steamid']);
		$trade->setMyItems(json_decode($this->request['my_items']));
		$trade->setHisItems(json_decode($this->request['his_items']));
		$trade->setMyRarities(json_decode($this->request['my_rarities']));
		$trade->setHisRarities(json_decode($this->request['his_rarities']));
		$trade->setMessage($this->request['message']);
		if($trade->saveTrade()){
			echo true;	
		}else{
			echo false;
		}
	}else{
		echo false;	
	}
}
if($reqKind === 'saveTrade') {
	$trade = new trade(2, $this->request['trade_id']);
	if($trade->valid) {
		$user = user::getInstance();
		//if($trade->getFromSteamId()==$user->getSteamId()) {
			$makeatrade = new makeatrade($trade->getFromSteamId(),$trade->getToSteamId());
		//} elseif($trade->getToSteamId()==$user->getSteamId()) {
		//	$makeatrade = new makeatrade($trade->getToSteamId(), $trade->getFromSteamId());	
		//}
		if($trade->getFromSteamId()==$user->getSteamId()) {
			$makeatrade->setMyItems(json_decode($this->request['my_items']));
			$makeatrade->setHisItems(json_decode($this->request['his_items']));
			$makeatrade->setMyRarities(json_decode($this->request['my_rarities']));
			$makeatrade->setHisRarities(json_decode($this->request['his_rarities']));
		} elseif($trade->getToSteamId()==$user->getSteamId()) {
			$makeatrade->setMyItems(json_decode($this->request['his_items']));
			$makeatrade->setHisItems(json_decode($this->request['my_items']));
			$makeatrade->setMyRarities(json_decode($this->request['his_rarities']));
			$makeatrade->setHisRarities(json_decode($this->request['my_rarities']));
		}
		$makeatrade->setMessage($this->request['message']);
		if($makeatrade->saveTrade($this->request['trade_id'])){
			echo true;	
		}else{
			echo false;
		}
	} else {
		echo false;
	}
}


//$array = $dv->output();
//echo '<pre>';
//print_r($array);
?>