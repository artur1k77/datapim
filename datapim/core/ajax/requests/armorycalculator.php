<?

if(isset($this->request['steamid'])){
	$armorycalc = new armorycalc();
	$armorycalc->loadInventory($this->request['steamid']);
	echo $armorycalc->renderArmory();
}else{
	echo 'No steamid given';	
}

?>