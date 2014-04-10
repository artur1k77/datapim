<?

class news{
	
	function __construct(){
		$this->mysqli = database::getInstance();
	}
	
	function setCategory($name){
		$result = $this->mysqli->query("SELECT * FROM news WHERE name='$name'");
		if($result){
			$array = $result->fetch_assoc();
			$this->activeCat = $array;
			return $array['id'];
		}else{
			utils::throwExcption('News category not found !!');
		}
	}
	
	function getNewsID(){
		if(is_array($this->activeCat)){
			$result = $this->mysqli->query("SHOW TABLE STATUS WHERE name = 'news_items'");
			$array = $result->fetch_assoc();
			return $array['Auto_increment']+1; // eerst volgende increment
		}else{
			utils::throwExcption('Set a category first use: setCategory()');
		}	
	}
	
	function saveNewsID(){
		if(is_array($this->activeCat)){
			$result = $this->mysqli->query("INSERT INTO news_items (nid) VALUES ('".$this->activeCat['id']."')");
			if($this->mysqli->error){
				return false;
			}else{
				return true;	
			}
		}else{
			utils::throwExcption('Set a category first use: setCategory()');
		}		
	}
	
	function getNews($type=false){
		$result = $this->mysqli->query("SELECT * FROM news_dotablog LIMIT 10");
		if($result) {
			while($news = $result->fetch_assoc()){
				$this->articles[] = $news;
			}
		}
	}
	
	function renderNews(){
		if(is_array($this->articles) && count($this->articles)>0){
			foreach($this->articles as $article){
				$news .= '<a href="/news/'.$article['gid'].'/" style="display:block;">';
				$news .= '<div class="newswrapper" id="'.$article['gid'].'">';
				
				$news .= '<div class="newsimg"><img src="/template/img/newsdotaclient.jpg" /></div>';
				$news .= '<div class="newscontents">';
				$news .= '<div class="newstitle">'.$article['title'].'<span class="newsdate">'.$article['date'].'</span></div>';
				$news .= '<div class="newscontentshort">Viewers: '.$this->short_text($article['contents'],350).'</div>';
				$news .= '</div>';
				
				$news .= '</div></a>';
			
			}
			return $news;

		}
	}

	function short_text($string,$chars=500){
		$string = strip_tags($string);
		if (strlen($string) > $chars) {
			$stringCut = substr($string, 0, $chars);
			$string = substr($stringCut, 0, strrpos($stringCut, ' ')).'...'; 
		}
		return $string;	
	}

	
}


?>