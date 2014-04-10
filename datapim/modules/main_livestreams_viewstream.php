<?
if($_GET['livestreamid']){
	$livestream = new livestreams();
	$livestream->loadLivestream('twitchtv',$_GET['livestreamid']);
}
?>
<section>
<div class="mcwrap">
	<div class="mcheader"><h1>Livestream: <? echo $livestream->livestream['name']; echo $livestream->livestream['status']; ?></h1></div>
	<div class="lsembed">
    	<? echo $livestream->outputHtmlStream(); ?>
    </div>
    <div class="lschat">
    	<? echo $livestream->outputHtmlChat(); ?>
    </div>
    <div id="ajaxresponse"></div>
<?
//global $mysqli;
//$result = $mysqli->query("SELECT * FROM table ORDER BY RAND() LIMIT 1");
//while($livestream = $result->fetch_assoc()){
	//echo '<div class="sblivestreamel"><div class="sblivestreamimg"><img width="50px" src="/media/livestreams/'.$livestream['previewsmall'].'"></div><div class="sblivestreamlink"><a id="sblvactive" href="'.$livestream['url'].'">'.substr($livestream['name'], 0, 30).'<br><span class="sblivestreamdesc">'.substr($livestream['status'], 0, 30).'</span></a></div></div>';
//}
?>
    <div class="clear"></div>
</div>
</section>