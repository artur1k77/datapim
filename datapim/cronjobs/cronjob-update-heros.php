<?

$config['cron']['dayoftheweek'] = '*'; // not implemented yet
$config['cron']['hour'] = '01';
$config['cron']['minute'] = '02';

if(runcronjobs::checkTime($config)){
	echo '<pre>';
	
	$cronjob = new cronjobs(true);
	
	// API call
	$cronjob->sendRequest('https://api.steampowered.com/IEconDota2_570/GetHeroes/v1/?language=en&key='.STEAM_API_KEY.'','json');
	$cronjob->loadModel('Heroes');
	$cronjob->saveData('apiresult');

	//print_r($cronjob->apiresult);

}else{
	echo 'not time yet';
}

?>
