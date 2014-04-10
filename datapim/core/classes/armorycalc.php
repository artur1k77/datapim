<?

class armorycalc{
	
	function __construct(){
		$this->mysqli = database::getInstance();
	}
	
	function loadInventory($steamid=false){
		$this->user = user::getInstance();
		$pointedQuery = new pointedQuery();
		$pointedQuery->setIgnorePointers();
		if($steamid){
			$this->playerinventory = playerinventory::getInventory(NULL,$pointedQuery,$steamid);
		}else{
			if($this->user->getValidated()){
				$this->playerinventory = playerinventory::getInventory(NULL,$pointedQuery,$this->user->steamid);
			}else{
				return false;
			}
		}
		$this->calc = $this->calcArmory();
	}
	
	function calcArmory(){
		if(is_array($this->playerinventory->getObjects())){
			$this->totalprice = 0;
			$this->totalkeys = 0; // reset voor volgende iteraties
			$this->matched = 0;
			$this->armoryObjects = 0;
			foreach($this->playerinventory->getObjects() as $cosmetic){
				$this->totalprice = $this->totalprice+($cosmetic->price_euro*$cosmetic->getQuantity());
				$this->totalkeys = $this->totalkeys+($cosmetic->price_keys*$cosmetic->getQuantity());
				$this->armoryObjects+=$cosmetic->getQuantity();
				if(!empty($cosmetic->price_euro)){
					$this->matched+=$cosmetic->getQuantity();
				}
			}
			return true;
		}else{
			return false;	
		}
	}
	
	function getTotalKeyPrice(){
		return $this->totalkeys;	
	}
	
	function getTotalDollarPrice(){
		return $this->totalprice;	
	}
	
	function renderArmory(){
		$calc = $this->calcArmory();
		if($this->calc){
			$html = '<div class="armorycalcresult">';
			$html .= 'Calculated '.$this->matched.' / '.$this->armoryObjects.' Items<br>';
			$html .= 'Total dollar value: $'.$this->totalprice.'<br>';
			$html .= 'Total key value: '.$this->totalkeys.'<br>';
			$html .= '</div>';	
			
			return $html;
		}else{
			return 'Can\'t Calculate inventory please try again.';	
		}
	}
		
}

?>