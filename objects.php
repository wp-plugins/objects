<?php
/*
Plugin Name: Object Collections
Description: Manage your object collection with Wordpress
Version: 0.1
Author: Frankie Roberto
Author URI: http://www.frankieroberto.com
Tags: museums, collection, objects
*/


function post_type_objects() {
	register_post_type( 'object', array(
										'label' => __('Objects'), 
								    'singular_label' => __('Object'),										
										'public' => true, 
										'show_ui' => true,
										'supports' => array('title', 'editor', 'author', 'thumbnail', 'excerpt', 'trackbacks', 'custom-fields', 'comments', 'revisions'),
										'rewrite' => array('slug' => 'objects'),
										'can_export' => true
										));
	 register_taxonomy( 'object-tags', 'object', array( 'hierarchical' => false, 'label' => __('Tags'), 'public' => true,	'show_ui' => true 	) ); 
	 register_taxonomy( 'collections', 'object', array( 'hierarchical' => true, 'label' => __('Collections'), 'public' => true,	'show_ui' => true 	) ); 
										
}


add_action('init', 'post_type_objects');

function object_info_meta_box() {
  global $post;
  // Use nonce for verification

  echo '<input type="hidden" name="created_info_noncename" id="created_info_noncename" value="' . 
    wp_create_nonce( plugin_basename(__FILE__) ) . '" />';

  // The actual fields for data entry
  echo '<label for="object-created-date">' . __("Date:") . '</label> ';
  echo ' <input type="text" name="object-created-date" value="' . get_post_meta($post->ID, 'object-created-date', true) . '" size="20" />';

  // The actual fields for data entry
  echo '<label for="object-colour">' . __("Colour:") . '</label> ';
  echo ' <input type="text" name="object-colour" value="' . get_post_meta($post->ID, 'object-colour', true) . '" size="20" />';

}


function object_info_save( $post_id ) {
  // verify this came from the our screen and with proper authorization,
  // because save_post can be triggered at other times

  if ( !wp_verify_nonce( $_POST['created_info_noncename'], plugin_basename(__FILE__) )) {
    return $post_id;
  }


  // OK, we're authenticated: we need to find and save the data

  $created_date = $_POST['object-created-date'];
  $colour = $_POST['object-colour'];


  // TODO: Do something with $mydata 
  // probably using add_post_meta(), update_post_meta(), or 
  // a custom table (see Further Reading section below)

  update_post_meta($post_id, 'object-created-date', $created_date);
  update_post_meta($post_id, 'object-colour', $colour);  
}

function add_object_meta_boxes() {
	add_meta_box('object-info-section', __('Object Info'), 'object_info_meta_box', 'object', 'side', 'high');
}

add_action('admin_menu', 'add_object_meta_boxes');
add_action('save_post', 'object_info_save');



?>