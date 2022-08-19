/*!
 * @author renan henrique - Data Control Informatica
 */
$(function(){
	$('.abas-seletivas-index a').click(function(e){
		var aThis = $(this).attr('href');
		$('.cx-img').stop().css('top','550px').animate({'top':'0'}, 700);
		$('.cx-dexcricao-texto').stop().fadeOut(0).removeClass('tag-block').addClass('tag-hidden').fadeIn(800);
		$( aThis ).addClass('tag-block').removeClass('tag-hidden');
		$('.abas-seletivas span a').removeClass('abas-seletivas-ativa');
		$(this).addClass('abas-seletivas-ativa');
		e.preventDefault();
	});	

	
	var owlBanner = $(".banner-index");
	owlBanner.owlCarousel({
		autoPlay			: 7000,
		items 				: 1, 			// 7 items above 1000px browser width
		itemsDesktop 		: [1090, 1], 	// 5 items between 1000px and 901px
		itemsDesktopSmall 	: [880, 1], 	// 3 items betweem 880px and 601px
		itemsTablet			: [400, 1], 	// 2 items between 600 and 0;
		itemsMobile 		: false, 		// itemsMobile disabled - inherit from itemsTablet option
		navigation 			: false,
		pagination			: true,
		lazyLoad			: true
	});
	
	var owlMarcas = $("#banner-marcas-produtos");
	owlMarcas.owlCarousel({
		autoPlay			: 3000,
		items 				: 7, 				// 7 items above 1000px browser width
		itemsDesktop 		: [1030, 7], 		// 5 items between 1000px and 901px
		itemsDesktopSmall 	: [880, 3], 		// 3 items betweem 880px and 601px
		itemsTablet			: [400, 2], 		// 2 items between 600 and 0;
		itemsMobile 		: false 			// itemsMobile disabled - inherit from itemsTablet option
	});
})