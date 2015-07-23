(function($){

	$('.wsuwp-person-additional > dl > dd').hide();

	$('.wsuwp-person-additional > dl > dt').click( function() {
		$(this).next('dd').toggle().parents('dl').toggleClass('disclosed');
	})

}(jQuery));