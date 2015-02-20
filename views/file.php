<input class="file" type="hidden" name="<?php echo $field_name; ?>" value="<?php echo esc_attr( $field_value ); ?>"/>
<?php if ( $field_value ) {
	$attachment = get_post( $field_value );
	?>
	<div class="has-file">
		<p><?php printf( __( "Current file: %s.", 'dp' ), $attachment->post_title ); ?>
			<?php edit_post_link( __( 'View file here', 'dp' ), '', '', $field_value ); ?>
		</p>
		<p>
			<a class="file-remove button" href="#"><?php _e( 'Remove file', 'dp' ); ?></a>
		</p>
	</div>
<?php } ?>
<div class="add-file <?php if( $field_value ) echo "hidden"; ?>">
	<input type="file" name="<?php echo $field_name; ?>" />
</div>
