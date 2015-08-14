<?php
/*
Plugin Name: Wordpress Feed Aggregator
Plugin URI: http://github.com/LJCOOL/wp-feed-aggregator
Description: Pulls and displays posts from multiple Facebook pages.
Version: 1.0.3
Author: Jay Newton, Shaawin Vsingam
*/
//include settings
include __DIR__ . '/options.php';
//include facebook php sdk
require_once __DIR__ . '/vendor/autoload.php';

//definitions for api keys
define("APP_ID", "");
define("APP_SECRET", "");
define("APP_TOKEN", "");

//Facebook page IDs retrieved from Options
$Facebook_IDs = array(
    'id_1' => "esc_attr( get_option('page-ID1')",
    'id_2' => "esc_attr( get_option('page-ID2')"
);

//Stripout page ID if whole URL pasted
/*
$facebook_string = 'facebook.com';
$pageID_check = stripos($id_1, $cff_facebook_string);

if ( $pageID_check ) {
    //Remove trailing slash if exists
    $id_1 = preg_replace('{/$}', '', $id_1);
    //Get last part of url
    $id_1 = substr( $id_1, strrpos( $id_1, '/' )+1 );
}*/

//creates fb object to make graph api calls
function wpfa_init_fb($app_id, $app_secret){
    $fb = new Facebook\Facebook([
        'app_id' => $app_id,
        'app_secret' => $app_secret,
        'default_graph_version' => 'v2.4'
    ]);
    return $fb;
}

function wpfa_call_graph_api($fb, $token, $request){
    try {
        $response = $fb->get($request, $token);
    } catch(Facebook\Exceptions\FacebookResponseException $e) {
        echo 'Graph returned an error: ' . $e->getMessage();
        exit;
    } catch(Facebook\Exceptions\FacebookSDKException $e) {
        echo 'Facebook SDK returned an error: ' . $e->getMessage();
        exit;
    }
    return $response;
}

//called when the plugin is activated
function wpfa_activate(){
    //fb object and token
    $token = APP_TOKEN;
    $fb = wpfa_init_fb(APP_ID, APP_SECRET);

    //get list of posts from page
    $response = wpfa_call_graph_api($fb, $token, '/123542974439976/posts');
    $posts = $response->getGraphEdge();

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
add_action ('activate_wp-feed-aggregator/wp-feed-aggregator.php', 'wpfa_activate');
//add_action ('init', 'wpfa_activate');

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
