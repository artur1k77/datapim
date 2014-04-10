var ajaxProcessing = false;
var tradeSending = false;
$(document).ready(function () {
	checkMessageTradeUpdates();
	var interval = setInterval(checkMessageTradeUpdates(), 60000);
	
	addCosmeticHoverFunction();
	addCosmeticClickFunction();
	
	var allCosmetics = $('.cosmetic');
	var scrollTop = 0;
	
	if(window.location.href.toString().split(window.location.host)[1].split('/')[1]=='trade-overview') {
		fetchTradeOverview();
	}
	
	if($('.cosmetic_container').length != 0) {
		var incId = 0;
		$('.cosmetic_container').each(function(index, element) {
			element.id = element.id+'_'+incId;
			incId++;
			//loadFilteredCosmetics(false, element);
		});
		$('.cosmetic_container.cs_inventory').each(function(index, element) {
			loadFilteredCosmetics(false, element);
		});
		$('.cosmetic_container.cs_overview').each(function(index, element) {
			loadFilteredCosmetics(false, element);
		});
		$('.cosmetic_container.cs_wishlist').each(function(index, element) {
			loadFilteredCosmetics(false, element);
		});
		$('.cosmetic_container.cs_compare').each(function(index, element) {
			loadFilteredCosmetics(false, element);
		});
	}
	if($('.lsoverviewwrap').length != 0) {
		$('.lsoverviewwrap').each(function(index, element) {
			loadFilteredLiveStreams(true, element);
		});
	}
	//loadFilteredCosmetics(true, $('.cosmetic_container.cs_overview'));
	
	$(document).scroll(function(e){
		if(ajaxProcessing || $('.cosmetic_container')==null)
			return false;
			
		var scrollAmount = $(window).scrollTop();
    	var documentHeight = $(document).height();
		var windowHeight = $(window).height();
		var downScroll = scrollAmount>scrollTop;
		
		if(downScroll && (scrollAmount >= documentHeight - windowHeight - 200)) {
			$('.cosmetic_container.cs_inventory').each(function(index, element) {
				if($(element).data('mode')!='snapshot') {
					loadFilteredCosmetics(false, element);
				}
			});
			$('.cosmetic_container.cs_overview').each(function(index, element) {
				if($(element).data('mode')!='snapshot') {
					loadFilteredCosmetics(false, element);
				}
			});
			$('.cosmetic_container.cs_wishlist').each(function(index, element) {
				if($(element).data('mode')!='snapshot') {
					loadFilteredCosmetics(false, element);
				}
			});
			$('.lsoverviewwrap').each(function(index, element) {
				loadFilteredLiveStreams(false, element);
			});
		}
		scrollTop=scrollAmount;
	});
	
	//***LIVESTREAM PART***//
	$('#livestreamSearch').on('keyup.searchLivestreams', function(e) {
		var searchText = $('#livestreamSearch').val();
		loadFilteredLiveStreams(true, $('.lsoverviewwrap')[0]);
	});
	//***END LIVESTREAM PART***//
	
	$('#cosmetic_filters_filtertype_hero_btn').click(function(e) {
        $.post( "/ajax/", { ajax: 1, request: "filters", reqKind: 'getHeroFilters' }).done(function( data ) {
			$( '#responseoverlay' ).html( data );
			$('#responseoverlay').show();
			$('.hero').hover(function(e) {
				var target_div = $('.hero_details.' + this.id);
				$(target_div).show();
			}, function(e) {
				var target_div = $('.hero_details.' + this.id);
				$(target_div).hide();
			}).mousedown(function(e) {
				if($('#filters_toadd #hero_filters').children('#'+this.id).length == 0 && $('#active_filters #hero_filters').children('#'+this.id).length == 0) {
					var clone = $(this).clone(false, false);
					clone.mousedown(function(e){
						if(e.which == 1) {
							clone.remove();	
							if($('#filters_toadd #hero_filters').children().length == 1) {
								$('#filters_toadd #hero_filters').hide();
							}
						}
					});
					clone.children('.hero_details').remove();
					clone.appendTo('#filters_toadd #hero_filters');
				}
				if($('#filters_toadd #hero_filters').children().length > 1) {
					$('#filters_toadd #hero_filters').show();
				}
			});
		});
    });
	
	$('#cosmetic_filters_filtertype_rarity_btn').click(function(e) {
        $.post( "/ajax/", { ajax: 1, request: "filters", reqKind: 'getRarityFilters' }).done(function( data ) {
			$( '#responseoverlay' ).html( data );
			$('#responseoverlay').show();
			$('.rarity').mousedown(function(e) {
				if($('#filters_toadd #rarity_filters').children('#'+this.id).length == 0 && $('#active_filters #rarity_filters').children('#'+this.id).length == 0) {
					var clone = $(this).clone(false, false);
					clone.mousedown(function(e){
						if(e.which == 1) {
							clone.remove();	
							if($('#filters_toadd #rarity_filters').children().length == 1) {
								$('#filters_toadd #rarity_filters').hide();
							}
						}
					});
					clone.appendTo('#filters_toadd #rarity_filters');
				}
				if($('#filters_toadd #rarity_filters').children().length > 1) {
					$('#filters_toadd #rarity_filters').show();
				}
			});
		});
    });
	
	$('#add_filters_btn').click(function(e) {
		var filtersToAdd = $('#filters_toadd').children();
		filtersToAdd.each(function(index, element) {
			var clones = $('#active_filters '+'#'+element.id).children().not('.sbheader').clone(false, false);
			clones.each(function(index, element) {
                $(element).click(function(e){
					if($(this).parent().children().length <= 2) {
						$(this).parent().hide();	
					}
					$(this).remove();
				});
            });
			clones.appendTo(element);
			if($(element).children().length > 1) {
				$(element).show();	
			} else {
				$(element).hide();
			}
		});
		$('#active_filters').fadeOut();
		$('#filter-selector').fadeIn();
		$('#responseoverlay').fadeIn();
	});
	
	$('#activate_filters_btn').click(function(e) {
		var activeFilters = $('#active_filters').children();
		activeFilters.each(function(index, element) {
			$('#active_filters '+'#'+element.id).children().not('.sbheader').remove();
            var clones = $('#filters_toadd '+'#'+element.id).children().not('.sbheader').clone(false, false);
			clones.each(function(index, element) {
                $(element).click(function(e){
					if($(this).parent().children().length <= 2) {
						$(this).parent().hide();	
					}
					$(this).remove();
					loadFilteredCosmetics(true, $(''+$('#activate_filters_btn').data('filtertarget')));
				});
            });
			clones.appendTo(element);
			$('#filters_toadd '+'#'+element.id).children().not('.sbheader').remove();
			if($(element).children().length > 1) {
				$(element).show();	
			} else {
				$(element).hide();
			}
        });
		
		var target = $(''+$(this).data('filtertarget'));
		
		loadFilteredCosmetics(true, target);
		
		$('#responseoverlay').fadeOut();
		$('#filter-selector').fadeOut();
		$('#active_filters').fadeIn();
	});
	
	$('#cancel_filters_btn').click(function(e) {
		var filtersToAdd = $('#filters_toadd').children();
		filtersToAdd.each(function(index, element) {
			$('#filters_toadd '+'#'+element.id).children().not('.sbheader').remove();
			
		});
		$('#responseoverlay').fadeOut();
		$('#filter-selector').fadeOut();
		$('#active_filters').fadeIn();
	});
	
	
	$('#refInv_btn').click(function(e) {
		if(ajaxProcessing)
			return false;
			
		ajaxProcessing = true;
		$( '.cosmetic_container.cs_inventory' ).html( 'Refreshing...' );
        $.post( "/ajax/", { ajax: 1, request: "trade", reqKind: 'refreshInventory' }).done(function( data ) {
			ajaxProcessing = false;
			$( '.cosmetic_container.cs_inventory' ).html( data );
		});
    });
	
	$('#save_btn').click(function(e) {
		$( '#response_container' ).html( 'Saving...' );
        $.post( "/ajax/", { ajax: 1, request: "trade", reqKind: 'saveChanges' }).done(function( data ) {
			$( '#response_container' ).html( data );
		});
    });
	
	$('.cosmetic_search_txt').on('keyup.searchCosmetics', function(e) {
		var searchText = $(this).val();
		var code = e.which;
		if(code==13){
			e.preventDefault();
		} else if(searchText.length > 1 || searchText.length==0){
			loadFilteredCosmetics(true, $(''+$('#activate_filters_btn').data('filtertarget')));
		}
    });
	
	$('#message_wrap .message_footer #confirm_btn').click(function(e) {
		$('#message_wrap').hide();
	});
});

