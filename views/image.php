<?php
$button_name = $field_name . '_button';
$type = 'image';
$class = isset( $extra['class'] ) ? $extra['class'] : 'options-page-image';
if ( $field_value ) {
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
	<input type="hidden" class="file-value" name="<?php echo $field_name; ?>" id="<?php echo $field_name; ?>" value="<?php echo esc_attr( $field_value ); ?>"/>

	<div class="file-missing <?php echo $uploader_div; ?>">
		<span><?php _e( 'No file selected.', 'dp' ); ?></span>
		<button class="button file-add" name="<?php echo $button_name; ?>" id="<?php echo $button_name; ?>"><?php _e( 'Add file', 'dp' ) ?></button>
	</div>
	<div class="file-exists clearfix <?php echo $display_div; ?>">
		<img class="file-icon" src="<?php echo $icon_src; ?>"/>
		<br/>
		<span class="file-name hidden"><?php echo $filename; ?></span>
		<a class="file-remove button" href="#"><?php _e( 'Remove', 'dp' ); ?></a>
	</div>
</div>