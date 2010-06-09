<?php
/*
Plugin Name: Object Collections
Description: Manage your object collection with Wordpress
Version: 1.0
Author: Frankie Roberto
Author URI: http://www.frankieroberto.com
Tags: museums, collection, objects
License: GPL2
*/


/*  Copyright 2010  Frankie Roberto  (email : frankie@frankieroberto.com)

    This program is free software; you can redistribute it and/or modify
    it under the terms of the GNU General Public License, version 2, as 
    published by the Free Software Foundation.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with this program; if not, write to the Free Software
    Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
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
										'supports' => array('title', 'editor', 'author', 'thumbnail', 'excerpt', 'trackbacks', 'comments', 'revisions'),
										'rewrite' => array('slug' => 'objects'),
										'can_export' => true
										));
	 register_taxonomy( 'object-tags', 'object', array( 'hierarchical' => false, 'label' => __('Tags'), 'public' => true,	'show_ui' => true, 'show_tagclour' => true) ); 
	 register_taxonomy( 'collections', 'object', array( 'hierarchical' => true, 'label' => __('Collections'), 'labels' => array('singular_name' => 'Collection', 'search_items' => 'Search Collections', 'popular_items' => 'Popular Collections', 'all_items' => 'All Collections', 'parent_item' => 'Parent Collection', 'parent_item_colon' => 'Parent Collection:', 'edit_item' => 'Edit Collection', 'update_item' => 'Update Collection', 'add_new_item' => 'Add Collection', 'new_item_name' => 'New Collection Name'), 'public' => true,	'show_ui' => true 	) ); 

	 register_taxonomy( 'object-types', 'object', array( 'hierarchical' => true, 'labels' => array('name' => 'Object Types', 'singular_name' => 'Object Type', 'search_items' => 'Search Object Types', 'popular_items' => 'Popular Object Types', 'all_items' => 'All Object Types', 'parent_item' => 'Parent Object Type', 'parent_item_colon' => 'Parent Object Type:', 'edit_item' => 'Edit Object Type', 'update_item' => 'Update Object Type', 'add_new_item' => 'Add Object Type', 'new_item_name' => 'New Object Type'), 'public' => true,	'show_ui' => true 	) ); 

	 register_taxonomy( 'object-materials', 'object', array( 'hierarchical' => true, 'labels' => array('name' => 'Materials', 'singular_name' => 'Material', 'add_new_item' => 'Add Material', 'parent_item' => 'Parent Material', 'all_items' => 'All Materials'), 'public' => true,	'show_ui' => true 	) ); 

	if (get_option('object-show-custom-fields'))  {
		add_post_type_support('object', 'custom-fields');
	}
										
}



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

function objects_right_now_content() {
		$num_objects = wp_count_posts( 'object' );
		$num_collections = wp_count_terms('collections');
		$num_object_types = wp_count_terms('object-types');
		$num_object_tags = wp_count_terms('object-tags');
		$num_object_materials = wp_count_terms('object-materials');
		
		echo "\n\t".'<tr>';

		// Objects
		$num = number_format_i18n( $num_objects->publish );
		$text = _n( 'Object', 'Objects', intval($num_objects->publish) );
		if ( current_user_can( 'edit_posts' ) ) {
			$num = "<a href='edit.php?post_type=objects'>$num</a>";
			$text = "<a href='edit.php?post_type=objects'>$text</a>";
		}		
		
		echo '<td class="first b b-posts">' . $num . '</td>';
		echo '<td class="t posts">' . $text . '</td>';
		echo '</tr><tr>';

		// Collections
		$num = number_format_i18n( $num_collections );
		$text = _n( 'Collection', 'Collections', $num_collections );
		if ( current_user_can( 'manage_categories' ) ) {
			$num = "<a href='edit-tags.php?taxonomy=collections'>$num</a>";
			$text = "<a href='edit-tags.php?taxonomy=collections'>$text</a>";
		}
		echo '<td class="first b b-cats">' . $num . '</td>';
		echo '<td class="t cats">' . $text . '</td>';

		echo '</tr><tr>';
		
		// Object types
		$num = number_format_i18n( $num_object_types );
		$text = _n( 'Object Type', 'Object Types', $num_object_types );
		if ( current_user_can( 'manage_categories' ) ) {
			$num = "<a href='edit-tags.php?taxonomy=object-types'>$num</a>";
			$text = "<a href='edit-tags.php?taxonomy=object-types'>$text</a>";
		}
		echo '<td class="first b b-cats">' . $num . '</td>';
		echo '<td class="t cats">' . $text . '</td>';

		echo '</tr><tr>';		

		// Object tags
		$num = number_format_i18n( $num_object_tags );
		$text = _n( 'Object Tag', 'Object Tags', $num_object_tags );
		if ( current_user_can( 'manage_categories' ) ) {
			$num = "<a href='edit-tags.php?taxonomy=object-tags'>$num</a>";
			$text = "<a href='edit-tags.php?taxonomy=object-tags'>$text</a>";
		}
		echo '<td class="first b b-cats">' . $num . '</td>';
		echo '<td class="t cats">' . $text . '</td>';

		echo '</tr><tr>';		

		// Materials
		$num = number_format_i18n( $num_object_materials );
		$text = _n( 'Material', 'Materials', $num_object_materials );
		if ( current_user_can( 'manage_categories' ) ) {
			$num = "<a href='edit-tags.php?taxonomy=materials'>$num</a>";
			$text = "<a href='edit-tags.php?taxonomy=materials'>$text</a>";
		}
		echo '<td class="first b b-cats">' . $num . '</td>';
		echo '<td class="t cats">' . $text . '</td>';

		echo '</tr>';
		
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

function show_object_settings_page() {

	
	?>	<div class="wrap"><?php screen_icon(); ?>
	<h2><?php echo esc_html( 'Object Settings' ); ?></h2>	
		
		<form method="post" action="options.php">

			<?php settings_fields('object-settings'); ?>
			<?php do_settings_sections('object-settings'); ?>
			
			
			<p class="submit">
			<input type="submit" name="Submit" class="button-primary" value="<?php esc_attr_e('Save Changes') ?>" />
			</p>
			</form>
			
		</form>
		</div>
	<?php
}


add_action('init', 'post_type_objects');
add_action('admin_menu', 'add_object_meta_boxes');
add_action('admin_menu', 'add_object_settings');
add_action('save_post', 'object_info_save');
add_action('right_now_content_table_end', 'objects_right_now_content');


function add_object_settings() {
 	// Add the section to reading settings so we can add our fields to it
 	add_settings_section('eg_setting_section', 'Object Settings', 'object_settings_section', 'general');
 	
 	// Add the field with the names and function to use for our new settings, put it in our new section
 	add_settings_field('object-show-custom-fields', 'Show Custom Fields?', 'eg_setting_callback_function', 'general', 'eg_setting_section');
 	
 	// Register our setting so that $_POST handling is done for us and our callback function just has to echo the <input>
 	register_setting('general','object-show-custom-fields');
 }// eg_settings_api_init()
 
 
  
 // ------------------------------------------------------------------
 // Settings section callback function
 // ------------------------------------------------------------------
 //
 // This function is needed if we added a new section. This function 
 // will be run at the start of our section
 //
 
 function object_settings_section() {
	// 	echo '<p>These settings relate to <a href="edit.php?post_type=object">Objects</a>.</p>';
 }
 
 
function eg_setting_callback_function() {
 	$checked = "";
 	
 	// Mark our checkbox as checked if the setting is already true
 	if (get_option('object-show-custom-fields')) 
 		$checked = " checked='checked' ";
 
	echo '<label for="object-show-custom-fields">';
 	echo "<input {$checked} name='object-show-custom-fields' id='object-show-custom-fields' type='checkbox' /> ";
	echo "This allows authors to any extra fields they like, but also reveals some default fields.";
	echo "</label>";
 } // eg_setting_callback_function()
?>