function addCosmeticHoverFunction() {
	$('.cosmetic_container').on('mouseenter.showCosmeticInfoHover', '.cosmetic', function(e) {
        var target_div = $(this).children('.cosmetic_details');
		$(target_div).show();
    }).on('mouseleave.showCosmeticInfoHover', '.cosmetic', function(e) {
		var target_div = $('.cosmetic_details.' + this.id);
		$(target_div).hide();
	});
}

function addCosmeticClickFunction() {
	$('#offer_trade, #save_trade').on('mousedown.sentTrade', function(e) {
		if(tradeSending)
			return false;
		
		var my_items = new Array();
		var my_rarities = new Array();
		var my_message = $('#message').val();
		var his_items = new Array();
		var his_rarities = new Array();
		var his_steamId = $('#to_his_items').data('steamid');
		
		$('#to_my_items').children('.cosmetic, .rarity').each(function(index, element) {
			var obj = {};
			obj.onr=index;
			obj.id=element.id;
			if($(element).hasClass('cosmetic')){
				my_items.push(obj);
			}
			if($(element).hasClass('rarity')){
				my_rarities.push(obj);
			}
		});
		$('#to_his_items').children('.cosmetic, .rarity').each(function(index, element) {
			var obj = {};
			obj.onr=index;
			obj.id=element.id;
			if($(element).hasClass('cosmetic')){
				his_items.push(obj);
			}
			if($(element).hasClass('rarity')){
				his_rarities.push(obj);
			}
		});
		
		console.log('my items: '+JSON.stringify(my_items));
		console.log('my rarities: '+JSON.stringify(my_rarities));
		console.log('my message: '+my_message);
		console.log('his items: '+JSON.stringify(his_items));
		console.log('his rarities: '+JSON.stringify(his_rarities));
		
		tradeSending=true;
		if(this.id=='save_trade') {
			var trade_id = $(this).data('tradeid');
			console.log('trade_id: '+trade_id);
			$.post( "/ajax/", { ajax: 1, request: "trade", reqKind: 'saveTrade', message: my_message, my_items: JSON.stringify(my_items), my_rarities: JSON.stringify(my_rarities), his_items: JSON.stringify(his_items), his_rarities: JSON.stringify(his_rarities), trade_id: trade_id }).done(function( data ) {
				tradeSending=false;	
				if(data==true) {
					$('#message_wrap').show();
					$('#message_wrap #message_contents').html('Save SUCCEEDED!!!');
				} else {
					$('#message_wrap').show();
					$('#message_wrap #message_contents').html('Oops something went wrong saving: '+data);
					return false;
				}
			});
		} else if(this.id=='offer_trade') {
			console.log('his steamid: '+his_steamId);
			$.post( "/ajax/", { ajax: 1, request: "trade", reqKind: 'addTrade', message: my_message, my_items: JSON.stringify(my_items), my_rarities: JSON.stringify(my_rarities), his_items: JSON.stringify(his_items), his_rarities: JSON.stringify(his_rarities), his_steamid: his_steamId }).done(function( data ) {
				tradeSending=false;	
				if(data==true) {
					$('#message_wrap').show();
					$('#message_wrap #message_contents').html('Offer SUCCEEDED!!!');
				} else {
					$('#message_wrap').show();
					$('#message_wrap #message_contents').html('Oops something went wrong offering: '+data);
					return false;
				}
			});
		} else {
			$('#message_wrap').show();
			$('#message_wrap #message_contents').html('Oh my god you killed Kenny<br>You Bastard!');
			return false;
		}
	});
	
	$('#to_my_items, #to_his_items').on('click', '.cosmetic, .rarity', function(e){
		$(this).remove();
	});
	
	$('#to_toggle_message').on('mousedown', function(e){
		$('#to_message').toggle();
	});
	
	$.post( "/ajax/", { ajax: 1, request: "filters", reqKind: 'getRarityFilters', mode: 'mat' }).done(function( data ) {
		$('.to_extra_options_list').each(function(index, element) {
            $(element).html(data);
			$('.to_extra_options_list').children('.rarity').each(function(index, element) {
                $(element).addClass('mat');
            });
        });
	});
	
	$('.to_extra_options_list').on('mousedown', '.rarity' , function(e) {
		var element = this;
		var clone = $(element).clone(false, false);
		var origin = $(element).parent().data('target');
		clone.addClass('large');
		if(origin=='current_user') {
			if($('#to_my_items').children().length<15)
				$('#to_my_items').append(clone);	
		} else {
			if($('#to_his_items').children().length<15)
				$('#to_his_items').append(clone);
		}
	});
	
	$('.to_inventory_toggle').on('mousedown.toggleInventory', '.to_toggle', function(e){
		console.log('toggled: '+this.id);
		$('.cosmetic_container.cs_inventory').data('target', this.id);
		if(this.id=='current_user') {
			$('.cosmetic_container.cs_inventory').parent().children('.mcheader').children('h1').text('My Inventory');
		} else {
			$('.cosmetic_container.cs_inventory').parent().children('.mcheader').children('h1').text('His Inventory');
		}
		loadFilteredCosmetics(true, $('.cosmetic_container.cs_inventory'));
	});
	
	$('.cosmetic_container.cs_inventory[data-mode=makeatrade]').on('mousedown.addtotrade', '.cosmetic', function(e) {
		var element = this;
		var defindex = this.id;
		var origin = $(element).parent().data('target');
		var clone = $(element).clone(false, false);
		clone.children('.cosmetic_details').hide();
		clone.children('.info').children('.markers').hide();
		clone.addClass('tiny');
		if(origin=='current_user') {
			if($('#to_my_items').children().length<15)
				$('#to_my_items').append(clone);	
		} else {
			if($('#to_his_items').children().length<15)
				$('#to_his_items').append(clone);
		}
	});
	
	$('.cosmetic_container.cs_inventory[data-target=current_user][data-mode!=makeatrade]').on('mousedown.tradeQuantityChange', '.cosmetic',function(e) {
		var element = this;
		var defindex = this.id;
		
		switch (e.which) {
			case 1:
				//Left
				console.log(this.id);
				$.post( "/ajax/", { ajax: 1, request: "trade", defIndex: this.id, reqKind: 'toggleTrading'}).done(function( data ) {
					if(data==1){
						$(element).children('.info').children('.markers').children('.tradeQuantity_marker').show();
					} else {
						$(element).children('.info').children('.markers').children('.tradeQuantity_marker').hide();
					}
				});
				break;
			case 2:
				//Middle
				break;
			case 3:
				//Right
				$.post( "/ajax/", { ajax: 1, request: "trade", defIndex: this.id, reqKind: 'toggleTrading'}).done(function( data ) {
					if(data==1){
						$(element).children('.info').children('.markers').children('.tradeQuantity_marker').show();
					} else {
						$(element).children('.info').children('.markers').children('.tradeQuantity_marker').hide();
					}
				});
				break;
			default:
				break;
		}
	});
	$('.cosmetic_container.cs_overview, .cosmetic_container.cs_inventory[data-target!=current_user][data-mode!=makeatrade], .cosmetic_container.cs_wishlist[data-target!=current_user]').on('mousedown.addToWishList', '.cosmetic',function(e) {
		if(ajaxProcessing)
			return false;
			
		var children = $('.cosmetic_container.cs_wishlist[data-target=current_user]').children();
		for(var i=0; i<children.length; i++) {
			if(children[i].id == this.id) {
				$('#message_wrap').show();
				$('#message_wrap #message_contents').html('This cosmetic is already part of your wishlist.');
				return false;
			}
		}
		
		ajaxProcessing = true;
		var target_div = $(this);
		var target_div_id = this.id;
		var clone = $(this).clone(false, false);
        $.post( "/ajax/", { ajax: 1, request: "wishlist", reqKind: 'addToWishlist', defIndex: this.id }).done(function( data ) {
			ajaxProcessing = false;
			console.log(data);
			var result = jQuery.parseJSON(data);
			if(result != null){
				if(result.status==true) {
					$(clone).prependTo($('.cosmetic_container.cs_wishlist[data-target=current_user]'));
					$(clone).children('.cosmetic_details').hide();
					$(clone).addClass('tiny');
					$(clone).children('.info').children('.markers').show();
					$('.cosmetic_container[data-target=current_user]').each(function(index, element) {
						$(element).children('#'+target_div_id).children('.info').children('.markers').children('.wishlist_marker').show();
                    });
					$(target_div).children('.info').children('.markers').children('.wishlist_marker').show();
					//$(target_div).html('asodjfhasidgjgab');
					if($('.cosmetic_container.cs_wishlist[data-target=current_user]').children().length>12){
						$('.cosmetic_container.cs_wishlist[data-target=current_user]').children().last().remove();
					}
				} else {
					$('#message_wrap').show();
					$('#message_wrap #message_contents').html(result.status);
				}
			}
		});
	});
	
	$('.cosmetic_container.cs_wishlist[data-target=current_user]').on('mousedown.removeFromWishList', '.cosmetic',function(e) {
		if(ajaxProcessing)
			return false;
		
		ajaxProcessing = true;
		var eleToRemove = this;
        $.post( "/ajax/", { ajax: 1, request: "wishlist", reqKind: 'removeFromWishlist', defIndex: this.id }).done(function( data ) {
			ajaxProcessing = false;
			console.log(data);
			var result = jQuery.parseJSON(data);
			if(result != null){
				if(result.status==true) {
					loadFilteredCosmetics(true, $('.cosmetic_container.cs_wishlist[data-target=current_user]'));
					$('.cosmetic_container').children('#'+eleToRemove.id).children('.info').children('.markers').children('.wishlist_marker').hide();
				}
			}
		});
	});
	
	$('.cosmetic_container').on('mousedown', '.rarity', function(e) {
		if($('#filters_toadd #rarity_filters').children('#'+this.id).length == 0 && $('#active_filters #rarity_filters').children('#'+this.id).length == 0) {
			var clone = $(this).clone(false, false);
			clone.mousedown(function(e){
				if($(this).parent().children().length <= 2) {
					$(this).parent().hide();	
				}
				$(this).remove();
				loadFilteredCosmetics(true, $(''+$('#activate_filters_btn').data('filtertarget')));
			});
			clone.appendTo('#active_filters #rarity_filters');
			loadFilteredCosmetics(true, $(''+$('#activate_filters_btn').data('filtertarget')));
		}
		if($('#active_filters #rarity_filters').children().length > 1) {
			$('#active_filters #rarity_filters').show();
		}
	});
}

