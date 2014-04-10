<?
class cosmeticfilters{
	
	
	
	function __construct() {
	}
	
	static function createFilterString($filterStrings) {
		if(is_array($filterStrings) && count($filterStrings)>0){
			$filter='WHERE ';
			for($i=0; $i<count($filterStrings); $i++) {
				$filter.=$filterStrings[$i];
				if($i!=count($filterStrings)-1) {
					$filter.=' AND ';
				}
			}
		}
		return $filter;
	}
	
	static function createHeroFilterString($heros) {
		if(is_array($heros) && count($heros)>0) {
			$filterString = 'cosmetic.hero IN ('.implode(',',$heros).')';
		}
		return $filterString;
	}
	
	static function createRarityFilterString($rarities) {
		if(is_array($rarities) && count($rarities)>0) {
			$filterString = 'cosmetic.item_rarity IN ('.implode(',',$rarities).')';
		}
		return $filterString;
	}
	
	static function createNoDefaultCosmeticString() {
		return "cosmetic.prefab!='default_item' AND cosmetic.prefab!='' AND cosmetic.image_inventory!='econ/testitem_slot_empty'";	
	}
}
?>
