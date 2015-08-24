<?php
/*
Plugin Name: Wordpress Feed Aggregator
Plugin URI: http://github.com/LJCOOL/wp-feed-aggregator
Description: Pulls and displays posts from multiple Facebook pages.
Version: 1.0.5
Author: Jay Newton, Shaawin Vsingam
*/
require_once(ABSPATH . 'wp-admin/includes/media.php');
require_once(ABSPATH . 'wp-admin/includes/file.php');
require_once(ABSPATH . 'wp-admin/includes/image.php');
//include settings
include_once __DIR__ . '/options.php';
//include api keys
include_once __DIR__ . '/keys.php';
//include facebook module
include_once __DIR__ . '/wpfa_fb.php';

function wpfa_cron_interval($schedules) {
    $schedules['five_minutes'] = array(
        'interval' => 300,
        'display'  => __('Every Five Minutes')
    );
    return $schedules;
}
add_filter('cron_schedules', 'wpfa_cron_interval');

//called when the plugin is activated
function wpfa_activate() {
    wpfa_generateInitialOptions();
}
register_activation_hook(__FILE__, 'wpfa_activate');

//register function to scheduled
add_action ('wpfa_cron_hook', 'wpfa_update');

function wpfa_reset_cron(){
    if(wp_next_scheduled('wpfa_cron_hook')) {
        wp_clear_scheduled_hook('wpfa_cron_hook');
        wp_schedule_event(time(), 'five_minutes', 'wpfa_cron_hook');
    }
    else {
        wp_schedule_event(time(), 'five_minutes', 'wpfa_cron_hook');
    }
}

//called when the plugin is deactivated
function wpfa_deactivate() {
   wp_clear_scheduled_hook('wpfa_cron_hook');
   //delete option for welcome message purposes.
   delete_option('wpfa_activated');
}
register_deactivation_hook( __FILE__, 'wpfa_deactivate');

//update wordpress with facebook posts
function wpfa_update() {
    //get the time of the last update
    $last_update = get_option('wpfa_last_update_time');
    update_option('wpfa_last_update_time', time());

    $fb_page = new wpfa_FbPage(APP_ID, APP_SECRET, APP_TOKEN);
    $id_list = wpfa_getSettingsList();
    foreach ($id_list as $id) {
        $posts = $fb_page->wpfa_get_posts($id);
        foreach ($posts as $p) {
            //compare time, add posts if newer than last update
            if ($p['created_time']->getTimeStamp() > $last_update){
                $post = $fb_page->wpfa_get_post($p['id']);
                $wp_post = new wpfa_Post($post);
                $wp_post->wpfa_publish();
            }
        }
    }
}

/* Checks if settings have been changed.
   Called towards the end of an admin page loading to avoid race condition with
   options page on plugin activation, added bonus is that errors are inserted
   into the footer and so doesn't take over the page. */
add_action('admin_footer','wpfa_checkOptions');

//Display infromative message to user upon plugin activation
add_action('admin_notices', 'wpfa_displayWelcome');


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
            'post_title' => wpfa_getWords($this->message),
            'post_content' => $this->message,
            'post_excerpt' => $this->message
        );
        //insert post
        $post_id = wp_insert_post($p);

        //attach photo to post
        $tmp = download_url($this->image);
        $file = array(
            'name' => basename($this->image),
            'tmp_name' => $tmp
        );
        $attach_id = media_handle_sideload($file, $post_id);
        add_post_meta($post_id, '_thumbnail_id', $attach_id);

        //publish post
        wp_publish_post($post_id);
    }
}

function wpfa_generateInitialOptions() {
    //instantiate 'variable like' IDs
    for ($i = 1; $i <=5; $i++ ) {
        update_option("fb_ID$i",'');
    }

    //sanitise settings in options menu upon activation
    for ($i = 1; $i <=5; $i++ ) {
        update_option("page-ID$i",'');
    }

    //option to store time of last update
    update_option('wpfa_last_update_time', 0);
}

//retrieves list of facebook IDs set by user
function wpfa_getSettingsList() {
    $list = array();
    for ($i = 1; $i <= 5; $i++) {
      if (get_option("page-ID$i") != '')
          array_push($list,get_option("page-ID$i"));
    }
    return $list;
}

//checks if settings have changed
function wpfa_checkOptions() {
    for ($i = 1; $i <=5; $i++ ) {
        // if the 'local' ID is different from what's in the settings and not empty
        if (get_option("page-ID$i") != get_option("fb_ID$i") &&
            get_option("page-ID$i") != '') {
            //call reset cron to retrieve new posts from facebook
            wpfa_reset_cron();
            //update our 'local' variable so it is in par with the settings
            update_option("fb_ID$i",get_option("page-ID$i"));
        }
    }
}

//displays a welcome message in an appropriate manner.
function wpfa_displayWelcome() {
    //check so it displays message only upon plugin activation
    if (WPFA_ACTIVATED != get_option('wpfa_activated')) {
        //adds a temporary, variable like option if it's just been activated
        update_option('wpfa_activated','WPFA_ACTIVATED');
        ?>
            <div class="updated">
            <p><?php echo "<strong>Please click on the <em>Feed Aggregator Options</em>
                           on the side pane to get started.</strong>" ?></p>
            </div>
        <?php
    }
}

//strips 4 words from the main content to use as the title
function wpfa_getWords($content) {
  //safe strip, handles stuff like commas and dashes
  preg_match("/(?:\w+(?:\W+|$)){0,4}/", $content, $title);
  //add a trailing ellipsis
  $title[0] .= "...";
  return $title[0];
}

?>
