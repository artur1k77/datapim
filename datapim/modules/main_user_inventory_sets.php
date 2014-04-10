<?php
	$user = user::getInstance();
	$mysqli = database::getInstance();

	
	if($user->getValidated()){
		$pointedQuery = new pointedQuery();
		$pointedQuery->setIgnorePointers();
		$playerinventory = playerinventory::getInventory(NULL,$pointedQuery,$user->steamid);
		
		//print_r($playerinventory->playercosmetics);

	
	// ffe tijdelijk hier om te testen
	foreach($playerinventory->getObjects() as $cosmetic) {
		if(!empty($cosmetic->item_set)){
			$useritemset[mysqli_real_escape_string($mysqli,$cosmetic->item_set)][$cosmetic->defindex] = $cosmetic;
		}
	}


	$result = $mysqli->query("SELECT c.name,c.defindex,c.image_url,c.image_fast,c.item_set,c.hero,h.name as heroname,h.imgfast FROM cosmetics as c LEFT JOIN heros as h ON c.hero=h.valveid WHERE c.item_set IN('".implode("','",array_keys($useritemset))."') ORDER BY h.name ASC,c.item_set ASC");
	echo $mysqli->error;
	while($array = $result->fetch_assoc()){
			$heros[$array['heroname']]['heroimg']['imgsmall'] = $array['imgfast'];
			$heros[$array['heroname']][$array['item_set']][] = $array;
	}
	
	foreach($heros as $name=>$itemsets){
		echo '<section>';
		echo '<div class="mcwrap">';
		echo '<div class="mcheader"><h1>'.$name.'</h1></div>';
		echo '<div class="setsleft" style="width:18%;float:left;padding:1%;"><img src="/media/heros/'.$itemsets['heroimg']['imgsmall'].'" style="margin:5px:"></div>';
		echo '<div class="setsright" style="width:80%;float:left;">';
		unset($itemsets['heroimg']);
		foreach($itemsets as $setname=>$items){
			echo '<span>'.ucfirst(str_replace('_',' ',$setname)).'</span><br>';
			$itemcount = 0;
			$itemsowned = 0;
			
			echo '<div id="'.$setname.'">';
			foreach($items as $item){
				echo '<div style="float:left;">';
				if(isset($useritemset[$setname][$item['defindex']])){
					echo '<img class="setimage greyscale" data-defindex="'.$item['defindex'].'" src="/media/cosmetics/'.$item['image_fast'].'" title="'.$item['name'].'" width="75px" style="margin:0 2px 0 0;">';
					echo '<div class="itemssetoverlay" style="position:absolute; margin:-58px 0 0 2px;"><img src="/template/img/vink.png" width="10px;"></div>';
					$itemsowned++;	
				}else{
					echo '<img class="setimage" data-defindex="'.$item['defindex'].'" src="/media/cosmetics/'.$item['image_fast'].'" title="'.$item['name'].'" width="75px" style="margin:0 2px 0 0;">';
					echo '<div class="itemssetoverlay" style="position:absolute; margin:-58px 0 0 2px;"><img src="/template/img/cross.png" width="10px;"></div>';
				}
				echo '</div>';
				$itemcount++;
			}
			echo '</div>';
			echo '<div class="clear"></div>';
			echo 'Owned '.$itemsowned.'/'.$itemcount.'<div class="mcbuttonsmall buttonitemsets" data-setname="'.$setname.'">To wishlist</div><br>';
			if($itemsowned==$itemcount){
				$totalsets++;	
			}
		}
		echo '</div>';
		echo '<div class="clear"></div>';
		echo '</div>';
		echo '</section>';
	}
	echo '<b>total complete sets: '.$totalsets.'</b>';
	echo '<pre>';
	//print_r($useritemset);
	echo '</pre>';
	} else {
		echo 'Not logged in...';	
	}
	
?>
