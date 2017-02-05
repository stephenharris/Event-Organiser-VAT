<?php

class EO_Booking_Form_Element_Vat_Number_View extends EO_Booking_Form_Element_View{

	function render(){

		wp_add_inline_style(
			'eo_front',
			"input.eo-booking-field-vat-number {background-repeat: no-repeat;background-position: 99% center;padding-right: 3px;}"
		);

		ob_start();
		include( eo_locate_template( 'eo-booking-form-input.php' ) );
		$html = ob_get_contents();
		ob_end_clean();

		$html .= eventorganiser_text_field(array(
			'type'  => 'hidden',
			'value' => $this->get_value( 'hash' ),
			'id'    => 'eo-booking-field-'.$this->element->id.'-2',
			'name'  => $this->element->get_field_name( 'h' ),
			'echo'  => 0,
		));

		return $html;
	}

	function get_value(){
		return $this->element->get_value( 'n' );
	}

	function get_name( $component = false ){
		return $this->element->get_field_name( 'n' );
	}

}
