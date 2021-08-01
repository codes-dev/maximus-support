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
class Maximus_support_Metaboxes {

	/**
	 * Short Description. (use period)
	 *
	 * Long Description.
	 *
	 * @since    1.0.0
	 */
	public function maximus_add_metaboxes() {
        add_meta_box( 
            'maximus_post_metabox', 
            'Post Settings', 
            array($this, 'maximus_display_post_metabox'), 
            'post', 
            'normal', 
            'default'
        );

        add_meta_box( 
            'maximus_page_metabox', 
            'Page Settings', 
            array($this, 'maximus_display_page_metabox'), 
            'page', 
            'normal', 
            'default'
        );
	}

    public function maximus_display_post_metabox($post)
    {
        # code...
        ob_start();
        wp_nonce_field( basename(__FILE__),  'maximus_post_nonce');
        $hideSidebar = get_post_meta( $post->ID, '_maximus_hide_post_sidebar', true );
        $hideAuthor = get_post_meta( $post->ID, '_maximus_hide_post_author', true );
        ?>
            <div class="maximus-post-setting">
                <p class="inside">
                    <input type="checkbox" name="maximus_post_sidebar" value="true" <?php checked( $hideSidebar, 'true' ) ?>>
                    <label for="maximus_post_sidebar">
                        <?php
                            esc_html_e( 'Hide Sidebar in page.', 'maximus_support' );
                        ?>
                    </label><br>
                </p>
                <p class="inside">
                    <input type="checkbox" name="maximus_post_author" value="true" <?php checked( $hideAuthor, 'true' ) ?>>
                    <label for="maximus_post_author">
                        <?php
                            esc_html_e( 'Hide Author in page.', 'maximus_support' );
                        ?>
                    </label><br>
                </p>
            </div>
        <?php
        echo ob_get_clean();
    }

    public function maximus_display_page_metabox($post)
    {
        # code...
        ob_start();
        wp_nonce_field( basename(__FILE__),  'maximus_page_nonce');
        $showTitle = get_post_meta( $post->ID, '_maximus_show_page_title', true );
        ?>
            <div class="maximus-page-setting">
                <p class="inside">
                    <input type="checkbox" name="maximus_page_title" value="true" <?php checked( $showTitle, 'true' ) ?>>
                    <label for="maximus_page_title">
                        <?php
                            esc_html_e( 'Show Page Title', 'maximus_support' );
                        ?>
                    </label><br>
                </p>
            </div>
        <?php
        echo ob_get_clean();
    }


    public function maximus_save_post_metabox($post_id, $post)
    {
        # code...

        // Checks save status - overcome autosave, etc.

        $edit_cap = get_post_type_object($post->post_type)->cap->edit_post;
        if (!current_user_can( $edit_cap, $post_id )) {
            # code...
            return;
        }
        if (!isset($_POST['maximus_post_nonce']) || !wp_verify_nonce( $_POST['maximus_post_nonce'], basename(__FILE__)  )) {
            # code...
            return;
        }

        $hideSidebar = get_post_meta( $post->ID, '_maximus_hide_post_sidebar', true );

        if ( array_key_exists( 'maximus_post_sidebar', $_POST ) && !$hideSidebar) {
            add_post_meta( $post->ID, '_maximus_hide_post_sidebar', $_POST['maximus_post_sidebar'] );
        }else if (array_key_exists( 'maximus_post_sidebar', $_POST ) && $hideSidebar){
            update_post_meta(
                $post->ID,
                '_maximus_hide_post_sidebar',
                $_POST['maximus_post_sidebar']
            );
        }else {
            # code...
            delete_post_meta( $post->ID, '_maximus_hide_post_sidebar');
        }

        $hideAuthor = get_post_meta( $post->ID, '_maximus_hide_post_author', true );

        if ( array_key_exists( 'maximus_post_author', $_POST ) && !$hideAuthor) {
            add_post_meta( $post->ID, '_maximus_hide_post_author', $_POST['maximus_post_author'] );
        }else if (array_key_exists( 'maximus_post_author', $_POST ) && $hideAuthor){
            update_post_meta(
                $post->ID,
                '_maximus_hide_post_author',
                $_POST['maximus_post_author']
            );
        }else {
            # code...
            delete_post_meta( $post->ID, '_maximus_hide_post_author');
        }
    }

    public function maximus_save_page_metabox($post_id, $post)
    {
        # code...
        

        $edit_cap = get_post_type_object($post->post_type)->cap->edit_post;
        if (!current_user_can( $edit_cap, $post_id )) {
            # code...
            return;
        }

        if (!isset($_POST['maximus_page_nonce']) || !wp_verify_nonce( $_POST['maximus_page_nonce'], basename(__FILE__) )) {
            # code...
            return;
        }

        $showTitle = get_post_meta( $post->ID, '_maximus_show_page_title', true );

        if ( array_key_exists( 'maximus_page_title', $_POST ) && !$showTitle) {
            add_post_meta( $post->ID, '_maximus_show_page_title', $_POST['maximus_page_title'] );
        }else if (array_key_exists( 'maximus_page_title', $_POST ) && $showTitle){
            update_post_meta(
                $post->ID,
                '_maximus_show_page_title',
                $_POST['maximus_page_title']
            );
        }else {
            # code...
            delete_post_meta( $post->ID, '_maximus_show_page_title');
        }
    }
}
