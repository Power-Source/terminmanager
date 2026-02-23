<?php global $appointments; ?>
<div class="postbox">
	<div class="inside" id="app_log">
	<?php
		if ( is_writable( $appointments->uploads_dir ) ) {
			if ( file_exists( $appointments->log_file ) )
				echo nl2br( file_get_contents( $appointments->log_file ) );
			else
				echo __( 'Es sind noch keine Protokollsätze vorhanden.', 'appointments' );
		}
		else
			echo __( 'Das Upload-Verzeichnis ist nicht beschreibbar.', 'appointments' );
		?>
	</div>
</div>

<form action="" method="post" id="clear-log-form">
	<?php _appointments_settings_submit_block( $tab, __( 'Protokolldatei löschen', 'appointments' ), 'secondary' ); ?>
</form>

<script type="text/javascript">
jQuery(document).ready(function ($) {
	$('#clear-log-form').submit(function (e) {
		return confirm('<?php _e( "Bist Du sicher, die Protokolldatei zu löschen?", "appointments" ); ?>');
	});
});
</script>