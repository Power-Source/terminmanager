<table class="form-table">
	<tr class="appointment-create-page">
		<th scope="row"><label for="make_an_appointment"><?php _e( 'Erstelle eine Terminbuchungsseite', 'appointments' ) ?></label></th>
		<td>
			&nbsp;<?php _e( 'mit', 'appointments' ) ?>&nbsp;
			<label for="app_page_type" class="screen-reader-text"><?php _e( 'Erstelle einen Terminbuchungskalender mit diesem Format', 'appointments' ); ?></label>
			<select>
				<option value="one_month"><?php _e( 'Zeitplan des aktuellen Monats', 'appointments' ) ?></option>
				<option value="two_months" <?php selected( 'two_months' == @$options['app_page_type'] ); ?>><?php _e( 'aktuellen und nächsten Monat', 'appointments' ) ?></option>
				<option value="one_week" <?php selected( 'one_week' == @$options['app_page_type'] ); ?>><?php _e( 'aktuelle und nächste Woche', 'appointments' ) ?></option>
				<option value="two_weeks" <?php selected( 'two_weeks' == @$options['app_page_type'] ); ?>><?php _e( 'aktuelle und nächsten Wochen', 'appointments' ) ?></option>
            </select>
            <a href="#" data-action="make_an_appointment_page" class="button" data-nonce="<?php echo wp_create_nonce( 'appointment-create-page' ); ?>"><?php esc_html_e( 'Erstelle Seite!', 'appointments' ); ?></a>
		</td>
	</tr>

	<tr>
		<th scope="row"><label for="show_legend"><?php _e( 'Legende anzeigen', 'appointments' ) ?></label></th>
		<td>
            <?php _appointments_html_chceckbox( $options, 'show_legend' ) ?>
			<p class="description"><?php _e( 'Gibt an, ob Beschreibungsfelder über dem Paginierungsbereich angezeigt werden sollen.', 'appointments' ) ?></p>
		</td>
	</tr>

	<tr>
		<th scope="row"><label for="color_set"><?php _e( 'Farbset', 'appointments' ) ?></label></th>
		<td>
			<select name="color_set" id="color_set">
				<option value="1" <?php selected( @$options['color_set'] == 1 ); ?>><?php _e( 'Voreinstellung 1', 'appointments' ) ?></option>
				<option value="2" <?php selected( @$options['color_set'] == 2 ); ?>><?php _e( 'Voreinstellung 2', 'appointments' ) ?></option>
				<option value="3" <?php selected( @$options['color_set'] == 3 ); ?>><?php _e( 'Voreinstellung 3', 'appointments' ) ?></option>
				<option value="0" <?php selected( @$options['color_set'] == 0 ); ?>><?php _e( 'Benutzerdefiniert', 'appointments' ) ?></option>
			</select>

			<p class="preset_samples" <?php if ( @$options['color_set'] == 0 ) { echo 'style="display:none"'; } ?>>
				<?php foreach ( $appointments->get_classes() as $class => $name ) :  ?>
					<label>
						<span> <?php echo $name ?>: </span>
								<span>
									<a href="javascript:void(0)" class="pickcolor <?php echo $class ?> hide-if-no-js" <?php if ( @$options['color_set'] != 0 ) {
										echo 'style="background-color:#' . $appointments->get_preset( $class, $options['color_set'] ) . '"'; } ?>>
									</a>
								</span>
					</label>
				<?php endforeach; ?>
			</p>
		</td>
	</tr>


	<tr class="custom_color_row" <?php if ( @$options['color_set'] != 0 ) { echo 'style="display:none"'; } ?>>
		<th scope="row"><?php _e( 'Benutzerdefiniertes Farbset', 'appointments' ) ?></th>
		<td colspan="2">
			<?php foreach ( $appointments->get_classes() as $class => $name ) :  ?>
				<label style="width:31%;display:block;float:left;">
					<span style="float:left"><?php echo $name ?>:</span>
							<span style="float:left;margin-right:8px;">
								<a href="javascript:void(0)" class="pickcolor hide-if-no-js" <?php if ( isset( $options[ $class . '_color' ] ) ) { echo 'style="background-color:#' . $options[ $class . '_color' ] . '"'; } ?>></a>
								<input style="width:50px" type="text" class="colorpicker_input" maxlength="6" name="<?php echo $class ?>_color" id="<?php echo $class ?>_color" value="<?php if ( isset( $options[ $class . '_color' ] ) ) { echo $options[ $class . '_color' ]; } ?>"/>
							</span>
				</label>
			<?php endforeach; ?>
			<div style="clear:both"></div>
			<span class="description"><?php _e( 'Wenn Du Benutzerdefinierten Farbsatz ausgewählt hast, gib für jede Zelle den 3- oder 6-stelligen Hex-Code der Farbe manuell ohne # vor ein, oder verwende den Farbpicker.', 'appointments' ) ?></span>
		</td>
	</tr>

	<tr valign="top">
		<th scope="row"><?php _e( 'Fordere dies vom Clienten an:', 'appointments' ) ?></th>
		<td colspan="2">
			<input type="checkbox" id="ask_name" name="ask_name" <?php if ( isset( $options['ask_name'] ) && $options['ask_name'] ) { echo 'checked="checked"'; } ?> />&nbsp;<label for="ask_name"><?php echo $appointments->get_field_name( 'name' ) ?></label>
			&nbsp;&nbsp;&nbsp;
			<input type="checkbox" id="ask_email" name="ask_email" <?php if ( isset( $options['ask_email'] ) && $options['ask_email'] ) { echo 'checked="checked"'; } ?> />&nbsp;<label for="ask_email"><?php echo $appointments->get_field_name( 'email' ) ?></label>
			&nbsp;&nbsp;&nbsp;
			<input type="checkbox" id="ask_phone" name="ask_phone" <?php if ( isset( $options['ask_phone'] ) && $options['ask_phone'] ) { echo 'checked="checked"'; } ?> />&nbsp;<label for="ask_phone"><?php echo $appointments->get_field_name( 'phone' ) ?></label>
			&nbsp;&nbsp;&nbsp;
			<input type="checkbox" id="ask_address" name="ask_address" <?php if ( isset( $options['ask_address'] ) && $options['ask_address'] ) { echo 'checked="checked"'; } ?> />&nbsp;<label for="ask_address"><?php echo $appointments->get_field_name( 'address' ) ?></label>
			&nbsp;&nbsp;&nbsp;
			<input type="checkbox" id="ask_city" name="ask_city" <?php if ( isset( $options['ask_city'] ) && $options['ask_city'] ) { echo 'checked="checked"'; } ?> />&nbsp;<label for="ask_city"><?php echo $appointments->get_field_name( 'city' ) ?></label>
			&nbsp;&nbsp;&nbsp;
			<input type="checkbox" id="ask_note" name="ask_note" <?php if ( isset( $options['ask_note'] ) && $options['ask_note'] ) { echo 'checked="checked"'; } ?> />&nbsp;<label for="ask_note"><?php echo $appointments->get_field_name( 'note' ) ?></label>
			&nbsp;&nbsp;&nbsp;
			<br/>
			<p class="description"><?php _e( 'Die ausgewählten Felder stehen im Bestätigungsbereich zur Verfügung und werden vom Client abgefragt. Wenn ausgewählt, muss diese ausgefüllt werden (außer im Notizfeld).', 'appointments' ) ?></p>
		</td>
	</tr>

	<tr>
		<th scope="row"><label for="additional_css"><?php _e( 'Zusätzliche CSS-Regeln', 'appointments' ) ?></label></th>
		<td colspan="2">
			<textarea class="widefat" rows="6" name="additional_css" id="additional_css"><?php echo esc_textarea( $options['additional_css'] ); ?></textarea>
			<p class="description"><?php _e( 'Du kannst CSS-Regeln hinzufügen, um das Styling anzupassen. Diese werden nur der Front-End-Terminseite hinzugefügt.', 'appointments' ) ?></p>
		</td>
	</tr>
	<?php do_action( 'app-settings-display_settings' ); ?>
</table>
