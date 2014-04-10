<?
require('/var/www/vhosts/dota2essentials.com/httpdocs/core/loader/autoload.php');


$steam = new steamapi('getHeros',false,false);
$output = $steam->sendrequest();

echo '<h3>output<h3><br>';
echo '<pre>';
$array = json_decode($output,true);
///print_r($array);

ob_start();
echo '<table>';
$i=0;
foreach($array['result']['heroes'] as $item){

	// get valvename
	$valvename = str_replace('npc_dota_hero_','',$item['name']);
	$imgverysmall = ''.$valvename.'-verysmall.png';
	$imgsmall = ''.$valvename.'-small.png';
	$imgmedium = ''.$valvename.'-medium.png';
	$imgbig = ''.$valvename.'-big.jpg';
	//query uitvoeren
	$sql = "INSERT INTO `heros` (name,ingamename,usedname,imgverysmall,imgsmall,imgmedium,imgbig,valveid) VALUES ('".$mysqli->real_escape_string($item['localized_name'])."','".$item['name']."','".$valvename."','".$imgverysmall."','".$imgsmall."','".$imgmedium."','".$imgbig."','".$item['id']."') ON DUPLICATE KEY UPDATE name='".$mysqli->real_escape_string($item['localized_name'])."' ";
	$mysqli->query($sql);
	echo  $mysqli->error;
	
	// images opslaan
	save_img('http://cdn.dota2.com/apps/dota2/images/heroes/'.$valvename.'_sb.png',$imgverysmall);
	save_img('http://cdn.dota2.com/apps/dota2/images/heroes/'.$valvename.'_hphover.png',$imgsmall);
	save_img('http://cdn.dota2.com/apps/dota2/images/heroes/'.$valvename.'_full.png',$imgmedium);
	save_img('http://cdn.dota2.com/apps/dota2/images/heroes/'.$valvename.'_vert.jpg',$imgbig);
	
	//<img src="/media/items/'.$filename.'" width="50px" />
	echo '<tr><td><img src="/media/heros/'.$imgsmall.'" width="50px" /></td><td></td><td>'.$item['localized_name'].'</td><td>'.$item['id'].'</td></tr>';
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

function save_img($url,$naam){
	//$naam = basename($url);
	if(!empty($naam)){
		if(!check_if_file_exists('media/heros/',$naam)){
			save_externalfile($url,'media/heros/',$naam);
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