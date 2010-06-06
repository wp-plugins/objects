<?php
/*
Plugin Name: Object Collections
Description: Manage your object collection with Wordpress
Version: 1.0
Author: Frankie Roberto
Author URI: http://www.frankieroberto.com
Tags: museums, collection, objects
*/


function post_type_objects() {
	register_post_type( 'object', array(
										'label' => __('Objects'), 
										'labels' => array('name' => __('Objects'), 'singular_name' => __('Object'), 'edit_item' => 'Edit Object', 'add_new_item' => 'Add Object', 'new_item' => 'New Object', 'not_found' => 'No objects found', 'search_items' => 'Search Objects', 'view_item' => 'View Object'),
										'description' => 'Physical objects that exist as part of a collection.',
								    'singular_label' => __('Object'),			
										'menu_position' => 20,
										'public' => true, 
										'show_ui' => true,
								    'capability_type' => 'post',										
										'publicly_queryable' => true,
										'exclude_from_search' => false,
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
  echo '<p><label for="object-created-date">' . __("Date:") . '</label> ';
  echo ' <input type="text" name="object-created-date" value="' . get_post_meta($post->ID, 'object-created-date', true) . '" size="20" /></p>';

  // The actual fields for data entry
  echo '<p><label for="object-colour">' . __("Colour:") . '</label> ';
  echo ' <input type="text" name="object-colour" value="' . get_post_meta($post->ID, 'object-colour', true) . '" size="20" /></p>';

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

	//add filter to insure the text Object, or object, is displayed when user updates an object 
	add_filter('post_updated_messages', 'object_updated_messages');
	function object_updated_messages( $messages ) {

	  $messages['object'] = array(
	    0 => '', // Unused. Messages start at index 1.
	    1 => sprintf( __('Object updated. <a href="%s">View object</a>'), esc_url( get_permalink($post_ID) ) ),
	    2 => __('Custom field updated.'),
	    3 => __('Custom field deleted.'),
	    4 => __('Object updated.'),
	    /* translators: %s: date and time of the revision */
	    5 => isset($_GET['revision']) ? sprintf( __('Object restored to revision from %s'), wp_post_revision_title( (int) $_GET['revision'], false ) ) : false,
	    6 => sprintf( __('Object published. <a href="%s">View object</a>'), esc_url( get_permalink($post_ID) ) ),
	    7 => __('Object saved.'),
	    8 => sprintf( __('Object submitted. <a target="_blank" href="%s">Preview object</a>'), esc_url( add_query_arg( 'preview', 'true', get_permalink($post_ID) ) ) ),
	    9 => sprintf( __('Object scheduled for: <strong>%1$s</strong>. <a target="_blank" href="%2$s">Preview book</a>'),
	      // translators: Publish box date format, see http://php.net/date
	      date_i18n( __( 'M j, Y @ G:i' ), strtotime( $post->post_date ) ), esc_url( get_permalink($post_ID) ) ),
	    10 => sprintf( __('Object draft updated. <a target="_blank" href="%s">Preview object</a>'), esc_url( add_query_arg( 'preview', 'true', get_permalink($post_ID) ) ) ),
	  );

	  return $messages;

}


add_action('admin_menu', 'add_object_meta_boxes');
add_action('save_post', 'object_info_save');



?>