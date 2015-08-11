<?php
/*
Plugin Name: Wordpress Feed Aggregator
Plugin URI: http://github.com/LJCOOL/wp-feed-aggregator
Description: Pulls and displays posts from multiple Facebook pages.
Version: 1.0.2
Author: Jay Newton, Shaawin Vsingam
*/
//include settings
include _DIR_ . '/options.php';
//include facebook php sdk
require_once __DIR__ . '/vendor/autoload.php';

//definitions for api keys
define("APP_ID", "");
define("APP_SECRET", "");
define("APP_TOKEN", "");

//create fb object to make graph api calls
function wpfa_init_fb($app_id, $app_secret){
    $fb = new Facebook\Facebook([
        'app_id' => $app_id,
        'app_secret' => $app_secret,
        'default_graph_version' => 'v2.4'
    ]);
    return $fb;
}

function wpfa_call_graph_api(){
    $token = APP_TOKEN;
    $fb = wpfa_init_fb(APP_ID, APP_SECRET);

    try {
        // Returns a `Facebook\FacebookResponse` object
        $response = $fb->get('/123542974439976/posts', $token);
    } catch(Facebook\Exceptions\FacebookResponseException $e) {
        echo 'Graph returned an error: ' . $e->getMessage();
        exit;
    } catch(Facebook\Exceptions\FacebookSDKException $e) {
        echo 'Facebook SDK returned an error: ' . $e->getMessage();
        exit;
    }
    //get the graph edge containing posts
    $posts = $response->getGraphEdge();

    //pull id and message content from each post (post is of type graphNode)
    foreach ($posts as $post) {
        wpfa_test_post($post['id'], $post['message']);
    }
}

//called when the plugin is activated
function wpfa_activate(){
    wpfa_call_graph_api();
}
add_action ('activate_wp-feed-aggregator/wp-feed-aggregator.php', 'wpfa_activate');

//insert and publish a basic test post
function wpfa_test_post($id, $message){
    //create a post
    $post = array(
        'post_name' => $id,
        'post_title' => $id,
        'post_content' => $message,
        'post_excerpt' => $message
    );

    //insert post
    $post_id = wp_insert_post($post);

    //publish post
    wp_publish_post($post_id);
}
?>
