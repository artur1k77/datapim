<?
//15C4421EA926C59BD2D320DA4A4051C8
class steamapi{
	
	private $apikey = '15C4421EA926C59BD2D320DA4A4051C8';
	
	function __construct($method=false,$customurl=false,$steamid=false){

			$this->setAPIkey($this->apikey);
			$this->setCall();
			if($steamid){
				$this->setSteamID($steamid);	
			}
			$this->setMethod($method,$customurl);

	}

	public function setAPIkey($key){
		if(strlen($key)==32 && ctype_alnum($key)){
			$this->apikey = $key;
		}else{
			utils::throwExcption('API key not valid..');
		}
	}
	
	public function setSteamID($steamid){
		if(is_numeric($steamid)){
			$this->steamid = $steamid;
		}else{
			utils::throwExcption('Steam ID key not valid..');
		}
	}
		
	public function setURL($url){
		//if(filter_var($url, FILTER_VALIDATE_URL))
			$this->url = $url;
		//}else{
			//utils::throwExcption('URL not valid.');
		//}
	}
	
	public function setCall($call=false){
		if(empty($call)){
			$this->call = 'GET';
			return true;	
		}
		if($call == 'GET' || $call == 'POST'){
			$this->url = $call;
		}else{
			utils::throwExcption('Wrong call type used.');
		}
	}
	
	public function setMethod($method,$customurl=false){
		if($customurl){
			$this->setURL($customurl);
		}else{
			if(ctype_alnum($method)){
				// beetje crappy beter losse functies
					if($method=='getCosmetics'){
						$this->setURL('http://api.steampowered.com/IEconItems_816/GetSchema/v0001/?language=en&key='.$this->apikey.'');
					}elseif($method=='getPlayerCosmetics'){
						$this->setURL('https://api.steampowered.com/IEconItems_570/GetPlayerItems/v0001/?language=en&key='.$this->apikey.'&steamid='.$this->steamid.'');
						//echo $this->url;
					}elseif($method=='getPlayeritems'){
						$this->setURL('https://api.steampowered.com/IEconItems_570/GetPlayerItems/v1/?language=en&key='.$this->apikey.'&steamid='.$this->steamid.'');
					}elseif($method=='getHeros'){
						$this->setURL('https://api.steampowered.com/IEconDota2_570/GetHeroes/v1/?language=en&key='.$this->apikey.'');
					}elseif($method=='getItems'){
						$this->setURL('http://www.dota2.com/jsfeed/itemdata'); // ja dit is niet de steamapi .. nee het boeide me niet het staat gewoon hier
					}elseif($method=='getUserInfo') {
						$this->setURL('https://api.steampowered.com/ISteamUser/GetPlayerSummaries/v0002/?key='.$this->apikey.'&steamids='.$this->steamid.'');
						//echo $this->url;
					} else{
						utils::throwExcption('Method does not exsist.');
					}	
			}else{
				utils::throwExcption('Method type is not valid.');
			}
		}
	}
	
	
	
	public function sendRequest(){
		$ch = curl_init();
		curl_setopt($ch,CURLOPT_URL,$this->url);
		curl_setopt($ch,CURLOPT_CUSTOMREQUEST, $this->call);
		if($this->call == 'POST'){
			curl_setopt($ch,CURLOPT_POST, true);
			curl_setopt($ch,CURLOPT_POSTFIELDS,$this->Data);
		}
		curl_setopt($ch,CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch,CURLOPT_HEADER, 1); // debugging only
		curl_setopt($ch,CURLOPT_VERBOSE, 1);
		
		curl_setopt($ch, CURLOPT_CONNECTTIMEOUT ,5); // wacht max 5 sec voor connectie server 
		curl_setopt($ch, CURLOPT_TIMEOUT, 30); // als er connectie is wacht max 30 sec voor request
		$result = curl_exec($ch);
		// curl object weggooien
		curl_close($ch);
		
		// header los halen van body
		$parts = explode("\r\n\r\nHTTP/", $result);
		$parts = (count($parts) > 1 ? 'HTTP/' : '').array_pop($parts);
		list($headers, $body) = explode("\r\n\r\n", $parts, 2);
		
		// array maken van headers
		//$curlheaders = $this->get_headers_from_curl_response($result);
		//print_r($curlheaders);
		
		//if($curlheaders['http_code']=='HTTP/1.1 200 OK' && $curlheaders['content-length']>0){
			return $body;
		//}else{
		//	utils::throwExcption('API result was empty or API didnt returned 200 OK.');	
		//}
	}


	function get_headers_from_curl_response($response)
	{
		$headers = array();
	
		$header_text = substr($response, 0, strpos($response, "\r\n\r\n"));
	
		foreach (explode("\r\n", $header_text) as $i => $line)
			if ($i === 0)
				$headers['http_code'] = $line;
			else
			{
				list ($key, $value) = explode(': ', $line);
	
				$headers[$key] = $value;
			}
	
		return $headers;
	}
	
	
	
		
}

?>