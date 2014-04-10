<?
class trade{	
	private $trade_id;
	
	public $from_steamid;
	private $from_cosmetics;
	private $old_from_cosmetics;
	private $from_rarities;
	private $old_from_rarities;
	public $to_steamid;
	private $to_cosmetics;
	private $old_to_cosmetics;
	private $to_rarities;
	private $old_to_rarities;
	private $messages;
	
	function getTradeId() {
		return $this->trade_id;	
	}
	function getFromSteamId() {
		return $this->from_steamid;	
	}
	function getFromCosmetics() {
		return $this->from_cosmetics;	
	}
	function getOldFromCosmetics() {
		return $this->old_from_cosmetics;	
	}
	function getFromRarities() {
		return $this->from_rarities;	
	}
	function getOldFromRarities() {
		return $this->old_from_rarities;	
	}
	function getToSteamId() {
		return $this->to_steamid;	
	}
	function getToCosmetics() {
		return $this->to_cosmetics;	
	}
	function getOldToCosmetics() {
		return $this->old_to_cosmetics;	
	}
	function getToRarities() {
		return $this->to_rarities;	
	}
	function getOldToRarities() {
		return $this->old_to_rarities;	
	}
	function getMessages() {
		return $this->messages;	
	}
	
	public function __construct($type, $id) {
		global $mysqli;
		if(user::getInstance()->getValidated()) {
			$steamid = user::getInstance()->getSteamId();
			if($type==1 && is_numeric($id)){ 
				$this->from_steamid=$steamid;
				$this->to_steamid=$id;
				$this->valid = true;
			} elseif($type==2 && is_numeric($id)) {
				$this->trade_id=$id;
				$query = $mysqli->prepare("SELECT from_steamid, to_steamid, lastupdate_steamid, status, creation_date, lastupdate FROM trades as t WHERE t.id=? AND (t.to_steamid=? OR t.from_steamid=?)");
				$query->bind_param("iii",$this->trade_id,$steamid,$steamid);
				$query->execute();
				
				$query->bind_result($from_steamid, $to_steamid, $lastupdate_steamid, $status, $creation_date, $lastupdate);
				
				$query->store_result();
				$numrows = $query->num_rows;
				$query->fetch();
				$this->from_steamid=$from_steamid;
				$this->to_steamid=$to_steamid;
				$query->close();
				if(!$numrows){
					$this->valid = false;	
				}else{
					$this->valid = true;
				}
				
				
				
			} else {
				return false;	
			}
		} else { 
			return false;
		}
	}
	
	public static function getInstance(){
		if(empty($_SESSION['trade'])){
			$c = __CLASS__;
			$_SESSION['trade'] = new $c;
		}
	
		return $_SESSION['trade'];
	}
	
	function updateTradeViewedState() {
		global $mysqli;
		if(user::getInstance()->getSteamId() == $this->from_steamid) {
			$stmt = $mysqli->prepare("UPDATE trades SET from_state=0 WHERE id=?");
		} elseif(user::getInstance()->getSteamId() == $this->to_steamid) {
			$stmt = $mysqli->prepare("UPDATE trades SET to_state=0 WHERE id=?");
		} else {
			return false;	
		}
		$stmt->bind_param("i",$this->trade_id);
		$stmt->execute();
		$stmt->close();
		return true;
	}
	
