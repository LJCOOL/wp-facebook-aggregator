<?php
/*
Plugin Name: Wordpress Feed Aggregator
Plugin URI: http://github.com/LJCOOL/wp-feed-aggregator
Description: Pulls and displays posts from multiple Facebook pages.
Version: 1.0.5
Author: Jay Newton, Shaawin Vsingam
*/
//include settings
include __DIR__ . '/options.php';
//include api keys
include __DIR__ . '/keys.php';
//include facebook module
include_once __DIR__ . '/wpfa_fb.php';

function wpfa_cron_interval($schedules) {
    $schedules['five_minutes'] = array(
        'interval' => 300,
        'display'  => __('Every Two Minutes')
    );
    return $schedules;
}
add_filter('cron_schedules', 'wpfa_cron_interval');

//called when the plugin is activated
function wpfa_activate() {
    if(!wp_next_scheduled('wpfa_cron_hook')) {
        wp_schedule_event(time(), 'five_minutes', 'wpfa_cron_hook');
    }
}
register_activation_hook(__FILE__, 'wpfa_activate');

function wpfa_wpfa() {
    $schedule = wp_get_schedule( 'wpfa_cron_hook' );
    error_log($schedule);
}
//register function to scheduled
add_action ('wpfa_cron_hook', 'wpfa_update');

//called when the plugin is deactivated
function wpfa_deactivate() {
   wp_clear_scheduled_hook('wpfa_cron_hook');
}
register_deactivation_hook( __FILE__, 'wpfa_deactivate');

//update wordpress with facebook posts
function wpfa_update() {
    error_log('CRON');
    $pages = array();
    array_push($pages, new wpfa_FbPage('123542974439976', APP_ID, APP_SECRET, APP_TOKEN));
    array_push($pages, new wpfa_FbPage('20528438720', APP_ID, APP_SECRET, APP_TOKEN));
    foreach ($pages as $page) {
        $posts = $page->wpfa_get_posts();
        //database check here
        //as an example, add the most recent posts (25)
        foreach ($posts as $p) {
            $post = $page->wpfa_get_post($p['id']);
            $wp_post = new wpfa_Post($post);
            $wp_post->wpfa_publish();
        }
    }
}

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
        if ($this->image != NULL){
            media_sideload_image($this->image, $post_id);
        }

        //publish post
        wp_publish_post($post_id);
    }
}
?>
