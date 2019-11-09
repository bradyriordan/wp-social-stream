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

            // Twitter section
            add_settings_section(
                'twitter_section_id', // ID
                'Twitter', // Title
                array( $this, 'print_twitter_section_info' ), // Callback
                'wp-social-stream-setting-admin' // Page
            ); 

            // Twitter fields
            add_settings_field(
                'twitter_handle', // ID
                'Twitter Handle', // Title 
                array( $this, 'twitter_handle_callback' ), // Callback
                'wp-social-stream-setting-admin', // Page
                'twitter_section_id' // Section           
            ); 

            add_settings_field(
                'twitter_consumer_key', // ID
                'Consumer Key', // Title 
                array( $this, 'twitter_consumer_key_callback' ), // Callback
                'wp-social-stream-setting-admin', // Page
                'twitter_section_id' // Section           
            ); 

            add_settings_field(
                'twitter_consumer_secret', // ID
                'Consumer Secret', // Title 
                array( $this, 'twitter_consumer_secret_callback' ), // Callback
                'wp-social-stream-setting-admin', // Page
                'twitter_section_id' // Section           
            ); 

            add_settings_field(
                'twitter_access_token', // ID
                'Access Token', // Title 
                array( $this, 'twitter_access_token_callback' ), // Callback
                'wp-social-stream-setting-admin', // Page
                'twitter_section_id' // Section           
            ); 

            add_settings_field(
                'twitter_access_token_secret', // ID
                'Access Token Secret', // Title 
                array( $this, 'twitter_access_token_secret_callback' ), // Callback
                'wp-social-stream-setting-admin', // Page
                'twitter_section_id' // Section           
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

            return $new_input;
        }

        /** 
         * Print the Section text
         */
        public function print_twitter_section_info()
        {
            print 'Enter your Twitter credentials below:';
        }

        /** 
         * Get the settings option array and print one of its values
         */
        public function twitter_handle_callback()
        {
            printf(
                '<input type="text" id="twitter_handle" name="wp_social_stream_options[twitter_handle]" value="%s" />',
                isset( $this->options['twitter_handle'] ) ? esc_attr( $this->options['twitter_handle']) : ''
            );
        }

        public function twitter_consumer_key_callback()
        {
            printf(
                '<input type="text" id="twitter_consumer_key" name="wp_social_stream_options[twitter_consumer_key]" value="%s" />',
                isset( $this->options['twitter_consumer_key'] ) ? esc_attr( $this->options['twitter_consumer_key']) : ''
            );
        }

        public function twitter_consumer_secret_callback()
        {
            printf(
                '<input type="text" id="twitter_consumer_secret" name="wp_social_stream_options[twitter_consumer_secret]" value="%s" />',
                isset( $this->options['twitter_consumer_secret'] ) ? esc_attr( $this->options['twitter_consumer_secret']) : ''
            );
        }

        public function twitter_access_token_callback()
        {
            printf(
                '<input type="text" id="twitter_access_token" name="wp_social_stream_options[twitter_access_token]" value="%s" />',
                isset( $this->options['twitter_access_token'] ) ? esc_attr( $this->options['twitter_access_token']) : ''
            );
        }

        public function twitter_access_token_secret_callback()
        {
            printf(
                '<input type="text" id="twitter_access_token_secret" name="wp_social_stream_options[twitter_access_token_secret]" value="%s" />',
                isset( $this->options['twitter_access_token_secret'] ) ? esc_attr( $this->options['twitter_access_token_secret']) : ''
            );
        }

        
    }

}

new WP_Social_Stream_Admin();