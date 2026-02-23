<h3><?php esc_html_e( 'PS Terminmanager: Meine Termine als Anbieter', 'appointments' ); ?></h3>
<table class="form-table">
	<tr>
		<th><?php _e( "Termine", 'appointments' ); ?></th>
		<td>
			<?php echo do_shortcode("[app_my_appointments status='pending,confirmed,paid,reserved' title='' _allow_confirm=1 provider_id=".$profileuser->ID."  provider=1 ".$gcal."]") ?>
		</td>
	</tr>
</table>

<?php if ( $allow_worker_confirm ): ?>
	<script type='text/javascript'>
		jQuery(document).ready(function($){
			$('#your-profile').submit(function() {
				if ( $('.app-my-appointments-confirm').is(':checked') ) {
					if ( !confirm('<?php echo esc_js( __("Bist Du sicher, die ausgewählten Termine zu bestätigen?","appointments") ) ?>') )
					{return false;}
				}
			});
		});
	</script>
<?php endif; ?>