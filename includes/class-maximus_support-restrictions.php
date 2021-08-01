<?php

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
class Maximus_support_Restrictions {
    public function hide_admin_toolbar()
    {
        # code...
        if ( !current_user_can( 'edit_posts' ) ) {
            # code...
            show_admin_bar( false );
        }
    }

    /**
     * Redirect back to homepage and not allow access to 
     * WP admin for Subscribers.
     */
    public function maximus_redirect_admin(){
        if ( ! defined('DOING_AJAX') && ! current_user_can('edit_posts') ) {
            wp_redirect( site_url() );
            exit;       
        }
    }
}
