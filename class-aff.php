<?php
/**
 * Plugin name: Admin Form Framework
 * Plugin URI: http://dreamproduction.com/wordpress/admin-form-framework
 * Description: Small framework for building Admin pages with forms. This plugin provides a wrapper for the WordPress Settings API that is a much easier, faster and extensible way of building your settings forms.
 * Version: 1.2.1
 * Author: Dan Stefancu
 * Author URI: http://stefancu.ro/
 * License: GPL-2.0+
 * License URI: http://www.gnu.org/licenses/gpl-2.0.txt
 */


/**
 * Small framework for adding options pages
 */
class Aff {

	var $title = 'Options Page';
	var $menu_title = 'AFF Options';
	var $page_slug = 'options_page';
	var $capability = 'manage_options';
	var $options_name = 'aff_options';
	var $section_title = '';
	var $parent_slug = 'options-general.php';
	var $menu_hook = 'admin_menu';
	var $button_text = null;
	var $multilingual_options = false;

	/*
	 * Enable/Disable saving files into media
	 * Set to false if files are one time use
	 */
	var $save_files = true;

	private $hook_suffix;

	var $fields = array();
	var $sections = array();
	var $saved_options;

	/**
	 * Only method called from outside.
	 */
	public function init() {

		if ( class_exists( 'Sitepress' ) && $this->multilingual_options ) {
			global $sitepress;

			if ( $sitepress->get_default_language() != $sitepress->get_current_language() ) {
				$this->options_name .= '-' . $sitepress->get_current_language();
			}
		}

		if ( empty($this->saved_options) ) {
			if( $this->menu_hook == "network_admin_menu" ) {
				$this->saved_options = get_site_option( $this->options_name );
				add_action('update_wpmu_options', array( $this, 'update_site_options') );
			} else {
				$this->saved_options = get_option( $this->options_name );
			}
		}

		add_action( $this->menu_hook, array( $this, 'add_page' ), 11 );

		add_action( 'admin_init', array( $this, 'save_files' ), 10 );
		add_action( 'admin_init', array( $this, 'sections_init' ), 11 );
		add_action( 'admin_init', array( $this, 'options_init' ), 12 );
		add_action( 'admin_enqueue_scripts', array( $this, 'admin_scripts' ) );
	}

	/**
	 * Register options page. Only for main site
	 */
	function add_page() {
		$this->hook_suffix = add_submenu_page(
			$this->parent_slug,
			$this->title,				// page title. for title bar
			$this->menu_title,			// menu title. for menu label
			$this->capability,			// capability
			$this->page_slug,			// slug. needed for sections, unique identifier - options.php?page=$page_slug
			array( $this, 'render_page' ) 				// page callback. renders the page itself
		);
	}

	/**
	 * Register extra sections on this option page.
	 */
	function sections_init() {
		add_settings_section(
			'general',		       // id
			$this->section_title,  // title
			'__return_false',	   // callback
			$this->page_slug	   // page slug
		);

		foreach ( $this->sections as $section ) {
			$section['callback'] =
				( isset( $section['callback'] ) && !empty( $section['callback'] ) ) ? $section['callback'] : '__return_false';

			add_settings_section(
				$section['name'],		// id
				$section['title'],		// title
				$section['callback'],	// callback
				$this->page_slug		// page slug
			);
		}
	}

	/**
	 * Handle file saving, adding attachment and storing id in options
	 *
	 * @see media_handle_upload()
	 */
	function save_files() {
		if ( $this->is_posted() && $_FILES && $this->save_files ) {
			$sorted = array();

			/**
			 * Reformat the structure of the $_FILES for media_handle_upload()
			 * Required structure: array( 'field_name' => file_data_array )
			 * WP Settings API puts field data as field_name => value
			 * inside file data array with keys:
			 * name, tmp_name, type, error, size
			 */
			foreach ( $_FILES[ $this->options_name ] as $file_data_key => $field_data_for_key ) {

				foreach ( $field_data_for_key as $field_name => $file_data )
					$sorted[$field_name][$file_data_key] = $file_data;

			}
			$_FILES = $sorted;

			foreach( $this->fields as $field ) {
				if ( $field['type'] != 'file' ) {
					continue;
				}
				// handle upload as unattached media file
				$attachment_id = media_handle_upload( $field['name'], 0 );
				// store attachment id in options
				if ( $attachment_id && ! is_wp_error( $attachment_id ) ) {
					$_POST[ $this->options_name ][ $field['name'] ] = $attachment_id;
				}
			}
		}
	}

	/**
	 * Generate settings and settings sections for our options page
	 */
	function options_init() {

		register_setting(
			$this->options_name,	// option group. used in render_page() -> settings_fields()
			$this->options_name		// option name. database option name

		);

		foreach ( $this->fields as $field ) {
			if ( $field ) {
				$section =
					isset( $field['section'] ) && $this->section_exists( $field['section'] ) ? $field['section'] : 'general';
				add_settings_field(
					$field['name'],						// field id (internal)
					$field['label'],					// field label
					array( $this, 'display_field' ),	// callback function
					$this->page_slug,					// page to add to
					$section,							// section to add to
					$field 								// extra args
				);
			}
		}
	}

	/**
	 * Handle enqueue for current option page.
	 *
	 * @param string $hookname
	 */
	function admin_scripts( $hookname ) {
		if ( $this->hook_suffix == $hookname ) {
			wp_enqueue_script( 'jquery' );

			wp_enqueue_media();

			wp_enqueue_script(
				'aff-general',             // name/id
				$this->url( 'aff.js' ),    // file
				array( 'jquery' )          // dependencies
			);
		}
	}

