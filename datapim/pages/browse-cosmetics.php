<?

$pageconfig['columns'] = 2;

$pageconfig['maincontent'][] = array('module'=>'main_user_wishlist','wishlist-mode'=>'Snapshot', 'wishlist-target'=>'current_user');
$pageconfig['maincontent'][] = 'main_browse_cosmetics';

$pageconfig['sidebar'][] = 'sb_cosmetic_filters';

$pageconfig['filtertarget'] = '.cosmetic_container.cs_overview';

?>