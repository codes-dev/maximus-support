<?php
/**
 * Define the Plugin settings functionality
 *
 * Loads and defines the settings for this plugin
 *
 * @link       www.codesport.com/
 * @since      1.0.0
 *
 * @package    maximus_support
 * @subpackage maximus_support/includes
 */

/**
 * Define the plugin settings functionality.
 *
 *
 * @since      1.0.0
 * @package    maximus_support
 * @subpackage maximus_support/includes
 * @author     Codes <codesenterprise@gmail.com>
 */
class Maximus_Settings {

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
	 * Load the plugin settings.
	 *
	 * @since    1.0.0
	 */

    public function addPluginAdminMenu() {
        //add_menu_page( $page_title, $menu_title, $capability, $menu_slug, $function, $icon_url, $position );
        add_menu_page(  
            $this->plugin_name, 
            'Transactions', 
            'administrator', 
            $this->plugin_name, 
            array( $this, 'displayPluginAdminDashboard' ), 
            'dashicons-chart-area', 
            26 
        );

        $page_hook = add_submenu_page( 
            $this->plugin_name, 
            'Sending', 
            'Sending', 
            'administrator', 
            $this->plugin_name.'-sending',
            array( $this, 'load_sending_list_table' )
        );
        
        /*
         * The $page_hook_suffix can be combined with the load-($page_hook) action hook
         * https://codex.wordpress.org/Plugin_API/Action_Reference/load-(page) 
         * 
         * The callback below will be called when the respective page is loaded	 	 
         */				
        add_action( 'load-'.$page_hook, array( $this, 'load_sending_list_table_screen_options' ) );
        
        $page_hook = add_submenu_page( 
            $this->plugin_name, 
            'Receiving', 
            'Receiving', 
            'administrator', 
            $this->plugin_name.'-receving',
            array( $this, 'load_receiving_list_table' )
        );
        
        /*
         * The $page_hook_suffix can be combined with the load-($page_hook) action hook
         * https://codex.wordpress.org/Plugin_API/Action_Reference/load-(page) 
         * 
         * The callback below will be called when the respective page is loaded	 	 
         */				
        add_action( 'load-'.$page_hook, array( $this, 'load_receiving_list_table_screen_options' ) );
        
    }

    public function displayPluginAdminDashboard() {
        require_once plugin_dir_path(  __FILE__ ) . 'partials/maximus_support-admin-display.php';
    }

    public function displayPluginAdminSettings() {
        // set this var to be used in the settings-display view
        $active_tab = isset( $_GET[ 'tab' ] ) ? $_GET[ 'tab' ] : 'general';
        if(isset($_GET['error_message'])){
            add_action('admin_notices', array($this,'pluginNameSettingsMessages'));
            do_action( 'admin_notices', $_GET['error_message'] );
        }
        require_once plugin_dir_path(  __FILE__ ) . 'partials/maximus_support-admin-settings-display.php';
    }

    /**
    * Screen options for the List Table
    *
    * Callback for the load-($page_hook_suffix)
    * Called when the plugin page is loaded
    * 
    * @since    1.0.0
    */
    public function load_sending_list_table_screen_options() {
        $arguments = array(
            'label'		=>	__( 'Transactions Per Page', 'maximus_support' ),
            'default'	=>	5,
            'option'	=>	'transactions_per_page'
        );
        add_screen_option( 'per_page', $arguments );
        /*
        * Instantiate the User List Table. Creating an instance here will allow the core WP_List_Table class to automatically
        * load the table columns in the screen options panel		 
        */	 
        require_once plugin_dir_path( __FILE__ ) . 'class-maximus_support-sending-log.php';
        $this->user_list_table = new Sending_List_Table('maximus_support');
    }

    /**
    * Screen options for the List Table
    *
    * Callback for the load-($page_hook_suffix)
    * Called when the plugin page is loaded
    * 
    * @since    1.0.0
    */
    public function load_receiving_list_table_screen_options() {
        $arguments = array(
            'label'		=>	__( 'Transactions Per Page', 'maximus_support' ),
            'default'	=>	5,
            'option'	=>	'transactions_per_page'
        );
        add_screen_option( 'per_page', $arguments );
        /*
        * Instantiate the User List Table. Creating an instance here will allow the core WP_List_Table class to automatically
        * load the table columns in the screen options panel		 
        */	 
        require_once plugin_dir_path( __FILE__ ) . 'class-maximus_support-receiving-log.php';
        $this->user_list_table = new Receiving_List_Table('maximus_support');
    }

