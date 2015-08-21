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

//called when the plugin is activated
function wpfa_activate(){
    $pages = array();
    wpfa_generateInitialOptions();
    //array_push($pages, new wpfa_FbPage(get_option('fb_ID1'), APP_ID, APP_SECRET, APP_TOKEN));
    //array_push($pages, new wpfa_FbPage(get_option('fb_ID2'), APP_ID, APP_SECRET, APP_TOKEN));
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

add_action('activate_wp-feed-aggregator/wp-feed-aggregator.php', 'wpfa_activate');
/* Checks if settings have been changed.
   Called towards the end of an admin page loading to avoid race condition with
   options page on plugin activation, added bonus is that errors are inserted
   into the footer and so doesn't take over the page. */
add_action('admin_footer','checkOptions');

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

function wpfa_generateInitialOptions() {
    update_option('fb_ID1','123542974439976');
    update_option('fb_ID2','20528438720');
    //instantiate 'variable like' IDs
    for ($i = 3; $i <=5; $i++ ) {
        update_option("fb_ID$i",'');
    }

    //sanitise settings in options menu
    for ($i = 1; $i <=5; $i++ ) {
      update_option("page-ID$i",'');
    }
}

//checks if settings have changed
function wpfa_checkOptions() {
    for ($i = 1; $i <=5; $i++ ) {
      if (get_option("page-ID$i") != get_option("fb_ID$i") &&
          get_option("page-ID$i") != '') {
            //get posts
            update_option("fb_ID$i",get_option("page-ID$i"));
          }
    }
}
?>
