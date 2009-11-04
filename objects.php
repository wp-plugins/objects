<?php
/*
Plugin Name: Objects
Description: Manage your object collection with Wordpress
Version: 0.1
Author: Frankie Roberto
Author URI: http://www.frankieroberto.com
Tags: museums, collection, objects
*/

function object_submit_meta_box($object) {
?>
	<div class="submitbox" id="submitlink">

		<div id="minor-publishing">

			<?php // Hidden submit button early on so that the browser chooses the right button when form is submitted with Return key ?>
			<div style="display:none;">
				<input type="submit" name="save" value="<?php esc_attr_e('Save'); ?>" />
			</div>
		</div>

		<div id="major-publishing-actions">
			<?php do_action('post_submitbox_start'); ?>


			<div id="publishing-action">
			<?php if ( !empty($link->link_id) ) { ?>
				<input name="save" type="submit" class="button-primary" id="publish" tabindex="4" accesskey="p" value="<?php esc_attr_e('Update Object') ?>" />
			<?php } else { ?>
				<input name="save" type="submit" class="button-primary" id="publish" tabindex="4" accesskey="p" value="<?php esc_attr_e('Add Object') ?>" />
			<?php } ?>
			</div>

			<div class="clear"></div>
		</div>
		<?php do_action('submitlink_box'); ?>
		<div class="clear"></div>
	</div>
<?php
}
#add_meta_box('linksubmitdiv', __('Save'), 'object_submit_meta_box', 'link', 'side', 'core');

/**
 * Display post tags form fields.
 *
 * @since 2.6.0
 *
 * @param object $post
 */
function object_tags_meta_box($object, $box) {
	$tax_name = esc_attr(substr($box['id'], 8));
	$taxonomy = get_taxonomy($tax_name);
	$helps = isset($taxonomy->helps) ? esc_attr($taxonomy->helps) : __('Separate tags with commas.');
?>
<div class="tagsdiv" id="<?php echo $tax_name; ?>">
	<div class="jaxtag">
	<div class="nojs-tags hide-if-js">
	<p><?php _e('Add or remove tags'); ?></p>
	<textarea name="<?php echo "tax_input[$tax_name]"; ?>" class="the-tags" id="tax-input[<?php echo $tax_name; ?>]"><?php echo esc_attr(get_terms_to_edit( $object->ID, $tax_name )); ?></textarea></div>

	<span class="ajaxtag hide-if-no-js">
		<label class="screen-reader-text" for="new-tag-<?php echo $tax_name; ?>"><?php echo $box['title']; ?></label>
		<input type="text" id="new-tag-<?php echo $tax_name; ?>" name="newtag[<?php echo $tax_name; ?>]" class="newtag form-input-tip" size="16" autocomplete="off" value="<?php esc_attr_e('Add new tag'); ?>" />
		<input type="button" class="button tagadd" value="<?php esc_attr_e('Add'); ?>" tabindex="3" />
	</span></div>
	<p class="howto"><?php echo $helps; ?></p>
	<div class="tagchecklist"></div>
</div>
<p class="tagcloud-link hide-if-no-js"><a href="#titlediv" class="tagcloud-link" id="link-<?php echo $tax_name; ?>"><?php printf( __('Choose from the most used tags in %s'), $box['title'] ); ?></a></p>

<?php
}



function object_edit_form() {
		if ( !empty($object_id) ) {
			$heading = sprintf( __( '<a href="%s">Links</a> / Edit Link' ), 'link-manager.php' );
			$submit_text = __('Update Link');
			$form = '<form name="editlink" id="editlink" method="post" action="link.php">';
			$nonce_action = 'update-bookmark_' . $link_id;
		} else {
	#		$heading = sprintf( __( '<a href="%s">Links</a> / Add New Link' ), 'link-manager.php' );
			$submit_text = __('Add Object');
			$form = '<form name="addobject" id="addobject" method="post" action="add-object.php">';
			$nonce_action = 'add-object';
		}


?> 


		<div class="wrap">
			<h2>Add New Object</h2>
			
			<div id="poststuff" class="metabox-holder has-right-sidebar">

				<div id="side-info-column" class="inner-sidebar">
					<?php

						#do_action('submitlink_box');
						$side_meta_boxes = do_meta_boxes( 'object', 'side', $object);

					?>
				</div>
			
				<div id="post-body">
					<div id="post-body-content">
						<div id="titlediv">
							<div id="titlewrap">
								<label class="screen-reader-text" for="title"><?php _e('Title') ?></label>
								<input type="text" name="post_title" size="30" tabindex="1" value="<?php echo esc_attr( htmlspecialchars( $object->object_title ) ); ?>" id="title" autocomplete="off" />			
							</div>
						</div>
					</div>
				</div>

			</div>
		</div>	
<?php
	
}

function object_edit_page() {
	
}

function object_new_page() {
	object_edit_form();
}

function object_admin_pages() {
	add_object_page(__("Object"), __("Objects"), 2, plugin_basename(__FILE__), "object_edit_page");
	add_submenu_page(plugin_basename(__FILE__), __("Edit"), __("Edit"), 2, "objects-edit", "object_edit_page");
	add_submenu_page(plugin_basename(__FILE__), __("Add New"), __("Add New"), 2, "object-new", "object_new_page");

	add_meta_box('objectsubmitdiv', __('Save'), 'object_submit_meta_box', 'object', 'side', 'core');
	
	do_action('do_meta_boxes', 'object', 'normal');
	do_action('do_meta_boxes', 'object', 'advanced');
	do_action('do_meta_boxes', 'object', 'side');	

	// all tag-style post taxonomies
	foreach ( get_object_taxonomies('post') as $tax_name ) {
		if ( !is_taxonomy_hierarchical($tax_name) ) {
			$taxonomy = get_taxonomy($tax_name);
			$label = isset($taxonomy->label) ? esc_attr($taxonomy->label) : $tax_name;
			$label = "Object Tags";

			add_meta_box('tagsdiv-' . $tax_name, $label, 'object_tags_meta_box', 'object', 'side', 'core');
		}
	}
	
	
}


add_action('admin_menu', 'object_admin_pages');



?>