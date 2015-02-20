<select name="<?php echo $field_name; ?>">
	<?php foreach ( $extra['options'] as $value => $title ): ?>
		<option value="<?php echo $value; ?>" <?php selected( $field_value, $value ); ?>><?php echo $title; ?></option>
	<?php endforeach; ?>
</select>