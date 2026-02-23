<?php
/**
 * @author: PSOURCE, Ignacio Cruz (igmoweb)
 * @version:
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! class_exists( 'App_Shortcode_Login' ) ) {
	/**
	 * Front-end login.
	 */
	class App_Shortcode_Login extends App_Shortcode {
		public function __construct () {
			$this->name = _x( 'Login', 'Login Shortcode', 'appointments' );
		}

		public function get_defaults() {
			return array(
				'login_text' => array(
					'type' => 'text',
					'name' => __( 'Anmeldetext', 'appointments' ),
					'value' => __('Bitte klicke hier, um Dich anzumelden:', 'appointments'),
					'help' => __('Text über den Anmeldeschaltflächen, gefolgt von einem Anmeldelink. Standard: "Bitte klicke hier, um Dich anzumelden:"', 'appointments'),
				),
				'redirect_text' => array(
					'type' => 'text',
					'name' => __( 'Umleitungstext', 'appointments' ),
					'value' => __('Login erforderlich, um einen Termin zu vereinbaren. Du wirst jetzt zur Anmeldeseite weitergeleitet.', 'appointments'),
					'help' => __('Javascript-Text, wenn die Front-End-Anmeldung nicht festgelegt ist und der Benutzer zur Anmeldeseite weitergeleitet wird', 'appointments'),
				),
			);
		}

		public function get_usage_info () {
			return __('Fügt Front-End-Anmeldeschaltflächen für Facebook, Twitter und ClassicPress ein.', 'appointments');
		}

		public function process_shortcode ($args=array(), $content='') {
			extract(wp_parse_args($args, $this->_defaults_to_args()));

			global $appointments;

			$ret  = '';
			$ret .= '<div class="appointments-login">';
			if ( !is_user_logged_in() && $appointments->options["login_required"] == 'yes' ){
				$ret .= $login_text. " ";
				$ret .= '<a href="javascript:void(0)" class="appointments-login_show_login" >'. __('Login', 'appointments') . '</a>';
			}
			$ret .= '<div class="appointments-login_inner">';
			$ret .= '</div>';
			$ret .= '</div>';

			$script  = '';
			$script .= "$('.appointments-login_show_login').on('click', function(){";
			if ( !@$appointments->options["accept_api_logins"] ) {
				$script .= 'var app_redirect=confirm("'.esc_js($redirect_text).'");';
				$script .= ' if(app_redirect){';
				$script .= 'window.location.href= "'.wp_login_url( ).'";';
				$script .= '}';
			}
			else {
				$script .= '$(".appointments-login_link-cancel").focus();';
			}
			$script .= "});";

			$appointments->add2footer( $script );

			return $ret;
		}
	}
}