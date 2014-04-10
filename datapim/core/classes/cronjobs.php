<?


class cronjobs{
		
	function __construct($verbose=false){
		if($verbose){
			$this->verboseStart();
			$this->verbose = $verbose;	
		}
		$this->mysqli = database::getInstance();
	}
	
	
	function sendRequest($url,$type){
		if($url){
			if($type=='json'){
				$obj = new steamapi(false,$url,false);
				$output = $obj->sendrequest();
				$output = json_decode($output,true);
				if(is_array($output) && count($output)>0){
					if(count($output)==1 && isset($output['result'])){ $output = $output['result']; } //  nodeloze array diepte weghalen.
					if(count($output)==1 && isset($output['appnews'])){ $output = $output['appnews']; } //  nodeloze array diepte weghalen.
					if(isset($output['data']['children'])){ $output = $output['data']; } //  nodeloze array diepte weghalen. hier at op verzinnen gaat te ver :P
					//print_r($output); die();
					$this->apiresult = $output;	
				}else{
					utils::throwExcption('Request was not a valid json response...');
					die('DIE STUPID CODE');
				}
			}else{
				utils::throwExcption('Request type not implemented...');
			}	
		}
	}
	
	public function getRequest(){
		return $this->apiresult;	
	}
	
	function loadModel($model){
		$this->datamodel = new datamodels();
		$this->datamodel->getModel($model);
		if(!is_array($this->datamodel->model)){
			utils::throwExcption('Datamodel not loaded..');
		}
	}
	
	function setNews($name){
		$this->isNews = true;
		$this->news = new news();
		$this->news->setCategory($name);
	}
	
	function loadKVFile($url){
		echo $url;echo '<br>';
		$parser = new kvparser();
		$parser->load($url);
		$parser->parse();
		
		$this->kvresult = $parser->toArray();
		//print_r($this->kvresult); die();
		if(count($this->kvresult)==1 && isset($this->kvresult['items_game'])){ $this->kvresult = $this->kvresult['items_game']; }

	}
	
	function saveData($type=false){
		if(!$type){ utils::throwExcption('No type set, please use "api" or "kv"'); die(); } 
			if(isset($this->apiresult) && isset($this->datamodel)){
				$this->processData($type);	
			}else{
				utils::throwExcption('SaveData model or API result not set');
			}

	}
	
	function processData($type){
		foreach($this->$type as $k=>$resultset){
			echo $k;
			if($this->datamodel->model['databasemodel'][$k]){
				
				$this->activemodel = $this->datamodel->model['databasemodel'][$k];
				
				if(!empty($this->activemodel['savetype'])){
					
					if($this->is_multi($resultset)){ // multi dimensie arrays
						foreach($resultset as $key=>$result){
							$this->updateindexkey = $key; // in dit geval key misschien ook custom hook erin gooien
							if(is_array($result)){
								$this->magicMatching($result);
								if(count($this->magicmatches)>0){
									//print_r($this->magicmatches);
									if($this->activemodel['savetype'] == 'multi'){
										$this->saveImages();
										$this->makeSQLObjMulti();
										$this->saveCustomImg();
										$this->makeSQL();
									}
								}else{
									utils::throwExcption('Magic matching failed to find any matches.');	
								}
							}else{
								utils::throwExcption('Result is not an array');	
							}
						}
					}else{  // flat arrays
						if($this->activemodel['savetype'] == 'single'){
							foreach($resultset as $key=>$value){
								$this->makeSQLObjSingle($key,$value);
								$this->makeSQL();
							}
						}
					}
				}else{
					utils::throwExcption('Savetype not set in datamodel...');
				}
			}
			if($this->verbose){
				$this->verboseOutput();	
			}
		}
	}
	