var loadFilteredCosmeticsAjaxQueue = {};

function loadPreviousFilteredCosmetics(target) {
	var loc = $(target).data('queryloc');
	var pagesize = $(target).data('pagesize');
	
	var newLoc = loc-(2*pagesize);
	if(newLoc<0) {
		newLoc=0;	
	}
	$(target).data('queryloc', newLoc);
	//console.log(newLoc);
	loadFilteredCosmetics(false, $(target));
}

//***LIVESTREAM PART***//
function loadFilteredLiveStreams(changed, target) {
	if(!$(target) || $(target).length==0 || ajaxProcessing)
		return false;
	
	if(changed) {
		$(target).data('queryloc', 0);
		$(target).data('pagesize', 50);
		$(target).html('');
	}
	
	var queryLoc = $(target).data('queryloc');
	var pageSize = $(target).data('pagesize');
		
	ajaxProcessing = true;
	$.post( "/ajax/", { ajax: 1, request: "livestreams", filter: $('#livestreamSearch').val(), queryLoc: queryLoc, pageSize: pageSize }).done(function( data ) {
		ajaxProcessing = false;
		//$( '.lsoverviewwrap' ).html( data );
		var ajaxReturn = jQuery.parseJSON(data);
		if(ajaxReturn != null){
			if(ajaxReturn.messages.length>0) {
				$(target).html(ajaxReturn.messages);
			} else {
				$(target).data('queryloc', ajaxReturn.queryLoc);
				$(target).append(ajaxReturn.htmlBody);
			}
		}
		ajaxProcessing = false;
	});
}
//***END LIVESTREAM PART***//

