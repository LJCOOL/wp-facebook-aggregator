<?php
/*
Plugin Name: Wordpress Feed Aggregator
Plugin URI: http://github.com/LJCOOL/wp-feed-aggregator
Description: The WordPress Feed Aggregator Plugin enables users to have posts from multiple (public) Facebook pages added to their existing WordPress blog as a native WordPress post.
Version: 1.0.6
Author: Jay Newton, Shaawin Vsingam
*/

require_once(ABSPATH . 'wp-admin/includes/post.php');
require_once(ABSPATH . 'wp-admin/includes/taxonomy.php');

//include settings
include_once __DIR__ . '/options.php';
//include facade to settings
include_once __DIR__ . '/wpfa_options_facade.php';
//include api keys
include_once __DIR__ . '/keys.php';
//include facebook module
include_once __DIR__ . '/wpfa_fb.php';
//include our post module
include_once __DIR__ . '/wpfa_post.php';


function wpfa_cron_interval($schedules) {
    $schedules['five_minutes'] = array(
        'interval' => 300,
        'display'  => __('Every Five Minutes')
    );
    return $schedules;
}
add_filter('cron_schedules', 'wpfa_cron_interval');

//called when the plugin is activated
function wpfa_activate() {
    wpfa_generateInitialOptions();
    wpfa_reset_cron();
}
register_activation_hook(__FILE__, 'wpfa_activate');

//register function to scheduled
add_action ('wpfa_cron_hook', 'wpfa_update');

function wpfa_reset_cron(){
    if(wp_next_scheduled('wpfa_cron_hook')) {
        wp_clear_scheduled_hook('wpfa_cron_hook');
        wp_schedule_event(time(), 'five_minutes', 'wpfa_cron_hook');
    }
    else {
        wp_schedule_event(time(), 'five_minutes', 'wpfa_cron_hook');
    }
}

//called when the plugin is deactivated
function wpfa_deactivate() {
   wp_clear_scheduled_hook('wpfa_cron_hook');
   //delete option for welcome message purposes.
   delete_option('wpfa_activated');
}
register_deactivation_hook( __FILE__, 'wpfa_deactivate');

//update wordpress with facebook posts
function wpfa_update() {
    //get the time of the last update
    $last_update = get_option('wpfa_last_update_time');
    update_option('wpfa_last_update_time', time());

    $fb_page = new wpfa_FbPage(APP_ID, APP_SECRET, APP_TOKEN);

    //get the list of facebook pages
    $id_list = wpfa_getIDList();
    foreach ($id_list as $id) {
        $cat_id = wpfa_check_category($fb_page, $id);
        //retrieve posts for a page
        $posts = $fb_page->get_posts($id);
        foreach ($posts as $p) {
            //compare time, add posts if newer than last update
            if ($p['created_time']->getTimeStamp() > $last_update){
                $post = $fb_page->get_post($p['id']);
                //only generate posts that we can handle properly
                if ($post) {
                    $wp_post = new wpfa_Post($post);
                    $wp_post->set_post_category($cat_id);
                    $wp_post->publish();
                }
            }
        }
    }
}

function wpfa_check_category($fb_page, $id){
    //get the name of the facebook page to use as a category name
    $page_name = $fb_page->get_page_name($id);
    $cat_id = get_cat_ID($page_name);
    //create a new category if it does not already exist
    if($cat_id == 0){
        $return = wp_insert_term($page_name, 'category');
        $cat_id = $return['term_id'];
    }
    return $cat_id;
}

function wpfa_gen_initial_posts($id){
    $fb_page = new wpfa_FbPage(APP_ID, APP_SECRET, APP_TOKEN);
    $cat_id = wpfa_check_category($fb_page, $id);
    $posts = $fb_page->get_posts($id);
    foreach ($posts as $p) {
        //check if post already exists in wordpress
        if (!wpfa_name_exists($p['id'])){
            $post = $fb_page->get_post($p['id']);
            //only generate posts that we can handle properly
            if ($post) {
                $wp_post = new wpfa_Post($post);
                $wp_post->set_post_category($cat_id);
                $wp_post->publish();
            }
        }
    }
}

//Display infromative message to user upon plugin activation
add_action('admin_notices', 'wpfa_displayWelcome');

function wpfa_name_exists($name) {
    global $wpdb;
    $query = "SELECT ID FROM $wpdb->posts WHERE 1=1 AND post_name LIKE %s";
    return (int) $wpdb->get_var( $wpdb->prepare($query, $name) );
}
?>
