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

    class WP_Social_Stream_Admin{
        /**
         * Holds the values to be used in the fields callbacks
         */
        private $options;

        // Initialize
        public function __construct()
        {
            add_action( 'admin_menu', array( $this, 'add_plugin_page' ) );
            add_action( 'admin_init', array( $this, 'page_init' ) );
        }

        // Admin page
        public function add_plugin_page()
        {
            // Will appear as it's own menu option
            add_menu_page(
                'Settings Admin', 
                'WP Social Stream', 
                'manage_options', 
                'wp-social-stream', 
                array( $this, 'create_admin_page' )
            );
        }

        // Render form
        public function create_admin_page()
        {
            // Set class property
            $this->options = get_option( 'wp_social_stream_options' );
            ?>
            <div class="wrap">
                <h1>WP Social Stream</h1>
                
                <form method="post" action="options.php">
                <?php
                    // This prints out all hidden setting fields
                    settings_fields( 'wp_social_stream_option_group' );
                    do_settings_sections( 'wp-social-stream-setting-admin' );
                    submit_button();
                ?>
                </form>
            </div>
            <?php
        }

        // Register settings and sections
        public function page_init()
        {        
            register_setting(
                'wp_social_stream_option_group', // Option group
                'wp_social_stream_options', // Option name
                array( $this, 'sanitize' ) // Sanitize
            );

             /************************************ 
                
                Sync Section
                
            ************************************/
            // Sync section
            add_settings_section(
                'sync_section_id', // ID
                'Sync Settings', // Title
                array( $this, 'sync_section_info' ), // Callback
                'wp-social-stream-setting-admin' // Page
            );             

            add_settings_field(
                'sync_setting', // ID
                'Sync', // Title 
                array( $this, 'select_callback' ),
                'wp-social-stream-setting-admin', // Page
                'sync_section_id', // Section
                ['id' => 'sync_setting']           
            ); 
            
            /************************************ 
                
                Twitter Section
                
            ************************************/
            // Twitter section
            add_settings_section(
                'twitter_section_id', // ID
                'Twitter', // Title
                array( $this, 'print_twitter_section_info' ), // Callback
                'wp-social-stream-setting-admin' // Page
            );             

            add_settings_field(
                'twitter_handle', // ID
                'Twitter Handle', // Title 
                array( $this, 'text_callback' ), // Callback
                'wp-social-stream-setting-admin', // Page
                'twitter_section_id', // Section 
                ['id' => 'twitter_handle']          
            ); 

            add_settings_field(
                'twitter_consumer_key', // ID
                'Consumer Key', // Title 
                array( $this, 'text_callback' ), // Callback
                'wp-social-stream-setting-admin', // Page
                'twitter_section_id', // Section 
                ['id' => 'twitter_consumer_key']               
            ); 

            add_settings_field(
                'twitter_consumer_secret', // ID
                'Consumer Secret', // Title 
                array( $this, 'text_callback' ), // Callback
                'wp-social-stream-setting-admin', // Page
                'twitter_section_id', // Section
                ['id' => 'twitter_consumer_secret']            
            ); 

            add_settings_field(
                'twitter_access_token', // ID
                'Access Token', // Title 
                array( $this, 'text_callback' ), // Callback
                'wp-social-stream-setting-admin', // Page
                'twitter_section_id', // Section
                ['id' => 'twitter_access_token']              
            ); 

            add_settings_field(
                'twitter_access_token_secret', // ID
                'Access Token Secret', // Title 
                array( $this, 'text_callback' ), // Callback
                'wp-social-stream-setting-admin', // Page
                'twitter_section_id', // Section
                ['id' => 'twitter_access_token_secret']            
            );
            
             /************************************ 
                
                GitHub Section
                
            ************************************/
            // GitHub section
            add_settings_section(
                'github_section_id', // ID
                'GitHub', // Title
                array( $this, 'print_github_section_info' ), // Callback
                'wp-social-stream-setting-admin' // Page
            );             

            add_settings_field(
                'github_token', // ID
                'GitHub Token', // Title 
                array( $this, 'text_callback' ), // Callback
                'wp-social-stream-setting-admin', // Page
                'github_section_id', // Section  
                ['id' => 'github_token']          
            ); 

        }

        /**
         * Sanitize each setting field as needed
         *
         * @param array $input Contains all settings fields as array keys
         */
        public function sanitize( $input )
        {
            $new_input = array();
            if( isset( $input['twitter_handle'] ) )
                $new_input['twitter_handle'] = sanitize_text_field( $input['twitter_handle'] );
            
            if( isset( $input['twitter_consumer_key'] ) )
                $new_input['twitter_consumer_key'] = sanitize_text_field( $input['twitter_consumer_key'] );

            if( isset( $input['twitter_consumer_secret'] ) )
                $new_input['twitter_consumer_secret'] = sanitize_text_field( $input['twitter_consumer_secret'] );

            if( isset( $input['twitter_access_token'] ) )
                $new_input['twitter_access_token'] = sanitize_text_field( $input['twitter_access_token'] );

            if( isset( $input['twitter_access_token_secret'] ) )
                $new_input['twitter_access_token_secret'] = sanitize_text_field( $input['twitter_access_token_secret'] );

            if( isset( $input['github_token'] ) )
                $new_input['github_token'] = sanitize_text_field( $input['github_token'] );
            
            if( isset( $input['sync_setting'] ) )
                $new_input['sync_setting'] = $input['sync_setting'];

            return $new_input;
        }

        // Callbacks for sections
        public function print_twitter_section_info()
        {
            print 'Enter your Twitter credentials below:';
        }

        public function print_github_section_info()
        {
            print 'Enter your GitHub credentials below:';
        }

        public function sync_section_info()
        {
            print 'Enter your sync settings below:';
        }

        // Callback for text fields
        public function text_callback($args){
            printf(
                '<input type="text" id="' . $args['id'] . '" name="wp_social_stream_options[' .  $args['id'] . ']" value="%s" />',
                isset( $this->options[$args['id']] ) ? esc_attr( $this->options[$args['id']]) : ''
            );
        }  
        

        // Callback for select fields
        public function select_callback($args){
            
            $select_sync_settings = array(
                '1-hour' => 'Every hour',
                '6-hours' => 'Every 6 hours',
                '24-hours' => 'Every 24 hours',
            );

            if($args['id'] == 'sync_setting'){
                $select_options = $select_sync_settings;
            } else {
                // other select
            }

            $selected_option = isset( $this->options[$args['id']] ) ? esc_attr( $this->options[$args['id']]) : '';

            echo '<select id="' . $args['id'] . '" name="wp_social_stream_options[' . $args['id'] . ']" />';
                    
                    foreach($select_options as $value => $option){
                        
                        $selected = selected($selected_option === $value, true, false);
                        echo '<option value="'. $value .'"' . $selected . '>' . $option . '</option>';  
                    
                    }
                
            echo '</select>'; 
        }

        
    }

}

new WP_Social_Stream_Admin();