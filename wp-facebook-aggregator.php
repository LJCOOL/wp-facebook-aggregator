<?php
/*
Plugin Name: Wordpress Facebook Aggregator
Plugin URI: http://github.com/LJCOOL/wp-facebook-aggregator
Description: Pulls and displays posts from multiple Facebook pages.
Version: 1.0.1
Author: Jay Newton, Shaawin Vsingam
*/

//initialise plugin
function wpfa_init(){
    wpfa_test_post();
}
add_action ('init', 'wpfa_init');

//calling this will insert and publish a basic test post
function wpfa_test_post(){
    //create a post
    $post = array(
        'post_name' => "test-post",
        'post_title' => "Test Post",
        'post_content' => "This is a test.",
        'post_excerpt' => "test"
    );

    //insert post
    $post_id = wp_insert_post($post);

    //publish post
    wp_publish_post($post_id);
}
?>
