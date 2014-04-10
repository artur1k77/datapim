<?
// pim de user class kan ik nu niet gebruiken om alle users aan te roepen misschien wel handig om generiek te doen
// nu maar ffe los
if(is_numeric($_GET['steamid'])){
$mysqli = database::getInstance();
$result = $mysqli->query("SELECT * FROM users WHERE steamid='".$_GET['steamid']."'");
$user = $result->fetch_assoc();

?>
<section>
<div class="mcwrap">
	<div class="mcheader"><h1><?=$user['profilename'];?></h1></div>
	<img src="<?=$user['avatarlarge'];?>">
    <div class="mcbuttonsmall" onclick="window.location='<?=$user['profileurl'];?>'">Steam Profile</div>
    <? if($_GET['steamid']!=user::getInstance()->steamid) { ?>
    	<div class="mcbuttonsmall" onclick="window.location='steam://friends/add/<?=$user['steamid'];?>'">Add to friends</div>
        <div class="mcbuttonsmall" onclick="window.location='/make-a-trade/<?=$user['steamid'];?>/'">Start a trade</div>
    <? } ?>
    <img src="/template/img/flags/<?=strtolower($user['loccountrycode']);?>.png">
    <? //echo geoip_country_code_by_name($_SERVER['REMOTE_ADDR']); ?>
    <div class="clear"></div>
</div>
</section>
<?
}else{
	// hier proberen als nog player info op te halen vanaf steam. lijkt het alsof we heel veel users hebben :)
?>
<section>
<div class="mcwrap">
	<div class="mcheader"><h1>Player not found</h1></div>
    Could not find the requested player id.
    <div class="clear"></div>
</div>
</section>
<?	
}
?>