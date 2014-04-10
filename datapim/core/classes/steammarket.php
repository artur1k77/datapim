<?

class steammarket{
	
	function __construct(){
		$this->mysqli = database::getInstance();	
	}
	
	function parseHTML($html){
		$dom = new DOMDocument();
		$dom->loadHTML($html);
		
		$xpath = new DOMXPath($dom);
		$items = $xpath->query('//a/div');
		
		$i=0;
		$this->result = array(); // leeg maken voor de volgende iteratie
		foreach($items as $item){
			preg_match_all('/([^\r\n^\t]+)/',$item->nodeValue,$matches);
			if(is_array($matches[0]) && count($matches[0])>0){
				$array['amountonmarket'] = str_replace(',','',$matches[0][0]);
				$array['price'] = str_replace('$','',$matches[0][2]);
				$array['itemname'] = $matches[0][3];
				$array['defindex'] = $this->getDefindex($matches[0][3]);
				$array['keyprice'] = $this->calcKeyprice(str_replace('$','',$matches[0][2]));
				if(!empty($array['defindex'])){ //als defindex niet matched ook nie opslaan
					$this->result[$i] = $array;	
				}
			}
			$i++;
		}		
	}
	
	function getDefindex($name){
		$query = "SELECT defindex FROM cosmetics WHERE item_name='".$this->mysqli->real_escape_string($name)."'";
		$result = $this->mysqli->query($query);
		if(!$this->mysqli->error){
			$defindex = $result->fetch_assoc();
			return $defindex['defindex'];
		}else{
			echo $query;
			utils::throwExcption('Could not match defindex: '.$name.'');
		}
	}
	
	function calcKeyprice($price){
		return	$price/1.79; // dit is in euros prijs is in dollars ... wat is een key in dollars :)
	}
	
	function saveData(){
		if(is_array($this->result)){
			foreach($this->result as $marketinfo){
				echo "INSERT INTO cosmetic_prices (defindex,price_euro,price_keys,amountonmarket) VALUES ('".$marketinfo['defindex']."','".$marketinfo['price']."','".$marketinfo['keyprice']."','".$marketinfo['amountonmarket']."') ON DUPLICATE KEY UPDATE priceeuro='".$marketinfo['price']."', pricekeys='".$marketinfo['keyprice']."', amountonmarket='".$marketinfo['amountonmarket']."'<br>";
				// mysql insert
				$this->mysqli->query("INSERT INTO cosmetic_prices (defindex,price_euro,price_keys,amountonmarket) VALUES ('".$marketinfo['defindex']."','".$marketinfo['price']."','".$marketinfo['keyprice']."','".$marketinfo['amountonmarket']."') ON DUPLICATE KEY UPDATE price_euro='".$marketinfo['price']."', price_keys='".$marketinfo['keyprice']."', amountonmarket='".$marketinfo['amountonmarket']."'");
			}
		}
	}
	

	function RenderCheapestRarityList($rarity,$offset=0,$resultonly=false){
		$this->mysqli->real_escape_string($rarity);
		
		if(!$resultonly){
			$name = $this->mysqli->query("SELECT cr.name FROM cosmetic_rarities cr WHERE valve_value='$rarity'");
			$name = $name->fetch_assoc();
			$html .= '<div class="ma_list_wrap">';
			$html .= '<span class="am_info_'.$rarity.'" data-offset="0"></span>';
			$html .= '<div class="headercontrols"><span class="hc_paginate_prev" data-rarity="'.$rarity.'">&lt;</span>&nbsp;&nbsp;<span class="hc_paginate_next" data-rarity="'.$rarity.'">&gt;</span></div>';
			$html .= '<div class="header">Cheapest '.$name['name'].'</div>';
		}
		
		$result = $this->mysqli->query("SELECT c.defindex,image_fast,c.name,c.item_name,p.price_euro,p.amountonmarket FROM `cosmetics` c INNER JOIN `cosmetic_prices` p on c.defindex=p.defindex WHERE c.defindex!=0 AND c.defindex!='NULL' AND c.item_rarity='$rarity' ORDER BY p.price_euro ASC LIMIT $offset, 10");
		$i=$offset+1;
		$html .= '<div class="am_result_'.$rarity.'">';
		if($result->num_rows){
			while($cosmetic = $result->fetch_assoc()){
				$html .= '<div class="am_item">';
				$html .= '<div class="am_list am_number">'.$i.'</div>';
				$html .= '<div class="am_list am_img"><a href="/item/'.$cosmetic['defindex'].'/"><img src="/media/cosmetics/'.$cosmetic['image_fast'].'" width="50px"></a></div>';
				$html .= '<div class="am_list am_name"><a href="/item/'.$cosmetic['defindex'].'/">'.$cosmetic['item_name'].'</a></div>';
				$html .= '<div class="am_list am_prices"><a href="http://steamcommunity.com/market/listings/570/'.$cosmetic['item_name'].'">$'.$cosmetic['price_euro'].'</a></div>';
				$html .= '</div>';
				
				$i++;
			}
		}else{
			$html .= '<div style="width:100%;text-align:center">No results found !</div>';	
		}
		$html .= '</div>';
		if(!$resultonly){
			$html .= '</div>';
		}
		echo $html;
	}
	
	
	
} 

?>