<?php
//include facebook php sdk
require_once __DIR__ . '/vendor/autoload.php';

/**
 * Facebook Page object.
 */
class wpfa_FbPage{
    private $fb;
    private $token;

    function __construct($app_id, $app_secret, $token){
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
            echo $request;
            exit;
        } catch(Facebook\Exceptions\FacebookSDKException $e) {
            echo 'Facebook SDK returned an error: ' . $e->getMessage();
            echo $request;
            exit;
        }
        return $response;
    }

    function wpfa_get_posts($page_ID){
        $request = '/'.$page_ID.'/posts?fields=id';
        $response = $this->wpfa_call_graph_api($request);
        return $response->getGraphEdge();
    }

    function wpfa_get_post($post_id){
        $p['id'] = $post_id;

        $request = '/'.$post_id.'?fields=object_id,message,status_type';
        $response = $this->wpfa_call_graph_api($request);
        $post = $response->getGraphNode();
        $p['message'] = $post['message'];
        //error_log($post['status_type']);

        //retrieve photo node with list images associated with it
        if ($post['status_type'] == 'added_photos'){
            $object_request = '/'.$post['object_id'].'?fields=images';
            $response = $this->wpfa_call_graph_api($object_request);
            $object = $response->getGraphNode();
            $p['image'] = $object['images'][0]['source'];
        }
        else {
            $p['image'] = NULL;
        }

        return $p;
    }
}
?>
