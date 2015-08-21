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
    generateOptions();
    array_push($pages, new wpfa_FbPage(get_option('fb_ID1'), APP_ID, APP_SECRET, APP_TOKEN));
    array_push($pages, new wpfa_FbPage(get_option('fb_ID2'), APP_ID, APP_SECRET, APP_TOKEN));
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

function generateOptions() {
    update_option('fb_ID1','123542974439976');
    update_option('fb_ID2','20528438720');
    update_option('fb_ID3','');
    update_option('fb_ID4','');
    update_option('fb_ID5','');

    update_option('page-ID1', NULL);
    update_option('page-ID2', NULL);
    update_option('page-ID3', NULL);
    update_option('page-ID4', NULL);
    update_option('page-ID5', NULL);
}

function checkOptions() {
    if (get_option('page-ID1') != get_option('fb_ID1')) {
      $pages = array();
      array_push($pages, new wpfa_FbPage(get_option('page-ID1'), APP_ID, APP_SECRET, APP_TOKEN));
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
      update_option('fb_ID1',get_option('page-ID1'));
    }
    if (get_option('page-ID2') != get_option('fb_ID2')) {
      $pages = array();
      array_push($pages, new wpfa_FbPage(get_option('page-ID2'), APP_ID, APP_SECRET, APP_TOKEN));
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
      update_option('fb_ID2',get_option('page-ID2'));
    }
}
?>
