jQuery(document).ready(function()
{
	if(jQuery('.wpis-slider-for').length > 0)
	{
		jQuery('.wpis-slider-for').slick({
			fade: true,
			arrows: false,
			slidesToShow: 1,
			slidesToScroll: 1,
			asNavFor: '.wpis-slider-nav'
		});
		
		jQuery('.wpis-slider-nav').slick({
			dots: false,
			arrows: false,
			centerMode: false,
			focusOnSelect: true,
			slidesToShow: 3,
			slidesToScroll: 1,
			asNavFor: '.wpis-slider-for'
		});
	}
});