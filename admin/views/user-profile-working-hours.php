<h3><?php esc_html_e( 'PS Terminmanager: Meine Arbeitszeiten', 'appointments' ); ?></h3>
<table class="form-table">
	<tr>
		<th><label><?php _e( "Arbeitszeit", 'appointments' ); ?></label></th>
		<td>
			<?php echo $appointments->working_hour_form('open') ?>
		</td>
	</tr>
	<tr>
		<th><label><?php _e("Pausenstunden", 'appointments'); ?></label></th>
		<td>
			<?php echo $appointments->working_hour_form('closed') ?>
		</td>
	</tr>
	<tr>
		<th><label for="open_datepick"><?php _e("Außergewöhnliche Arbeitstage", 'appointments'); ?></label></th>
		<td>
			<div class="app-datepick" data-rel="#open_datepick"></div>
			<input type="hidden" class="widefat" id="open_datepick" name="open[exceptional_days]" value="<?php echo $result['open']; ?>">
		</td>
	</tr>
	<tr>
		<th><label for="closed_datepick"><?php _e("Urlaube", 'appointments'); ?></label></th>
		<td>
			<div class="app-datepick" data-rel="#closed_datepick"></div>
			<input type="hidden" class="widefat" id="closed_datepick" name="closed[exceptional_days]" value="<?php echo $result['closed']; ?>">
		</td>
	</tr>
</table>
<?php wp_nonce_field( 'app_exceptions-' . $worker_id, 'app_exceptions_nonce' ); ?>
<input type="hidden" name="worker_id" value="<?php echo $worker_id; ?>">
<script type="text/javascript">
	jQuery(document).ready(function($){
		$('.app-datepick').each( function() {
			new AppDatepicker( $(this) );
		});
	});
</script>