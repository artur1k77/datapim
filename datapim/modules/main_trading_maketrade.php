<?
$user = user::getInstance();
if((is_numeric($_GET['steamid']) && $user->steamid!=$_GET['steamid'] || is_numeric($_GET['tradeid'])) && $user->getValidated()){ 
?>
<section>
<div class="mcwrap">
<?
	if(isset($_GET['tradeid'])) { // edit trade
		$trade = new trade(2, $_GET['tradeid']);
		$trade->getTradeInfo();
		echo $trade->renderTradeHTML($trade->from_steamid,$trade->to_steamid,false);
	}else{ // new trade
		$trade = new trade(1, $_GET['steamid']);
		echo $trade->renderTradeHTML($user->steamid,$_GET['steamid'],true);
	}
	
	if($trade->valid) {
		if($trade->getTradeId()) {
			if(!$trade->updateTradeViewedState()) {
				echo '<h1>UPDATE VIEWSTATE ERROR</h1>';
			}
		}
//echo '<pre>';
//print_r($trade);
?>    
</div>
</section>

<div id="contentleft">
<?
$main['maincontent'][] = array('module'=>'main_user_inventory', 'inventory-target'=>'current_user', 'inventory-mode'=>'makeatrade','inventory-toggle'=>true,'steamid1'=>$trade->from_steamid,'steamid2'=>$trade->to_steamid);
echo $this->load_html_modules($main['maincontent']);
?>
</div>

<div id="contentright">
<? 
$sidebar['sidebar'][] = array('module'=>'sb_cosmetic_filters', 'unfixed'=>true);
echo $this->load_html_modules($sidebar['sidebar']);
?>
</div>


<?
	} else {
		$this->invalid=true;
	}
} else { // als er geen steam id of user niet ingelogd is de trade onmogelijk
	$this->invalid=true;
}
if($this->invalid) {
?>
<section>
<div class="mcwrap">
	<div class="mcheader fullwidth"><h1>Make a trade</h1></div>
    Something went wrong... Please try again.
</div>
</section>


<?
}
?>