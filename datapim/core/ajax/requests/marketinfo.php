<?

if(isset($this->request['offset']) && is_numeric($this->request['rarity'])){
	$market = new steammarket();
	$market->RenderCheapestRarityList($this->request['rarity'],$this->request['offset'],true);
}else{
	echo 'No results found!';	
}




?>