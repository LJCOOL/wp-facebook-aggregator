<?php

require_once(ABSPATH . 'wp-admin/includes/image.php');
require_once(ABSPATH . 'wp-admin/includes/media.php');
require_once(ABSPATH . 'wp-admin/includes/file.php');

class wpfa_Post{
    private $id;
    private $content;
    private $excerpt;
    private $images;
    private $category;

    function __construct($post){
        $this->id = $post['id'];
        $this->content = $post['content'];
        $this->excerpt = $post['excerpt'];
        $this->images = $post['images'];
    }

    function set_post_category($category){
        $this->category = $category;
    }

    function publish(){
        //append [gallery] tag if there multiple images to add to the post
        if (count($this->images) > 1) {
            $this->content .= '<br> [gallery]';
        }

        //create the post array
        $p = array(
            'post_name' => $this->id,
            'post_title' => $this->get_title(),
            'post_content' => $this->content,
            'post_excerpt' => $this->excerpt
        );
        //insert post
        $post_id = wp_insert_post($p);

        //attach featured image to post
        if ($this->images){
            $url = rawurldecode($this->images[0]);
            $tmp = download_url($url);
            preg_match('/[^\?]+\.(jpg|jpe|jpeg|gif|png)/i', $url, $matches);
            $file = array(
                'name' => basename($matches[0]),
                'tmp_name' => $tmp
            );
            $attach_id = media_handle_sideload($file, $post_id);
            add_post_meta($post_id, '_thumbnail_id', $attach_id);
        }

        //attach additional images to post
        if (count($this->images) > 1) {
            //skip duplicating the first image as it is already the featured image
            for ($i=1; $i < count($this->images); $i++) {
                media_sideload_image($this->images[$i], $post_id);
            }
        }

        //set the post's category
        $post_categories = array($this->category);
        wp_set_post_categories($post_id, $post_categories);

        //publish post
        wp_publish_post($post_id);
    }

    //strips 4 words from the main content to use as the title
    function get_title() {
      //safe strip, handles stuff like commas and dashes
      preg_match("/(?:[^\s,\.;\?\!]+(?:[\s,\.;\?\!]+|$)){0,4}/", $this->excerpt, $title);
      //add a trailing ellipsis
      $title[0] .= "...";
      //check for a link in the title
      if (preg_match("/http(s|):\/\/\S+/", $title[0]) === 1)
      {
          return 'Shared Link';
      }
      return $title[0];
    }
}
?>
