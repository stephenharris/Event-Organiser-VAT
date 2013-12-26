jQuery(document).ready(function($){
	
	/**
	 * Listens for a change in the checkout total and
	 * adds the appropriate about of VAT. If VAT amount
	 * is above 0, show VAT row, otherwise hide it. 
	 */
	wp.hooks.addFilter( 'eventorganiser.checkout_cart', function( cart ){

		var vat = eo_pro_vat.vat_percent;
		var vat_amount = cart.total*(vat/100);
		
		cart.total = cart.total + vat_amount;

		if( vat_amount === 0 ){
			$('#eo-booking-vat').parents('tr').hide();
		}else{
			$('#eo-booking-vat').parents('tr').show();
		}

		$('#eo-booking-vat').text( parseFloat( vat_amount ).toFixed(2) );

		return cart;
				
	}, 100 );

});