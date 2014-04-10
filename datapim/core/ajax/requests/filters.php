<?
//$this->request;
//$playerinventory = playerinventory::getInstance('');

$reqKind = $this->request['reqKind'];
$filterTarget = $this->request['filterTarget'];

if($reqKind === 'getHeroFilters') {
	foreach(hero::getAllHeroes() as $hero) {
		echo $hero->renderasHtml();	
	}
} elseif($reqKind === 'getRarityFilters') {
	$rarities = rarities::getInstance();
	 foreach($rarities->getRarities() as $rarity) {
		if($this->request['mode'] == 'mat') {
			echo $rarity->getMaTRenderString();
		} else {
			echo $rarity->getHtmlRenderString();
		}
	}
} elseif($reqKind === 'getFilteredCosmetics') {
	$filterData = json_decode($this->request['filterData']);
	
	$targetUser = $this->request['targetUser'];
	$queryLoc = $this->request['queryLoc'];
	$pageSize = $this->request['pageSize'];
	
	$pointedQuery = new pointedquery();
	$pointedQuery->setQueryLocation($queryLoc);
	$pointedQuery->setPageSize($pageSize);
	
	$filters[] = cosmeticfilters::createNoDefaultCosmeticString();
	$heroFilter = cosmeticfilters::createHeroFilterString($filterData->hero_filters);
	if(!empty($heroFilter)){
		$filters[] = $heroFilter;
	}
	$rarityFilter = cosmeticfilters::createRarityFilterString($filterData->rarity_filters);
	if(!empty($rarityFilter)){
		$filters[] = $rarityFilter;
	}
	if(!empty($filterData->searchText)){
		$filters[] = "cosmetic.item_name like '%".$filterData->searchText."%'";
	}
	
	if($targetUser === 'current_user') {
		if(user::getInstance()->getValidated()) {
			$targetUserId = user::getInstance()->getSteamId();
		} else {
			$targetUserId = false;
			$pointedQuery->messages[] = 'NOT LOGGED IN';
		}
	} else {
		$targetUserId = $targetUser;
	}
	
	if($targetUserId!=false || $filterTarget==='overview'){
		if($filterTarget === 'overview'){
			$pointedResults = cosmetic::getAllCosmetics(cosmeticfilters::createFilterString($filters), $pointedQuery);
			
			//$pointedResults->renderAsJson(true);
		} elseif($filterTarget === 'inventory') {		
			$pointedResults = playerinventory::getInventory(cosmeticfilters::createFilterString($filters), $pointedQuery, $targetUserId);
			
			
		} elseif($filterTarget === 'wishlist') {
			$pointedResults = playerwishlist::getWishlist(cosmeticfilters::createFilterString($filters), $pointedQuery, $targetUserId);
			
			//$pointedResults->renderAsJson(false);
		}
		
		if($targetUser == 'current_user' || $filterTarget==='overview') {	
				$pointedResults->renderAsJson(true);
		} else {
			$pointedResults->renderAsJson(true);
		}
	} else {
		$pointedQuery->renderAsJson(false);
	}
}
?>