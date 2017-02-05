<?php

class EO_Booking_Form_Element_Vat_Number extends EO_Booking_Form_Element_Input{

	var $type = 'vat-number';

	var $default_view = 'EO_Booking_Form_Element_Vat_Number_View';

	static function get_type_name(){
		return __( 'VAT number', 'eventorganiservat' );
	}

	function get_defaults(){
		return array(
			'label' => __( 'VAT number', 'eventorganiservat' ),
		);
	}

	function is_required(){
		return false;
	}

	function is_valid( $input ){
		return true;
	}

	/**
	 * The actual value of this field is an array (VAT number and hash)
	 * but when we use EO_Booking_Form_Element_Vat_Number::get_value()
	 * with no argument we are probably just after the VAT number. The hash
	 * is only validating.
	 */
	function get_value( $component = false ){
		$component = ( false === $component ? 'n' : $component );
		return parent::get_value( $component );
	}

	function save( $booking_id ){
		if( $this->get_value() ){
			$vat_number =  $this->get_value('n');
			$vat_number_hash = $this->get_value('h');

			$valid = ( $vat_number_hash === wp_hash( $vat_number ) );

			// If it's not valid, check again...
			if ( ! $valid ) {
				$vat_validation = new EO_Vat_Validation();
				$valid = $vat_validation->check( $vat_number );
			}

			update_post_meta( $booking_id, '_eo_booking_vat_number', $vat_number );
			update_post_meta( $booking_id, '_eo_booking_vat_number_valid', $valid ? 1 : 0 );
		}
		parent::save( $booking_id );
	}

}
