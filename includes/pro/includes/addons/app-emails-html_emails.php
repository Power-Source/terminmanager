<?php
/*
Plugin Name: HTML-E-Mails zulassen
Description: Standardmäßig sendet das Plugin Nur-Text-E-Mails. Durch Aktivieren dieser Erweiterung können Deine E-Mails als HTML gesendet werden.
Plugin URI: https://cp-psource.github.io/terminmanager-pro/
Version: 1.1
AddonType: Emails
Author: PSOURCE
*/

class App_Emails_HtmlEmails {

	private function __construct () {}

	public static function serve () {
		$me = new App_Emails_HtmlEmails;
		$me->_add_hooks();
	}

	private function _add_hooks () {
		add_filter('app-emails-content_type', array($this, 'switch_content_type')); 
	}

	public function switch_content_type () {
		return 'text/html';
	}
}
App_Emails_HtmlEmails::serve();