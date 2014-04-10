<section>
<div id="<? echo $this->arguments['unfixed']==true?'':'filterwrap'; ?>" class="sbwrap">
	<div id="responseoverlay"></div>
	<div class="sbheader"><h1>Filters</h1></div>
    <div class="sbfilterlist">    
        <!--<div class="sblistbutton" id="refInv_btn" type="button">Refrosh Inventory</div>
        <div class="sblistbutton" id="save_btn" type="button">Save Changes</div>-->
        <input type="text" class="cosmetic_search_txt">
        <div id="response_container"></div>
		<br />
        <div class="sblistbutton" id="add_filters_btn" type="button">Add Filter</div>
        <div id="active_filters">
            <div id="hero_filters" style="display: none">
               	<div class="sbheader"><b>Filtered Heroes</b></div>
            </div>
            <div id="rarity_filters" style="display: none">
               	<div class="sbheader"><b>Filtered Rarities</b></div>
            </div>
            <div id="type_filters" style="display: none">
                <div class="sbheader"><b>Filtered Types</b></div>
        	</div>
        </div>
        <div class="clear"></div>
    </div>
</div>
<div id="filter-selector" class="sbwrap" style="display:none; <? echo $this->arguments['unfixed']==true?'':'position: fixed;'; ?> width:244px;">
        <div class="sbheader"><h1>New Filter</h1></div>
        <div class="sbfilterlist">
        	<div class="mcbutton" id="cancel_filters_btn">Cancel</div>
            <div class="mcbutton" id="activate_filters_btn" data-filtertarget="<?php echo $this->pageconfig['filtertarget']; ?>">Add</div>
            <br />
            <br />
            <div id="filtertype_btn_list">
            	<div id="cosmetic_filters_filtertype_hero_btn" class="filterlistbutton, sblistbutton" >Hero</div>
            	<div id="cosmetic_filters_filtertype_rarity_btn" class="filterlistbutton, sblistbutton" >Rarity</div>
            	<div id="cosmetic_filters_filtertype_type_btn" class="filterlistbutton, sblistbutton" >Type</div>
        	</div>
			<div id="filters_toadd">
            	<div id="hero_filters" style="display: none">
                	<div class="sbheader"><b>Heroes to Filter</b></div>
                </div>
                <div id="rarity_filters" style="display: none">
                	<div class="sbheader"><b>Rarities to Filter</b></div>
                </div>
                <div id="type_filters" style="display: none">
                	<div class="sbheader"><b>Types to Filter</b></div>
                </div>
            </div>
            <div class="clear"></div>
        </div>
</div>
</section>
