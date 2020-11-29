<?php
// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die( 'Something went wrong.' );
}



if ( ! class_exists( 'swp_acf_field_color_palette' ) )  {
	class swp_acf_field_color_palette extends acf_field {
		/**
		 * __construct
		 *
		 * This function will setup the field type data
		 *
		 * @type	function
		 * @date	5/03/2014
		 * @since	5.0.0
		 *
		 * @param	n/a
		 * @return	n/a
		 */
		function __construct( $settings ) {
			// vars
			$this->name	 = 'color_palette';
			$this->label	= __( 'Color Palette', 'swp-acf-cp' );
			$this->category = 'jquery';
			$this->defaults = array(
				'readonly'      => 'readonly',
				'choices'		=> array(),
				'default_value'	=> '',
				'return_format'	=> 'value'
			);
			$this->settings = $settings;

			// do not delete!
	    	parent::__construct();
		}

		/**
		 * render_field_settings()
		 *
		 * Create extra settings for your field. These are visible when editing a field
		 *
		 * @type   action
		 * @since  3.6
		 * @date   23/01/13
		 *
		 * @param  $field (array) the $field being edited
		 * @return n/a
		 */
		function render_field_settings( $field ) {
			// encode choices (convert from array)
			$field['choices'] = acf_encode_choices( $field['choices'] );

			// choices
			acf_render_field_setting( $field, array(
				'label'			=> __( 'Choices', 'swp-acf-cp' ),
				'instructions'	=> __( 'Enter each choice on a new line.', 'swp-acf-cp' ) . '<br><br>' . __( 'For more control, you may specify both a value and label like this:', 'swp-acf-cp' ). '<br><br>' . __( '#ff0000 : Red', 'swp-acf-cp' ),
				'name'			=> 'choices',
				'type'			=> 'textarea',
			) );

			acf_render_field_setting( $field, array(
				'label'			=> __( 'Default Value', 'swp-acf-cp' ),
				'instructions'	=> '',
				'type'			=> 'text',
				'name'			=> 'default_value',
				'placeholder'	=> '#FFFFFF'
			) );

			acf_render_field_setting( $field, array(
				'label'			=> __( 'Return Format', 'swp-acf-cp' ),
				'instructions'	=> __( 'Specify the value returned', 'swp-acf-cp' ),
				'type'			=> 'select',
				'name'			=> 'return_format',
				'choices'		=> array(
					'value'			=> __( 'Value', 'swp-acf-cp' ),
					'label'			=> __( 'Label', 'swp-acf-cp' ),
					'array'			=> __( 'Both (Array)', 'swp-acf-cp' )
				)
			) );
		}

		/**
		 * format_value()
		 *
		 * This filter is appied to the $value after it is loaded from the db and before it is returned to the template
		 *
		 * @type   filter
		 * @since  3.6
		 * @date   23/01/13
		 *
		 * @param  $value (mixed) the value which was loaded from the database
		 * @param  $post_id (mixed) the $post_id from which the value was loaded
		 * @param  $field (array) the field array holding all the field options
		 *
		 * @return $value (mixed) the modified value
		 */
		function format_value( $value, $post_id, $field ) {
			if ( empty( $value ) ) {
				return $value;
			}

			$label = acf_maybe_get( $field['choices'], $value, $value );

			// value
			if ( $field['return_format'] == 'value' ) {
				// do nothing

			// label
			} elseif ( $field['return_format'] == 'label' ) {
				$value = $label;

			// array
			} elseif ( $field['return_format'] == 'array' ) {
				$value = array(
					'value'	=> $value,
					'label'	=> $label
				);
			}

			return $value;
		}

		/**
	     * update_field()
	     *
	     * This filter is applied to the $field before it is saved to the database
	     *
	     * @type   filter
	     * @date   23/01/2013
	     * @since  3.6.0
	     *
	     * @param  $field (array) the field array holding all the field options
	     * @return $field
	     */
		function update_field( $field ) {
			$field['choices'] = acf_decode_choices( $field['choices'] );

			return $field;
		}

		/**
		 * render_field()
		 *
		 * Create the HTML interface for your field
		 *
		 * @param  $field (array) the $field being rendered
		 *
		 * @type   action
		 * @since  3.6
		 * @date   23/01/13
		 *
		 * @param  $field (array) the $field being edited
		 * @return n/a
		 */
		function render_field( $field ) {
			$hidden_input = acf_get_sub_array( $field, array( 'name', 'value' ) );
			$text_input = acf_get_sub_array( $field, array( 'id', 'class', 'name', 'value', 'readonly' ) );

            /**
             * Pass all choices by a custom HTML attribute to initialise the
             * Color Picker with the user's palette.
             *
             * The use of `data-` attribute is deprecated because the color picker
             * will pick it to its initialisation and we haven't any way to
             * override it after :/
             */
			$choices = acf_get_array( $field['choices'] );
			if ( ! empty( $choices ) ) {
				$text_input[ 'swp-acf-cp-palettes' ] = json_encode( array_keys( $choices ) );
			}

			// html
			?>
			<div class="acf-color-palette">
				<?php acf_hidden_input( $hidden_input ); ?>
        <input type="text" <?php echo acf_esc_attr( $text_input ); ?> class="color-picker" data-alpha-enabled="true">
			</div>
			<?php
		}

		/**
		 * input_admin_enqueue_scripts()
		 *
		 * This action is called in the admin_enqueue_scripts action on the edit screen where your field is created.
		 * Use this action to add CSS + JavaScript to assist your render_field() action.
		 *
		 * @type   action (admin_enqueue_scripts)
		 * @since  3.6
		 * @date   23/01/13
		 *
		 * @param  n/a
		 * @return n/a
		 */
		function input_admin_enqueue_scripts() {
			$url = $this->settings['url'];
			$version = $this->settings['version'];
			$min = defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ? '' : '.min';

			wp_enqueue_style( 'wp-color-picker' );
			wp_register_script( 'wp-color-picker-alpha', "{$url}assets/vendor/wp-color-picker-alpha/wp-color-picker-alpha{$min}.js", array( 'wp-color-picker' ), "3.0.0", true );
			wp_add_inline_script(
				'wp-color-picker-alpha',
				'jQuery( function() { jQuery( ".color-picker" ).wpColorPicker(); } );'
			);
			wp_enqueue_script( 'wp-color-picker-alpha' );

			wp_enqueue_style( 'swp-acf-cp', "{$url}assets/css/input{$min}.css", array( 'wp-color-picker', 'acf-input' ), $version );
		    wp_enqueue_script( 'swp-acf-cp', "{$url}assets/js/input{$min}.js", array( 'wp-color-picker', 'acf-input' ), $version, true );
		}
	}

	new swp_acf_field_color_palette( $this->settings );
}
