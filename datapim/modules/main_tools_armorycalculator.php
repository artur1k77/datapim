<?
$user = user::getInstance();
?>
<section>
<div class="mcwrap">
	<div class="mcheader"><h1>Armory Calculator</h1></div>
    <div class="armorywrap">
        <div class="armoryimage">
            <img src="/media/cosmetics/expander_warchest.53d62c6d7296c7523c4b73e37e3b23b7119c8603.png">
        </div>
        <div class="armorytext">
            <p>Always wanted to know how much you inventory is worth?. Try our armory calculator.<br><br>
            We get our prices daily from the steam market to get you the best estimate available.<br>
            Due to the new crafting system we cant calculate everything but we will try our best.</p>
            <div class="mcbutton" id="buttonac" data-steamid="<?=$user->steamid;?>" style="margin:10px 0;">Calculate Armory</div>
            <div id="ajaxresponse"></div>
        </div>
    </div>
    

    <div class="clear"></div>
</div>
</section>