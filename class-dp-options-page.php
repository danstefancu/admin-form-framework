<?php
/*
Plugin name: DP Options Page
Plugin URI: http://dreamproduction.com/wordpress/dp-options-page
Description: Small framework for option pages.
Version: 0.3
Author: Dan Stefancu
Author URI: http://stefancu.ro/
*/


/**
 * Small framework for adding options pages
 */
class DP_Options_Page {
	var $title = 'DP Options';
	var $menu_title = 'DP Options';
	var $page_slug = 'dp_options';
	var $capability = 'manage_options';
	var $options_name = 'dp_options';
	var $section_title = '';
	var $parent_slug = 'options-general.php';
	var $menu_hook = 'admin_menu';

	private $hook_suffix;

	var $fields = array();
	var $extra_sections = array();
	var $saved_options;

	public function init() {

		$this->saved_options = get_option( $this->options_name );

		if ($this->extra_sections)
			add_action( 'admin_init', array($this, 'extra_sections_init') );

		add_action( 'admin_init', array($this, 'options_init') );
		add_action( $this->menu_hook , array($this, 'add_page') );
		add_action( 'admin_enqueue_scripts', array($this, 'admin_scripts' ) );
	}

	/**
	 *
	 * name - coffee_check
	 * label - Want a coffee?
	 * type - checkbox
	 * description - If the user want coffee or not
	 *
	 * @param array $args
	 */
	public function add_field( $args = array() ) {
		$this->fields[] = $args;
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
			array($this, 'render_page') 				// page callback. renders the page itself
		);
	}

	function url( $file ) {
		$dir_path = dirname(__FILE__);

		if( !file_exists( trailingslashit( $dir_path) . $file ) )
			return false;

		$home_path = untrailingslashit( get_home_path() );

		$path_from_root = str_replace($home_path, '', $dir_path );
		$path_from_root = str_replace( '\\', '/', $path_from_root);

		return home_url( trailingslashit( $path_from_root ) . $file );
	}

	function admin_scripts( $hookname ) {
		if( $this->hook_suffix == $hookname ) {
			wp_enqueue_script( 'jquery' );

			wp_enqueue_media();

			wp_enqueue_script(
				'dp-options',      // name/id
				$this->url('dp-options.js'), // file
				array( 'jquery' )             // dependencies
			);
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

		add_settings_section(
			'general',				// id
			$this->section_title,	// title
			'__return_false',		// callback
			$this->page_slug		// page slug
		);

		foreach ($this->fields as $field) {
			if ($field) {
				$section = isset($field['section']) && $this->section_exists($field['section']) ? $field['section'] : 'general';
				add_settings_field(
					$field['name'],						// field id (internal)
					$field['label'],					// field label
					array($this, 'display_field'),		// callback function
					$this->page_slug,					// page to add to
					$section,							// section to add to
					$field 								// extra args
				);
			}
		}
	}

	/**
	 * Add extra section for this option page.
	 *
	 * @param array $args - 'name', 'title' strings
	 */
	function add_extra_section( $args ) {
		$this->extra_sections[] = array( 'name' => $args['name'], 'title' => $args['title'] );

	}

	/**
	 * Register extra sections on this option page.
	 */
	function extra_sections_init() {
		foreach ($this->extra_sections as $section) {
			add_settings_section(
				$section['name'],		// id
				$section['title'],		// title
				'__return_false',		// callback
				$this->page_slug		// page slug
			);
		}
	}

	/**
	 * Check if the section exists on current options page.
	 * @param $section
	 *
	 * @return bool
	 */
	function section_exists($section) {
		global $wp_settings_sections;

		foreach ($wp_settings_sections[$this->page_slug] as $section_name => $args ) {
			if ($section === $section_name)
				return true;
		}

		return false;
	}

	/**
	 * Callback for register_settings_field().
	 *
	 * @param array $field options passed by register_settings_field()
	 * @see inl_course_options_init(options_init
	 */
	function display_field( $field ) {
		$current_option_name = isset($field['name']) ? $field['name'] : '';

		$field_callback = isset($field['type']) ? 'display_' . $field['type'] : 'display_text';

		$field_name = "{$this->options_name}[{$current_option_name}]";
		$field_value = isset($this->saved_options[$current_option_name]) ? $this->saved_options[$current_option_name] : '';
		$extra = $field;

		$this->$field_callback($field_name, $field_value, $extra);
		$this->display_description($field['description']);
	}

	function display_description($text = '') {
		if ($text) { ?>
			<p class="description"><?php echo $text; ?></p>
		<?php
		}
	}

	function display_textarea($field_name, $field_value, $extra = array()) {
		?>
		<textarea class="large-text" name="<?php echo $field_name; ?>"><?php echo esc_textarea( $field_value ); ?></textarea>
	<?php
	}

	function display_checkbox($field_name, $field_value, $extra = array()) {
		?>
		<input type="checkbox" name="<?php echo $field_name; ?>" value="1" <?php echo checked( 1, $field_value ); ?> />
	<?php
	}

	function display_text($field_name, $field_value, $extra = array()) {
		?>
		<input type="text" class="regular-text" name="<?php echo $field_name; ?>" value="<?php echo esc_attr( $field_value ); ?>" />
	<?php
	}

	function display_select($field_name, $field_value, $extra = array()) {
		?>
		<select name="<?php echo $field_name; ?>">
			<?php foreach($extra['select_options'] as $value => $title): ?>
				<option value="<?php echo $value; ?>" <?php selected($field_value, $value); ?>><?php echo $title; ?></option>
			<?php endforeach; ?>
		</select>
	<?php
	}

	function display_radio($field_name, $field_value, $extra = array() ) {
		if( !isset( $extra['radio_options'] ) )
			return;

		foreach( $extra['radio_options'] as $value => $title ): ?>
			<label><input type="radio" name="<?php  echo $field_name; ?>" value="<?php echo $value;?>" <?php checked( $field_value, $value );?>/>
				<?php echo $title; ?>
			</label></br>
		<?php
		endforeach;
	}

	function display_image( $field_name, $field_value, $extra = array() ) {
		$button_name = $field_name . '_button';
		$type = 'image';
		$class = isset( $extra['class'] ) ? $extra['class'] : 'options-page-image';
		if ($field_value) {
			$attachment = get_post( $field_value );
			$filename = basename( $attachment->guid );
			$icon_src = wp_get_attachment_image_src( $attachment->ID, 'medium' );
			$icon_src = array_values( $icon_src );
			$icon_src = array_shift( $icon_src );
			$uploader_div = 'hidden';
			$display_div = '';
		} else {
			$uploader_div = '';
			$display_div = 'hidden';
			$filename = '';
			$icon_src = wp_mime_type_icon( $type );
		}

		?>

		<div class="<?php echo $class; ?> dp-field" data-type="<?php echo $type; ?>">
			<input type="hidden" class="file-value" name="<?php echo $field_name; ?>" id="<?php echo $field_name; ?>" value="<?php echo esc_attr($field_value); ?>" />

			<div class="file-missing <?php echo $uploader_div; ?>">
				<span><?php _e('No file selected.', 'dp'); ?></span>
				<button class="button file-add" name="<?php echo $button_name; ?>" id="<?php echo $button_name; ?>"><?php _e('Add file', 'dp') ?></button>
			</div>
			<div class="file-exists clearfix <?php echo $display_div; ?>">
				<img class="file-icon" src="<?php echo $icon_src; ?>" />
				<br/>
				<span class="file-name hidden"><?php echo $filename; ?></span>
				<a class="file-remove button" href="#"><?php _e('Remove', 'dp'); ?></a>
			</div>
		</div>
	<?php
	}

	/**
	 * Display the options page
	 */
	function render_page() {
		?>
		<div class="wrap">
			<?php screen_icon(); ?>

			<h2><?php echo $this->title; ?></h2>

			<form method="post" action="<?php echo admin_url( 'options.php' ); ?>">
				<?php
				settings_fields( $this->options_name );
				do_settings_sections( $this->page_slug );
				submit_button();
				?>
			</form>
		</div><!-- .wrap -->
	<?php
	}
}