<?php
/*
Plugin Name: Objects
Description: Manage your object collection with Wordpress
Version: 0.1
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
				<div class="postbox" id="submitdiv">
					<h3 class="wplc_plaincursor">
						<span><?php _e('Publish', $wplc_domain); ?></span>
					</h3>
					<div class="inside">
						<?php object_submit_meta_box(); ?>
					</div>
				</div>
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

}


add_action('admin_menu', 'object_admin_pages');
#add_meta_box('objectsubmitdiv', __('Save'), 'object_submit_meta_box', 'link', 'side', 'core');


?>