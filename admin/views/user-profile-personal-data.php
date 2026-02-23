<h3><?php _e( "PS Terminmanager: Persönliche Daten", 'appointments' ); ?></h3>
<table class="form-table">
	<tr>
		<th><label for="app_name"><?php _e("Mein Name", 'appointments'); ?></label></th>
		<td>
			<input type="text" id="app_name" class="regular-text" name="app_name" value="<?php echo get_user_meta( $profileuser->ID, 'app_name', true ) ?>" <?php echo $is_readonly ?> />
		</td>
	</tr>

	<tr>
		<th><label for="app_email"><?php _e("Meine E-Mail", 'appointments'); ?></label></th>
		<td>
			<input type="text" id="app_email" class="regular-text" name="app_email" value="<?php echo get_user_meta( $profileuser->ID, 'app_email', true ) ?>" <?php echo $is_readonly ?> />
		</td>
	</tr>

	<tr>
		<th><label for="app_phone"><?php _e("Meine Telefonnummer", 'appointments'); ?></label></th>
		<td>
			<input type="text" id="app_phone" class="regular-text" name="app_phone" value="<?php echo get_user_meta( $profileuser->ID, 'app_phone', true ) ?>"<?php echo $is_readonly ?> />
		</td>
	</tr>

	<tr>
		<th><label for="app_address"><?php _e("Meine Addresse", 'appointments'); ?></label></th>
		<td>
			<input type="text" id="app_address" class="widefat" name="app_address" value="<?php echo get_user_meta( $profileuser->ID, 'app_address', true ) ?>" <?php echo $is_readonly ?> />
		</td>
	</tr>

	<tr>
		<th><label for="app_city"><?php _e("Mein Ort", 'appointments'); ?></label></th>
		<td>
			<input type="text" id="app_city" class="regular-text" name="app_city" value="<?php echo get_user_meta( $profileuser->ID, 'app_city', true ) ?>" <?php echo $is_readonly ?> />
		</td>
	</tr>
</table>