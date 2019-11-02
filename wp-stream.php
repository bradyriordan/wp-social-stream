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


if ( !class_exists( 'WP_Social_Stream' ) ) {

    class WP_Social_Stream {  
        
        var $providers;
        var $provider;
        function __construct( $config ) {
            $this->providers = $config['providers'];
            $this->provider = $config['provider'];
            if ( is_admin() ) {
                require_once( plugin_dir_path( __FILE__ ).'wp-stream-admin.php' );
            }		            
        }               

    }

}
new WP_Social_Stream( $config );