<?
if($this->request['reqKind'] == 'userprofile'){
	$user = user::getInstance();
	if($user->getValidated()){
		if(is_array($this->request['userdata'])){
			$user->saveUserSettings($this->request['userdata']);
		}else{
			return 0;	
		}
	}
}
?>