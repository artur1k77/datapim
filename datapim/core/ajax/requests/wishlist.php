<?
$user = user::getInstance();
$reqKind = $this->request['reqKind'];
if($user->getValidated()) {
	$userwishlist = playerwishlist::getInstance();
	$defIndex = intval($this->request['defIndex']);
	
	$return['status']=false;
	$return['defIndex']=$defIndex;
	$return['steamId']=$user->steamid;
	if($reqKind==='addToWishlist') {
		if(!empty($defIndex) && is_integer($defIndex)) {
			$return['status']=$userwishlist->addWishlistCosmetic($defIndex);
		}
	} elseif($reqKind==='removeFromWishlist') {
		if(!empty($defIndex) && is_integer($defIndex)) {
			$return['status']=$userwishlist->removeWishlistCosmetic($defIndex);
		}
	}
	echo json_encode($return);
}
// hier wishlist toevoegen weghalen reuqest afhandelen

?>