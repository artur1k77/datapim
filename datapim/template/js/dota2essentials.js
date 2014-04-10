$(document).ready(function () {
	var ajaxProcessing2 = false;
	if($('.sliderwrap').length != 0){
		var menuHideTimer = false;
		var removeActiveClassTimer = false;
		var target_div = null;
		var old_div = null;
		var inMain = null;
		var inSub = null;
		var instantToggle = null;
		
		$('.sliderwrap').bjqs({
			// width and height need to be provided to enforce consistency
			// if responsive is set to true, these values act as maximum dimensions
			width : 730,
			height : 250,
			
			// animation values
			animtype : 'slide', // accepts 'fade' or 'slide'
			animduration : 900, // how fast the animation are
			animspeed : 4000, // the delay between each slide
			automatic : true, // automatic
			
			// control and marker configuration
			showcontrols : true, // show next and prev controls
			centercontrols : true, // center controls verically
			nexttext : 'Next', // Text for 'next' button (can use HTML)
			prevtext : 'Prev', // Text for 'previous' button (can use HTML)
			showmarkers : true, // Show individual slide markers
			centermarkers : true, // Center markers horizontally
			
			// interaction values
			keyboardnav : true, // enable keyboard navigation
			hoverpause : true, // pause the slider on hover
			
			// presentational options
			usecaptions : true, // show captions for images using the image title tag
			randomstart : true, // start slider at random slide
			responsive : true // enable responsive capabilities (beta)
		});
	}
	
	addLivestreamHoverFunction();
	
	$('#usernav .mainusernavitem').mouseenter(function () {
		//alert('erin');
		target_div = $('.usersubnav.' + this.id);
		if(instantToggle) {
			target_div.show();
		} else {
			target_div.slideDown();
		}
		instantToggle = true;
		$(this).addClass('menuactive');
	}).mouseleave(leaving);
	
	$('.usersubnav').mouseenter(function () {
		reset_MenuHideTimer();
		reset_RemoveActiveClassTimer();
		
		instantToggle = true;
		inSub = true;
		target_div = $(this);
	}).mouseleave(leavingSub);
	
	$('#nav a.mainnavitem').mouseenter(function () {
		if(this.id != null && this.id!='') {
			target_div = $('.subnav.' + this.id);
			if(instantToggle) {
				target_div.show();
			} else {
				target_div.slideDown();
			}
			instantToggle = true;
			$('.subnav').not(target_div).hide();
		} else {
			$('.subnav').not(target_div).slideUp();
		}
		
		//instantToggle = true;
		$(this).addClass('menuactive');
		
		var divs2 = $('#nav a.mainnavitem');
		divs2 = divs2.not($(this));
		divs2.removeClass('menuactive');
		
	}).mouseleave(leaving);
	
	$('.subnav').mouseenter(function () {
		reset_MenuHideTimer();
		reset_RemoveActiveClassTimer();
		
		instantToggle = true;
		inSub = true;
		target_div = $(this);
	}).mouseleave(leavingSub);
	
	function leavingSub() {
		target_div = null;
		old_div.removeClass('menuactive');
		inSub = null;
		
		start_MenuHideTimer();
	}
	
	function leaving(queryType) {
		target_div = null;
		old_div = $(this);
		start_RemoveActiveClassTimer();
		start_MenuHideTimer(queryType);
	}
	
	function start_RemoveActiveClassTimer() {
		reset_RemoveActiveClassTimer();
		removeActiveClassTimer = setTimeout(function() {
			if(old_div && !inSub) {
				old_div.removeClass('menuactive');
			}
			removeActiveClassTimer = false;
		}, 50);
	}
	
	function reset_RemoveActiveClassTimer() {
		if(removeActiveClassTimer){
			clearTimeout(removeActiveClassTimer);
			removeActiveClassTimer = false;	
		}
	}
	
	
	function start_MenuHideTimer(queryType) {
		reset_MenuHideTimer();
		menuHideTimer = setTimeout(function() {
			//var divs = $('.subnav')
			var divs = $('.subnav, .usersubnav');
			if(target_div) {
				divs = divs.not(target_div);	
			} else {
				instantToggle = null;	
				inSub = null;
			}
			divs.slideUp();
			menuHideTimer = false;
		}, 150);
	}
	
	function reset_MenuHideTimer() {
		if(menuHideTimer) {
			clearTimeout(menuHideTimer);
			menuHideTimer = false;
		}
	}
	
	
	
	// ajax call test
	$( "#buttondv" ).click(function() {
		$.post( "/ajax/", { ajax: 1, request: "divinecourage", amount: $("#dvgentype").val() }).done(function( data ) {
			$( "#ajaxresponse" ).empty().append( data );
		});
	});
	
	$( "#buttonac" ).click(function() {
		$.post( "/ajax/", { ajax: 1, request: "armorycalculator", steamid: $(this).data("steamid") }).done(function( data ) {
			$( "#ajaxresponse" ).empty().append( data );
		});
	});

	$( ".helpexpander" ).click(function() {
		$( ".helpme" ).toggle( "fast", function() {
		});
	});	
	$( ".buttonitemsets" ).click(function() {
		var itemslist = getDefindexes($(this).data('setname'));
		console.log( itemslist );
		$.post( "/ajax/", { ajax: 1, request: "wishlist", items:itemslist }).done(function( data ) {
			$( "#ajaxresponse" ).empty().append( data );
		});
	});
	
	function getDefindexes(parentdiv){
		var array = [];
		var div = '#'+parentdiv;
		$(div).children(".setimage").each(function() {
			console.log($(this).data['defindex']);
			array.push($(this).data['defindex']);
		});	
		return array;
	}
	
	// livestreams
	function addLivestreamHoverFunction() {
		$('.lsoverviewwrap').on('mouseenter.showLivestreamInfoHover', '.livestreamwrapper', function(e) {
			var target_div = $('.livestreamhover.' + this.id);
			$(target_div).show();
		}).on('mouseleave.showLivestreamInfoHover', '.livestreamwrapper', function(e) {
			var target_div = $('.livestreamhover.' + this.id);
			$(target_div).hide();
		});
	}
	
	// market analyse

		$('.hc_paginate_prev').on('click', function(e) {
			var rarity = $(this).data("rarity");
			
			var info_div = '.am_info_'+rarity;

			var offset = $(info_div).data("offset");
			var offset = offset-10;
			if(offset<0){ var offset=0;}

			var target_div = '.am_result_'+rarity;

			$.post( "/ajax/", { ajax: 1, request: "marketinfo", rarity: rarity, offset: offset, direction: "prev" }).done(function( data ) {
				$(target_div).html( data );
			});
			
			$(info_div).data("offset",offset);
		});
		
		$('.hc_paginate_next').on('click', function(e) {
			var rarity = $(this).data("rarity");
			
			var info_div = '.am_info_'+rarity;

			var offset = $(info_div).data("offset");
			var offset = offset+10;

			var target_div = '.am_result_'+rarity;

			$.post( "/ajax/", { ajax: 1, request: "marketinfo", rarity: rarity, offset: offset, direction: "next" }).done(function( data ) {
				$(target_div).html( data );
			});
			
			if(offset<0){ var offset=0;}
			$(info_div).data("offset",offset);
		});

	
});