<?php
/*
Plugin Name: Advanced Custom Fields: Color Palette
Plugin URI: https://github.com/7studio/acf-color-palette
Description: Add a new ACF field type: "Color Palette" which allows you to use the color picker with a defined color palette only.
Version: 1.0.0
Author: Xavier Zalawa
Author URI: http://www.7studio.fr
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html
Text Domain: swp-acf-cp
Domain Path: /lang
*/



// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die( 'Cheatin&#8217; uh?' );
}



define( 'SWP_ACF_COLOR_PALETTE_VERSION', '1.0.0' );
define( 'SWP_ACF_COLOR_PALETTE_FILE', __FILE__ );
define( 'SWP_ACF_COLOR_PALETTE_URL', plugin_dir_url( SWP_ACF_COLOR_PALETTE_FILE ) );
define( 'SWP_ACF_COLOR_PALETTE_DIR', plugin_dir_path( SWP_ACF_COLOR_PALETTE_FILE ) );



if ( ! class_exists( 'swp_acf_plugin_color_palette' ) ) {
	class swp_acf_plugin_color_palette {
		// vars
		var $settings;

		/**
		 * __construct
		 *
		 * This function will setup the class functionality
		 *
		 * @type   function
		 * @date   17/02/2016
		 * @since  1.0.0
		 *
		 * @param  n/a
		 * @return n/a
		 */
		function __construct() {
			$this->settings = array(
				'version'	=> SWP_ACF_COLOR_PALETTE_VERSION,
				'url'		=> SWP_ACF_COLOR_PALETTE_URL,
				'path'		=> SWP_ACF_COLOR_PALETTE_DIR
			);

			// set text domain
            add_action( 'plugins_loaded', array( $this, 'load_plugin_textdomain' ) );

			// include field
			add_action( 'acf/include_field_types', array( $this, 'include_field_types' ) ); // v5
			add_action( 'acf/register_fields', array( $this, 'include_field_types' ) ); // v4
		}

        /**
         * load_plugin_textdomain
         *
         * This function will load the textdomain file
         *
         * @type   function
         * @date   20/03/2018
         * @since  1.0.0
         *
         * @param  n/a
         * @return n/a
         */
        function load_plugin_textdomain() {
            load_plugin_textdomain( 'swp-acf-cp', false, plugin_basename( SWP_ACF_COLOR_PALETTE_DIR ) . '/lang' );
        }

		/**
		 * include_field_types
		 *
		 * This function will include the field type class
		 *
		 * @type   function
		 * @date   17/02/2016
		 * @since  1.0.0
		 *
		 * @param  $version (int) major ACF version. Defaults to false
		 * @return n/a
		 */
		function include_field_types( $version = false ) {
			// support empty $version
			if ( ! $version ) {
				$version = 4;
			}

			include_once( "fields/class-swp-acf-color-palette-v{$version}.php" );
		}
	}

	new swp_acf_plugin_color_palette();
}
