<?php
/**
 * Returns the VAT percentage
 * 
 * @since 1.0.0
 * @ignore
 * @return float VAT percentage
 */
function eventorganiservat_get_vat_percent(){
	
	$options = array_merge( 
			array(
				'percent' => 0 //default 0 - no VAT
			),
			get_option( 'eventorganiservat_options', array() ) 
		);
	
	return floatval( $options['percent'] );
}

/**
 * Returns the VAT label
 *
 * @since 1.0.0
 * @ignore
 * @return float VAT percentage
 */
function eventorganiservat_get_vat_label(){

	$options = array_merge(
			array(
					'label' => __( 'VAT', 'event-organiser-vat' ),
			),
			get_option( 'eventorganiservat_options', array() )
	);

	return $options['label'];
}

/**
 * Add VAT row to ticket picker 
 * 
 * @since 1.0.0
 * @ignore
 * @param unknown_type $event_id
 * @param unknown_type $form
 */
function _eventorganiservat_vat_row( $event_id, $form ){
	
	//Get currency symbol
	$currency = eventorganiser_pro_get_option( 'currency' );
	$symbol = eventorganiser_get_currency_symbol( $currency );
	$placeholder = ( eventorganiser_pro_get_option( 'currency_position' ) == 1 ? '%1$s%2$s' : '%2$s%1$s' );
	
	//'VAT' row
	?>
		<tr class="eo-booking-vat-row" style="display:none;">
			
			<td><strong> 
				<?php printf( 
						'%s (%s%%)',
						esc_html( eventorganiservat_get_vat_label() ), 
						eventorganiservat_get_vat_percent() 
				); ?>
			</strong></td>
			
			<td> <?php printf( $placeholder, $symbol, '<span id="eo-booking-vat"></span>' ); ?></td>
			
			<td></td>
			
		</tr>
	<?php

	//Enqueue scripts
	wp_enqueue_script( 'eo_pro_vat' );
}
add_action( 'eventorganiser_booking_pre_total_row', '_eventorganiservat_vat_row', 500, 2 );


/**
 * Store VAT for the booking in bookg meta
 * 
 * @since 1.0.0
 * @ignore
 * @param int $booking_id
 */
function _eventorganiservat_store_vat( $booking_id  ){
	
	//New booking, so VAT won't be stored yet, so below returns pre-vat total price
	$amount = floatval( eo_get_booking_meta( $booking_id, 'booking_amount', true )  );
	$vat = eventorganiservat_get_vat_percent();
	$vat_amount = number_format( ($vat * $amount )/100, 2 );
	
	update_post_meta( $booking_id, '_eo_booking_vat_percent', $vat );
	update_post_meta( $booking_id, '_eo_booking_vat_amount', $vat_amount  );
}
add_action( 'eventorganiser_new_booking', '_eventorganiservat_store_vat', 5 );


/**
 * Adds VAT to the booking total
 * 
 * @since 1.0.0
 * @ignore
 * @param float $amount The booking total (pre VAT)
 * @param int $booking_id
 * @return float $amount The booking total (gross)
 */
function _eventorganiservat_apply_vat( $amount, $booking_id ){

	$vat_amount = get_post_meta( $booking_id, '_eo_booking_vat_amount', true  );
	
	if( $vat_amount && floatval( $vat_amount ) > 0 ){
		$amount = floatval( $vat_amount ) + $amount;
	}
	
	return $amount;
}
add_filter( 'eventorganiser_get_booking_meta_booking_amount', '_eventorganiservat_apply_vat', 500, 2 );


/**
 * Add a 'VAT' row to bookee / admin emails
 * 
 * @since 1.0.0
 * @ignore
 * @param string $rows HTML of rows to insert just prior to 'total' row
 * @param int $booking_id Booking ID
 * @return string HTML of rows to insert just prior to 'total' row
 */
function _eventorganiservat_append_vat_row_to_email( $rows, $booking_id ){
	
	$vat_amount = get_post_meta( $booking_id, '_eo_booking_vat_amount', true  );
	
	if( $vat_amount && floatval( $vat_amount ) > 0 ){
		
		$rows .= sprintf( 
			'<tr> <td>%s</td> <td>%s</td> <td></td> </tr>',
			eventorganiservat_get_vat_label(),
			eo_format_price( floatval( $vat_amount ) )
		);
	}
	
	return $rows;	
	
}
add_filter( 'eventorganiser_email_ticket_list_pre_total', '_eventorganiservat_append_vat_row_to_email', 500, 2 ); //email to bookee
add_filter( 'eventorganiser_get_booking_table_for_email_pre_total', '_eventorganiservat_append_vat_row_to_email', 500, 2 ); //email to admin


/**
 * Adds support for PayPal checkout. 
 * 
 * Sets the 'tax_cart' attribute of the cart.
 *
 * @since 1.0.0
 * @ignore
 * @param unknown_type $cart
 * @param unknown_type $booking
 * @return mixed
 */
function eventorganiservat_add_vat_paypal_cart( $cart, $booking ){
	
	/*
	 $cart['custom'] = build_query( array(
	 		'booking_id' => $booking['booking_id'],
	 		'event_id' => $booking['event_id'],
	 		'occurrence_id' => $booking['occurrence_id'],
	 		'booking_user' => $booking['booking_user'],
	 		'ticket_quantity' => $ticket_quantity
	 ) );
	*/
	if( $vat_amount = eo_get_booking_meta( $booking['booking_id'], 'vat_amount', true ) ){
		$cart['tax_cart'] = $vat_amount;
	}
	
	return $cart;
}
add_filter( 'eventorganiser_pre_gateway_checkout_paypal', 'eventorganiservat_add_vat_paypal_cart', 10, 2 );
