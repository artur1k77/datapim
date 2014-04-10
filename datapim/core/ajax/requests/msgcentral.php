<?
if($this->request['reqKind'] == 'checkUpdates'){
	$user = user::getInstance();
	if($user->getValidated()){
		$msgcentral  = new msgsystem($user->steamid);
		if($msgcentral){
			$result = $msgcentral->fetchUpdates();
			echo json_encode($result);
		}
	}
}
?>