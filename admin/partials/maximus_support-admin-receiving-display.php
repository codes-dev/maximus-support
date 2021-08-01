<?php

/**
 * Provide a admin area view for the plugin
 *
 * This file is used to markup the admin-facing aspects of the plugin.
 *
 * @link       www.xuro.com
 * @since      1.0.0
 *
 * @package    Maximus_support
 * @subpackage Maximus_support/admin/partials
 */
?>

<!-- This file should primarily consist of HTML with a little bit of PHP. -->
<div class="wrap">    
    <h2><?php _e('Transactions (Receiving)', 'maximus_support'); ?></h2>
        <div id="nds-wp-list-table-demo">			
            <div id="nds-post-body">		
                <form id="nds-user-list-form" method="get">					
                <input type="hidden" name="page" value="<?php echo $_REQUEST['page'] ?>" />
                <?php 
                    $this->user_list_table->search_box( __( 'Find', 'maximus_support' ), 'maximus-transaction-find');
                    $this->user_list_table->display(); 
                ?>			
                </form>
            </div>			
        </div>
</div>
