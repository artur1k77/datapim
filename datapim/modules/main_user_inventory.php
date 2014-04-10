<?php
	$user = user::getInstance();
	
	$target = $this->arguments['inventory-target'];
	$mode = $this->arguments['inventory-mode'];
	
	// meuk voor toggle functie
	$toggle = $this->arguments['inventory-toggle'];
	$toggleid1 = $this->arguments['steamid1'];
	$toggleid2 = $this->arguments['steamid2'];
	
	$user1 = new userinfo($toggleid1);
	$user1->getInfo();
	$user2 = new userinfo($toggleid2);
	$user2->getInfo();	
	
	$current = user::getInstance()->getSteamId() == $toggleid1?'current_user':$toggleid1;
	$current2 = user::getInstance()->getSteamId() == $toggleid2?'current_user':$toggleid2;
	// einde
	
	$targetSteamId = $_GET['steamid'];
	
	$size = 75;
	if($mode=='Snapshot') {
		$size=12;
	}
	
	$dataTarget = $target=='current_user'?'current_user':$targetSteamId;
	
	//if($user->getValidated()) {
?>
    <div id="response_container"></div>
        <div class="mcwrap">
        <? if($toggle){ ?>
	        <div class="mcheader"><div class="to_inventory_toggle"><div id="<?=$current;?>" class="to_toggle toggle_left"><img src="<?=$user1->info->avatarsmall;?>" width="30px"><?=$user1->info->profilename;?></div><div id="<?=$current2;?>" class="to_toggle toggle_right"><?=$user2->info->profilename;?><img src="<?=$user2->info->avatarsmall;?>" width="30px"></div></div></div>
        <? } ?>
       	<div class="mcheader"><h1><? echo $target=='current_user'?'My ':'His '; ?>Inventory</h1></div>
        <div id="<? echo $dataTarget.'_inventory_container';?>" class="cosmetic_container cs_inventory" data-queryloc="0" data-pagesize="<? echo $size;?>" data-containertarget="inventory" data-target="<? echo $dataTarget; ?>" data-mode="<? echo strtolower($mode);?>" style="padding: 10px; height: 160px;width: 720px;" >
	<!--<div class="cosmetic_container inventory" data-containertarget="inventory" style="padding: 10px 0 0 10px; width: 90%;" oncontextmenu="return false;">-->
		</div>
    	<div class="clear"></div>
    </div>
<?
	//}
?>