<?	
	$user = user::getInstance();
	if($user->getValidated()){	
		$tradeoverview = new tradeoverview($user->steamid);
		$tradeoverview->getTrades($this->request['start'], $this->request['view']);
		
		if($tradeoverview->trades){
			foreach($tradeoverview->trades as $trade){	
			echo $tradeoverview->renderTrade($trade);
		}
		}else{
			echo 'No trades found!';	
		}
		
		echo '<pre>';
		print_r($tradeoverview->trades);
		echo '</pre>';
	}
?>