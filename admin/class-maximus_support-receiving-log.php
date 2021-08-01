<?php
    if ( ! class_exists( 'WP_List_Table' ) ) {
        require_once( ABSPATH . 'wp-admin/includes/class-wp-list-table.php' );
    }

    /**
     * Class for displaying registered WordPress Users
     * in a WordPress-like Admin Table with row actions to 
     * perform user meta opeations
     */
    class Receiving_List_Table extends WP_List_Table {
        // just the barebone implementation.
        public function get_columns() {		
            $table_columns = array(
                'cb'		=> '<input type="checkbox" />', // to display the checkbox.
                'id'		=> __( 'transaction ID', 'maximus_support' ), // to display the checkbox.			 
                'transaction_date'	=> __( 'transaction Date', 'maximus_support' ),
                'from_country'	=> __( 'From', 'maximus_support' ),			
                'to_country' => __( 'To', 'maximus_support' ),
                'conversion_amount'		=> __( 'Conversion Amount', 'maximus_support' ),
                'amount'		=> __( 'Amount', 'maximus_support' ), // to display the checkbox.			 
                'account_id'	=> __( 'Account Id', 'maximus_support' ),		
                'account_email' => __( 'Account E-mail', 'maximus_support' ),
                'account_owner'		=> __( 'User', 'maximus_support' ),
                'account_owner_id'		=> __( 'User Id', 'maximus_support' ), // to display the checkbox.	
                'account_country'		=> __( 'User Country', 'maximus_support' ), // to display the checkbox.	
            );		
            return $table_columns;		   
        }	
        
        public function no_items() {
            _e( 'No Transactions avaliable.', $this->plugin_text_domain );
        }
        //Query, filter data, handle sorting, pagination, and any other data-manipulation required prior to rendering
        public function prepare_items() {
	    
            // code to handle bulk actions
            
            //used by WordPress to build and fetch the _column_headers property
            //$this->_column_headers = $this->get_column_info();		      
            //$table_data = $this->fetch_table_data();

            // check if a search was performed.
	        $user_search_key = isset( $_REQUEST['s'] ) ? wp_unslash( trim( $_REQUEST['s'] ) ) : '';
	
	        $this->_column_headers = $this->get_column_info();
	
            // check and process any actions such as bulk actions.
            $this->handle_table_actions();

            // fetch the table data
            $table_data = $this->fetch_table_data();
            // filter the data in case of a search
            if( $user_search_key ) {
                $table_data = $this->filter_table_data( $table_data, $user_search_key );
            }	
            
            // code to handle data operations like sorting and filtering
            
            // start by assigning your data to the items variable
            $this->items = $table_data;	
            
            // code to handle pagination
            $users_per_page = $this->get_items_per_page( 'transactions_per_page' );
            $table_page = $this->get_pagenum();		

            // provide the ordered data to the List Table
            // we need to manually slice the data based on the current pagination
            $this->items = array_slice( $table_data, ( ( $table_page - 1 ) * $users_per_page ), $users_per_page );

            // set the pagination arguments		
            $total_users = count( $table_data );
            $this->set_pagination_args( array (
                'total_items' => $total_users,
                'per_page'    => $users_per_page,
                'total_pages' => ceil( $total_users/$users_per_page )
            ) );
        }
  
        public function fetch_table_data() {
            global $wpdb;
            $table_name = $wpdb->prefix . "transactions"; 
            $wpdb_table = $table_name;		
            $orderby = ( isset( $_GET['orderby'] ) ) ? esc_sql( $_GET['orderby'] ) : 'transaction_date';
            $order = ( isset( $_GET['order'] ) ) ? esc_sql( $_GET['order'] ) : 'ASC';
    
            $user_query = "SELECT 
                            id, 
                            transaction_date, 
                            from_country, 
                            to_country, 
                            conversion_amount, 
                            amount, 
                            account_id, 
                            account_email, 
                            account_owner,
                            account_owner_id,
                            account_country
                            FROM 
                            $wpdb_table 
                            WHERE reason = 'receiving'
                            ORDER BY $orderby $order";
    
            // query output_type will be an associative array with ARRAY_A.
            $query_results = $wpdb->get_results( $user_query, ARRAY_A  );
            
            // return result array to prepare_items.
            return $query_results;		
        }	

        public function column_default( $item, $column_name ) {
            switch ( $column_name ) {			
                case 'transaction_date':
                default:
                return $item[$column_name];
            }
        }	

        /**
         * Get value for checkbox column.
        *
        * @param object $item  A row's data.
        * @return string Text to be placed inside the column <td>.
        */
        protected function column_cb( $item ) {
                return sprintf(		
                '<label class="screen-reader-text" for="transaction_' . $item['id'] . '">' . sprintf( __( 'Select %s' ), $item['transaction_date'] ) . '</label>'
                . "<input type='checkbox' name='transactions[]' id='transaction_{$item['id']}' value='{$item['id']}' />"					
                );
        }

        protected function get_sortable_columns() {
            /*
            * actual sorting still needs to be done by prepare_items.
            * specify which columns should have the sort icon.	
            */
            $sortable_columns = array (
                    'id' => array( 'id', true ),
                    'transaction_date'=>'transaction_date'
                );
            return $sortable_columns;
        }    

        // filter the table data based on the search key
        public function filter_table_data( $table_data, $search_key ) {
            $filtered_table_data = array_values( array_filter( $table_data, function( $row ) use( $search_key ) {
                foreach( $row as $row_val ) {
                    if( stripos( $row_val, $search_key ) !== false ) {
                        return true;
                    }				
                }			
            } ) );

            return $filtered_table_data;

        }

        // Returns an associative array containing the bulk action.
        public function get_bulk_actions() {
            /*
            * on hitting apply in bulk actions the url paramas are set as
            * ?action=bulk-download&paged=1&action2=-1
            * 
            * action and action2 are set based on the triggers above and below the table		 		    
            */
            $actions = array(
                //'bulk-delete' => 'Delete transactions'
            );
            return $actions;
        }
        
        public function handle_table_actions() {		
            /*
             * Note: Table bulk_actions can be identified by checking $_REQUEST['action'] and $_REQUEST['action2']
             * action - is set if checkbox from top-most select-all is set, otherwise returns -1
             * action2 - is set if checkbox the bottom-most select-all checkbox is set, otherwise returns -1
             */    
            /*if ( ( isset( $_REQUEST['action'] ) && $_REQUEST['action'] === 'bulk-delete' ) || ( isset( $_REQUEST['action2'] ) && $_REQUEST['action2'] === 'bulk-delete' ) ) {
        
                $nonce = wp_unslash( $_REQUEST['_wpnonce'] );	
                /*
                 * Note: the nonce field is set by the parent class
                 * wp_nonce_field( 'bulk-' . $this->_args['plural'] );	 
                 */
                /*
                if ( ! wp_verify_nonce( $nonce, 'bulk-users' ) ) { // verify the nonce.
                    $this->invalid_nonce_redirect();
                }
                else {
                    include_once( 'views/partials-wp-list-table-demo-bulk-download.php' );
                    $this->graceful_exit();
                }
            }*/
        }

        /*
        * Method for rendering the user_login column.
        * Adds row action links to the user_login column.
        * e.g. url/users.php?page=nds-wp-list-table-demo&action=view_usermeta&user=18&_wpnonce=1984253e5e
        */
        /*protected function column_transaction_type( $item ) {		
            $admin_page_url =  admin_url( 'users.php' );

            // row action to view usermeta.
            $query_args_view_usermeta = array(
                'page'		=>  wp_unslash( $_REQUEST['page'] ),
                'action'	=> 'view_usermeta',
                'user_id'	=> absint( $item['id']),
                '_wpnonce'	=> wp_create_nonce( 'view_usermeta_nonce' ),
            );
            $view_usermeta_link = esc_url( add_query_arg( $query_args_view_usermeta, $admin_page_url ) );		
            $actions['view_usermeta'] = '<a href="' . $view_usermeta_link . '">' . __( 'View Meta', $this->plugin_text_domain ) . '</a>';		

            // similarly add row actions for add usermeta.

            $row_value = '<strong>' . $item['transaction_type'] . '</strong>';
            return $row_value . $this->row_actions( $actions );
        }*/
    }
?>