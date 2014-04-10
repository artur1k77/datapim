<?
// probleem met variabele plaatjes ... kan veel disk space innemen als dit oneindigt runned :) 
// file cleanup iets bedenken
require('/var/www/vhosts/dota2essentials.com/httpdocs/core/loader/autoload.php');

$twitch = new steamapi(false,'https://api.twitch.tv/kraken/search/streams?q=dota2&limit=25',false);
$output = $twitch->sendrequest();

echo '<h3>output<h3><br>';
echo '<pre>';
$array = json_decode($output,true);
//print_r($array);


ob_start();
echo '<table>';
$i=0;
foreach($array['streams'] as $item){
	$arraytmp['viewers'] = $item['viewers'];
	$arraytmp['name'] = $item['channel']['display_name'];
	$arraytmp['status'] = $item['channel']['status'];
	$arraytmp['previewsmall'] = basename($item['preview']['small']);
	$arraytmp['previewmedium'] = basename($item['preview']['medium']);
	$arraytmp['previewlarge'] = basename($item['preview']['large']);
	$arraytmp['views'] = $item['channel']['views'];
	$arraytmp['logo'] = basename($item['channel']['logo']);
	$arraytmp['url'] = $item['channel']['url'];
	$arraytmp['twitchid'] = $item['channel']['_id'];
	
	//unset($item);

    // insert opbouwen
	$columns = implode(", ",array_keys($arraytmp));
	$escaped_values = array_map('array_map_callback', array_values($arraytmp));
	$values  = implode(", ", $escaped_values);
	
	// on update opbouwen
	$tmp = '';
	foreach($arraytmp as $a=>$b){
		$tmp .= ''.$a.'='.array_map_callback($b).',';	
	}
	$tmp = substr_replace($tmp ,"",-1);
	
	//query uitvoeren
	$sql = "INSERT INTO `livestreams` ($columns) VALUES ($values) ON DUPLICATE KEY UPDATE $tmp";
	$mysqli->query($sql);
	echo  $mysqli->error;
	
	// images opslaan
	save_img($item['preview']['small']);
	save_img($item['preview']['medium']);
	save_img($item['preview']['large']);
	save_img($item['channel']['logo']);
	
	$filename = basename($arraytmp['previewsmall']);
	//<img src="/media/items/'.$filename.'" width="50px" />
	echo '<tr><td><img src="/media/livestreams/'.$filename.'" width="50px" /></td><td>'.$arraytmp['name'].'</td><td>'.$arraytmp['status'].'</td><td></td></tr>';
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
		if(!check_if_file_exists('media/livestreams/',$naam)){
			save_externalfile($url,'media/livestreams/',$naam);
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