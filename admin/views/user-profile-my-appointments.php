<h3><?php esc_html_e( "PS Terminmanager: Meine Termine", 'appointments' ); ?></h3>
<table class="form-table">
	<tr>
		<th><?php _e( 'Termine', 'appointments' ); ?></th>
		<td>
			<?php echo do_shortcode("[app_my_appointments allow_cancel=1 title='' client_id=".$profileuser->ID." ".$gcal."]") ?>
		</td>
	</tr>
</table>

<?php if ( $allow_cancel ): ?>
	<script type='text/javascript'>
		jQuery(document).ready(function ($) {
			$('#your-profile').submit(function () {
				if ($('.app-my-appointments-cancel').is(':checked')) {
					if (!confirm('<?php echo esc_js( __( "Bist Du sicher, die ausgewählten Termine abzusagen?", "appointments" ) ) ?>')) {
						return false;
					}
				}
			});
		});
	</script>
<?php endif; ?>