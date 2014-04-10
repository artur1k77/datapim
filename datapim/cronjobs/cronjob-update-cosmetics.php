<?

$config['cron']['dayoftheweek'] = '*'; // not implemented yet
$config['cron']['hour'] = '01';
$config['cron']['minute'] = '12';

if(runcronjobs::checkTime($config)){
	echo '<pre>';
	
	$cronjob = new cronjobs(true);
	
	// API call
	$cronjob->sendRequest('http://api.steampowered.com/IEconItems_570/GetSchema/v0001/?language=en&key='.STEAM_API_KEY.'','json');
	$cronjob->loadModel('Cosmetics');
	$cronjob->saveData('apiresult');
	
	// KV parser
	$cronjob->loadKVFile($cronjob->apiresult['items_game_url']);
	$cronjob->loadModel('CosmeticsKV');
	$cronjob->saveData('kvresult');
	
	
	
	
	//print_r($cronjob->datamodel->model);
	//print_r($cronjob->kvresult);
	var_dump($cronjob->kvresult);
	//print_r($cronjob->apiresult);

}else{
	echo 'not time yet';
}

?>