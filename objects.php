<?php
/*
Plugin Name: Object Collections
Description: Manage your object collection with Wordpress
Version: 0.1
Author: Frankie Roberto
Author URI: http://www.frankieroberto.com
Tags: museums, collection, objects
*/

// Plugin DB Installation
//function objects_install() {
//
//}
//register_activation_hook(__FILE__, "objects_install");


function object_submit_meta_box($object) {
?>
	<div class="submitbox" id="submitlink">

		<div id="minor-publishing">

			<?php // Hidden submit button early on so that the browser chooses the right button when form is submitted with Return key ?>
			<div style="display:none;">
				<input type="submit" name="save" value="<?php esc_attr_e('Save'); ?>" />
			</div>
			
			<div id="minor-publishing-actions">
				<div id="save-action">
					<?php if ( 'publish' != $post->post_status && 'future' != $post->post_status && 'pending' != $post->post_status )  { ?>
						<input <?php if ( 'private' == $post->post_status ) { ?>style="display:none"<?php } ?> type="submit" name="save" id="save-post" value="<?php esc_attr_e('Save Draft'); ?>" tabindex="4" class="button button-highlighted" />
					<?php } elseif ( 'pending' == $post->post_status && $can_publish ) { ?>
						<input type="submit" name="save" id="save-post" value="<?php esc_attr_e('Save as Pending'); ?>" tabindex="4" class="button button-highlighted" />
					<?php } ?>
				</div>

				<div id="preview-action">
					<?php
					if ( 'publish' == $post->post_status ) {
						$preview_link = esc_url(get_permalink($post->ID));
						$preview_button = __('Preview Changes');
					} else {
						$preview_link = esc_url(apply_filters('preview_post_link', add_query_arg('preview', 'true', get_permalink($post->ID))));
						$preview_button = __('Preview');
					}
					?>
					<a class="preview button" href="<?php echo $preview_link; ?>" target="wp-preview" id="post-preview" tabindex="4"><?php echo $preview_button; ?></a>
					<input type="hidden" name="wp-preview" id="wp-preview" value="" />
				</div>

				<div class="clear"></div>
			</div><?php // /minor-publishing-actions ?>
			
			<div id="misc-publishing-actions">

				<div class="misc-pub-section">
					<label for="post_status"><?php _e('Status:') ?></label>
					<span id="post-status-display">
						<?php	_e('Draft'); ?>
					</span>
					<a href="#post_status" class="edit-post-status hide-if-no-js" tabindex='4'><?php _e('Edit') ?></a>

				<div id="post-status-select" class="hide-if-js">
				<input type="hidden" name="hidden_post_status" id="hidden_post_status" value="<?php echo esc_attr($post->post_status); ?>" />
				<select name='post_status' id='post_status' tabindex='4'>
				<?php if ( 'publish' == $post->post_status ) : ?>
				<option<?php selected( $post->post_status, 'publish' ); ?> value='publish'><?php _e('Published') ?></option>
				<?php elseif ( 'private' == $post->post_status ) : ?>
				<option<?php selected( $post->post_status, 'private' ); ?> value='publish'><?php _e('Privately Published') ?></option>
				<?php elseif ( 'future' == $post->post_status ) : ?>
				<option<?php selected( $post->post_status, 'future' ); ?> value='future'><?php _e('Scheduled') ?></option>
				<?php endif; ?>
				<option<?php selected( $post->post_status, 'pending' ); ?> value='pending'><?php _e('Pending Review') ?></option>
				<option<?php selected( $post->post_status, 'draft' ); ?> value='draft'><?php _e('Draft') ?></option>
				</select>

				 <a href="#post_status" class="save-post-status hide-if-no-js button"><?php _e('OK'); ?></a>
				 <a href="#post_status" class="cancel-post-status hide-if-no-js"><?php _e('Cancel'); ?></a>
				</div>

				</div><?php // /misc-pub-section ?>

				<div class="misc-pub-section " id="visibility">
					<?php _e('Visibility:'); ?> <span id="post-visibility-display"><?php

					$visibility = 'public';
					$visibility_trans = __('Public');

					echo esc_html( $visibility_trans ); ?></span>

				<a href="#visibility" class="edit-visibility hide-if-no-js"><?php _e('Edit'); ?></a>

				<div id="post-visibility-select" class="hide-if-js">
				<input type="hidden" name="hidden_post_password" id="hidden-post-password" value="<?php echo esc_attr($post->post_password); ?>" />
				<input type="hidden" name="hidden_post_visibility" id="hidden-post-visibility" value="<?php echo esc_attr( $visibility ); ?>" />

				<input type="radio" name="visibility" id="visibility-radio-public" value="public" <?php checked( $visibility, 'public' ); ?> /> <label for="visibility-radio-public" class="selectit"><?php _e('Public'); ?></label><br />
				<input type="radio" name="visibility" id="visibility-radio-password" value="password" <?php checked( $visibility, 'password' ); ?> /> <label for="visibility-radio-password" class="selectit"><?php _e('Password protected'); ?></label><br />
				<span id="password-span"><label for="post_password"><?php _e('Password:'); ?></label> <input type="text" name="post_password" id="post_password" value="<?php echo esc_attr($post->post_password); ?>" /><br /></span>
				<input type="radio" name="visibility" id="visibility-radio-private" value="private" <?php checked( $visibility, 'private' ); ?> /> <label for="visibility-radio-private" class="selectit"><?php _e('Private'); ?></label><br />

				<p><a href="#visibility" class="save-post-visibility hide-if-no-js button"><?php _e('OK'); ?></a>
				<a href="#visibility" class="cancel-post-visibility hide-if-no-js"><?php _e('Cancel'); ?></a></p>
				</div>

				</div><?php // /misc-pub-section ?>

			<?php
			// translators: Publish box date formt, see http://php.net/date
			$datef = __( 'M j, Y @ G:i' );
			if ( 0 != $post->ID ) {
				if ( 'future' == $post->post_status ) { // scheduled for publishing at a future date
					$stamp = __('Scheduled for: <b>%1$s</b>');
				} else if ( 'publish' == $post->post_status || 'private' == $post->post_status ) { // already published
					$stamp = __('Published on: <b>%1$s</b>');
				} else if ( '0000-00-00 00:00:00' == $post->post_date_gmt ) { // draft, 1 or more saves, no date specified
					$stamp = __('Publish <b>immediately</b>');
				} else if ( time() < strtotime( $post->post_date_gmt . ' +0000' ) ) { // draft, 1 or more saves, future date specified
					$stamp = __('Schedule for: <b>%1$s</b>');
				} else { // draft, 1 or more saves, date specified
					$stamp = __('Publish on: <b>%1$s</b>');
				}
				$date = date_i18n( $datef, strtotime( $post->post_date ) );
			} else { // draft (no saves, and thus no date specified)
				$stamp = __('Publish <b>immediately</b>');
				$date = date_i18n( $datef, strtotime( current_time('mysql') ) );
			}

			if ( $can_publish ) : // Contributors don't get to choose the date of publish ?>
				<div class="misc-pub-section curtime misc-pub-section-last">
					<span id="timestamp"><?php printf($stamp, $date); ?></span>
					<a href="#edit_timestamp" class="edit-timestamp hide-if-no-js" tabindex='4'><?php _e('Edit') ?></a>
					<div id="timestampdiv" class="hide-if-js"><?php touch_time(($action == 'edit'),1,4); ?></div>
				</div><?php // /misc-pub-section ?>
			<?php endif; ?>

			</div>
			<div class="clear"></div>
		</div>

			
			
		<div id="major-publishing-actions">
			<?php do_action('post_submitbox_start'); ?>
				<div id="delete-action">
				<?php
				if (!empty($object)) { ?>
				<a class="submitdelete deletion" href="<?php echo wp_nonce_url("admin.php?page=objects/objects.php&amp;action=delete&amp;id=".$object['ID'], 'delete-post_' . $object['ID']); ?>" onclick="if ( confirm('You are about to delete this object. OK?') ) {return true;}return false;">

				
				<?php _e('Delete'); ?></a>
				<?php } ?>
				</div>

			<div id="publishing-action">
			<?php if ( !empty($object) ) { ?>
				<input name="save" type="submit" class="button-primary" id="publish" tabindex="4" accesskey="p" value="<?php esc_attr_e('Update Object') ?>" />
			<?php } else { ?>
				<input name="save" type="submit" class="button-primary" id="publish" tabindex="4" accesskey="p" value="<?php esc_attr_e('Publish Object') ?>" />
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
 * Display object tags form fields.
 *
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

function object_comments_status_meta_box($object){
?>
<input name="advanced_view" type="hidden" value="1" />
<p><label for="comment_status" class="selectit">
<input name="comment_status" type="checkbox" id="comment_status" value="open" <?php checked($object['comment_status'], 'open'); ?> />
<?php _e('Allow Comments') ?></label></p>
<p><?php _e('These settings apply to this object only.'); ?></p>
<?php
}

function object_admin_pages() {
//	add_object_page(__("Object"), __("Objects"), 2, plugin_basename(__FILE__), "object_edit_page");
//	add_submenu_page(plugin_basename(__FILE__), __("Edit"), __("Edit"), 2, "objects/objects.php", "object_edit_page", plugin_basename(__FILE__));
//	add_submenu_page(plugin_basename(__FILE__), __("Add New"), __("Add New"), 2, "object-edit", "object_new_page");

//	add_meta_box('pagesubmitdiv', __('Save'), 'object_submit_meta_box', 'object', 'side', 'core');
//	add_meta_box('pagecommentstatusdiv', __('Discussion'), 'object_comments_status_meta_box', 'object', 'normal', 'core');
	
//	do_action('do_meta_boxes', 'object', 'normal');
//	do_action('do_meta_boxes', 'object', 'advanced');
//	do_action('do_meta_boxes', 'object', 'side');	

	// all tag-style post taxonomies
//	foreach ( get_object_taxonomies('post') as $tax_name ) {
//		if ( !is_taxonomy_hierarchical($tax_name) ) {
//			$taxonomy = get_taxonomy($tax_name);
//			$label = isset($taxonomy->label) ? esc_attr($taxonomy->label) : $tax_name;
//			$label = "Object Tags";

//			add_meta_box('tagsdiv-' . $tax_name, $label, 'object_tags_meta_box', 'object', 'side', 'core');
//		}
//	}
	
	
}



// add_action('admin_menu', 'object_admin_pages');

function post_type_objects() {
	register_post_type( 'objects',
                array( 'label' => __('Objects'), 'public' => true, 'show_ui' => true ) );
}
add_action('init', 'post_type_movies');


#add_action('activate_objects', "objects_install");



?>