	function getTradeInfo() {
		global $mysqli;
			
			$query2 = $mysqli->prepare("SELECT trade_id, defindex, rarity, steamid, creation_date, lastupdate, order_number, state FROM trade_items as ti WHERE ti.trade_id=?");
			$query2->bind_param("i",$this->trade_id);
			$query2->execute();
			$query2->bind_result($trade_id, $defindex, $rarity, $steamid, $creation_date, $lastupdate, $order_number, $state);
			
			$from_defindexes=array();
			$old_from_defindexes=array();
			$to_defindexes=array();
			$old_to_defindexes=array();
			$from_rarityIds=array();
			$old_from_rarityIds=array();
			$to_rarityIds=array();
			$old_to_rarityIds=array();
			while($query2->fetch()) {
				if($steamid===$this->from_steamid) {
					if($defindex!=NULL){
						if($state===1) {
							$from_defindexes[$order_number]=$defindex;	
						} else {
							$old_from_defindexes[$order_number]=$defindex;	
						}
					}
					if($rarity!=NULL){
						if($state===1) {
							$from_rarityIds[$order_number]=$rarity;	
						} else {
							$old_from_rarityIds[$order_number]=$rarity;	
						}
					}
				}
				if($steamid===$this->to_steamid) {
					if($defindex!=NULL){
						if($state===1) {
							$to_defindexes[$order_number]=$defindex;
						} else {
							$old_to_defindexes[$order_number]=$defindex;
						}
					}
					if($rarity!=NULL){
						if($state===1) {
							$to_rarityIds[$order_number]=$rarity;	
						} else {
							$old_to_rarityIds[$order_number]=$rarity;	
						}
					}
				}
			}
			$from_cosmetics = cosmetic::getCosmeticsByDefindexes($from_defindexes);
			$old_from_cosmetics = cosmetic::getCosmeticsByDefindexes($old_from_defindexes);
			$from_rarities = rarity::getRaritiesByIds($from_rarityIds);
			$old_from_rarities = rarity::getRaritiesByIds($old_from_rarityIds);
			$to_cosmetics = cosmetic::getCosmeticsByDefindexes($to_defindexes);
			$old_to_cosmetics = cosmetic::getCosmeticsByDefindexes($old_to_defindexes);
			$to_rarities = rarity::getRaritiesByIds($to_rarityIds);
			$old_to_rarities = rarity::getRaritiesByIds($old_to_rarityIds);
			
			foreach($from_defindexes as $order_number=>$defindex) {
				if(array_key_exists($defindex, $from_cosmetics)){
					$this->from_cosmetics[$order_number] = $from_cosmetics[$defindex];
				}
			}
			foreach($old_from_defindexes as $order_number=>$defindex) {
				if(array_key_exists($defindex, $old_from_cosmetics)){
					$this->old_from_cosmetics[$order_number] = $old_from_cosmetics[$defindex];
				}
			}
			foreach($from_rarityIds as $order_number=>$valve_value) {
				if(array_key_exists($valve_value, $from_rarities)){
					$this->from_rarities[$order_number] = $from_rarities[$valve_value];
				}
			}
			foreach($old_from_rarityIds as $order_number=>$valve_value) {
				if(array_key_exists($valve_value, $old_from_rarities)){
					$this->old_from_rarities[$order_number] = $old_from_rarities[$valve_value];
				}
			}
			foreach($to_defindexes as $order_number=>$defindex) {
				if(array_key_exists($defindex, $to_cosmetics)){
					$this->to_cosmetics[$order_number] = $to_cosmetics[$defindex];
				}
			}
			foreach($old_to_defindexes as $order_number=>$defindex) {
				if(array_key_exists($defindex, $old_to_cosmetics)){
					$this->old_to_cosmetics[$order_number] = $old_to_cosmetics[$defindex];
				}
			}
			foreach($to_rarityIds as $order_number=>$valve_value) {
				if(array_key_exists($valve_value, $to_rarities)){
					$this->to_rarities[$order_number] = $to_rarities[$valve_value];
				}
			}
			foreach($old_to_rarityIds as $order_number=>$valve_value) {
				if(array_key_exists($valve_value, $old_to_rarities)){
					$this->old_to_rarities[$order_number] = $old_to_rarities[$valve_value];
				}
			}
			
		$this->messages=trademessage::getTradeMessages($this->trade_id);
	}
	
