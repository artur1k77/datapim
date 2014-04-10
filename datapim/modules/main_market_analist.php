<?
$market = new steammarket();

?>

<section>
<div class="mcwrap">
	<div class="mcheader"><h1>Analyse the steam market</h1></div>
    <? $market->RenderCheapestRarityList(1); ?>
    <? $market->RenderCheapestRarityList(2); ?>
	<? $market->RenderCheapestRarityList(3); ?>
    <? $market->RenderCheapestRarityList(4); ?>
    <? $market->RenderCheapestRarityList(5); ?>
    <? $market->RenderCheapestRarityList(6); ?>
    <? $market->RenderCheapestRarityList(7); ?>
    <? $market->RenderCheapestRarityList(8); ?>
    <div class="clear"></div>
</div>
</section>