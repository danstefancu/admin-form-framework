=== DP Options Page ===
Contributors: (this should be a list of wordpress.org userid's)
Donate link: http://example.com/
Tags: development, settings
Requires at least: 3.0.1
Tested up to: 3.7
Stable tag: 0.2
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

This plugin provides a wrapper for the WordPress Settings API that is a much easier, faster and extensible way of building your settings forms.

== Description ==

This plugin will help you quickly set up a standard WordPress settings page for your project, be it a theme or a plugin. That's it!


Below is a "Hello World" piece of code to quickly get you an ideea. It will generate a standard settings page with a checkbox, complete markup, save logic.

`<?php 

add_action( 'init', 'my_options_create_page', 11 );

function my_options_create_page() {
  $options_page = new DP_Options_Page();

  // Add a checkbox
  $options_page->add_field(
    array(
      'name'        => 'simple_checkbox',
      'label'       => __( 'This plugin is awesome', 'my_options' ),
      'type'        => 'checkbox',
      'description' => 'Help your users with a nice description.',
      'section'     => 'general', // Default section. You can add new ones as easily as adding a field.
    )
  );

  // Render the page - this is mandatory
  $options_page->init();

?>`

= There are a zillion options plugins out there. Why this? =

All plugins will inevitably have their limitations, but DP Options Page is written in form of an object that can be easily extended to suit your needs.

= Get started =

For a complete example / settings page template, open up the included my_options.php.example file.


== Installation ==

1. Upload to the `/wp-content/plugins/` directory
1. Activate the plugin through the 'Plugins' menu in WordPress
1. Follow the instructions to get started with your first settings page.

== Frequently Asked Questions ==

= Does this plugin do anything by itself? =

No. This plugin is targeted at developers of WordPress themes and plugins.

= Why not use WordPress Settings API directly? =

The Settings API can be difficult to use by the novice developer. Writing the settings pages means a lot of code that can be difficult to follow, and a lot of repetitive HTML code. DP Options Page wraps it all up and allows you to quickly add the options you need without thinking about the technicalities of defining, saving and displaying the options on the options page.

= When should I not use DP Options Page? =

If you require custom markup or validation, this plugin may get in your way. However, if your custom needs are limited, you can easily extend the settings class with new option types that satisfy your particular needs.

== Screenshots ==

// TODO: Not sure how to use this
1. This screen shot description corresponds to screenshot-1.(png|jpg|jpeg|gif). Note that the screenshot is taken from
the /assets directory or the directory that contains the stable readme.txt (tags or trunk). Screenshots in the /assets 
directory take precedence. For example, `/assets/screenshot-1.png` would win over `/tags/4.3/screenshot-1.png` 
(or jpg, jpeg, gif).
2. This is the second screen shot

== Changelog ==

= 0.3 =
First public release.
* Added readme.txt
* Added new field types
* Added example code file

== Upgrade notice ==

= 0.3 =
This is the first public release. It has been carefully reviewed and includes a significant number of improvements.
