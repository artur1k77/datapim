<?

$config['cron']['dayoftheweek'] = '*'; // not implemented yet
$config['cron']['hour'] = '*';
$config['cron']['minute'] = '*';

if(runcronjobs::checkTime($config)){
	echo '<pre>';
	ob_start();
	$cronjob = new cronjobs(true);

	$cronjob->setNews('reddit');
	$cronjob->sendRequest('http://www.reddit.com/r/dota2/new.json?sort=new&count=500','json');
	$cronjob->loadModel('Reddit');
	$cronjob->saveData('apiresult');

	print_r($cronjob->apiresult);
	echo '</pre>';
}else{
	echo 'not time yet';
}

?>