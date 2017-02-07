jQuery(document).ready(function($){

	/**
	 * A fix for the toFixed() rounding errors
	 */
	var eoVatToFixed = function( number, precision ) {

		precision = ( typeof precision == 'undefined' ? 0 : precision );

		//Lets ensure the part we want to keep is the (rounded) integer
		var roundedInt   = Math.round( number * Math.pow( 10, precision ) );
		var roundedFloat = roundedInt /  Math.pow( 10, precision );

		//Ensure the appropriate number of decimal points are returned
		return roundedFloat.toPrecision( roundedInt.toString().length );
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

		$('#eo-booking-vat').text( eoVatToFixed( parseFloat( vat_amount, 2 ), 2 ) );

		return cart;

	}, 100 );

});
