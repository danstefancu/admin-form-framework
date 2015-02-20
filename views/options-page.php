<?php
global $wp_version;
?>
<div class="wrap">
	<?php if ( version_compare( $wp_version, '3.8', '<' ) ) { screen_icon(); } ?>

	<h2><?php echo $this->title; ?></h2>

	<?php

	if( $this->menu_hook == "network_admin_menu" ) {
		$action = network_admin_url( 'settings.php' );
	} else {
		$action = admin_url( 'options.php' );
	}

	$encryption_type = '';
	$field_types = $this->get_field_types();

	if( in_array( 'file', $field_types ) )
		$encryption_type = 'enctype="multipart/form-data"';
	?>

	<form method="post" action="<?php echo $action; ?>" <?php echo $encryption_type; ?>>
		<?php
		settings_fields( $this->options_name );
		if ( $this->menu_hook == "network_admin_menu" ) {
			wp_nonce_field( 'siteoptions' );
		}
		do_settings_sections( $this->page_slug );
		if ( $this->button_text !== false ) submit_button( $this->button_text );
		?>
	</form>
</div><!-- .wrap -->
