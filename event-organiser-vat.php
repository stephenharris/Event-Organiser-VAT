<?php
/*
 * Plugin Name: Event Organiser VAT
 * Plugin URI:  http://wp-event-organiser.com
 * Description: Adds VAT to Event Organiser Pro
 * Version:     {{version}}
 * Author:      Stephen Harris
 * Author URI:  http://wp-event-organiser.com
 * License:     GPLv2+
 * Text Domain: eventorganiservat
 * Domain Path: /languages
 */

/**
 * Copyright (c) 2013 Stephen Harris (email : contact@stephenharris.info)
 *
 * This program is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License, version 2 or, at
 * your discretion, any later version, as published by the Free
 * Software Foundation.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
 */

// Useful global constants
define( 'EVENTORGANISERVAT_VERSION', '{{version}}' );
define( 'EVENTORGANISERVAT_URL', plugin_dir_url( __FILE__ ) );
define( 'EVENTORGANISERVAT_DIR', plugin_dir_path( __FILE__ ) );

require_once( EVENTORGANISERVAT_DIR . 'includes/vat.php' );
require_once( EVENTORGANISERVAT_DIR . 'admin/settings.php' );

/**
 * Default initialization for the plugin:
 * - Registers the default textdomain.
 */
function eventorganiservat_init() {

	load_plugin_textdomain( 'event-organiser-vat', false, dirname( plugin_basename( __FILE__ ) ) . '/languages/' );

	$version = defined( 'EVENTORGANISERVAT_VERSION' ) ? EVENTORGANISERVAT_VERSION : false;
	$ext = (defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG) ? '' : '.min';

	//Register scripts
	wp_register_script( 'eo_pro_vat', EVENTORGANISERVAT_URL . "assets/js/vat{$ext}.js", array( 'eo-wp-js-hooks' ), $version );

	wp_localize_script( 'eo_pro_vat', 'eo_pro_vat', array(
		'vat_percent' => eventorganiservat_get_vat_percent(),
		'vat_number_valid' => false,
		'images_url' => EVENTORGANISERVAT_URL . 'assets/images/',
		'ajax_url' => admin_url( 'admin-ajax.php' ),
	));

}
add_action( 'init', 'eventorganiservat_init' );

spl_autoload_register(function ($class_name) {
	$map = array(
		'EO_Booking_Form_Element_Vat_Number_View' => EVENTORGANISERVAT_DIR . 'includes/class-eo-booking-form-element-vat-number-view.php',
		'EO_Booking_Form_Element_Vat_Number' => EVENTORGANISERVAT_DIR . 'includes/class-eo-booking-form-element-vat-number.php',
		'EO_Vat_Validation' => EVENTORGANISERVAT_DIR . 'includes/lib/class-eo-vat-validation.php',
	);

	if ( isset( $map[$class_name] ) ) {
		include $map[$class_name];
	}
});

add_filter( 'eventorganiser_booking_form_element_types', function($elements){
	$elements['advanced']['vat-number'] = 'EO_Booking_Form_Element_Vat_Number';
	return $elements;
} );

add_action( 'admin_footer-settings_page_event-settings',function(){
	?>
	<script>
		eo.bfc.Model.EOFormElementVatNumber = eo.bfc.Model.EOFormElementInput.extend({
			defaults:{
				label: eo.gettext("VAT Number"),
				name: eo.gettext("VAT Number"),
				placeholder: '',
				description: "",
				required: false,
				field_type: 'text',
				parent: 0,
			},
		});
	</script>
	<?php
}, 20 );


add_action( 'wp_ajax_eventorganiser-check-vat-number', 'eventorganiser_vat_verify_vat_number' );
add_action( 'wp_ajax_nopriv_eventorganiser-check-vat-number', 'eventorganiser_vat_verify_vat_number' );
function eventorganiser_vat_verify_vat_number() {

	$vat_validation = new EO_Vat_Validation();
	$valid = $vat_validation->check( $_GET['vat_number'] );

	wp_send_json(array(
		'vat_number' => $vat_number,
		'vat_number_hash' => $valid ? wp_hash( $vat_number ) : false,
		'valid' => $valid
	));
}