	function renderTradeHTML($steamid1,$steamid2,$empty=false){
		
		$user1 = new userinfo($steamid1);
		$user1->getInfo();
		$user2 = new userinfo($steamid2);
		$user2->getInfo();
		
	
		if($user1->info->steamid == user::getInstance()->getSteamId()){
			$user1->info->currentuser = 'current_user';
			$user2->info->currentuser = $user2->info->steamid;
			$leftbox = 'to_my_items';
			$rightbox = 'to_his_items';
		}elseif($user2->info->steamid == user::getInstance()->getSteamId()){
			$user1->info->currentuser = $user1->info->steamid;
			$user2->info->currentuser = 'current_user';
			$leftbox = 'to_his_items';
			$rightbox = 'to_my_items';		
		}
	
		
		if($empty){
			$html .= '<div class="mcheader fullwidth"><h1>Make a trade</h1></div>';
		}else{
			$html .= '<div class="mcheader fullwidth"><h1>Edit trade</h1></div>';
		}
		$html .= '<div class="to_container">';
		$html .= '<div class="to_profileleft"><a href="/user/'.$user1->info->steamid.'/" target="_blank"><img src="'.$user1->info->avatarlarge.'" width="80px">&nbsp;&nbsp;<a href="/user/'.$user1->info->steamid.'/" target="_blank">'.$user1->info->profilename.'</a></div>';
		$html .= '<div class="to_profileright"><a href="/user/'.$user2->info->steamid.'/" target="_blank">'.$user2->info->profilename.'</a>&nbsp;&nbsp;<a href="/user/'.$user2->info->steamid.'/" target="_blank"><img src="'.$user2->info->avatarlarge.'" width="80px"></a></div>';
		$html .= '</div>';
		
		$html .= '<div class="to_container">';
		$html .= '<div class="to_left">';
		$html .= '<div data-target="'.$user1->info->currentuser.'" class="to_bar to_extra_options_list"></div>';
		$html .= '</div>';
		$html .= '<div class="to_middle">';
		if($empty){
		$html .= '<div class="to_send_trade_btn" style="text-align:center;"><div id="offer_trade" class="mcbutton">Offer trade</div></div>';
		}else{
		$html .= '<div class="to_send_trade_btn" style="text-align:center;"><div id="save_trade" data-tradeid="'.$this->trade_id.'" class="mcbutton">Save trade</div></div>';	
		}
		$html .= '</div>';
		$html .= '<div class="to_right">';
		$html .= '<div data-target="'.$user2->info->currentuser.'" class="to_bar to_extra_options_list"></div>';
		$html .= '</div>';
		$html .= '</div>';
		
		$html .= '<div class="to_container">';
		$html .= '<div id="'.$leftbox.'" class="to_left to_offer_my_items" data-steamid="'.$user2->info->currentuser.'">';
		if(!$empty){ // fill with the trade elements 
			if(is_array($this->from_rarities) && count($this->from_rarities)>0 && is_array($this->from_cosmetics) && count($this->from_cosmetics)>0) {
				$list = $this->from_cosmetics+$this->from_rarities;
			} elseif(is_array($this->from_rarities) && count($this->from_rarities)>0) {
				$list = $this->from_rarities;
			} elseif(is_array($this->from_cosmetics) && count($this->from_cosmetics)>0) {
				$list = $this->from_cosmetics;
			}
			
			ksort($list);
			foreach($list as $obj){
				$html .= $obj->getHtmlRenderString(false);
			}
		}
		$html .= '</div>';
		$html .= '<div class="to_middle to_arrow_right" style="text-align:center;"><img src="/template/img/trading.png"></div>';
		$html .= '<div id="'.$rightbox.'" class="to_right to_offer_for_items" data-steamid="'.$user2->info->steamid.'">';
		if(!$empty){ // fill with the trade elements
			if(is_array($this->to_rarities) && count($this->to_rarities)>0 && is_array($this->to_cosmetics) && count($this->to_cosmetics)>0) {
				$list = $this->to_cosmetics+$this->to_rarities;
			} elseif(is_array($this->to_rarities) && count($this->to_rarities)>0) {
				$list = $this->to_rarities;
			} elseif(is_array($this->to_cosmetics) && count($this->to_cosmetics)>0) {
				$list = $this->to_cosmetics;
			}
			ksort($list);
			foreach($list as $obj){
				$html .= $obj->getHtmlRenderString(false);
			}
		}
		$html .= '</div>';
		$html .= '</div>';
		
		$html .= '<div class="to_container">';
		$html .= '<div class="to_left">';
		$html .= '<!--<div class="to_bar to_suggest_for_items">jou items suggest</div>-->';
		$html .= '<div class="to_add_msg_btn" style="text-align:center;"><div id="to_toggle_message" class="mcbutton">Add message</div></div>';
		$html .= '</div>';
		$html .= '<div class="to_middle">';
		$html .= '</div>';
		$html .= '<div class="to_right">';
		$html .= '<!--<div class="to_bar to_suggest_my_items">zijn item suggest</div>-->';
		$html .= '</div>';
		$html .= '</div>';
		
		$html .= '<div class="to_container">';
		$html .= '<div class="to_left to_offer_my_items">';
		if(!$empty){ // fill with the trade elements 
			$list3 = array();
			if(is_array($this->old_from_rarities) && count($this->old_from_rarities)>0 && is_array($this->old_from_cosmetics) && count($this->old_from_cosmetics)>0) {
				$list3 = $this->old_from_cosmetics+$this->old_from_rarities;
			} elseif(is_array($this->old_from_rarities) && count($this->old_from_rarities)>0) {
				$list3 = $this->old_from_rarities;
			} elseif(is_array($this->old_from_cosmetics) && count($this->old_from_cosmetics)>0) {
				$list3 = $this->old_from_cosmetics;
			}
			
			ksort($list3);
			foreach($list3 as $obj){
				$html .= $obj->getHtmlRenderString(false);
			}
		}
		$html .= '</div>';
		$html .= '<div class="to_middle to_arrow_right" style="text-align:center;"><img src="/template/img/trading.png"></div>';
		$html .= '<div class="to_right to_offer_for_items">';
		if(!$empty){ // fill with the trade elements
			$list4 = array();
			if(is_array($this->old_to_rarities) && count($this->to_rarities)>0 && is_array($this->old_to_cosmetics) && count($this->old_to_cosmetics)>0) {
				$list4 = $this->old_to_cosmetics+$this->old_to_rarities;
			} elseif(is_array($this->old_to_rarities) && count($this->old_to_rarities)>0) {
				$list4 = $this->old_to_rarities;
			} elseif(is_array($this->old_to_cosmetics) && count($this->old_to_cosmetics)>0) {
				$list4 = $this->old_to_cosmetics;
			}
			ksort($list4);
			foreach($list4 as $obj){
				$html .= $obj->getHtmlRenderString(false);
			}
		}
		$html .= '</div>';
		$html .= '</div>';
		
		$html .= '<div class="to_container" id="to_message" style="display:none;">';
		$html .= '<div class="to_left to_message">';
		$html .= '<label for="message">Message:</label><br>';
		$html .= '<textarea id="message" rows="10" placeholder="Type here ..."></textarea>';
		$html .= '</div>';
		$html .= '<div class="to_middle">';
		$html .= '</div>';
		$html .= '<div class="to_right">';
		if(count($this->messages)>0){
			foreach($this->messages as $message){
				$html .= '<div class="tr_message_history">'.$message->getMessage().'</div>';
			}
		}
		$html .= '</div>';
	
		$html .= '</div>';
		
		$html .= '<div class="clear"></div>';
		
		
		//$html .= '<div class="to_container">';
		//$html .= '<div class="to_inventory_toggle">';
		//$html .= '<div id="'.$user1->info->currentuser.'" class="to_toggle">'.$user1->info->profilename.'<img src="'.$user1->info->avatarsmall.'" width="30px"></div>';
		//$html .= '<div id="'.$user2->info->currentuser.'" class="to_toggle">'.$user2->info->profilename.'<img src="'.$user2->info->avatarsmall.'" width="30px"></div>';
		//$html .= '</div>';
		//$html .= '</div>';
		
		return $html;
	}
}
?>