    /*
    * Display the User List Table
    * Callback for the add_users_page() in the add_plugin_admin_menu() method of this class.
    */
    public function load_sending_list_table(){
        // query, filter, and sort the data
        $this->user_list_table->prepare_items();

        // render the List Table
        include_once( plugin_dir_path( __FILE__ ) .'partials/maximus_support-admin-sending-display.php' );
    }

    /*
    * Display the User List Table
    * Callback for the add_users_page() in the add_plugin_admin_menu() method of this class.
    */
    public function load_receiving_list_table(){
        // query, filter, and sort the data
        $this->user_list_table->prepare_items();

        // render the List Table
        include_once( plugin_dir_path( __FILE__ ) .'partials/maximus_support-admin-receiving-display.php' );
    }

    /**
    * Screen options for the List Table
    *
    * Callback for the load-($page_hook_suffix)
    * Called when the plugin page is loaded
    * 
    * @since    1.0.0
    */
    public function load_play_list_table_screen_options() {
        $arguments = array(
            'label'		=>	__( 'Plays Per Page', 'maximus_support' ),
            'default'	=>	5,
            'option'	=>	'plays_per_page'
        );
        add_screen_option( 'per_page', $arguments );
        /*
        * Instantiate the User List Table. Creating an instance here will allow the core WP_List_Table class to automatically
        * load the table columns in the screen options panel		 
        */	 
        require_once plugin_dir_path(  __FILE__  ) . 'class-maximus_support-plays-log.php';
        $this->user_list_table = new Play_List_Table('maximus_support');
    }
    /*
    * Display the User List Table
    * Callback for the add_users_page() in the add_plugin_admin_menu() method of this class.
    */
    public function load_play_list_table(){
        // query, filter, and sort the data
        $this->user_list_table->prepare_items();

        // render the List Table
        include_once( plugin_dir_path( __FILE__ ) .'partials/maximus_support-admin-plays-display.php' );
    }
    
    public function pluginNameSettingsMessages($error_message){
        switch ($error_message) {
            case '1':
                $message = __( 'There was an error adding this setting. Please try again.  If this persists, shoot us an email.', 'my-text-domain' );                 
                $err_code = esc_attr( 'maximus_support_enquiry_email' );                 
                $setting_field = 'maximus_support_enquiry_email';                 
                break;
        }
        $type = 'error';
        add_settings_error(
               $setting_field,
               $err_code,
               $message,
               $type
           );
    }

    public function registerAndBuildFields() {     
        $this->registerEmailSection();
        $this->buildEmailFields();
    }

    public function registerEmailSection()
    {
        # code...
        /**
       * First, we add_settings_section. This is necessary since all future settings must belong to one.
       */
        add_settings_section(
            // ID used to identify this section and with which to register options
            'maximus_support_email_section', 
            // Title to be displayed on the administration page
            '',  
            // Callback used to render the description of the section
            array( $this, 'plugin_name_display_general_account' ),    
            // Page on which to add this section of options
            'plugin_name_general_settings'                   
        );
    }

