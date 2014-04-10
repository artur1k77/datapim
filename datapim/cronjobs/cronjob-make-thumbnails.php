<?

$config['cron']['dayoftheweek'] = '*'; // not implemented yet
$config['cron']['hour'] = '01';
$config['cron']['minute'] = '40';

if(runcronjobs::checkTime($config)){
	
	/*
	// delete crap uit je folder als je weer is alles verneukt!!!!
	foreach(glob(''.MEDIA_PATH_COSMETICS.'*.jpg*') as $jpg){
		if(is_file($jpg)){
			unlink($jpg);
		}
	}
	*/
	
	// test versie voor image resizing naar thumbs
	$this->mysqli = database::getInstance();
	$cronjob = new cronjobs(true);
	
	// cosmetics
	$result = $this->mysqli->query("SELECT image_url,defindex FROM cosmetics");
	
	while($array = $result->fetch_assoc()){
		if(!empty($array['image_url'])){
			$cosmetics[] = $array;	
		}
	}
	//print_r($cosmetics);
	if(count($cosmetics)>0){
		foreach($cosmetics as $cosmetic){
			echo 'saving '.MEDIA_PATH_COSMETICS.''.$cosmetic['image_url'].' as '.MEDIA_PATH_COSMETICS.str_replace('.png','',$cosmetic['image_url']).'-fast.jpg<br>';
			$cronjob->generate_image_thumbnail(''.MEDIA_PATH_COSMETICS.''.$cosmetic['image_url'].'',''.MEDIA_PATH_COSMETICS.str_replace('.png','',$cosmetic['image_url']).'-fast.jpg',105,70);	
			$this->mysqli->query("UPDATE cosmetics SET image_fast='".str_replace('.png','',$cosmetic['image_url'])."-fast.jpg' WHERE defindex='".$cosmetic['defindex']."'");
		}
	}else{
		echo 'Array is leeg';	
	}
	
	// heroes
	$result = $this->mysqli->query("SELECT imgverysmall,id FROM heros");
	
	while($array = $result->fetch_assoc()){
		if(!empty($array['imgverysmall'])){
			$heros[] = $array;	
		}
	}
	//print_r($cosmetics);
	if(count($heros)>0){
		foreach($heros as $hero){
			echo 'saving '.MEDIA_PATH_HEROS.''.$hero['imgverysmall'].' as '.MEDIA_PATH_HEROS.str_replace('.png','',$hero['imgverysmall']).'-fast.jpg<br>';
			$cronjob->generate_image_thumbnail(''.MEDIA_PATH_HEROS.''.$hero['imgverysmall'].'',''.MEDIA_PATH_HEROS.str_replace('.png','',$hero['imgverysmall']).'-fast.jpg',59,33);	
			$this->mysqli->query("UPDATE heros SET imgfast='".str_replace('.png','',$hero['imgverysmall'])."-fast.jpg' WHERE id='".$hero['id']."'");
		}
	}else{
		echo 'Array is leeg';	
	}
	

}else{
	echo 'not time yet';
}

?>