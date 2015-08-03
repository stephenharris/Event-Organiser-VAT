<?php
/*
 * Plugin Name: Event Organiser VAT
 * Plugin URI:  http://wp-event-organiser.com
 * Description: Adds VAT to Event Organiser Pro
 * Version:     1.0.4
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
define( 'EVENTORGANISERVAT_VERSION', '1.0.4' );
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
	));

}
add_action( 'init', 'eventorganiservat_init' );