function loadFilteredCosmetics(changed, target) {
	if(!$(target) || $(target).length==0)
		return false;
	
	var filters = {};
	
	if($($('#activate_filters_btn').data('filtertarget')).not($(target)).length==0){
		var activeFilters = $('#active_filters').children();
		activeFilters.each(function(index, element) {
			filters[element.id] = new Array();
			$(element).children().not('.sbheader').each(function(index, filter) {
				filters[element.id].push(filter.id);
			});
		});
		var searchText = $('.cosmetic_search_txt').val();
		if(searchText!=null){
			if(searchText.length > 1 || searchText.length==0) {
				filters['searchText'] = searchText;
			}
		}
	}
	console.log(JSON.stringify(filters));
	
	if(changed) {
		$(target).data('queryloc', 0);
		//$(target).data('pagesize', 75);
		$(target).html('');
	}
	
	var queryLoc = $(target).data('queryloc');
	var pageSize = $(target).data('pagesize');
	
	ajaxProcessing = true;
	//console.log(loadFilteredCosmeticsAjaxQueue);
	if(loadFilteredCosmeticsAjaxQueue[target.id] && loadFilteredCosmeticsAjaxQueue[target.id].readystate != 4  && loadFilteredCosmeticsAjaxQueue[target.id].readyState != 0){
    	loadFilteredCosmeticsAjaxQueue[target.id].abort();
    }
	
	if($(target).data('containertarget')=='compare') {
		loadFilteredCosmeticsAjaxQueue[target.id] = $.post( "/ajax/", { ajax: 1, request: "trade", reqKind: 'compareWishlistInventory', queryLoc: queryLoc, pageSize: pageSize, iTarget: $(target).data('itarget'), wTarget: $(target).data('wtarget')}).done(function( data ) {
			//console.log(data);
			var ajaxReturn = jQuery.parseJSON(data);
			if(ajaxReturn != null){
				console.log(ajaxReturn);
				if(ajaxReturn.messages.length>0) {
					$(target).html(ajaxReturn.messages);
				} else {
					$(target).data('queryloc', ajaxReturn.queryLoc);
					$(target).html(ajaxReturn.htmlBody);
				}
			}
			ajaxProcessing = false;
		});		
	} else {
		loadFilteredCosmeticsAjaxQueue[target.id] = $.post( "/ajax/", { ajax: 1, request: "filters", reqKind: 'getFilteredCosmetics', filterTarget: $(target).data('containertarget'), filterData: JSON.stringify(filters), queryLoc: queryLoc, pageSize: pageSize, targetUser: $(target).data('target')}).done(function( data ) {
			//console.log(data);
			var ajaxReturn = jQuery.parseJSON(data);
			if(ajaxReturn != null){
				console.log(ajaxReturn);
				if(ajaxReturn.messages.length>0) {
					$(target).html(ajaxReturn.messages);
				} else {
					$(target).data('queryloc', ajaxReturn.queryLoc);
					$(target).append(ajaxReturn.htmlBody);
				}
			}
			ajaxProcessing = false;
		});
	}	
}

function checkMessageTradeUpdates() {
	$.post( "/ajax/", { ajax: 1, request: "msgcentral", reqKind: 'checkUpdates' }).done(function( data ) {
		var result = jQuery.parseJSON(data);
		if(result != null){
			var messages = result.newMessages;
			var trades = result.newTrades;
			if(messages > 0) {
				$('.commnotify_message').html(messages);
				$('.commnotify_message').show();
			} else {
				$('.commnotify_message').hide();
			}
			if(trades > 0) {
				$('.commnotify_trade').html(trades);
				$('.commnotify_trade').show();
			} else {
				$('.commnotify_trade').hide();
			}
		}
	});
}

function fetchTradeOverview() {
	var start = $('.tradeoverview').data('start');
	var view = $('.tradeoverview').data('view');
	$.post( "/ajax/", { ajax: 1, request: "tradeoverview", start: start, view: view }).done(function( data ) {
		$('.tradeoverview').html(data);
	});	
}