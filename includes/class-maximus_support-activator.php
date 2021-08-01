<?php

/**
 * Fired during plugin activation
 *
 * @link       www.xuro.com
 * @since      1.0.0
 *
 * @package    Maximus_support
 * @subpackage Maximus_support/includes
 */

/**
 * Fired during plugin activation.
 *
 * This class defines all code necessary to run during the plugin's activation.
 *
 * @since      1.0.0
 * @package    Maximus_support
 * @subpackage Maximus_support/includes
 * @author     Codes <codesenterprise@gmail.com>
 */
class Maximus_support_Activator {

	/**
	 * Short Description. (use period)
	 *
	 * Long Description.
	 *
	 * @since    1.0.0
	 */
	public static function activate($network_wide) {
		global $wpdb;

		if ( is_multisite() && $network_wide ) {
			// Get all blogs in the network and activate plugin on each one
			$blog_ids = $wpdb->get_col( "SELECT blog_id FROM $wpdb->blogs" );
			foreach ( $blog_ids as $blog_id ) {
				switch_to_blog( $blog_id );
				create_table();
				restore_current_blog();
			}
		} else {
			create_table();
		}		
	}
}

function create_table() {
    global $wpdb;
    $table_name = $wpdb->prefix . 'transactions';

    if( $wpdb->get_var( "show tables like '{$table_name}'" ) != $table_name ) {

        $sql = "CREATE TABLE " . $table_name . " (
            id mediumint(9) NOT NULL AUTO_INCREMENT,
			reason VARCHAR(100) NOT NULL,
            from_country VARCHAR(100) NOT NULL,
			to_country VARCHAR(100) NOT NULL,
			transaction_date datetime DEFAULT '0000-00-00 00:00:00' NOT NULL,
			conversion_amount VARCHAR(100) NOT NULL,
			amount VARCHAR(100) NOT NULL,
			account_id INT NOT NULL,
			account_email VARCHAR(100) NOT NULL,
			account_owner VARCHAR(100) NOT NULL,
			account_owner_id INT NOT NULL,
			account_country VARCHAR(100) NOT NULL,
            PRIMARY KEY  (id)
        );";

        require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
        dbDelta( $sql );

        $plugin_name_db_version = '1.0';
	
		add_option( 'maximus_wpg_db_version', $plugin_name_db_version );
    }
}