    public function buildEmailFields()
    {
        # code...
        /**
       * Second, add_settings_field
       * Third, register_setting
       */
        //unset($args);

        $fields = array(
            array (
                'label' =>  'Email Host',
                'type'      => 'input',
                'subtype'   => 'text',
                'id'    => 'maximus_support_email_host',
                'name'      => 'maximus_support_email_host',
                'required' => 'true',
                'get_options_list' => '',
                'value_type'=>'normal',
                'wp_data' => 'option'
            ),
            array (
                'label' =>  'Email Host Port',
                'type'      => 'input',
                'subtype'   => 'number',
                'id'    => 'maximus_support_email_port',
                'name'      => 'maximus_support_email_port',
                'required' => 'true',
                'get_options_list' => '',
                'value_type'=>'normal',
                'wp_data' => 'option'
            ),
            array (
                'label' =>  'Email Address',
                'type'      => 'input',
                'subtype'   => 'email',
                'id'    => 'maximus_support_email',
                'name'      => 'maximus_support_email',
                'required' => 'true',
                'get_options_list' => '',
                'value_type'=>'normal',
                'wp_data' => 'option'
            ),
            array (
                'label' =>  'Email Password',
                'type'      => 'input',
                'subtype'   => 'password',
                'id'    => 'maximus_support_email_password',
                'name'      => 'maximus_support_email_password',
                'required' => 'true',
                'get_options_list' => '',
                'value_type'=>'normal',
                'wp_data' => 'option'
            )
        );


        foreach ($fields as $key => $value) {
            # code...
            $args = $value;
            add_settings_field(
                $value['id'],
                $value['label'],
                array( $this, 'plugin_name_render_settings_field' ),
                'plugin_name_general_settings',
                'maximus_support_email_section',
                $args
            );


            register_setting(
                'plugin_name_general_settings',
                $value['id']
            );
        }

        /*unset($args);
        $args = ;
        add_settings_field(
            'maximus_support_email_password',
            'Enquiry Email Password',
            array( $this, 'plugin_name_render_settings_field' ),
            'plugin_name_general_settings',
            'maximus_support_email_section',
            $args
        );


        register_setting(
            'plugin_name_general_settings',
            'maximus_support_email_password'
        );*/
        
    }

    public function plugin_name_display_general_account() {
        echo '<p>These settings apply to the support email functionality.</p>';
    } 

    public function plugin_name_render_settings_field($args) {
        /* EXAMPLE INPUT
                  'type'      => 'input',
                  'subtype'   => '',
                  'id'    => $this->plugin_name.'_example_setting',
                  'name'      => $this->plugin_name.'_example_setting',
                  'required' => 'required="required"',
                  'get_option_list' => "",
                    'value_type' = serialized OR normal,
        'wp_data'=>(option or post_meta),
        'post_id' =>
        */     
        if($args['wp_data'] == 'option'){
            $wp_data_value = get_option($args['name']);
        } elseif($args['wp_data'] == 'post_meta'){
            $wp_data_value = get_post_meta($args['post_id'], $args['name'], true );
        }

        switch ($args['type']) {

            case 'input':
                $value = ($args['value_type'] == 'serialized') ? serialize($wp_data_value) : $wp_data_value;
                if($args['subtype'] != 'checkbox'){
                    $prependStart = (isset($args['prepend_value'])) ? '<div class="input-prepend"> <span class="add-on">'.$args['prepend_value'].'</span>' : '';
                    $prependEnd = (isset($args['prepend_value'])) ? '</div>' : '';
                    $step = (isset($args['step'])) ? 'step="'.$args['step'].'"' : '';
                    $min = (isset($args['min'])) ? 'min="'.$args['min'].'"' : '';
                    $max = (isset($args['max'])) ? 'max="'.$args['max'].'"' : '';
                    if(isset($args['disabled'])){
                        // hide the actual input bc if it was just a disabled input the informaiton saved in the database would be wrong - bc it would pass empty values and wipe the actual information
                        echo $prependStart.'<input type="'.$args['subtype'].'" id="'.$args['id'].'_disabled" '.$step.' '.$max.' '.$min.' name="'.$args['name'].'_disabled" size="40" disabled value="' . esc_attr($value) . '" /><input type="hidden" id="'.$args['id'].'" '.$step.' '.$max.' '.$min.' name="'.$args['name'].'" size="40" value="' . esc_attr($value) . '" />'.$prependEnd;
                    } else {
                        echo $prependStart.'<input type="'.$args['subtype'].'" id="'.$args['id'].'" "'.$args['required'].'" '.$step.' '.$max.' '.$min.' name="'.$args['name'].'" size="40" value="' . esc_attr($value) . '" />'.$prependEnd;
                    }
                    /*<input required="required" '.$disabled.' type="number" step="any" id="'.$this->plugin_name.'_cost2" name="'.$this->plugin_name.'_cost2" value="' . esc_attr( $cost ) . '" size="25" /><input type="hidden" id="'.$this->plugin_name.'_cost" step="any" name="'.$this->plugin_name.'_cost" value="' . esc_attr( $cost ) . '" />*/

                } else {
                    $checked = ($value) ? 'checked' : '';
                    echo '<input type="'.$args['subtype'].'" id="'.$args['id'].'" "'.$args['required'].'" name="'.$args['name'].'" size="40" value="1" '.$checked.' />';
                }
                break;
            default:
                # code...
                break;
        }
    }
}