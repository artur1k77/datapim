<?
class tradefinder{

	function __construct(){
		$this->mysqli = database::getInstance();
	}
	
	
	function setType($type){
		if($type=='cosmetic'){
			$this->compareType = $type;
			$this->callFunction = 'matchCosmetic';
		}elseif($type=='wishlist'){
			$this->compareType = $type;
			$this->callFunction = 'matchWishlist';
		}else{
			return false;	
		}
	}
	
	function getTrades($id){
		if(isset($this->compareType)){
			$this->id = $id;
			$function = $this->callFunction;
			$this->$function();
		}
		if(count($this->resultSet)>0){
			return 	$this->resultSet;
		}else{
			return 'Nothing found';	
		}
	}
	
	/**MONSTER QUERY COMPARE voltooid
	
	SELECT steamid, MAX(wLCount) + MAX(uCCount) as TotalCount
FROM (
  SELECT steamid, count(*) as wLCount, 0 as uCCount
  FROM user_cosmetics WHERE defindex IN (SELECT defindex FROM user_wishlist WHERE steamid=76561198004741611) AND steamid!=76561198004741611 AND quantity>0 AND tradeQuantity>0 GROUP BY steamid
  UNION ALL
SELECT steamid, 0, COUNT(*)
  FROM user_wishlist WHERE defindex IN (SELECT defindex FROM user_cosmetics WHERE steamid=76561198004741611 AND quantity>0 AND tradeQuantity>0) AND steamid!=76561198004741611 GROUP BY steamid
) AS Both1
GROUP BY steamid
ORDER BY TotalCount DESC

	**/
	
	function matchCosmetic(){
		$result = $this->mysqli->query("SELECT steamid FROM user_cosmetics WHERE defindex=".$this->id." AND quantity>0 AND tradeQuantity>0 ORDER BY lastupdate DESC");
		if($result->num_rows){
			while($array = $result->fetch_assoc()){
				$this->resultSet[$array['steamid']] = $array['steamid'];	
			}
		}else{
			return false;	
		}
	}
	
	function matchWishlist(){
		$user = user::getInstance();
		if($user->getValidated()){
			
			// compare wishlist
			
			$result = $this->mysqli->query("SELECT uC.steamid, COUNT(*) as matches FROM user_cosmetics AS uC JOIN users u ON uC.steamid=u.steamid WHERE defindex IN (SELECT defindex FROM user_wishlist WHERE steamid=".$user->steamid.") AND uC.steamid!=".$user->steamid." AND quantity>0 GROUP BY uC.steamid ORDER BY online DESC, matches DESC");
			if($result->num_rows){
				while($array = $result->fetch_assoc()){
					$this->resultSet[$array['steamid']] = $array['matches'];	
				}
			}else{
				return false;	
			}
		}else{
			return false;	
		}
	}
}
?>