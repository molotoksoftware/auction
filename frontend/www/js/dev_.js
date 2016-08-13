$('document').ready(function(){
	$('a.select_town_link').click(function(){
		$('ul.town_list').slideToggle();
	});

	$('ul.town_list').find('li').click(function(){
		$('a.select_town_link').text($(this).text());
		$('ul.town_list').slideToggle();
	});

	$('ul.main_nav li span.str').click(function(){
		$(this).closest('li').find('ul').slideToggle();
		$(this).closest('li').toggleClass('active');
	});

	$('a.select_cat span').click(function(){
		$('.search_cat').slideToggle();
		$(this).closest('a').toggleClass('active');
		jQuery('.scroll-pane').jScrollPane();
	});

	$('.search_cat ul').find('li').click(function(){
		$('a.select_cat span.select_cat_span_left').text($(this).text());
		$('input.hidden_cat').val($(this).text());
		$('.search_cat').slideToggle();
		$('a.select_cat').toggleClass('active');
	});
	$('a.link_select_1 span').click(function(){
		$('div.create_lot_right_cat_list_2').slideToggle();
	});
	$('div.create_lot_right_cat_list_2 ul').find('li').click(function(){
		$('a.link_select_1').html($(this).text()+'<span></span><div class="clear"></div>');
		$('div.create_lot_right_cat_list_2').slideToggle();
		$('a.link_select_1 span').click(function(){
			$('div.create_lot_right_cat_list_2').slideToggle();
		});
	});
	$('a.link_select_2 span').click(function(){
		$('div.create_lot_right_cat_list_1').slideToggle();
	});
	$('div.create_lot_right_cat_list_1 ul').find('li').click(function(){
		$('a.link_select_2').html($(this).text()+'<span></span><div class="clear"></div>');
		$('div.create_lot_right_cat_list_1').slideToggle();
		$('a.link_select_2 span').click(function(){
			$('div.create_lot_right_cat_list_1').slideToggle();
		});
	});
	$('a.link_select_3 span').click(function(){
		$('div.create_lot_right_cat_list_3').slideToggle();
	});
	$('div.create_lot_right_cat_list_3 ul').find('li').click(function(){
		$('a.link_select_3').html($(this).text()+'<span></span><div class="clear"></div>');
		$('div.create_lot_right_cat_list_3').slideToggle();
		$('a.link_select_3 span').click(function(){
			$('div.create_lot_right_cat_list_3').slideToggle();
		});
	});
	$('a.link_select_4 span').click(function(){
		$('div.create_lot_right_cat_list_4').slideToggle();
	});
	$('div.create_lot_right_cat_list_4 ul').find('li').click(function(){
		$('a.link_select_4').html($(this).text()+'<span></span><div class="clear"></div>');
		$('div.create_lot_right_cat_list_4').slideToggle();
		$('a.link_select_4 span').click(function(){
			$('div.create_lot_right_cat_list_4').slideToggle();
		});
	});

});