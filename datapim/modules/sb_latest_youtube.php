<section>
<div class="sbwrap">
	<div class="sbheader">Youtube</div>
    <div class="sblivestreamlist">
<?
global $mysqli;
$result = $mysqli->query("SELECT * FROM news_youtube ORDER BY publisheddate DESC LIMIT 5");
while($youtube = $result->fetch_assoc()){
	echo '<div class="sblivestreamel"><div class="sblivestreamimg"><img width="50px" src="'.preg_replace('/hqdefault/','default',$youtube['img']).'"></div><div class="sblivestreamlink"><a id="sblvactive" href="/youtube/'.$youtube['id'].'/">'.substr($youtube['channeltitle'], 0, 30).'<br><span class="sblivestreamdesc">'.substr($youtube['title'], 0, 30).'</span></a></div></div>';
}
?>
	</div>
    <div class="clear"></div>
</div>
</section>