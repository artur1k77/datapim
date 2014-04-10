<?
$user = user::getInstance();
if($user->getValidated()){	
?>
    <section>
    <div class="mcwrap">
        <div class="mcheader"><h1>Trading Overview</h1></div>
        <div class="mcbuttonsmall" onclick="$('.tradeoverview').data('start',0).data('view','new');fetchTradeOverview();">New/Updated Trades</div>
        <div class="mcbuttonsmall" onclick="$('.tradeoverview').data('start',0).data('view','unchanged');fetchTradeOverview();">Unchanged Trades</div>
        <div class="tradeoverview" data-start="0" data-view="new">
        
        </div>
    </div>
    </section>
<?
}else{
?>

<section>
<div class="mcwrap">
	<div class="mcheader fullwidth"><h1>Trade Overview</h1></div>
    You must be logged in to use this page !
</div>
</section>


<?
}
?>
