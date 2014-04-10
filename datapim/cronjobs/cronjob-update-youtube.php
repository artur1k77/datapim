<?

$config['cron']['dayoftheweek'] = '*'; // not implemented yet
$config['cron']['hour'] = '*';
$config['cron']['minute'] = '15';

if(runcronjobs::checkTime($config)){
	echo '<pre>';
	
	$cronjob = new cronjobs(true);
	$cronjob->getNewsCategory('youtube');
	if(is_array($cronjob->newssources)){
		foreach($cronjob->newssources as $youtubechannel){
			$cronjob->activenewssource = $youtubechannel;
			if(isset($youtubechannel['youtubechannelid'])){
				$cronjob->sendRequest('https://www.googleapis.com/youtube/v3/activities?part=id%2Csnippet%2CcontentDetails&channelId='.$youtubechannel['youtubechannelid'].'&key='.GOOGLE_API_KEY.'&results=50','json');
				$cronjob->loadModel('NewsYoutube');
				$cronjob->saveData('apiresult');
				if(isset($cronjob->apiresult['nextPageToken']) && $youtubechannel['importme']==1){ // alleen alles ophalen bij import anders is eerste pagina genoeg
					// beetje broken poging om youtube pagination te doen... het werkt alles mee gezegd....
					$this->nptoken =  true;
					while($this->nptoken){
						$this->nptoken =  $cronjob->apiresult['nextPageToken'];
						echo 'token:'.$this->nptoken.' <Br>';
						$cronjob->sendRequest('https://www.googleapis.com/youtube/v3/activities?pageToken='.$this->nptoken.'&part=id%2Csnippet%2CcontentDetails&channelId='.$youtubechannel['youtubechannelid'].'&key='.GOOGLE_API_KEY.'&results=50','json');
						$cronjob->loadModel('NewsYoutube');
						$cronjob->saveData('apiresult');
						if(!isset($cronjob->apiresult['nextPageToken'])){
							break;
						}
					}
					$cronjob->setImportMe($youtubechannel['id'],0);
				}
			}
		}
	}
	print_r($cronjob->newssources);

	print_r($cronjob->apiresult);

}else{
	echo 'not time yet';
}

?>