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
            add_action( 'admin_init', array($this,'wpst_settings') );
        }
            
        public function wpst_admin_page(){
            add_menu_page( 'WP Social Stream', 'WP Social Stream', 'manage_options', 'wp-social-stream', array($this, 'wpst_admin_page_content') );
        }

        public function wpst_settings(){
            register_setting( 'wpst_api_credentials', 'wpst_twitter' );
	        register_setting( 'wpst_api_credentials', 'wpst_github' );
	        register_setting( 'wpst_api_credentials', 'wpst_youtube' );
        }

        public function wpst_admin_page_content(){
            ?>

            <div class="wrap">

            <h1>WP Social Stream</h1>

            <form method="post" action="options.php">
                <?php settings_fields( 'my-cool-plugin-settings-group' ); ?>
                <?php do_settings_sections( 'my-cool-plugin-settings-group' ); ?>
                <table class="form-table">
                    <tr valign="top">
                    <th scope="row">New Option Name</th>
                    <td><input type="text" name="new_option_name" value="<?php echo esc_attr( get_option('new_option_name') ); ?>" /></td>
                    </tr>
                    
                    <tr valign="top">
                    <th scope="row">Some Other Option</th>
                    <td><input type="text" name="some_other_option" value="<?php echo esc_attr( get_option('some_other_option') ); ?>" /></td>
                    </tr>
                    
                    <tr valign="top">
                    <th scope="row">Options, Etc.</th>
                    <td><input type="text" name="option_etc" value="<?php echo esc_attr( get_option('option_etc') ); ?>" /></td>
                    </tr>
                </table>
                
                <?php submit_button(); ?>

            </form>
            
            </div>
       <?php }       

    }

}
new WP_Social_Stream_Admin();