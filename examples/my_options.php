<?php
/*
 * Plugin name: My Options
 * Description: This simple plugin is a demonstration of all the available settings options available to AFF. You can use it to learn using the plugin, or simply start modifying it to fit your needs.
 *
 * Note: Make sure you move it one level up. WordPress does not discover nested modules.
*/

// Action for creating an options page
add_action( 'init', 'my_options_create_page', 11 );

/**
 * Display the demonstrative My options_page.
 * This function is hooked to the init hook in setup()
 *
 * @uses  AFF::add_extra_section() To add page sections.
 * @uses  AFF::add_field() To add fields.
 * @uses  AFF::init() To render the page.
 *
 * @see AFF to see how the page is registered and displayed.
 *
 * @return void
 */
function my_options_create_page() {

	// Do nothing if plugin is not active
	if ( ! class_exists( 'Aff' ) ) {
		return;
	}

	$options_page = new Aff();

	// Page title
	$options_page->title = __( 'My Theme Options', 'textdomain' );

	// Link label in the sidebar menu
	$options_page->menu_title = __( 'My Options', 'textdomain' );

	// Page slug. wp-admin/parent.php?page=slug
	// Default: 'options_page'
	$options_page->page_slug = 'theme_options';

	// Permissions required to access/view the page.
	// Default: 'manage_options'
	$options_page->capability = 'edit_theme_options';

	// Slug of the page parent. This configures which menu this page will be under.
	// Default: options-general.php (Settings)
	$options_page->parent_slug = 'themes.php';

	// Can be changed to 'network_menu' - to add a page in the network admin menu
	$options_page->menu_hook = 'admin_menu';

	// The name of the option which will contain all settings in the page
	$options_page->options_name = 'my_theme_options';

	// By default you get a 'general' section that includes all you fields with no specific section
	// You can set it's title like this
	// Default: ''
	$options_page->section_title = '';

	// Image field.
	$options_page->add_field(
		array(
			 'name'    => 'logo',
			 'label'   => __( 'Logo', 'textdomain' ),
			 'type'    => 'image',
			 'section' => 'general', // optional for default section
			 'description'    => '', // optional
		)
	);

	// Text field
	$options_page->add_field(
		array(
			 'name'   => 'description',
			 'label'  => __( 'Short description', 'textdomain' ),
			 'type'   => 'text',
		)
	);

	// Checkbox field
	$options_page->add_field(
		array(
			 'name'        => 'sticky_header',
			 'label'       => __( 'Sticky header', 'textdomain' ),
			 'type'        => 'checkbox',
		)
	);

	// If you need more sections besides the default "general", add them like this
	$options_page->add_section(
		array(
			 'name'  => 'color',
			 'title' => __( 'Colors', 'textdomain' )
		)
	);

	// Select field
	$options_page->add_field(
		array(
			 'name'           => 'color_palette',
			 'label'          => __( 'Color palette', 'textdomain' ),
			 'type'           => 'select',

			 'options' => array(
				 '#AA0000'    => __( 'Red', 'textdomain' ),
				 '#00AA00'    => __( 'Green', 'textdomain' ),
				 '#0000AA'    => __( 'Blue', 'textdomain' ),
			 ),
			 'section' => 'color',
		)
	);

	// Radio field
	$options_page->add_field(
		array(
			 'name'           => 'color2_palette',
			 'label'          => __( 'Secondary color palette', 'textdomain' ),
			 'type'           => 'radio',
			 'options' => array(
				 '#AA0000'    => __( 'Red', 'textdomain' ),
				 '#00AA00'    => __( 'Green', 'textdomain' ),
				 '#0000AA'    => __( 'Blue', 'textdomain' ),
			 ),
			 'section' => 'color',
		)
	);

	$options_page->add_section(
		array(
			 'name'  => 'meta',
			 'title' => __( 'Meta informations', 'textdomain' )
		)
	);

	// Textarea
	$options_page->add_field(
		array(
			 'name'    => 'contact_data',
			 'label'   => __( 'Contact info', 'textdomain' ),
			 'type'    => 'textarea',
			 'description' => __( 'You can enter your physical and internet adress.', 'textdomain' ),
			 'section' => 'meta'
		)
	);

	// Text field
	$options_page->add_field(
		array(
			 'name'    => 'copyright_text',
			 'label'   => __( 'Copyright text', 'textdomain' ),
			 'type'    => 'text',
			 'section' => 'meta'
		)
	);

	// Set button text to false to hide the submit button.
	// If declared `null` it defaults to 'Save changes'
	$options_page->button_text = __( 'Save theme options', 'textdomain' );

	// Render the page - this is mandatory
	$options_page->init();
}

/**
 * This function demonstrates extracting values saved in the options page. It is called nowhere.
 *
 * @uses  get_option() To get full options array
 *
 * @return string
 */
function get_my_options_copyright_text() {
	$my_options = get_option( 'my_theme_options' );
	$individual_option = $my_options['copyright_text'];

	return $individual_option;
}
