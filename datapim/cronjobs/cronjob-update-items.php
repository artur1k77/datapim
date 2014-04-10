<?

$config['cron']['dayoftheweek'] = '*'; // not implemented yet
$config['cron']['hour'] = '01';
$config['cron']['minute'] = '10';

if(runcronjobs::checkTime($config)){
	echo '<pre>';
	
	$cronjob = new cronjobs(true);
	
	// API call
	$cronjob->sendRequest('http://www.dota2.com/jsfeed/itemdata','json');
	$cronjob->loadModel('Items');
	$cronjob->saveData('apiresult');

	//print_r($cronjob->apiresult);

}else{
	echo 'not time yet';
}

?>
