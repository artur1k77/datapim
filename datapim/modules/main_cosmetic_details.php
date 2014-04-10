<?

// nu maar ffe los class cosmetic hiervoor nog intergreren
if(is_numeric($_GET['defindex'])){
$mysqli = database::getInstance();
$result = $mysqli->query("SELECT * FROM cosmetics as c LEFT JOIN cosmetic_prices as p ON c.defindex=p.defindex WHERE c.defindex='{$_GET['defindex']}'");
$cosmetic = $result->fetch_assoc();

print_r($cosmetic);

?>
<section>
<div class="mcwrap">
	<div class="mcheader"><h1><?=$cosmetic['name'];?></h1></div>
	<img src="/media/cosmetics/<?=$cosmetic['image_url'];?>">
    <?=$cosmetic['item_description'];?>
    <div class="mcbuttonsmall" onclick="window.location='http://steamcommunity.com/market/listings/570/<?=$cosmetic['name'];?>'">See item on steam market</div>
    <div class="clear"></div>
</div>
</section>
<?
}else{
	
?>
<section>
<div class="mcwrap">
	<div class="mcheader"><h1>Item not found</h1></div>
    Could not find the requested item id.
    <div class="clear"></div>
</div>
</section>
<?	
}
?>