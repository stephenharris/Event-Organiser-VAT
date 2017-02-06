jQuery(document).ready(function($){

	/**
	 * A fix for the toFixed() rounding errors
	 * @see http://stackoverflow.com/a/10015178/932391
	 */
	var eoVatToFixed = function( number, precision ) {

		// If number is integer, append zeros.
		if ( number % 1 === 0 ) {
			return number.toFixed(precision);
		}

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

	var eoVatRecalculate = function() {
		//Trigger change of input
		if( typeof( eventorganiserpro ) != "undefined" && typeof( eventorganiserpro.eoCart ) != "undefined" ){
			eventorganiserpro.eoCart.calculate();
		}else{
			$('.eo-booking-ticket-qty input').first().trigger('change');
		}
	};

	$( '.eo-booking-field-vat-number' ).on( 'blur', function() {
		var $input = $(this);
		var vat_number = $(this).val();
		var $form = $input.parents('form');
		$form.find('[type=submit]').prop('disabled', true);
		if( vat_number ){
			$input.css('background-image', 'url(' + eo_pro_vat.images_url + 'loading.gif)');
			var inputParent = $(this).parent();
			$.ajax({
				type: "GET",
				data: {
					vat_number: vat_number,
					action: 'eventorganiser-check-vat-number',
				},
				dataType: "JSON",
				url: eo_pro_vat.ajax_url,
				success: function( data ){
					if( data.valid ){
						$input.css('background-image', 'url(' + eo_pro_vat.images_url + 'success.png)');
						eo_pro_vat.vat_number_valid = true;
						$input.parent().find( '#' + $input.attr('id') + '-2' ).val( data.vat_number_hash );
					} else {
						$input.css('background-image', 'url(' + eo_pro_vat.images_url + 'failure.png)');
						eo_pro_vat.vat_number_valid = false;
						$input.parent().find( '#' + $input.attr('id') + '-2' ).val( data.vat_number_hash );
					}
				},
				complete: function( e, status ){
					if( status == 'parsererror' || status == 'error' || status == 'timeout' || status == 'abort' ){
						$input.css('background-image', 'url(' + eo_pro_vat.images_url + 'error.png)');
						eo_pro_vat.vat_number_valid = false;
						$input.parent().find( '#' + $input.attr('id') + '-2' ).val( '' );
					}
					eoVatRecalculate();
					$form.find('[type=submit]').prop('disabled', false);
				}
			});

		} else {
			$(this).css('background-image', 'url(' + eo_pro_vat.images_url + 'failure.png)');
			eo_pro_vat.vat_number_valid = false;
			eoVatRecalculate();
			$form.find('[type=submit]').prop('disabled', false);
		}

	} );

	/**
	 * Listens for a change in the checkout total and
	 * adds the appropriate about of VAT. If VAT amount
	 * is above 0, show VAT row, otherwise hide it.
	 */
	wp.hooks.addFilter( 'eventorganiser.checkout_cart', function( cart ){

		var vat = eo_pro_vat.vat_percent;

		if ( eo_pro_vat.vat_number_valid ) {
			vat = 0;
		}

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
