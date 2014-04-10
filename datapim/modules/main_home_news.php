<?
	$news = new news();
	$news->getNews();
?>

<section>
<div class="mcwrap" style="margin-top:40px;">
	<div class="mcheader"><h1>Latest Dota 2 News</h1></div>
	<div class="dota2news">
    	<?
			echo $news->renderNews();
		?>
    </div>
    <div class="clear"></div>
</div>
</section>
