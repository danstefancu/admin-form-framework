<?php
/*
 * Plugin name: My Options
 *
 * This simple plugin is a demonstration of all the available settings options available to DP_Options_Page.
 * You can use it to learn using the plugin, or simply start modifying it to fit your needs.
*/

// Action for creating an options page
add_action( 'init', 'my_options_create_page', 11 );

/**
 * Display the demonstrative My options_page.
 * This function is hooked to the init hook in setup()
 *
 * @uses  DP_Options_Page::add_extra_section() To add page sections.
 * @uses  DP_Options_Page::add_field() To add fields.
 * @uses  DP_Options_Page::init() To render the page.
 * 
 * @see DP_Options_Page to see how the page is registered and displayed.
 * 
 * @return void
 */
function my_options_create_page() {
  $options_page               = new DP_Options_Page();

  // Page title
  $options_page->title        = __( 'My Theme Options', 'my_options' );

  // Link label in the menu
  $options_page->menu_title   = __( 'My Options', 'my_options' );

  // Page slug
  $options_page->page_slug    = 'my_theme_options';

  // The name of the option which will contain all settings in the page
  //TODO: What does this do?
  $options_page->options_name = 'my_theme_options';

  // Slug of the page parent. This configures which menu this page will be under.
  // Comment this line to use the default options-general.php (Settings)
  $options_page->parent_slug  = 'themes.php';

  // If you need more sections besides the defaul "general", add them like this
  $options_page->add_extra_section(
    array(
      'name'  => 'header',
      'title' => __('Header', 'my_options')
    )
  );

  // Select field
  $options_page->add_field(
    array(
      'name'        => 'color_pallete',
      'label'       => __( 'Color palette', 'my_options' ),
      'type'        => 'select',
      'description' => '',
      'section'     => 'header',
      'select_options' => array(
        '#AA0000' => 'Red',
        '#00AA00' => 'Green',
        '#0000AA' => 'Blue',
      )
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

  $options_page->add_extra_section(
    array(
      'name'  => 'footer',
      'title' => __('Footer', 'my_options')
    )
  );

  // Image field. 
  // TODO: What about regular files?
  $options_page->add_field(
    array(
      'name'        => 'logo',
      'label'       => __( 'Logo', 'my_options' ),
      'type'        => 'image',
      'description' => '',
      'section'     => 'footer'

    )
  );
  
  // Textarea
  $options_page->add_field(
    array(
      'name'        => 'contact_data',
      'label'       => __( 'Contact info', 'my_options' ),
      'type'        => 'textarea',
      'description' => '',
      'section'     => 'footer'
    )
  );

  // Text field
  $options_page->add_field(
    array(
      'name'        => 'copyright_text',
      'label'       => __( 'Copyright text', 'my_options' ),
      'type'        => 'text',
      'description' => '',
      'section'     => 'footer'
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
  $my_options = get_option('my_options');
  $sticky_header = $my_options['sticky_header'];

  return $sticky_header;
}