	/**
	 * Add extra section for this option page.
	 *
	 * @param array $options
	 *
	 * @var string $options ['name']
	 * @var string $options ['title']
	 * @var string $options ['callback']
	 */
	function add_section( $options = array() ) {
		$this->sections[] = $options;
	}

	/**
	 *
	 * @param array $args
	 *
	 * @var string $args ['name'] coffee_check
	 * @var string $args ['label'] Want a coffee?
	 * @var string $args ['type'] checkbox
	 * @var string $args ['description'] If the user want coffee or not
	 *
	 */
	public function add_field( $args = array() ) {
		$this->fields[] = $args;
	}


	/**
	 * Check if the section exists on current options page.
	 *
	 * @param $section
	 *
	 * @return bool
	 */
	function section_exists( $section ) {
		global $wp_settings_sections;

		foreach ( $wp_settings_sections[$this->page_slug] as $section_name => $args ) {
			if ( $section === $section_name ) {
				return true;
			}
		}

		return false;
	}

	/**
	 * Callback for register_settings_field().
	 *
	 * @param array $field options passed by register_settings_field()
	 */
	function display_field( $field ) {
		$current_option_name = isset( $field['name'] ) ? $field['name'] : '';

		$field_callback = isset( $field['type'] ) ? 'display_' . $field['type'] : 'display_text';

		$field_name = "{$this->options_name}[{$current_option_name}]";
		$field_value =
			isset( $this->saved_options[$current_option_name] ) ? $this->saved_options[$current_option_name] : '';
		$extra = $field;

		$this->$field_callback( $field_name, $field_value, $extra );
		if ( isset( $field['description'] ) ) {
			$this->display_description( $field['description'] );
		}
	}

	/**
	 * @param string $text
	 */
	function display_description( $text = '' ) {
		include( 'views/description.php' );
	}

	/**
	 * @param string $field_name
	 * @param string $field_value
	 * @param array $extra
	 */
	function display_textarea( $field_name, $field_value, $extra = array() ) {
		include( 'views/textarea.php' );
	}

	/**
	 * @param string $field_name
	 * @param string $field_value
	 * @param array $extra
	 */
	function display_checkbox( $field_name, $field_value, $extra = array() ) {
		include( 'views/checkbox.php' );
	}

	/**
	 * @param string $field_name
	 * @param string $field_value
	 * @param array $extra
	 */
	function display_text( $field_name, $field_value, $extra = array() ) {
		include ( 'views/text.php' );
	}

	/**
	 * @param string $field_name
	 * @param string $field_value
	 * @param array $extra
	 */
	function display_select( $field_name, $field_value, $extra = array() ) {
		if ( ! isset( $extra['options'] ) ) {
			return;
		}
		include( 'views/select.php' );
	}

	/**
	 * @param string $field_name
	 * @param string $field_value
	 * @param array $extra
	 */
	function display_radio( $field_name, $field_value, $extra = array() ) {
		if ( !isset( $extra['options'] ) )
			return;

		include( 'views/radio.php' );
	}

	/**
	 * @param string $field_name
	 * @param string $field_value
	 * @param array $extra
	 */
	function display_image( $field_name, $field_value, $extra = array() ) {
		include( 'views/image.php' );
	}

	/**
	 * @param string $field_name
	 * @param string $field_value
	 * @param array $extra
	 */
	function display_file( $field_name, $field_value, $extra = array() ) {
		include( 'views/file.php' );
	}

	/**
	 * Returns all used field types
	 *
	 * @return array
	 */
	function get_field_types() {
		$field_types = array();

		foreach( $this->fields as $field ) {
			if( ! in_array( $field['type'], $field_types ) ) {
				$field_types[] = $field['type'];
			}
		}

		return $field_types;
	}
	/**
	 * Display the options page
	 */
	function render_page() {
		include( 'views/options-page.php' );
	}

	/**
	 * Update site options in case the form is a network admin menu
	 * @return void
	 */
	function update_site_options() {

		if ( isset( $_POST[ $this->options_name ] ) ) {
			$value = stripslashes_deep( $_POST[ $this->options_name ] );
			update_site_option( $this->options_name, $value );

			if( isset( $_POST['_wp_http_referer'] ) ) {
				wp_redirect( add_query_arg( 'updated', 'true', $_POST['_wp_http_referer'] ) );
				exit();
			}
		}
	}

	/**
	 * Generate dynamic URL for a file to be included.
	 *
	 * @param string $file
	 *
	 * @return bool|string
	 */
	function url( $file ) {
		$dir_path = dirname( __FILE__ );
		$dir_path = str_replace( '\\', '/', $dir_path );

		if ( !file_exists( trailingslashit( $dir_path ) . $file ) )
			return false;

		$home_path = untrailingslashit( get_home_path() );
		$home_path = str_replace( '\\', '/', $home_path );

		$path_from_root = str_replace( $home_path, '', $dir_path );
		$path_from_root = str_replace( '\\', '/', $path_from_root );

		return home_url( trailingslashit( $path_from_root ) . $file );
	}

	/**
	 * Is posted data from this form?
	 *
	 * Relies on data set by settings_fields() used in $this::render_page()
	 *
	 * @see Aff::render_page()
	 * @see settings_fields()
	 *
	 * @return bool
	 */
	function is_posted() {
		return ( isset( $_POST['option_page'] ) && $_POST['option_page'] == $this->options_name );
	}

	function posted_data() {
		return ( $_POST[ $this->options_name ] );
	}
}
