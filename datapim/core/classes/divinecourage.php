<?

class divinecourage{
	
	protected $db;
	
	function __construct(){
		$this->db = database::getInstance();	
	}
	
	function divine_me($count){
		if(is_numeric($count) && $count<=10){
			$this->herocount = $count;
 			$this->load_divine_set();
		}
	}
	
	function load_divine_set(){
		$this->get_random_hero();
	}
	
	function get_random_hero(){
		$statement = $this->db->query('SELECT * FROM heros ORDER BY RAND() LIMIT '.$this->herocount.'');
		$i=0;
		while($heros = $statement->fetch_assoc()){
			
			$this->dataset['heroes'][$i] = $heros;
			$this->dataset['heroes'][$i]['selecteditems'] = $this->get_random_items();
			
			$this->usedheros[] = $heros['id'];
			
			$i++;
		}
	}
	
	function get_random_items(){
		$statement = $this->db->query("SELECT * FROM items WHERE valveid IN (214,50,180,48) ORDER BY RAND() LIMIT 1");
		while($items = $statement->fetch_assoc()){
			$array[] = $items;	
		}
		
		$notin = " AND valveid NOT IN (214,50,180,48,67,69,118,162,170,149,106,193,104,201,202,203,143,129,174) ";
		$where = " WHERE cost>1000 AND qual != 'consumable'  AND created=1 ";
		$statement = $this->db->query("SELECT * FROM items $where $notin ORDER BY RAND() LIMIT 4 ");
		while($items = $statement->fetch_assoc()){
			$array[] = $items;	
		}
		return $array;
	}
	
	function outputHTML(){
		if(isset($this->dataset)){
			unset($this->html);
			foreach($this->dataset['heroes'] as $hero){
				$this->html .= '<div class="dcresult">';
				
				$this->html .= '<div class="dcheroimg"><img src="/media/heros/'.$hero['imgmedium'].'" title="'.$hero['name'].'"></div>';
				$this->html .= '<div class="dctext"><div class="dcheading">'.$hero['name'].'</div>';
				$cptext = ''.$hero['name'].' with ';
				$this->html .= '<div class="dvitems">';
				foreach($hero['selecteditems'] as $item){
					$this->html .= '<img src="/media/items/'.$item['img'].'" title="'.$item['dname'].'" width="70px">';
					$cptext .= ''.$item['dname'].', ';
				}
				$this->html .= '</div>';
				$this->html .= '<div class="dccptext">'.substr($cptext,0,-2).'</div>';
				$this->html .= '</div>';
				$this->html .= '<div class="clear"></div>';
				$this->html .= '</div>';
			}
		}
		return $this->html;
	}
	
	function output(){
		return $this->dataset;
	}
	
}