	function magicMatching($array){
		unset($this->magicmatches);
		array_walk_recursive($array,array($this, 'getMatches'));
		
		if(is_array($this->activemodel['specialfieldsarray'])){
			foreach($this->activemodel['specialfieldsarray'] as $key=>$value){
				//if(in_array($key,$array)){
					if(preg_match('/(.*)\{(.*)\}/',$value,$func)){
						if(method_exists($this,$func[2])){
							$v = $this->$func[2]($array[$key]);
							$tmpvalue = $func[1];
							$this->magicmatches[$key] = $v;
						}
					}
				//}
			}
		}	
	}
	
	function getMatches($value,$key){
		//echo 'array walk: '.$key.' - '.$value.'<br>';
		if(!is_int($key)){ // geeft misschien problemen nodig voor als key een int is
			if(in_array($key,array_keys($this->activemodel['dataset']))){
				$this->magicmatches[$key] = $value;
			}
		}
		
	}
	
	function makeSQLObjMulti(){

		foreach($this->magicmatches as $k=>$v){
			$array[$this->activemodel['dataset'][$k]] = $v;  	
		}
		
		if($this->isNews){
			$array['niid'] = $this->news->getNewsID();	
		}

		if(is_array($this->activemodel['specialfields'])){
			foreach($this->activemodel['specialfields'] as $key=>$value){
				if(preg_match('/(.*)\{(.*)\}/',$value,$func)){
					if(method_exists($this,$func[2])){
						$v = $this->$func[2]($this->magicmatches[$key]);
						$tmpvalue = $func[1];
						$array[$tmpvalue] = $v;
						//echo 'running function '.$func[2].' result: '.$v.' saving in : '.$func[1].'<br>';
					}
				}
			}
		}		

		$this->sqlObj = $array;
		//print_r($this->sqlObj);

	}
	
	function makeSQLObjSingle($key,$value){
		
		// dit misschien mooier ?
		$array[$this->activemodel['dataset'][0]] = $key;
		$array[$this->activemodel['dataset'][1]] = $value;

		$this->sqlObj = $array;
	}
	
				

	
	function is_multi($array) {
    	return (count($array) != count($array, 1));
	}
	 
	
	function makeSQL(){
		if(isset($this->sqlObj) && is_array($this->sqlObj)){
			if($this->is_multi($this->sqlObj)){
				utils::throwExcption('MakeSQL cant handle multi dimensional arrays yet...');
			}else{
				$this->renderSQLString($this->sqlObj);	
			}
		}else{
			utils::throwExcption('SQLOBJ is not set or is not an array');
		}
	}
	
	function giveNIDtoOld(){
		$this->updateNID = true;	
	}
	
	function renderSQLString($array){
		$columns = implode(", ",array_keys($array));
		$columns = preg_replace('/\:(.*)\:/','',$columns); // functienamen uit sql string slopen
		$escaped_values = array_map(array($this, 'giveQuotes'), array_values($array));
		$values  = implode(", ", array_values($escaped_values));
		
		if(!$this->updateNID){
			unset($array['niid']); // voor news insert anders overschrijf ej niid on duplicate keys 
		}
		foreach($array as $a=>$b){
			$duplicates .= ''.$a.'='.$this->giveQuotes($b).',';	
		}
		$duplicates = substr_replace($duplicates ,"",-1);
		
		if(isset($this->activemodel['updateindexkey']) && isset($this->updateindexkey)){
			$updatekey = " WHERE {$this->activemodel['updateindexkey']}='{$this->updateindexkey}' ";
		}
		if(isset($this->updateindexkey) && isset($updatekey)){
			$sql = "UPDATE `{$this->activemodel['tablename']}` SET $duplicates $updatekey";
		}else{
			$sql = "INSERT INTO `{$this->activemodel['tablename']}` ($columns) VALUES ($values) ON DUPLICATE KEY UPDATE $duplicates $updatekey";
		}
		echo $sql;echo '<br>';
		// dit nog netjes preparen enzo
		$this->mysqli->query($sql);
		echo $this->mysqli->error;	echo '<br>';
		
		// stukje erbij voor nieuwe news systeem
		if($this->isNews){
			if($this->mysqli->affected_rows==1){ // insert
				$this->news->saveNewsID();
			}elseif($this->mysqli->affected_rows==2){ // update
				// geen nieuws id opslaan want die had tie al.
			}else{ // niks gedaan dus update met alles het zelfde
				
			}
		}
	}	
	
	
	function giveQuotes($a)
	{
		return "'".$this->mysqli->real_escape_string($a)."'";
	}
	
