<?php

/**
 * The admin-specific functionality of the plugin.
 *
 * @link       www.xuro.com
 * @since      1.0.0
 *
 * @package    Maximus_support
 * @subpackage Maximus_support/admin
 */

/**
 * The admin-specific functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the admin-specific stylesheet and JavaScript.
 *
 * @package    Maximus_support
 * @subpackage Maximus_support/admin
 * @author     Codes <codesenterprise@gmail.com>
 */
class Maximus_support_Admin {

	/**
	 * The ID of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $plugin_name    The ID of this plugin.
	 */
	private $plugin_name;

	/**
	 * The version of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $version    The current version of this plugin.
	 */
	private $version;

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    1.0.0
	 * @param      string    $plugin_name       The name of this plugin.
	 * @param      string    $version    The version of this plugin.
	 */
	public function __construct( $plugin_name, $version ) {

		$this->plugin_name = $plugin_name;
		$this->version = $version;

	}

	/**
	 * Register the stylesheets for the admin area.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_styles() {

		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in Maximus_support_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The Maximus_support_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */
		global $pagenow;
		if ($pagenow !== 'post.php') {
			# code...
			return;
		}
		wp_enqueue_style( $this->plugin_name, plugin_dir_url( __DIR__ ) . 'dist/assets/css/admin.css', array(), $this->version, 'all' );

	}

	/**
	 * Register the JavaScript for the admin area.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_scripts() {

		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in Maximus_support_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The Maximus_support_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */
		global $pagenow;
		if ($pagenow !== 'post.php') {
			# code...
			return;
		}
		wp_enqueue_script( $this->plugin_name, plugin_dir_url( __DIR__ ) . 'dist/assets/js/admin.js', array( 'jquery' ), $this->version, false );

	}

}
