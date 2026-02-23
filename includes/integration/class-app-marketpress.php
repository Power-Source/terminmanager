<?php

class Appointments_Integrations_MarketPress {

	public function __construct() {
		if ( ! $this->is_mp_active() ) {
			return;
		}
		// Add MP Default options
		add_filter( 'appointments_default_options', array( $this, 'default_options' ) );
		// Add settings section
		add_action( 'appointments_settings_tab-main-section-payments', array( $this, 'show_settings' ) );
		add_filter( 'app-options-before_save', array( $this, 'save_settings' ) );

		// AJAX-Handler IMMER registrieren!
		add_action( 'wp_ajax_make_an_appointment_mp_page', array( $this, 'create_page' ) );

		if ( ! $this->is_integration_active() ) {
			return;
		}
		if ( defined( 'MP_VERSION' ) && version_compare( MP_VERSION, '3.0', '>=' ) ) {
			require_once( 'marketpress/class_app_mp_bridge.php' );
			App_MP_Bridge::serve();
		} else {
			require_once( 'marketpress/class_app_mp_bridge_legacy.php' );
			App_MP_Bridge_Legacy::serve();
		}
	}

	private function is_mp_active() {
		// class_app_mp_bridge (for MP>3.0)
		// class_app_mp_bridge_legacy (for MP < 3.0)
		global $mp;
		return class_exists( 'MarketPress' ) && is_object( $mp );
	}

	private function is_integration_active() {
		$options = appointments_get_options();
		return $this->is_mp_active() && $options['use_mp'] && $options['payment_required'] === 'yes';
	}

	/**
	 * Add default MP options to Appointments options
	 *
	 * @param array $options
	 *
	 * @return array
	 */
	public function default_options( $options ) {
		$mp_options = array(
			'use_mp' => false,
			'make_an_appointment_product' => false,
			'app_page_type_mp' => 'one_month',
		);
		return array_merge( $options, $mp_options );
	}

	public function show_settings() {
		$options = appointments_get_options();
		$product_page_query = new WP_Query( array(
			'post_type'      => 'product',
			'post_title'     => 'Terminbuchung',
			'posts_per_page' => 1,
		) );
		$product_page = $product_page_query->posts ? $product_page_query->posts[0] : null;
		wp_reset_postdata(); // Zurücksetzen der globalen $post Variable
		?>
		<div class="payment_row">
			<h3 class="mp-integration"><?php _e( 'MarketPress aktivieren', 'appointments' ); ?></h3>
			<table class="form-table mp-integration">
				<tr>
					<th scope="row"><label for="use_mp"><?php _e( 'Integration aktivieren', 'appointments' ); ?></label></th>
					<td>
						<?php _appointments_html_chceckbox( $options, 'use_mp', 'payment_use_mp' ) ?>
						<p class="description"><?php _e( 'Termine können als Produkte festgelegt werden. Jeder einer Produktseite hinzugefügte Termin-Shortcode macht diese Seite zu einer "Termin-Produktseite". Einzelheiten findest Du in den FAQ.', 'appointments' ) ?></p>
					</td>
				</tr>
				<?php do_action( 'app-settings-payment_settings-marketpress' ); ?>
				<tr class="payment_use_mp">
					<th scope="row"><label for="make_an_appointment_product"><?php _e( 'Produktseite erstellen', 'appointments' ); ?></label></th>
					<td class="appointment-create-page">
						<?php _e( 'mit', 'appointments' ) ?>
						<label for="app_page_type_mp" class="screen-reader-text"><?php _e( 'Wähle Produktseitentyp', 'appointments' ); ?></label>
						<select>
							<option value="one_month"><?php _e( 'Zeitplan des aktuellen Monats', 'appointments' ) ?></option>
							<option value="two_months" <?php selected( $options['app_page_type_mp'], 'two_months' ); ?>><?php _e( 'aktuelle und nächste Monatspläne', 'appointments' ) ?></option>
							<option value="one_week" <?php selected( $options['app_page_type_mp'], 'one_week' ); ?>><?php _e( 'Zeitplan der aktuellen Woche', 'appointments' ) ?></option>
							<option value="two_weeks" <?php selected( $options['app_page_type_mp'], 'two_weeks' ); ?>><?php _e( 'aktuelle und nächste Woche', 'appointments' ) ?></option>
						</select>
						<a href="#" data-action="make_an_appointment_mp_page" class="button" data-nonce="<?php echo wp_create_nonce( 'appointment-create-page' ); ?>"><?php esc_html_e( 'SEITE ERSTELLEN!', 'appointments' ); ?></a>
						<p class="description"><?php _e( 'Wie oben unter "Terminseite erstellen", jedoch werden diesmal Termin-Shortcodes in eine neue Produktseite eingefügt und der Seitentitel lautet "Terminbuchung". Dies ist auch der Produktname.', 'appointments' ) ?></p>
						<?php if ( $product_page ) :  ?>
							<p class="description"><?php _e( '<b>Hinweis:</b> Du hast bereits eine solche Seite. Wenn Du dieses Auswahl aktivierst, wird eine weitere Seite mit demselben Titel erstellt. ', 'appointments' ) ?>
								<a href="<?php echo admin_url( 'post.php?post=' . $product_page->ID . '&action=edit' ) ?>" target="_blank"><?php _e( 'SEITE BEARBEITEN', 'appointments' ) ?></a> |
								<a href="<?php echo get_permalink( $product_page->ID ) ?>" target="_blank"><?php _e( 'SEITE ANSEHEN', 'appointments' ) ?></a>
							</p>
						<?php endif; ?>
					</td>
				</tr>
			</table>
		</div>
		<script>
			(function ($) {
				var payment_required = $('#payment_required');
				payment_required.on('change', function() {
					var value = $(this).val();
					if ( 'no' === value ) {
						$('.mp-integration').hide();
					}
					else {
						$('.mp-integration').show();
					}
				});
				payment_required.trigger( 'change' );
			})(jQuery);
		</script>
		<?php
	}

