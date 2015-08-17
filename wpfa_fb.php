<?php
//include facebook php sdk
require_once __DIR__ . '/vendor/autoload.php';

/**
 * Facebook Page object.
 */
class wpfa_FbPage{
    private $page_ID;
    private $fb;
    private $token;

    function __construct($page_ID, $app_id, $app_secret, $token){
        $this->page_ID = $page_ID;
        $this->token = $token;
        $this->fb = new Facebook\Facebook([
            'app_id' => $app_id,
            'app_secret' => $app_secret,
            'default_graph_version' => 'v2.4'
        ]);
    }

    function wpfa_call_graph_api($request){
        try {
            $response = $this->fb->get($request, $this->token);
        } catch(Facebook\Exceptions\FacebookResponseException $e) {
            echo 'Graph returned an error: ' . $e->getMessage();
            exit;
        } catch(Facebook\Exceptions\FacebookSDKException $e) {
            echo 'Facebook SDK returned an error: ' . $e->getMessage();
            exit;
        }
        return $response;
    }

    function wpfa_get_posts(){
        $request = '/'.$this->page_ID.'/posts?fields=id';
        $response = $this->wpfa_call_graph_api($request);
        return $response->getGraphEdge();
    }

    function wpfa_get_post($post_id){
        $request = '/'.$post_id.'?fields=object_id,message';
        $response = $this->wpfa_call_graph_api($request);
        $post = $response->getGraphNode();

        //retrieve photo node with list images associated with it
        $photo_request = '/'.$post['object_id'].'?fields=images';
        $response = $this->wpfa_call_graph_api($photo_request);
        $photo = $response->getGraphNode();

        return array(
            'id' => $post_id,
            'message' => $post['message'],
            'image' => $photo['images'][0]['source']);
    }
}
 ?>
