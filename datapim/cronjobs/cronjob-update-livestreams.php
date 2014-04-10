<?

$config['cron']['dayoftheweek'] = '*'; // not implemented yet
$config['cron']['hour'] = '*';
$config['cron']['minute'] = '10';

if(runcronjobs::checkTime($config)){
	echo '<pre>';
	
	$cronjob = new cronjobs(true);
	
	// API call
	$cronjob->resetViews();  // geeft wel een paar sec views nul op de site .. geen id of dit makkelijker kan.
	$cronjob->sendRequest('https://api.twitch.tv/kraken/search/streams?q=dota2&limit=100','json');
	$cronjob->loadModel('twitchtv');
	$cronjob->saveData('apiresult');
	
	// next pages iteraties
	
	if(isset($cronjob->apiresult['_links']['next'])){
		$total = ceil($cronjob->apiresult['_total']/100);
		$i=1;
		while($i <= $total){	
			$this->nptoken =  $cronjob->apiresult['_links']['next'];
			echo 'token:'.$this->nptoken.' '.$i.' <Br>';
			$cronjob->sendRequest($this->nptoken,'json');
			$cronjob->loadModel('twitchtv');
			$cronjob->saveData('apiresult');
			$i++;
		}
	}

	print_r($cronjob->apiresult);

}else{
	echo 'not time yet';
}

?>
