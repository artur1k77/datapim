<?


class livestreams{
	
	function __construct(){
		$this->mysqli = database::getInstance();
	}

	// view a livestream 
	function loadLivestream($type,$id){
		if(is_string($type) && is_numeric($id)){
			$this->type = $type;
			$this->id = $id;
			$this->loadLivestreamData();
		}else{
			utils::throwExcption('Type en ID need to be set');	
		}
	}
	
	function loadLivestreamData(){
		$query = "SELECT * FROM livestreams WHERE twitchid='{$this->id}'";
		$result = $this->mysqli->query($query);
		if(!$this->mysqli->error){
			$this->livestream = $result->fetch_assoc();
		}else{
			utils::throwExcption('No livestream data available');
		}
	}
	
	function outputHtmlStream(){
		if(is_array($this->livestream)){
			return '<object type="application/x-shockwave-flash" height="439" width="720" id="live_embed_player_flash" data="http://www.twitch.tv/widgets/live_embed_player.swf?channel='.$this->livestream['twitchname'].'" bgcolor="#000000"><param name="wmode" value="transparent" /><param name="allowFullScreen" value="true" /><param name="allowScriptAccess" value="always" /><param name="allowNetworking" value="all" /><param name="movie" value="http://www.twitch.tv/widgets/live_embed_player.swf" /><param name="flashvars" value="hostname=www.twitch.tv&channel='.$this->livestream['twitchname'].'&auto_play=true&start_volume=25" /></object>';
		}else{
			utils::throwExcption('No livestream data availeble');
		}
	}
	
	function outputHtmlChat(){
		if(is_array($this->livestream)){
			return '<iframe frameborder="0" scrolling="no" id="chat_embed" src="http://twitch.tv/chat/embed?channel='.$this->livestream['twitchname'].'&amp;popout_chat=true" height="500" width="720"></iframe>';
		}else{
			utils::throwExcption('No livestream data available');
		}
	}
	
	
	// livestream overview 

	function getLivestreams($filter, $pointedQuery){
		$livestreams = array();
		$mysqli = database::getInstance();
		
		if(empty($pointedQuery)) {
			$pointedQuery = new pointedquery();
		}
		if(!isset($filter) || empty($filter)) {
			$filter = '';	
		}
		//$pointedQuery->messages[] = "SELECT * FROM livestreams ".$filter." ORDER BY viewers DESC ".$pointedQuery->createLimitString();
		
		$query = "SELECT * FROM livestreams ".$filter." ORDER BY viewers DESC ".$pointedQuery->createLimitString();
		$result = $this->mysqli->query($query);
		if(!$this->mysqli->error){
			while($ls = $result->fetch_assoc()){
				$livestream = new livestream();
				$livestream->twitchid = $ls['twitchid'];
				$livestream->previewmedium = $ls['previewmedium'];
				$livestream->status = $ls['status'];
				$livestream->name = $ls['name'];
				$livestream->logo = $ls['logo'];
				$livestream->viewers = $ls['viewers'];
				$livestreams[] = $livestream;
			}
		}else{
			utils::throwExcption('No livestream data available');
		}
		$pointedQuery->incrementQueryLocation();
		$pointedQuery->setObjects($livestreams);
		
		return $pointedQuery;
	
	}
	
	function renderLivestreams(){
		if(count($this->livestreams)>0){
			foreach($this->livestreams as $livestream){
				$ls .= '<a href="/livestream/'.$livestream['twitchid'].'/" style="display:block;">';
				$ls .= '<div class="livestreamwrapper" id="'.$livestream['twitchid'].'">';
				
				$ls .= '<div class="livestreamhover '.$livestream['twitchid'].'" style="display:none;"><img src="'.$livestream['previewmedium'].'" width="150px">'.$livestream['status'].'</div>';
				
				$ls .= '<div class="livestreamchannelname">'.$livestream['name'].'</div>';
				$ls .= '<div class="livestreamlogo"><img  src="'.$livestream['logo'].'" width="150px"></div>';
				$ls .= '<div class="livestreamchannelname">Viewers: '.$livestream['viewers'].'</div>';
				
				$ls .= '</div></a>';
				
			}
		}else{
			$ls = 'Livestream data is unavailable please try again later....';	
		}	
		return $ls;	
	}
	
}