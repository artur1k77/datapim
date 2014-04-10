<?

class makeatrade{
	
	function __construct($steamid1,$steamid2){
		if(is_numeric($steamid1) && is_numeric($steamid2)){
			$this->mysqli = database::getInstance();
			$this->steamid1 = $steamid1;
			$this->steamid2 = $steamid2;
		}else{
			return false;
		}
	}
	
	function validateInventory($steamid, $defindexes){
		
	}
	
	function setMyItems($items){ // hier nog checking in of het in je inventory bestaat
		if(is_array($items) && count($items)>0){
			foreach($items as $item){
				$this->myitems[] = $item;	
			}
			$this->checkmyitems = true;
		}
	}
	
	function setHisItems($items){ // hier nog checking in of het in je inventory bestaat
		if(is_array($items) && count($items)>0){
			foreach($items as $item){
				$this->hisitems[] = $item;	
			}
			$this->checkhisitems = true;
		}		
	}
	
	function setMyRarities($items){ // hier nog checking in of het in je inventory bestaat
		if(is_array($items) && count($items)>0){
			foreach($items as $item){
				$this->myrarities[] = $item;	
			}
			$this->checkmyrarities = true;
		}
	}
	
	function setHisRarities($items){ // hier nog checking in of het in je inventory bestaat
		if(is_array($items) && count($items)>0){
			foreach($items as $item){
				$this->hisrarities[] = $item;	
			}
			$this->checkhisrarities = true;
		}		
	}
	
	function setMessage($msg){
		if(!empty($msg)){
			$this->message = strip_tags($msg);
			$this->setMessage = true;
		}else{
			$this->message = '';
			$this->setMessage = false;
		}
	}
	
	function setStatus($status){ // als we nog meerdere statussen nodig hebben dan deze setten en returnen ?
		$this->status = $status;	
	}
	
	function saveTrade($trade_id=false){
		
		if(($this->checkmyitems || $this->checkmyrarities) && ($this->checkhisitems || $this->checkhisrarities)){
			
			// trade aanmaken en id opvragen
			if($trade_id) {
				$this->tradeID = $trade_id;
				$invokingSteamId = user::getInstance()->getSteamId();
				$lastUpdatedBySteamId;
				$this->to_state = $this->steamid2==$invokingSteamId?0:1;
				$this->from_state = $this->steamid1==$invokingSteamId?0:1;
				if($stmt = $this->mysqli->prepare("SELECT lastupdate_steamid FROM trades WHERE id=?")) {
					$stmt->bind_param("i",$this->tradeID);
					$stmt->execute();
					$stmt->bind_result($lastUpdatedBySteamId);
					$stmt->fetch();
					echo $stmt->error;
					$stmt->close();	
				} else {
					echo 'error 2.1.1';
					return false;	
				}
				if ($stmt = $this->mysqli->prepare("UPDATE trades SET to_state=?, from_state=?, lastupdate_steamid=? WHERE id=?")) {
					$stmt->bind_param("iiii", $this->to_state, $this->from_state, $invokingSteamId, $this->tradeID);
					$stmt->execute();
					echo $stmt->error;
					$stmt->close();	
				} else{
					echo 'error 2.1.2';
					return false;
				}
				if($invokingSteamId != $lastUpdatedBySteamId) {
					if($stmt = $this->mysqli->prepare("DELETE FROM trade_items WHERE trade_id=? AND state=0")) {
						$stmt->bind_param("i", $this->tradeID);
						$stmt->execute();
						echo $stmt->error;
						$stmt->close();
					} else{
						echo 'error 2.2.1';
						return false;
					}
					if ($stmt = $this->mysqli->prepare("UPDATE trade_items SET state=0 WHERE trade_id=?")) {
						$stmt->bind_param("i", $this->tradeID);
						$stmt->execute();
						echo $stmt->error;
						$stmt->close();	
					} else{
						echo 'error 2.2.2';
						return false;
					}
				} else {
					if($stmt = $this->mysqli->prepare("DELETE FROM trade_items WHERE trade_id=? AND state=1")) {
						$stmt->bind_param("i", $this->tradeID);
						$stmt->execute();
						echo $stmt->error;
						$stmt->close();
					} else{
						echo 'error 2.2.0';
						return false;
					}
				}
			} else {
				if ($stmt = $this->mysqli->prepare("INSERT INTO trades (from_steamid,to_steamid,status) VALUES (?,?,1)")) {
					$stmt->bind_param("ii", $this->steamid1,$this->steamid2);
					$stmt->execute();
					$this->tradeID = $stmt->insert_id;
					echo $stmt->error;
					$stmt->close();	
				}else{
					echo 'error 2.0';
					return false;
				}
			}
			
			if($this->checkmyitems){
			// mijn items toevoegen
				if ($stmt = $this->mysqli->prepare("INSERT INTO trade_items (trade_id,defindex,steamid,order_number) VALUES (?,?,?,?)")) {
					$stmt->bind_param("iiii", $this->tradeID,$defindex,$this->steamid1,$order_number);
					foreach($this->myitems as $obj){
						$defindex = $obj->id;
						$order_number = $obj->onr;
						$stmt->execute();
					}
					echo $stmt->error;
					$stmt->close();	
				}
			}
			
			if($this->checkhisitems){
			// zijn items toevoegen
				if ($stmt = $this->mysqli->prepare("INSERT INTO trade_items (trade_id,defindex,steamid,order_number) VALUES (?,?,?,?)")) {
					$stmt->bind_param("iiii", $this->tradeID,$defindex,$this->steamid2,$order_number);
					foreach($this->hisitems as $obj){
						$defindex = $obj->id;
						$order_number = $obj->onr;
						$stmt->execute();
					}
					echo $stmt->error;
					$stmt->close();	
				}
			}
			
			if($this->checkmyrarities){
				// mijn rarities toevoegen
				if ($stmt = $this->mysqli->prepare("INSERT INTO trade_items (trade_id,rarity,steamid,order_number) VALUES (?,?,?,?)")) {
					$stmt->bind_param("iiii", $this->tradeID,$rarity,$this->steamid1,$order_number);
					foreach($this->myrarities as $obj){
						$rarity = $obj->id;
						$order_number = $obj->onr;
						$stmt->execute();
					}
					echo $stmt->error;
					$stmt->close();	
				}				
			}
			
			if($this->checkhisrarities){
				// mijn rarities toevoegen
				if ($stmt = $this->mysqli->prepare("INSERT INTO trade_items (trade_id,rarity,steamid,order_number) VALUES (?,?,?,?)")) {
					$stmt->bind_param("iiii", $this->tradeID,$rarity,$this->steamid2,$order_number);
					foreach($this->hisrarities as $obj){
						$rarity = $obj->id;
						$order_number = $obj->onr;
						$stmt->execute();
					}
					echo $stmt->error;
					$stmt->close();	
				}				
			}
			
			// mesage items toevoegen
			if($this->setMessage){
				if ($stmt = $this->mysqli->prepare("INSERT INTO trade_messages (from_steamid,trade_id,message) VALUES (?,?,?)")) {
					$m = user::getInstance()->getSteamId();
					$stmt->bind_param("iis", $m,$this->tradeID,$this->message);
					$stmt->execute();
					echo $stmt->error;
					$stmt->close();	
				}
			}

			return true;			
		}else{
			echo 'error 1';
			return false;
		}
		
		
	}
	
}