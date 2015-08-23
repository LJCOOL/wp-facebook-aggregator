<?php
/*
Plugin Name: Wordpress Feed Aggregator
Plugin URI: http://github.com/LJCOOL/wp-feed-aggregator
Description: Pulls and displays posts from multiple Facebook pages.
Version: 1.0.5
Author: Jay Newton, Shaawin Vsingam
*/
require_once(ABSPATH . 'wp-admin/includes/media.php');
require_once(ABSPATH . 'wp-admin/includes/file.php');
require_once(ABSPATH . 'wp-admin/includes/image.php');
//include settings
include_once __DIR__ . '/options.php';
//include api keys
include_once __DIR__ . '/keys.php';
//include facebook module
include_once __DIR__ . '/wpfa_fb.php';

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
    //since we still have those 2 hardcoded ids, set cron here
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
}
register_deactivation_hook( __FILE__, 'wpfa_deactivate');

//update wordpress with facebook posts
function wpfa_update() {
    $fb_page = new wpfa_FbPage(APP_ID, APP_SECRET, APP_TOKEN);
    $id_list = wpfa_getSettingsList();
    foreach ($id_list as $id) {
        error_log($id);
        $posts = $fb_page->wpfa_get_posts($id);
        //database check here
        //as an example, add the most recent posts (25)
        foreach ($posts as $p) {
            $post = $fb_page->wpfa_get_post($p['id']);
            $wp_post = new wpfa_Post($post);
            $wp_post->wpfa_publish();
        }
    }
}

/* Checks if settings have been changed.
   Called towards the end of an admin page loading to avoid race condition with
   options page on plugin activation, added bonus is that errors are inserted
   into the footer and so doesn't take over the page. */
add_action('admin_footer','wpfa_checkOptions');


class wpfa_Post{
    private $id;
    private $message;
    private $image;

    function __construct($post){
        $this->id = $post['id'];
        $this->message = $post['message'];
        $this->image = $post['image'];
    }

    function wpfa_publish(){
        //create a post
        $p = array(
            'post_name' => $this->id,
            'post_title' => " ",
            'post_content' => $this->message,
            'post_excerpt' => $this->message
        );
        //insert post
        $post_id = wp_insert_post($p);

        //attach photo to post
        $tmp = download_url($this->image);
        $file = array(
            'name' => basename($this->image),
            'tmp_name' => $tmp
        );
        $attach_id = media_handle_sideload($file, $post_id);
        add_post_meta($post_id, '_thumbnail_id', $attach_id);

        //publish post
        wp_publish_post($post_id);
    }
}

function wpfa_generateInitialOptions() {
    //instantiate 'variable like' IDs
    for ($i = 1; $i <=5; $i++ ) {
        update_option("fb_ID$i",'');
    }

    //sanitise settings in options menu upon activation
    for ($i = 1; $i <=5; $i++ ) {
        update_option("page-ID$i",'');
    }
}

//retrieves list of facebook IDs set by user
function wpfa_getSettingsList() {
    $list = array();
    for ($i = 1; $i <= 5; $i++) {
      if (get_option("page-ID$i") != '')
          array_push($list,get_option("page-ID$i"));
    }
    return $list;
}

//checks if settings have changed
function wpfa_checkOptions() {
    for ($i = 1; $i <=5; $i++ ) {
      // if the 'local' ID is different from what's in the settings and not empty
        if (get_option("page-ID$i") != get_option("fb_ID$i") &&
            get_option("page-ID$i") != '') {
            //call reset cron to retrieve new posts from facebook
            wpfa_reset_cron();
            //update our 'local' variable so it is in par with the settings
            update_option("fb_ID$i",get_option("page-ID$i"));
        }
    }
}
?>
