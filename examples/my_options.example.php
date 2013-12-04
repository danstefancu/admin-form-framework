<?php
/*
 * Plugin name: My Options
 *
 * This simple plugin is a demonstration of all the available settings options available to AFF.
 * You can use it to learn using the plugin, or simply start modifying it to fit your needs.
 *
 * Note: Make sure you move it one level up. Wordpress does not discover nested modules.
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

	if (class_exists('AFF')) {
		$options_page = new AFF();
	} else {
		return;
	}


	// Page title
	$options_page->title = __( 'My Theme Options', 'my_options' );

	// Link label in the sidebar menu
	$options_page->menu_title = __( 'My Options', 'my_options' );

	// Page slug. wp-admin/parent.php?page=slug
	// Default: 'option_page'
	$options_page->page_slug = 'theme_options';

	// Permissions required to access the page. See add_submenu_page()
	// Default: 'manage_options'
	$options_page->capability = 'edit_theme_options';

	// The name of the option which will contain all settings in the page
	$options_page->options_name = 'my_theme_options';

	// By default you get a 'general' section that includes all you fields with no specific section
	// You can set it's title like this
	// Default: ''
	$options_page->section_title = '';

	// Slug of the page parent. This configures which menu this page will be under.
	// Default: options-general.php (Settings)
	$options_page->parent_slug = 'themes.php';

	// Can be changed to 'network_menu' - to add a page in the network admin menu
	$options_page->menu_hook = 'admin_menu';

	// If you need more sections besides the defaul "general", add them like this
	$options_page->add_section(
		array(
			'name'  => 'header',
			'title' => __( 'Header', 'my_options' )
		)
	);

	// Select field
	$options_page->add_field(
		array(
			'name'           => 'color_palette',
			'label'          => __( 'Color palette', 'my_options' ),
			'type'           => 'select',
			'description'    => '', // optional
			'select_options' => array(
				'#AA0000'    => 'Red',
				'#00AA00'    => 'Green',
				'#0000AA'    => 'Blue',
			),
			'section' => 'header',
		)
	);

	// Radio field
	$options_page->add_field(
		array(
			'name'           => 'color2_palette',
			'label'          => __( 'Secondary color palette', 'my_options' ),
			'type'           => 'radio',
			'description'    => '', // optional
			'radio_options' => array(
				'#AA0000'    => 'Red',
				'#00AA00'    => 'Green',
				'#0000AA'    => 'Blue',
			),
			'section' => 'header',
		)
	);

	// Checkbox field
	$options_page->add_field(
		array(
			'name'        => 'sticky_header',
			'label'       => __( 'Sticky header', 'my_options' ),
			'type'        => 'checkbox',
			'description' => 'Checking this will make the header sticky on desktop devices. Mobile devices have a sticky header by default.',
			'section'     => 'header',
		)
	);


	$options_page->add_section(
		array(
			'name'  => 'footer',
			'title' => __( 'Footer', 'my_options' )
		)
	);

	// Image field.
	$options_page->add_field(
		array(
			 'name'    => 'logo',
			 'label'   => __( 'Logo', 'my_options' ),
			 'type'    => 'image',
			 'section' => 'footer'

		)
	);

	// Textarea
	$options_page->add_field(
		array(
			 'name'    => 'contact_data',
			 'label'   => __( 'Contact info', 'my_options' ),
			 'type'    => 'textarea',
			 'section' => 'footer'
		)
	);

	// Text field
	$options_page->add_field(
		array(
			'name'    => 'copyright_text',
			'label'   => __( 'Copyright text', 'my_options' ),
			'type'    => 'text',
			'section' => 'footer'
		)
	);

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
function my_options_get_copyright_text() {
	$my_options = get_option( 'my_theme_options' );
	$sticky_header = $my_options['sticky_header'];

	return $sticky_header;
}
