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
            error_log('Graph returned an error: ' . $e->getMessage() . 'with request: ' . $request);
        } catch(Facebook\Exceptions\FacebookSDKException $e) {
            error_log('Facebook SDK returned an error: ' . $e->getMessage() . 'with request: ' . $request);
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

    function get_attachments($post_id){
        $request = '/'.$post_id.'/attachments';
        $response = $this->call_graph_api($request);
        return $response->getGraphEdge();
    }

    function get_post($post_id){
        $p['id'] = $post_id;
        $p['images'] = array();

        $request = '/'.$post_id.'?fields=object_id,message,status_type';
        $response = $this->call_graph_api($request);
        $post = $response->getGraphNode();

        //handle different post types
        switch ($post['status_type']) {
            case 'added_photos':
                $attachments = $this->get_attachments($post_id);
                foreach ($attachments as $a){
                    switch ($a['type']) {
                        case 'album':
                            foreach ($a['subattachments'] as $sub) {
                                array_push($p['images'], $sub['media']['image']['src']);
                            }
                            break;

                        case 'photo':
                            array_push($p['images'], $a['media']['image']['src']);
                            break;
                        default:
                            $p['images'] = NULL;
                            break;
                    }
                }
                break;
            case 'shared_story':
                $p['images'] = NULL;
                break;
            default:
                return NULL;
                break;
        }

        //don't generate empty posts
        if ($post['message'] == '') {
            return NULL;
        }

        //insert hyperlink tags around links
        $message = preg_replace("/http(s|):\/\/\S+/", '<a href="$0">$0</a>', $post['message']);

        //get the post's text content and append a hyperlink back to facebook
        $fb_link = '<br><br><a href="http://www.facebook.com/'. $post_id .'"><i>View original post on Facebook</i></a>';
        $p['content'] = $message . $fb_link;
        return $p;
    }
}
?>
