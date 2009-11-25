<?php
/*
Plugin Name: Object
Description: Manage your object collection with Wordpress
Version: 0.2
Author: Frankie Roberto
Author URI: http://www.frankieroberto.com
Tags: museums, collection, objects
*/

function object_submit_meta_box() {
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
<div id="delete-action">
<?php
if ( !empty($_GET['action']) && 'edit' == $_GET['action'] && current_user_can('manage_links') ) { ?>
	<a class="submitdelete deletion" href="<?php echo wp_nonce_url("link.php?action=delete&amp;link_id=$link->link_id", 'delete-bookmark_' . $link->link_id); ?>" onclick="if ( confirm('<?php echo esc_js(sprintf(__("You are about to delete this link '%s'\n  'Cancel' to stop, 'OK' to delete."), $link->link_name )); ?>') ) {return true;}return false;"><?php _e('Delete'); ?></a>
<?php } ?>
</div>

<div id="publishing-action">
<?php if ( !empty($link->link_id) ) { ?>
	<input name="save" type="submit" class="button-primary" id="publish" tabindex="4" accesskey="p" value="<?php esc_attr_e('Update Link') ?>" />
<?php } else { ?>
	<input name="save" type="submit" class="button-primary" id="publish" tabindex="4" accesskey="p" value="<?php esc_attr_e('Add Link') ?>" />
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


function object_edit_formt() {
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
			<h2>Add Object</h2>
			
			<div id="poststuff" class="metabox-holder<?php echo 2 == $screen_layout_columns ? ' has-right-sidebar' : ''; ?>">

			<div id="side-info-column" class="inner-sidebar">
			<?php

			do_action('submitlink_box');
			$side_meta_boxes = do_meta_boxes( 'object', 'side', $object );

			?>
			</div>
			
			<div id="post-body">
			<div id="post-body-content">
			<div id="namediv" class="stuffbox">
			<h3><label for="link_name"><?php _e('Name') ?></label></h3>
			<div class="inside">
				<input type="text" name="link_name" size="30" tabindex="1" value="<?php echo esc_attr($object->object_name); ?>" id="link_name" />
			    <p><?php _e('Example: Nifty blogging software'); ?></p>
			</div>
			</div>
			</div>
			<?php object_submit_meta_box(); ?>
			

		</div>
<?php
	
}

function object_edit_page() {
	
}

function object_new_page() {
	object_edit_formt();
}

function object_admin_pages() {
	add_object_page(__("Object"), __("Objects"), 2, plugin_basename(__FILE__), "object_edit_page");
	add_submenu_page(plugin_basename(__FILE__), __("Edit"), __("Edit"), 2, "objects-edit", "object_edit_page");
	add_submenu_page(plugin_basename(__FILE__), __("Add New"), __("Add New"), 2, "object-new", "object_new_page");

}


add_action('admin_menu', 'object_admin_pages');
add_meta_box('objectsubmitdiv', __('Save'), 'object_submit_meta_box', 'object-new', 'side', 'core');


?>