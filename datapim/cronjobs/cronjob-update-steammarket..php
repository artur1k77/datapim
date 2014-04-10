<?

$config['cron']['dayoftheweek'] = '*'; // not implemented yet
$config['cron']['hour'] = '*';
$config['cron']['minute'] = '*';

if(runcronjobs::checkTime($config)){
	echo '<pre>';
	ob_start();
	$cronjob = new cronjobs(true);
	$steammarket = new steammarket();

	$cronjob->sendRequest('http://steamcommunity.com/market/search/render/?query=appid:570&start=0&count=100','json');

	$array = $cronjob->getRequest();
	
	$steammarket->parseHTML($array['results_html']);
	$steammarket->saveData();
	
	if($cronjob->apiresult['total_count']>100){
		$total = ceil($cronjob->apiresult['total_count']/100);
		$i=1;
		while($i <= $total){
			usleep(250000); // kwart sec sleep	
			$this->nptoken =  'http://steamcommunity.com/market/search/render/?query=appid:570&start='.($i*100).'&count=100';
			$i++;
			echo 'token:'.$this->nptoken.' '.$i.' <Br>';
			$cronjob->sendRequest($this->nptoken,'json');
			$array = $cronjob->getRequest();
			$steammarket->parseHTML($array['results_html']);
			$steammarket->saveData();
			ob_end_flush();			
		}
		
	}
	print_r($steammarket->result);
	echo '</pre>';
}else{
	echo 'not time yet';
}

?>