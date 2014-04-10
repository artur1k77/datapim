<?php
	$user = user::getInstance();
	
	$mode = $this->arguments['mode'];
	$iTarget = $this->arguments['itarget'];
	$wTarget = $this->arguments['wtarget'];
	
	$targetSteamId = $_GET['steamid'];
	
	$size = 75;
	if($mode=='Snapshot') {
		$size=12;
	}
	
	$idataTarget = $iTarget=='current_user'?'current_user':$targetSteamId;
	$wdataTarget = $wTarget=='current_user'?'current_user':$targetSteamId;
	
	//if($user->getValidated()) {
?>
    <div id="response_container"></div>
        <div class="mcwrap">
        <div class="mcheader"><h1><? echo $iTarget=='current_user'?'I have':'He has'; ?> what <? echo $wTarget=='current_user'?'I want':'He wants';?></h1></div>
        <div class="cosmetic_container cs_compare" data-containertarget="compare" data-queryloc="0" data-pagesize="<? echo $size;?>" data-itarget="<? echo $idataTarget; ?>" data-wtarget="<? echo $wdataTarget; ?>" data-mode="<? echo strtolower($mode);?>" style="padding: 10px; width: 720px;" >
	<!--<div class="cosmetic_container inventory" data-containertarget="inventory" style="padding: 10px 0 0 10px; width: 90%;" oncontextmenu="return false;">-->
		</div>
    	<div class="clear"></div>
        <div class="mcfooter"><div class="mcbuttonsmall" onClick="loadPreviousFilteredCosmetics($('.cosmetic_container.cs_compare[data-itarget=<? echo $idataTarget; ?>][data-wtarget=<? echo $wdataTarget; ?>]'));">Previous</div><div class="mcbuttonsmall" onClick="loadFilteredCosmetics(false, $('.cosmetic_container.cs_compare[data-itarget=<? echo $idataTarget; ?>][data-wtarget=<? echo $wdataTarget; ?>]'));">Next</div></div>
    </div>
<?
	//}
?>