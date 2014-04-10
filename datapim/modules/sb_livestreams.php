<section>
<div class="sbwrap">
	<div class="sbheader">Livestreams</div>
    <div class="sblivestreamlist">
<?
$mysqli = database::getInstance();
$result = $mysqli->query("SELECT * FROM livestreams ORDER BY viewers DESC LIMIT 5");
while($livestream = $result->fetch_assoc()){
	echo '<div class="sblivestreamel"><div class="sblivestreamimg"><img width="50px" src="'.$livestream['previewsmall'].'"></div><div class="sblivestreamlink"><a id="sblvactive" href="/livestream/'.$livestream['twitchid'].'/">'.substr($livestream['name'], 0, 30).'<br><span class="sblivestreamdesc">'.substr($livestream['status'], 0, 25).'</span></a></div></div>';
}
?>
	</div>
    <div class="clear"></div>
</div>
</section>