	public function save_settings( $options ) {
		$options['use_mp']           = isset( $_POST['use_mp'] );
		$options['app_page_type_mp'] = @$_POST['app_page_type_mp'];
		if ( ! empty( $_POST['cart_name_format'] ) ) {
			$options['cart_name_format'] = wp_strip_all_tags( stripslashes_deep( $_POST['cart_name_format'] ) );
		}
		return $options;
	}

	/**
	 * Allow to create Appointment product in MarketPress.
	 *
	 * @since 2.3.0
	 */
	public function create_page() {
		error_log('AJAX-Request angekommen: ' . print_r($_POST, true)); // <--- Debug-Log
		$data = array(
			'message' => __( 'Etwas ist schief gelaufen!', 'appointments' ),
		);
		if (
			! isset( $_POST['_wpnonce'] )
			|| ! isset( $_POST['app_page_type'] )
			|| ! wp_verify_nonce( $_POST['_wpnonce'], 'appointment-create-page' )
		) {
			wp_send_json_error( $data );
		}
		$tpl = ! empty( $_POST['app_page_type'] ) ? $_POST['app_page_type'] : false;
		$page_id = wp_insert_post(
			array(
				'post_title'   => _x( 'Terminbuchung', 'Standard-Seitenname für die MarketPress-Integration.', 'appointments' ),
				'post_status'  => 'publish',
				'post_type'    => 'product',
				'post_content' => App_Template::get_default_page_template( $tpl ),
				'meta_input'   => array(
					'product_type' => 'digital',
					// Set a meta to define this is an app mp product
					'mp_app_product' => 1,
				),
			)
		);
		if ( $page_id ) {
			add_post_meta( $page_id, 'file_url', get_permalink( $page_id ) );
			add_post_meta( $page_id, 'sku', 'appointment-'. $page_id );
			$data = array(
				'message' => sprintf(
					__( 'Seite wurde erfolgreich erstellt: %s', 'appointments' ),
					sprintf(
						'<a href="%s">%s</a>',
						esc_url( get_page_link( $page_id ) ),
						esc_html__( 'Seite anzeigen', 'appointments' )
					)
				),
			);
			wp_send_json_success( $data );
		}
		wp_send_json_error( $data );
	}
}

new Appointments_Integrations_MarketPress();
