<?

$config['cron']['dayoftheweek'] = '*'; // not implemented yet
$config['cron']['hour'] = '01';
$config['cron']['minute'] = '20';

if(runcronjobs::checkTime($config)){
	echo '<pre>';
	
	$cronjob = new cronjobs(true);
	
	// API call
	$cronjob->setNews('dotablog');
	//$cronjob->giveNIDtoOld(); // alleen gebruiken als we oude shit importen zonder NIID
	$cronjob->sendRequest('http://api.steampowered.com/ISteamNews/GetNewsForApp/v0002/?appid=570&count=1000&maxlength=10000&format=json','json');
	$cronjob->loadModel('dotablog');
	$cronjob->saveData('apiresult');

}else{
	echo 'not time yet';
}

?>