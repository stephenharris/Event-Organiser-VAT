jQuery(document).ready(function($){

	/**
	 * A fix for the toFixed() rounding errors
	 * @see http://stackoverflow.com/a/10015178/932391
	 */
	var eoVatToFixed = function( number, precision ) {
		var str = Math.abs(number).toString(),
			negative = number < 0,
			lastNumber, mult;

		str        = str.substr(0, str.indexOf('.') + precision + 2);
		lastNumber = str.charAt(str.length - 1);
		str        = str.substr(0, str.length - 1);
	
		if ( lastNumber >= 5 ) {
			mult = Math.pow(10, str.length - str.indexOf('.') - 1);
			str = (+str + 1 / mult).toString();
		}
		return str * (negative ? -1 : 1);
	};
	
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

		$('#eo-booking-vat').text( eoVatToFixed( parseFloat( vat_amount ), 2 ) );

		return cart;
				
	}, 100 );

});
