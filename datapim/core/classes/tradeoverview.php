<?
class tradeoverview{	

	
	public function __construct($steamid) {
		if(is_numeric($steamid)){
			$this->mysqli = database::getInstance();
			$this->steamid = $steamid;
			$this->user = user::getInstance();
		}else{
			return false;	
		}
	}
	
	function getTrades($start=0, $view='new'){
		if(!is_numeric($start)) {
			$start=0;	
		}
		$fromStateFilter=1;
		$toStateFilter=1;
		if($view==='new') {
			$fromStateFilter=1;
			$toStateFilter=1;
		} elseif($view==='unchanged') {
			$fromStateFilter=0;
			$toStateFilter=0;
		}
		
		$r = $this->mysqli->query("SELECT t.id as trade_id, t.from_steamid, t.to_steamid, t.creation_date, t.status, t.lastupdate, t.lastupdate_steamid, ti.steamid, ti.defindex, ti.rarity, cosm_rarity.color, cosm.image_fast FROM (SELECT id, from_steamid, to_steamid, creation_date, status, lastupdate, lastupdate_steamid, from_state, to_state FROM trades WHERE (from_steamid = '".$this->steamid."' AND from_state=".$fromStateFilter.") OR (to_steamid = '".$this->steamid."' AND to_state=".$toStateFilter.") ORDER BY lastupdate DESC LIMIT ".$start.",10) as t INNER JOIN trade_items as ti ON t.id=ti.trade_id LEFT JOIN cosmetics as cosm ON ti.defindex=cosm.defindex LEFT JOIN cosmetic_rarities as cosm_rarity ON ti.rarity=cosm_rarity.valve_value WHERE ti.state=1 ORDER BY t.lastupdate DESC, t.id DESC, ti.order_number ASC");
		
		while($result = $r->fetch_assoc()){
			// arraytje mooie maken
			$trades[$result['trade_id']]['tradeid'] = $result['trade_id'];
			$trades[$result['trade_id']]['from_steamid'] = $result['from_steamid'];
			$trades[$result['trade_id']]['to_steamid'] = $result['to_steamid'];
			$trades[$result['trade_id']]['creation_date'] = $result['creation_date'];
			$trades[$result['trade_id']]['status'] = $result['status'];
			$trades[$result['trade_id']]['lastupdate'] = $result['lastupdate'];
			$trades[$result['trade_id']]['lastupdate_steamid'] = $result['lastupdate_steamid'];
			unset($result['id'],$result['from_steamid'],$result['to_steamid'],$result['creation_date'],$result['status'],$result['lastupdate']);
			$trades[$result['trade_id']]['items'][] = $result;	
		}
		if(count($trades)>0){
			$this->trades = $trades;
		}else{
			return false;	
		}
	}
	
	function renderTrade($info){
		$tz = new DateTimeZone('Europe/Amsterdam');
		
		if(!is_array($info)){ return false;}
		if($info['from_steamid']==$this->steamid){
			$user1 = $this->user;
			$user2 = new userinfo($info['to_steamid']);
			$user2->getInfo();
			$user2 = $user2->info;

		}else{
			$user1 = new userinfo($info['from_steamid']);
			$user1->getInfo();
			$user1 = $user1->info;
			$user2 = $this->user;

		}
		
		foreach($info['items'] as $item){
			if($item['steamid'] == 	$user1->steamid){
				$user1items[] = $item;	
			}else{
				$user2items[] = $item;
			}
		}

		$html .= '<div class="tr_overview">';
			$lU = new DateTime($info['lastupdate']);
			$html .= '<center><b>Last Change: </b>'.$lU->setTimeZone($tz)->format('d/m/y H:i:s').'</center>';
			$html .= '<div class="tr_overview_el tr_overview_img">'.($user1->steamid==$info['lastupdate_steamid']?'<img style="position: absolute;" height="15px" width="15px" src="/template/img/trade_icon.jpg">':'').'<img src="'.$user1->avatarmedium.'"></div>';
			$html .= '<div class="tr_overview_el tr_overview_cosmetics">';
			foreach($user1items as $item){
				if($item['defindex']) {
					$html .= '<img src=/media/cosmetics/'.$item['image_fast'].' width="30px;" 	style="margin:2px;">';
				} else {
					$html .= '<div style="background-color:'.$item['color'].'; margin:2px; width:30px; height:20px; display:inline-block;"></div>';	
				}
			}
			$html .= '</div>';
			
			$html .= '<div class="tr_overview_el"><img src="/template/img/trading.png" width="50px"></div>';
		
			$html .= '<div class="tr_overview_el tr_overview_cosmetics">';
			foreach($user2items as $item){
				if($item['defindex']) {
					$html .= '<img src=/media/cosmetics/'.$item['image_fast'].' width="30px;" 	style="margin:2px;">';
				} else {
					$html .= '<div style="background-color:'.$item['color'].'; margin:2px; width:30px; height:20px; display:inline-block;"></div>';	
				}	
			}	
			$html .= '</div>';
			$html .= '<div class="tr_overview_el tr_overview_img">'.($user2->steamid==$info['lastupdate_steamid']?'<img style="position: absolute;" height="15px" width="15px" src="/template/img/trade_icon.jpg">':'').'<img src="'.$user2->avatarmedium.'"></div>';
			
		
		$html .= '<div class="clear"></div>';
		$html .= '<div class="mcbuttonsmall" onclick="window.location=\'/edit-trade/'.$info['tradeid'].'/\'">Edit Trade</div>';
		$html .= '</div>';
		
		//print_r($user1);
		//print_r($user2);
		return $html;		
	}
}
?>