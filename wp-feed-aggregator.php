<?php
/*
Plugin Name: Wordpress Feed Aggregator
Plugin URI: http://github.com/LJCOOL/wp-feed-aggregator
Description: Pulls and displays posts from multiple Facebook pages.
Version: 1.0.4
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
    $page = new wpfa_FbPage('123542974439976', APP_ID, APP_SECRET, APP_TOKEN);
    $posts = $page->wpfa_get_posts();

    //here we would check against the database

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

function breh(){
    foreach ($posts as $p) {
        $post_request = '/'.$p['id'].'?fields=object_id,message';
        $response = wpfa_call_graph_api($fb, $token, $post_request);
        $post = $response->getGraphNode();

        //retrieve photo node with list images associated with it
        $photo_request = '/'.$post['object_id'].'?fields=images';
        $response = wpfa_call_graph_api($fb, $token, $photo_request);
        $photo = $response->getGraphNode();

        //insert one of the images into a post along with the post's text
        $message = $post['message'].'<img src="'.$photo['images'][0]['source'].'" /img>';
        wpfa_test_post($post['id'], $message);
    }
}
?>
