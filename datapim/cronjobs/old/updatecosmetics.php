<?
require('/var/www/vhosts/dota2essentials.com/httpdocs/core/loader/autoload.php');


$steam = new steamapi('getCosmetics',false,false);
$output = $steam->sendrequest();

echo '<h3>output<h3><br>';
echo '<pre>';
$array = json_decode($output,true);
///print_r($array);

ob_start();
echo '<table>';
$i=0;

$extraInfoUrl = $array['result']['items_game_url'];

$parser = new kvparser();
$parser->load($extraInfoUrl);
$parser->parse();

$extraInfo = $parser->toObj();

foreach($array['result']['items'] as $item){
	unset($item['attributes'],$item['tool'],$item['capabilities']); // dit parse ik nog niet

    // insert opbouwen
	$columns = implode(", ",array_keys($item));
	//@TODO Dit moet nog netter
	$columns.=', item_rarity';
	$escaped_values = array_map('array_map_callback', array_values($item));
	$values  = implode(", ", $escaped_values);
	//@TODO Dit moet nog netter
	$item_rarity = $extraInfo->items_game->items->$item['defindex']->item_rarity;
	$item_rarity_number = 1;
	if($item_rarity){
		$item_rarity_number=$extraInfo->items_game->rarities->$item_rarity->value;
	}
	$values.=', '.$item_rarity_number;
	
	// on update opbouwen
	$tmp = '';
	foreach($item as $a=>$b){
		$tmp .= ''.$a.'='.array_map_callback($b).',';	
	}
	$tmp = substr_replace($tmp ,"",-1);
	$tmp.=', item_rarity='.$item_rarity_number;
	
	//query uitvoeren
	$sql = "INSERT INTO `cosmetics` ($columns) VALUES ($values) ON DUPLICATE KEY UPDATE $tmp";
	$mysqli->query($sql);
	echo  $mysqli->error;
	
	// images opslaan
	save_img($item['image_url']);
	save_img($item['image_url_large']);
	
	$filename = basename($item['image_url']);
	//<img src="/media/items/'.$filename.'" width="50px" />
	echo '<tr><td></td><td>'.$item['name'].'</td><td>'.$item['defindex'].'</td></tr>';
	ob_end_flush();
	$i++;
}
echo '</table>';
echo 'Aantal items: '.$i.'';


function array_map_callback($a)
{
  global $mysqli;
  if(!empty($a)){
  	return "'".@mysqli_real_escape_string($mysqli, $a)."'";
  }else{
	return "''"; 
  }
}

function check_if_file_exists($location,$name){
	$file = '/var/www/vhosts/dota2essentials.com/httpdocs/'.$location.''.$name.'';
	if(file_exists($file)){
		return true;	
	}else{
		return false;	
	}
		
}

function save_img($url){
	$naam = basename($url);
	if(!empty($naam)){
		if(!check_if_file_exists('media/cosmetics/',$naam)){
			save_externalfile($url,'media/cosmetics/',$naam);
		}
	}
}

function save_externalfile($url,$location,$name){
		$output = '/var/www/vhosts/dota2essentials.com/httpdocs/'.$location.''.$name.'';
		if(file_put_contents($output, file_get_contents($url))){
			return true;	
		}else{
			return false;
		}
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
?>