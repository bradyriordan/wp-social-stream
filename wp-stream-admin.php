<?php
/*
Plugin Name: WP Social Stream
Plugin URI: http://bradyriordan.com/wp-social-stream
Description: Aggregate social posts in one view
Author: Brady Riordan
Author URI: http://bradyriordan.com
Version: 1.0.0
*/

// create admin screen for youtube, github, and twitter credentials
// every x hours, fetch content from each social providers and store in db
// create shortcode that return content from db

// exit if file is called directly
if (!defined('ABSPATH')) {
	exit;
}


if ( !class_exists( 'WP_Social_Stream_Admin' ) ) {

    class WP_Social_Stream_Admin {  
        
        function __construct( ) {
            add_action('admin_menu', array($this,'wpst_admin_page') );
            add_action('admin_init', array($this,'wpst_register_settings') );
        }
            
        // Add menu and page
        public function wpst_admin_page(){
            add_menu_page( 'WP Social Stream', 'WP Social Stream', 'manage_options', 'wp-social-stream', array($this, 'wpst_admin_page_content') );
        }

        // Form
        public function wpst_admin_page_content(){
            ?>

            <div class="wrap">

            <h1>WP Social Stream</h1>

            <form method="post" action="options.php">

                <?php settings_fields( 'wpst_options' ); ?>

                <?php do_settings_sections( 'wp-social-stream' ); ?>
                
                <?php submit_button(); ?>

            </form>
            
            </div>

        <?php }
        
        // Register sections and settings
        public function wpst_register_settings(){

            register_setting(
                'wpst_options',
                'wpst_options',
                'wpst_validate_options'
            );


            // Sections
            add_settings_section(
                'wpst_api_credentials',
                'API Credentials',
                array($this, 'wpst_callback_section'),
                'wp-social-stream'
            );	

            // Settings fields
            add_settings_field(
                'wpst_twitter',
                'Twitter',
                array($this, 'wpst_callback_field_text'),
                'wp-social-stream',
                'wpst_api_credentials',
                ['id' => 'wpst_twitter', 'label' => 'Twitter']
            );
	
        }

        // Section callback
        public function wpst_callback_section(){
            echo '<p>Enter your API credentials below</p>';
        }

        // Text field callback
        public function wpst_callback_field_text($args){
            $options = get_option('wpst_options');

            $id    = isset($args['id'])    ? $args['id']    : '';
            $label = isset($args['label']) ? $args['label'] : '';

            $value = isset($options[$id]) ? sanitize_text_field($options[$id]) : '';

            echo '<input id="' . $id . '" name="' . $id . '" type="text" size="40" value="' . $value . '"><br />';
            echo '<label for="' . $id . '">' . $label . '</label>';
        }

        // Validate input
        public function wpst_validate_options($input) {   
        
            // Twitter     
            if (isset($input['wpst_twitter'])) {  
                $input['wpst_twitter'] = sanitize_text_field($input['wpst_twitter']);
            }
            return $input;
        }

    }

}
new WP_Social_Stream_Admin();