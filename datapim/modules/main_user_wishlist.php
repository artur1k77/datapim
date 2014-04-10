<?php
	$user = user::getInstance();
	
	$target = $this->arguments['wishlist-target'];
	$mode = $this->arguments['wishlist-mode'];
	//echo '<pre>';
	//print_r($this->arguments);
	$targetSteamId = $_GET['steamid'];
	
	$size = 75;
	if($mode=='Snapshot') {
		$size=12;
	}
	
	$dataTarget = $target=='current_user'?'current_user':$targetSteamId;
	
	//if($user->getValidated()) {
?>
	<div class="mcwrap">
    <div class="mcheader"><h1><? echo $target=='current_user'?'My-':''; ?>Wishlist <? echo $mode; ?></h1></div>
	<div class="cosmetic_container cs_wishlist" data-queryloc="0" data-pagesize="<? echo $size;?>" data-containertarget="wishlist" data-target="<? echo $dataTarget; ?>" data-mode="<? echo strtolower($mode);?>" style="padding: 10px; height: 160px;width: 720px;" >
    </div>
    <div class="clear"></div>
    </div>
<?
	//}
?>
