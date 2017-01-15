<?php
/*
Plugin Name: Post Thumbnails Widget
Plugin URI: http://j4ck.lp5.com.ua
Description: An example plugin to demonstrate my skill.
Version: 0.1
Author: Ruslan Mayatskiy
Author URI: http://j4ck.lp5.com.ua
License: GPL2

    Copyright 2017 Author Name

    This program is free software; you can redistribute it and/or
    modify it under the terms of the GNU General Public License,
    version 2, as published by the Free Software Foundation.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.
*/

add_action("widgets_init", function () {
    register_widget("JackWidget");
});

function j4ck_add_scripts_admin() {
	wp_enqueue_script( 'jquery' );
	wp_enqueue_script( 'jquery-ui-autocomplete' );
	wp_register_style( 'jquery-ui-styles','http://ajax.googleapis.com/ajax/libs/jqueryui/1.8/themes/base/jquery-ui.css' );
	wp_enqueue_style( 'jquery-ui-styles' );
	wp_register_script( 'j4ck-autocomplete', plugins_url( '/assets/js/autocomplete.js', __FILE__ ), array( 'jquery', 'jquery-ui-autocomplete' ), '1.0', false );
	wp_localize_script( 'j4ck-autocomplete', 'j4ckAutocompleteSearch', array( 'url' => admin_url( 'admin-ajax.php' ) ) );
	wp_enqueue_script( 'j4ck-autocomplete' );
}

add_action( 'admin_enqueue_scripts', 'j4ck_add_scripts_admin' );
add_action('siteorigin_panel_enqueue_admin_scripts', 'j4ck_add_scripts_admin');

function j4ck_add_scripts_frontend() {
	wp_register_style( 'post-thumbnail-style', plugins_url( '/assets/css/post-thumbnails-style.css', __FILE__ ) );
	wp_enqueue_style( 'post-thumbnail-style' );
}

add_action( 'wp_enqueue_scripts', 'j4ck_add_scripts_frontend' );

function autocomplete_search() {
		$term = strtolower( $_GET['term'] );
		$suggestions = [];

    if (preg_match('/\//', $term)) {
      $post_id = url_to_postid($term);
      $args = 'p='.$post_id;
    } else $args = 's='.$term;

		$loop = new WP_Query( $args );

		while( $loop->have_posts() ) {
			$loop->the_post();
			$suggestion = array();
			$suggestion['label'] = get_the_title();
			$suggestion['id'] = get_the_ID();

			$suggestions[] = $suggestion;
		}

		wp_reset_query();


    	$response = json_encode( $suggestions );
    	echo $response;
    	exit();

}

add_action( 'wp_ajax_autocomplete_search', 'autocomplete_search' );
// add_action( 'wp_ajax_nopriv_my_search', 'autocomplete_search' );

class JackWidget extends WP_Widget
{
    public function __construct() {
        parent::__construct("jack_widget", "Post Thumbnail Widget",
            array("description" => "A simple widget to show my skill"));
    }

    public function form($instance) {
      $title = "";
      $post = "";
      $post_id = "";
      $layout = "";

      if (!empty($instance)) {
          $title = $instance["title"];
          $post = $instance["post"];
          $post_id = $instance["post_id"];
          $layout = $instance["layout"];
      }

      $titleId = $this->get_field_id("title");
      $titleName = $this->get_field_name("title");
      echo '<label for="' . $titleId . '">Title</label><br>';
      echo '<input id="' . $titleId . '" type="text" name="' .
      $titleName . '" value="' . $title . '"><br>';

      $postId = $this->get_field_id("post");
      $postName = $this->get_field_name("post");
      echo '<label for="' . $postId . '">Post title (or URL)</label><br>';
      echo '<input id="' . $postId . '" type="text" class="j4ck-post-autocomplete" name="' .
      $postName . '" value="' . $post . '"><br>';

      $post_idId = $this->get_field_id("post_id");
      $post_idName = $this->get_field_name("post_id");
      echo '<input type="hidden" id="'.$post_idId.'" class="j4ck-post-id" name="'.$post_idName.'" value="'.$post_id.'">';

      $layoutId = $this->get_field_id("layout");
      $layoutName = $this->get_field_name("layout");
      echo '<label for="' . $postId . '">Layout</label><br>';
      echo '<select name="'.$layoutName.'" id="'.$layoutId.'">
              <option'; echo ($layout == 'big') ? ' selected ' : '';  echo ' value="big">Big Featured Image</option>
              <option'; echo ($layout == 'left') ? ' selected ' : '';  echo ' value="left">Float left</option>
              <option'; echo ($layout == 'right') ? ' selected ' : '';  echo ' value="right">Float right</option>
            </select>';
    }

  public function update($newInstance, $oldInstance) {
    $values = array();
    $values["title"] = htmlentities($newInstance["title"]);
    $values["post"] = htmlentities($newInstance["post"]);
    $values["layout"] = htmlentities($newInstance["layout"]);
    $values["post_id"] = htmlentities($newInstance["post_id"]);
    return $values;
  }

  public function widget($args, $instance) {
    $title = $instance["title"];
    $text = $instance["post"];
    $layout = $instance["layout"];
    $post_id = $instance["post_id"];

    $thumbnail_url = get_the_post_thumbnail_url($post_id);
    $pst = get_post( $post_id );
    $desc = wp_trim_words( $pst->post_content );
    $desc = str_replace( $text, '', $desc );

    switch ($layout)  {
      case 'big':
         require(__DIR__.'/assets/layout/bigimage.php');
         break;
      case 'left':
         require(__DIR__.'/assets/layout/floatleft.php');
         break;
      case 'right':
         require(__DIR__.'/assets/layout/floatright.php');
         break;
    }
    // echo "<h2>Title: $title</h2>";
    // echo "<p>Text: $text</p>";
    // echo "<p>Layout: $layout</p>";
    // echo "<p>Post ID: $post_id</p>";
  }

}

?>
