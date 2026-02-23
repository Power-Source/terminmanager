<?php
/**
 * @author: PSOURCE, Ignacio Cruz (igmoweb)
 * @version:
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! class_exists( 'App_Shortcode_Pagination' ) ) {
	/**
	 * Pagination shortcode.
	 */
	class App_Shortcode_Pagination extends App_Shortcode {

		public function __construct() {
			$this->name = __( 'Termine Paginierung', 'appointments' );
		}

		public function get_defaults() {
			return array(
				'step'    => array(
					'type' => 'text',
					'name' => __( 'Wochen/Monate Nr.', 'appointments' ),
					'value'   => 1,
					'help'    => __( 'Die Anzahl der Wochen oder Monate, die die ausgewählte Zeit mit jedem nächsten oder vorherigen Linkklick erhöht oder verringert. Du kannst 4 eingeben, wenn Du 4 Zeitplantabellen auf der Seite hast.', 'appointments' ),
				),
				'month'   => array(
					'type' => 'checkbox',
					'name' => __( 'Monat', 'appointments' ),
					'value'   => 0,
					'help'    => __( 'Wenn diese Option aktiviert ist, bedeutet der Schrittparameter Monat, andernfalls Woche. Kurz gesagt, überprüfe den monatlichen Zeitplan.', 'appointments' ),
				),
				'date'    => array(
					'type' => 'datepicker',
					'name' => __( 'Datum (YYYY-MM-DD Format)', 'appointments' ),
					'value'   => '',
					'help'    => __( 'Dies ist nur erforderlich, wenn sich dieser Shortcode über den Zeitplan-Shortcodes befindet. Andernfalls folgen die Datumseinstellungen der Zeitplan-Shortcodes. Standard: Aktuelle Woche oder Monat', 'appointments' ),
				),
				'anchors' => array(
					'type' => 'checkbox',
					'name' => __( 'Anker', 'appointments' ),
					'value'   => 1,
					'help'    => __( 'Wenn Du dieses Argument deaktivierst, wird verhindert, dass Paginierungslinks Zeitplan-Hash-Anker hinzufügen.', 'appointments' ),
				),
			);
		}

		public function get_usage_info() {
			return __( 'Fügt Paginierungscodes (Links für vorherige, nächste Woche oder Monat) und den Legendenbereich ein.', 'appointments' );
		}

		public function process_shortcode( $args = array(), $content = '' ) {
			global $appointments;

			$options      = appointments_get_options();
			$current_time = current_time( 'timestamp' );
			$args         = wp_parse_args( $args, $this->_defaults_to_args() );

			// Force a date
			if ( $args['date'] && ! isset( $_GET["wcalendar"] ) ) {
				$time              = strtotime( $args['date'], $current_time );
				$_GET["wcalendar"] = $time;
			} else {
				if ( isset( $_GET["wcalendar"] ) && (int) $_GET['wcalendar'] ) {
					$time = (int) $_GET["wcalendar"];
				} else {
					$time = $current_time;
				}
			}

			if ( ! $args['month'] ) {
				$prev                = $time - ( $args['step'] * 7 * 86400 );
				$next                = $time + ( $args['step'] * 7 * 86400 );
				$prev_min            = $current_time - $args['step'] * 7 * 86400;
				$next_max            = $current_time + ( $appointments->get_app_limit() + 7 * $args['step'] ) * 86400;
				$month_week_next     = __( 'Nächste Woche', 'appointments' );
				$month_week_previous = __( 'Vorherige Woche', 'appointments' );
			} else {
				$prev                = $appointments->first_of_month( $time, - 1 * $args['step'] );
				$next                = $appointments->first_of_month( $time, $args['step'] );
				$prev_min            = $appointments->first_of_month( $current_time, - 1 * $args['step'] );
				$next_max            = $appointments->first_of_month( $current_time, $args['step'] ) + $appointments->get_app_limit() * 86400;
				$month_week_next     = __( 'Nächster Monat', 'appointments' );
				$month_week_previous = __( 'Vorheriger Monat', 'appointments' );
			}

			$hash = ! empty( $args['anchors'] ) && (int) $args['anchors']
				? '#app_schedule'
				: '';

			ob_start();

			// Legends
			if ( 'yes' == $options['show_legend'] ) {
				$n = 0;
				?>
				<div class="appointments-legend">
					<table class="appointments-legend-table">
						<tr>
							<?php foreach ( $appointments->get_classes() as $class => $name ): ?>
								<td class="class-name"><?php echo esc_html( $name ); ?></td>
								<td class="<?php echo esc_attr( $class ); ?>">&nbsp;</td>
								<?php $n ++; ?>
								<?php if ( 0 === $n % 3 ): ?>
									</tr>
									<tr>
								<?php endif; ?>
							<?php endforeach; ?>
						</tr>
					</table>
				</div>

				<?php
				// Do not let clicking box inside legend area
				$appointments->add2footer( '$("table.appointments-legend-table td.free").on("click", false);' );
			}

			// Pagination
			?>
			<div class="appointments-pagination">
				<?php if ( $prev > $prev_min ): ?>
					<div class="<?php echo apply_filters( 'appointments_pagination_shortcode_previous_class', 'previous' ); ?>">
						<a href="<?php echo esc_url( add_query_arg( "wcalendar", $prev ) . $hash ); ?>">&laquo; <?php echo $month_week_previous; ?></a>
					</div>
				<?php endif; ?>
				<?php if ( $next < $next_max ): ?>
					<div class="<?php echo apply_filters( 'appointments_pagination_shortcode_next_class', 'next' ); ?>">
						<a href="<?php echo esc_url( add_query_arg( "wcalendar", $next ) . $hash ); ?>"><?php echo $month_week_next; ?> &raquo;</a>
					</div>
				<?php endif; ?>
				<div style="clear:both"></div>
			</div>
			<?php



			return ob_get_clean();
		}
	}
}
