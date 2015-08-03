<?php
/**
 * Regsister the settings & settings section
 */
function eventorganiservat_register_settings( $tab_id ) {

	register_setting( 'eventorganiser_bookings', 'eventorganiservat_options'  );
		
	add_action( "load-settings_page_event-settings", 'eventorganiservat_add_settings_fields', 10, 0 );
}
add_action( "eventorganiser_register_tab_bookings", 'eventorganiservat_register_settings', 50 );


function eventorganiservat_add_settings_fields(){
	
	add_settings_field( 
		'eo-vat-percent',  
		__( 'VAT (%)', 'event-organiser-vat' ), 
		'eventorganiser_text_field' , 
		'eventorganiser_bookings', 
		'bookings',
		array(
			'type' => 'text',
			'min' => 0,
			'max' => 100,
			'value' => eventorganiservat_get_vat_percent(),
			'label_for' => 'eo-vat-percent',
			'id' => 'eo-vat-percent',
			'name' => 'eventorganiservat_options[percent]',
		)
	);
	
	add_settings_field(
		'eo-vat-label',
		__( 'VAT label', 'event-organiser-vat' ),
		'eventorganiser_text_field' ,
		'eventorganiser_bookings',
		'bookings',
		array(
			'type' => 'text',
			'value' => eventorganiservat_get_vat_label(),
			'label_for' => 'eo-vat-label',
			'id' => 'eo-vat-label',
			'name' => 'eventorganiservat_options[label]',
		)
	);
}
