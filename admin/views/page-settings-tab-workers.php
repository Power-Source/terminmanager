<?php
global $appointments, $wpdb;
?>

<?php if ( isset( $_GET['added'] ) ): ?>
	<div class="updated">
		<p><?php _e( 'Dienstleister hinzugefügt', 'appointments' ); ?></p>
	</div>
<?php endif; ?>

<?php foreach ( $sections as $section => $name ): ?>
	<?php $section_file = _appointments_get_settings_section_view_file_path( $tab, $section ); ?>
	<?php if ( $section_file ): ?>
		<div class="app-settings-section" id="app-settings-section-<?php echo $section; ?>">
			<h3><?php echo esc_html( $name ); ?></h3>
			<?php include_once( $section_file ); ?>
			<?php do_action( "appointments_settings_tab-$tab-section-$section" ); ?>
		</div>
	<?php endif; ?>
<?php endforeach; ?>


<?php do_action( "appointments_settings-$tab" );  ?>

<script type="text/javascript">
    jQuery( document ).ready( function( $ ) {
        // Initialize Choices.js for multiselect (replaces jQuery UI Multiselect)
        if (typeof Choices !== 'undefined') {
            document.querySelectorAll('.add_worker_multiple').forEach(function(selectElement) {
                if (!selectElement._choices) {
                    var choices = new Choices(selectElement, {
                        removeItemButton: true,
                        placeholderValue: '<?php echo esc_js( __( 'Gewählte Services', 'appointments' ) ) ?>',
                        searchPlaceholderValue: '<?php echo esc_js( __( 'Suche...', 'appointments' ) ) ?>',
                        noResultsText: '<?php echo esc_js( __( 'Keine Ergebnisse gefunden', 'appointments' ) ) ?>',
                        itemSelectText: '<?php echo esc_js( __( 'Klicke zum Auswählen', 'appointments' ) ) ?>',
                        maxItemText: function(maxItemCount) {
                            return '<?php echo esc_js( __( 'Nur', 'appointments' ) ) ?> ' + maxItemCount + ' <?php echo esc_js( __( 'Werte können hinzugefügt werden', 'appointments' ) ) ?>';
                        }
                    });
                    // Store reference for later access
                    selectElement._choices = choices;
                }
            });
        }
    } );
</script>

