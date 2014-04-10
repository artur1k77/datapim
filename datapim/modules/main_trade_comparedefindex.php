<?
$tradefinder = new tradefinder();
$tradefinder->setType('cosmetic');
$tradefinder->getTrades($_GET['defindex']);



?>
<section>
	<div class="mcwrap">
		<div class="mcheader"><h1>Users trading this item</h1></div>
    	<?
				$userinfo = new userinfo();
				$userinfo->getMultiInfo($tradefinder->resultSet);
				//echo '<pre>';
				//print_r($userinfo->info);
				//echo '</pre>';
				foreach($userinfo->info as $user){
					if($user->online==1){ $class='online'; }else{ $class='';}
					echo '<div class="usermeuk '.$class.'" style="float:left;margin:10px;"><a href="/user/'.$user->steamid.'"><img src="'.$user->avatarmedium.'" title="'.$user->profilename.'"></a></div>';
				}
		?>
        <div class="clear"></div>
    </div>
</section>