	function resetViews(){
		$this->mysqli->query("UPDATE livestreams SET viewers=0");	
	}
	
	function saveImages(){
		if(isset($this->activemodel['images'])){
			foreach($this->magicmatches as $k=>$v){
				if(in_array($k,$this->activemodel['images']) && !empty($v)){
					$filename = basename($v);
					$this->magicmatches[$k] = $filename;
					$this->saveimg($v,$filename);
				}
			}
		}
	}
	
	function saveCustomImg(){
		if(isset($this->activemodel['customimageurl'])){
			foreach($this->activemodel['customimageurl'] as $dbfield=>$url){
				if(preg_match('/\{(.*)\}/',$url,$fieldname)){
					$url = preg_replace('/\{(.*)\}/',$this->sqlObj[$fieldname[1]],$url);
					$filename = basename($url);
					$this->sqlObj[$dbfield] = $filename;
					$this->saveimg($url,$filename);
				}
			}
		}		
	}
	

	
	function check_if_file_exists($location,$filename){
		if(file_exists($location.$filename)){
			return true;	
		}else{
			return false;	
		}
			
	}
	
	function saveimg($url,$filename){
		if(!empty($filename)){
			
			if(!$this->check_if_file_exists($this->datamodel->model['config']['img_path'],$filename)){
				$this->save_externalfile($url,$this->datamodel->model['config']['img_path'],$filename);
				//echo 'Img not located saving fresh copy: '.$filename.'<br>';
			}else{
				//echo 'Img needed comparison: '.$filename.'<br>';
				
				
				if($this->isFileChanged($url,$this->datamodel->model['config']['img_path'].$filename,$filename)){
					$this->save_externalfile($url,$this->datamodel->model['config']['img_path'],$filename);
					//echo 'Img exsist, remote file newer then local, saving image: '.$filename.'<br>';
				}else{
					//echo 'Img exsists remote file same as local or no MD5 hash in filename, no save<br>';
				}
				
			}
			
		}
	}
	
	function isFileChanged($remote,$local,$filename){
		if(preg_match('/\.(.*)\./',$filename,$remote)){
			$remote = $remote[1];
			$local = sha1_file($local);
			echo $remote; echo '<br>';
			if(is_string($remote) && is_string($local)){
				if($remote==$local){
					return false; 	
				}else{
					return true;
				}
			}else{
				utils::throwExcption('Cant calculate MD5 hash for file...');	
			}
		}else{
			return true;
			utils::throwExcption('Cant find MD5 hash in filename');	
		}
	}
	
	function save_externalfile($url,$location,$filename){
			
			if(file_put_contents($location.$filename, file_get_contents($url))){
				return true;	
			}else{
				return false;
			}
			
			return true;
	}
	
	function check_if_in_database($mysqli,$table,$condition,$value){
		if ($stmt = $mysqli->prepare("SELECT COUNT(*) FROM `cosmetics` WHERE ".$condition."=?")) {
			$stmt->bind_param("ss", $value);
			$stmt->execute();
			$stmt->bind_result($count);
			$stmt->fetch();
			$stmt->close();
		}
		
		return ($count > 0 ? true : false);	
	}
	
	function getNewsCategory($type=false){
		if($type){
			if ($stmt = $this->mysqli->query("SELECT * FROM `news_index` WHERE type='$type'")) {
				while($row = $stmt->fetch_assoc()){
					$this->newssources[] = $row;
				}
			}
		}else{
			utils::throwExcption('getNewsCategory: no type set.');
		}
	}
	
	function setImportMe($id,$value){
		echo "UPDATE `news_index` SET importme='$value' WHERE id='$id'<br>";
		if ($stmt = $this->mysqli->query("UPDATE `news_index` SET importme='$value' WHERE id='$id'")) {
			return true;
		}else{
			return false;	
		}
	}
	
