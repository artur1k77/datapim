<?php
require '/var/www/vhosts/dota2essentials.com/httpdocs/core/loader/autoload.php';

function back() {
	if(isset($_GET['url'])){
		header('Location: '.$_GET['url']);
	} else {
		header('Location: /');
	}
}

try {
	$openid = new lightopenid('dota2essentials.com');
	$user = user::getInstance();
	//$user->getUserInfo();
	if(isset($_GET['logout'])){
		$user->logout();
		unset($_GET['url']);
		back();	
	} elseif(!$openid->mode) {
		if(!$user->getValidated()) {
			$openid->identity = 'https://steamcommunity.com/openid';
			//$openid->required = array('contact/email');
			//$openid->optional = array('namePerson', 'namePerson/friendly');
			header('Location: ' . $openid->authUrl(false));
		} else {
			$val = $user->getValidated();
			back();
		}
	} elseif($openid->mode == 'cancel') {
		back();
    } else {
        if($openid->validate()) {
        	$id = $openid->identity;
			//echo $openid->identity;
            // identity is something like: http://steamcommunity.com/openid/id/76561197960435530
            // we only care about the unique account ID at the end of the URL.
            $ptn = "/^http:\/\/steamcommunity\.com\/openid\/id\/(7[0-9]{15,25}+)$/";
            preg_match($ptn, $id, $matches);
			
			$steamid = $matches[1];
			$user->processAuth($steamid);
			back();
        } 
        else {
			back();
        }
    }
} catch(ErrorException $e) {
    echo $e->getMessage();
}

?>