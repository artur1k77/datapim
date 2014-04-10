<?
$pageconfig['columns'] = 2;

$pageconfig['maincontent'][] = 'main_user_details';
if(isset($_GET['steamid']) && $_GET['steamid']!=user::getInstance()->steamid && user::getInstance()->getValidated()) {
	$pageconfig['maincontent'][] = array('module'=>'main_trade_compareinventorywishlist', 'mode'=>'Snapshot', 'itarget'=>'other_user', 'wtarget'=>'current_user');
	$pageconfig['maincontent'][] = array('module'=>'main_trade_compareinventorywishlist', 'mode'=>'Snapshot', 'itarget'=>'current_user', 'wtarget'=>'other_user');
}

$pageconfig['sidebar'][] = 'sb_latest_youtube';
$pageconfig['sidebar'][] = 'sb_ads_gameservers';
$pageconfig['sidebar'][] = 'sb_ads_adsense1';
$pageconfig['sidebar'][] = 'sb_ads_paypaldonate';





?>