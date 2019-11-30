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

define( 'WP_SOCIAL_STREAM_VERSION', '1.0.0' );

if ( !class_exists( 'WP_Social_Stream' ) ) {

    class WP_Social_Stream {  
        
        function __construct( $config ) {           
            if ( is_admin() ) {
                require_once( plugin_dir_path( __FILE__ ).'wp-stream-admin.php' );
                require_once( plugin_dir_path( __FILE__ ).'wp-stream-db.php' );
            }		            
        }               

    }

}
new WP_Social_Stream( $config );


// Create db table
function create_database_tables(){

    global $wpdb;

    $table_name = $wpdb->prefix . 'social_stream_data';
            
    $charset_collate = $wpdb->get_charset_collate();

    $sql = "CREATE TABLE $table_name (
        id mediumint(9) NOT NULL AUTO_INCREMENT,
        time datetime DEFAULT '0000-00-00 00:00:00' NOT NULL,
        source tinytext NOT NULL,
        content text NOT NULL,                
        PRIMARY KEY  (id)
    ) $charset_collate;";

    require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
    dbDelta( $sql );

}

register_activation_hook( __FILE__, 'create_database_tables' );