	function verboseStart(){
		ob_start();	
	}
	
	function verboseOutput(){
		ob_flush();
	}
	
	
	// custom function used by data models
	
	function extractheroname($s){
		return str_replace('npc_dota_hero_','',$s);
	}
	
	function striphtml($s){
		return strip_tags($s);
	}
	
	function arraytojson($s){
		if(is_array($s)){
			return base64_encode(json_encode($s));
		}else{
			return $s;	
		}
	}
	
	function gettwitchname($s){
		return str_replace('http://www.twitch.tv/','',$s);	
	}
	
	function getfromarray($array){
		echo 'script runned<br>';
		print_r($array);
		echo '<br>';
		if(is_array($array)){
			return $array['url'];	
		}
	}
	
	function getnid(){
		return $this->activenewssource['id'];
	}
	
	function convYTtimeToDate($s){
	    return date("Y-m-d H:i:s", strtotime($s));
	}
	
	function getheroname($s){
		if(is_array($s)){
	    	return key($s);
		}else{
			return false;	
		}
	}
	
	function getvalveid($s){
		if(is_array($s)){
			$valveid = $this->mysqli->query("SELECT valveid FROM heros WHERE ingamename='".key($s)."'")->fetch_object()->valveid;
	    	return $valveid;
		}else{
			return false;	
		}
	}
	
	function saveupdatekey(){
		return $this->updateindexkey;	
	}
	
	function getraritycolorhex($s){
		return $this->kvresult['colors'][$s]['hex_color'];
	}
	
	function getrarityindexkv($s){
		$rarity = $this->kvresult['rarities'][$s]['value'];
		if(isset($rarity)){
	    	return $this->kvresult['rarities'][$s]['value'];
		}else{
			return 1;	
		}
	}
	
	function savecosmeticthumb($s){
		$this->generate_image_thumbnail($s,''.MEDIA_PATH_COSMETICS.basename($s).'-fast.jpg',105,70);
		return ''.basename($s).'-fast.jpg';
	}
	





	function generate_image_thumbnail($source_image_path, $thumbnail_image_path,$imgwidth=150,$imgheight=150){
		list($source_image_width, $source_image_height, $source_image_type) = getimagesize($source_image_path);
		switch ($source_image_type) {
			case IMAGETYPE_GIF:
				$source_gd_image = imagecreatefromgif($source_image_path);
				break;
			case IMAGETYPE_JPEG:
				$source_gd_image = imagecreatefromjpeg($source_image_path);
				break;
			case IMAGETYPE_PNG:
				$source_gd_image = imagecreatefrompng($source_image_path);
				break;
		}
		if ($source_gd_image === false) {
			return false;
		}
		$source_aspect_ratio = $source_image_width / $source_image_height;
		$thumbnail_aspect_ratio = $imgwidth / $imgheight;
		if ($source_image_width <= $imgwidth && $source_image_height <= $imgheight) {
			$thumbnail_image_width = $source_image_width;
			$thumbnail_image_height = $source_image_height;
		} elseif ($thumbnail_aspect_ratio > $source_aspect_ratio) {
			$thumbnail_image_width = (int) ($imgheight * $source_aspect_ratio);
			$thumbnail_image_height = $imgheight;
		} else {
			$thumbnail_image_width = $imgwidth;
			$thumbnail_image_height = (int) ($imgwidth / $source_aspect_ratio);
		}
		$thumbnail_gd_image = imagecreatetruecolor($thumbnail_image_width, $thumbnail_image_height);
		imagecopyresampled($thumbnail_gd_image, $source_gd_image, 0, 0, 0, 0, $thumbnail_image_width, $thumbnail_image_height, $source_image_width, $source_image_height);
		imagejpeg($thumbnail_gd_image, $thumbnail_image_path, 90);
		imagedestroy($source_gd_image);
		imagedestroy($thumbnail_gd_image);
		return true;
	}

	
	
}