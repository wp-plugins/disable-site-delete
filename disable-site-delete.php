<?php

/*
 * Plugin Name: Disable Site Delete
 * Author: Brajesh Singh
 * Author URI: http://buddydev.com/members/sbrajesh/
 * Plugin URI: http://buddydev.com/plugins/disable-delete-site/
 * Version: 1.0
 * Network: true
 * Description: It disables the site delete capability of blog owners(admins) on a multisite blog network. Only network administrators can delete the sites. Works with WordPress Multisite and BuddyPress
 * License: GPL
 * 
 * 
 */
/**
 * Helper class for disablin site delete
 * I am using Fused prefix as it easier(and comes from the name of my other site)
 */
class Fused_Disable_Site_Delete_Helper{
    
    private static $instance;
    
    private function __construct() {
     //remove from menu   
     add_action( 'admin_menu', array($this,'remove_delete_from_menu' ));
     //disable delete capability
     add_action('delete_blog',array($this,'disable_delete_cap'),10,2);
     //when a site deletion is initiated, WordPress sends a mail. We will not allow that and kill it right there.
     //add_action('delete_site_email_content',array($this,'disable_delete_email'));
     add_action('pre_update_option_delete_blog_hash',array($this,'disable_delete_option'),10,2);
     //add localization
     add_action('plugins_loaded',array($this,'load_localization'));
    }
    
    /**
     * Creates singleton instance
     * 
     * @return Fused_Disable_Site_Delete_Helper 
     */
    public static function get_instance(){
        
        if( ! isset ( self::$instance ) )
                self::$instance = new self();
        
        return self::$instance;
        
    }
    /**
     * Load the localization file
     */
    function load_localization(){
         $mofile=plugin_dir_path(__FILE__).'/languages/'.get_locale().'.mo';
         load_textdomain( 'disable-delete-site', $mofile);
    }
    
    /**
     * Remove the delete site link from the tools menu if the user is not network administrator
     * @return type 
     */
    public function remove_delete_from_menu () {
        if(is_super_admin())
            return ;//do not prevent super administrators
        //for everyone else
        remove_submenu_page( 'tools.php', 'ms-delete-site.php' );

    }
    /** 
     * do not allow wpmu_delete_blog to delete a site if the action is triggered by non network administrator
     * @param type $blog_id
     * @param type $drop
     * @return type 
     */
    public function disable_delete_cap($blog_id, $drop){
        //if super admin, don't do anything
        if(is_super_admin())
            return $blog_id;
        
        wp_die(__('You are not allowed to delete this site. Please contact network administrator for any help','disable-delete-site'));
        
    }
    /**
     * We hack around update_option to avoid sending the mail to the user which is used to confirm/delete the site
     * 
     * @param type $new_val
     * @param type $old_val
     * @return type 
     */
    function disable_delete_option($new_val,$old_val){
        if(is_super_admin())
            return $new_val;
        
        wp_die(__('You are not allowed to delete this site. Please contact network administrator for any help','disable-delete-site'));
        
    }
    
}

Fused_Disable_Site_Delete_Helper::get_instance();