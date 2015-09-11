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

    function call_graph_api($request){
        try {
            $response = $this->fb->get($request, $this->token);
        } catch(Facebook\Exceptions\FacebookResponseException $e) {
            error_log('Graph returned an error: ' . $e->getMessage());
            exit;
        } catch(Facebook\Exceptions\FacebookSDKException $e) {
            error_log('Facebook SDK returned an error: ' . $e->getMessage());
            exit;
        }
        return $response;
    }

    function get_posts($page_ID){
        $request = '/'.$page_ID.'/posts?limit=10&fields=id,created_time';
        $response = $this->call_graph_api($request);
        return $response->getGraphEdge();
    }

    function get_page_name($page_ID){
        $request = '/'.$page_ID.'?fields=name';
        $response = $this->call_graph_api($request);
        $object = $response->getGraphNode();
        return $object['name'];
    }

    function get_post($post_id){
        $p['id'] = $post_id;

        $request = '/'.$post_id.'?fields=object_id,message,status_type';
        $response = $this->call_graph_api($request);
        $post = $response->getGraphNode();

        //error_log($post['status_type']);
        //handle different post types
        switch ($post['status_type']) {
            case 'added_photos':
                //retrieve photo node with list of image qualities associated with it
                $object_request = '/'.$post['object_id'].'?fields=images';
                $response = $this->call_graph_api($object_request);
                $object = $response->getGraphNode();
                //get the hyperlink of the highest quality image
                $p['image'] = $object['images'][0]['source'];
                break;
            case 'shared_story':
                $p['image'] = NULL;
                break;
            default:
                return NULL;
                break;
        }

        //don't generate empty posts
        if ($post['message'] == '') {
            return NULL;
        }

        //get the post's text content and append a hyperlink back to facebook
        $fb_link = '<br><br><a href="http://www.facebook.com/'. $post_id .'">View on Facebook</a>';
        $p['content'] = $post['message'] . $fb_link;
        return $p;
    }
}
?>
