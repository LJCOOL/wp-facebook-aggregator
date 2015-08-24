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
//include facade to settings
include_once __DIR__ . '/wpfa_options_facade.php';
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
    $id_list = wpfa_getSettingsList();
    foreach ($id_list as $id) {
        $posts = $fb_page->wpfa_get_posts($id);
        foreach ($posts as $p) {
            //compare time, add posts if newer than last update
            if ($p['created_time']->getTimeStamp() > $last_update){
                $post = $fb_page->wpfa_get_post($p['id']);
                $wp_post = new wpfa_Post($post);
                $wp_post->wpfa_publish();
            }
        }
    }
}

//Display infromative message to user upon plugin activation
add_action('admin_notices', 'wpfa_displayWelcome');


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
            //'post_title' => wpfa_getTitle($this->message),
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

//strips 4 words from the main content to use as the title
function wpfa_getTitle($content) {
  //safe strip, handles stuff like commas and dashes
  preg_match("/(?:\w+(?:\W+|$)){0,4}/", $content, $title);
  //add a trailing ellipsis
  $title[0] .= "...";
  return $title[0];
}

?>
