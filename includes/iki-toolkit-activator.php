<?php

/**
 * Fired during plugin activation.
 *
 * This class defines all code necessary to run during the plugin's activation.
 *
 * @since      1.0.0
 */
class Iki_Toolkit_Activator {

	public static function activate() {
		update_option( 'iki_toolkit_flush_rewrite_rules_flag', true );
	}


}