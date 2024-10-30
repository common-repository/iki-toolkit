<?php


/**
 * Fired during plugin deactivation.
 *
 * This class defines all code necessary to run during the plugin's deactivation.
 */
class Iki_Toolkit_Deactivator {

	public static function deactivate() {
		delete_option( 'iki_toolkit_flush_rewrite_rules_flag' );
		flush_rewrite_rules();

	}

}