<?
require('/var/www/vhosts/dota2essentials.com/httpdocs/core/loader/autoload.php');


$steam = new steamapi('getItems',false,false);
$output = $steam->sendrequest();

echo '<h3>output<h3><br>';
echo '<pre>';
$array = json_decode($output,true);
//print_r($array);

ob_start();
echo '<table>';
$i=0;
foreach($array['itemdata'] as $item){
	$item['description'] = $item['desc']; // desc is standaard in sql dus hernoemen naar description
	$item['attrib'] = strip_tags($item['attrib']); // zit html in dus eruit gestript
	$item['valveid'] = $item['id']; // gebruiken onze eigen db id dus valveid toewijzen
	unset($item['components'],$item['desc'],$item['id']); // dit parse ik nog niet

    // insert opbouwen
	$columns = implode(", ",array_keys($item));
	$escaped_values = array_map('array_map_callback', array_values($item));
	$values  = implode(", ", $escaped_values);
	
	// on update opbouwen
	$tmp = '';
	foreach($item as $a=>$b){
		$tmp .= ''.$a.'='.array_map_callback($b).',';	
	}
	$tmp = substr_replace($tmp ,"",-1);
	
	//query uitvoeren
	$sql = "INSERT INTO `items` ($columns) VALUES ($values) ON DUPLICATE KEY UPDATE $tmp";
	$mysqli->query($sql);
	echo  $mysqli->error;
	
	// images opslaan
	save_img('http://cdn.dota2.com/apps/dota2/images/items/'.$item['img'].'');
	
	$filename = basename($item['img']);
	//<img src="/media/items/'.$filename.'" width="50px" />
	echo '<tr><td><img src="/media/items/'.$filename.'" width="50px" /></td><td>'.$item['dname'].'</td><td>'.$item['cost'].'</td><td></td></tr>';
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
		if(!check_if_file_exists('media/items/',$naam)){
			save_externalfile($url,'media/items/',$naam);
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