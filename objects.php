<?php
/*
Plugin Name: Object Collections
Description: Manage your object collection with Wordpress
Version: 0.2
Author: Frankie Roberto
Author URI: http://www.frankieroberto.com
Tags: museums, collection, objects
*/

// Plugin Installation
function objects_install() {



}
register_activation_hook(__FILE__, "objects_install");

function add_physical_object_type() {
	add_rewrite_rule('(collection)/([0-9]+)$', 'index.php?object_id=$matches[1]');
  add_rewrite_tag('%object_id%', '[0-9]+');	
	register_post_type( 'physical-object', array('exclude_from_search' => false) );
	register_taxonomy( 'object_tag', 'physical-object', array('hierarchical' => false, 'label' => __('Object Tags'), 'query_var' => true, 'rewrite' => true) ) ;
	

}
add_action('init', 'add_physical_object_type');

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
				<a class="submitdelete deletion" href="<?php echo wp_nonce_url("admin.php?page=objects/objects.php&amp;action=delete&amp;id=".$object->ID, 'delete-post_' . $object->ID); ?>" onclick="if ( confirm('You are about to delete this object. OK?') ) {return true;}return false;">

				
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
<input name="comment_status" type="checkbox" id="comment_status" value="open" <?php checked($object->comment_status, 'open'); ?> />
<?php _e('Allow Comments') ?></label></p>
<p><?php _e('These settings apply to this object only.'); ?></p>
<?php
}

function object_edit_form() {

	wp_print_scripts('autosave');
	wp_print_scripts('post');
	if ( user_can_richedit() )
		wp_print_scripts('editor');
	add_thickbox();
	wp_print_scripts('media-upload');
	wp_print_scripts('word-count');
	
	if(function_exists('wp_tiny_mce')) wp_tiny_mce();
	
	
	global $wpdb; 
	global $id;
	if (isset($_GET['id'])) {
		$id = $_GET['id'];
	} elseif (isset($_POST['id'])) {
		$id = $_POST['id'];
		$message = "Object updated.";
	}	
	
	if (isset($id)) {
		
		$object = get_post($id);
		$object_id = $object->ID;

	}
	get_currentuserinfo();
	global $user_ID;	
	
	
	require_once('includes/meta-boxes.php');
	
	// all tag-style post taxonomies
	foreach ( get_object_taxonomies('physical-object') as $tax_name ) {
		if ( !is_taxonomy_hierarchical($tax_name) ) {
			$taxonomy = get_taxonomy($tax_name);
			$label = isset($taxonomy->label) ? esc_attr($taxonomy->label) : $tax_name;

			add_meta_box('tagsdiv-' . $tax_name, $label, 'post_tags_meta_box', 'object', 'side', 'core');
		}
	}

?> 
<?php
if(!is_null($message)) {
?>
	<div id="message" class="updated fade">
		<p>
			<?php echo $message; ?>
		</p>
	</div>
<?php
}
?>

		<div class="wrap">
			<h2><?php echo $object_id ? __("Edit Event") : __("Add New Object"); ?></h2>
			
			<form name="object" action="admin.php?page=object-edit" method="post" id="post">
				<?php if(!empty($object_id)) { ?>
					<input type="hidden" name="id" value="<?php echo $object_id; ?>" />
				<?php	}	?>

			
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
									<input type="text" name="object_title" size="30" tabindex="1" value="<?php echo esc_attr( htmlspecialchars( $object->post_title ) ); ?>" id="title" autocomplete="off" />			
								</div>
								<div class="inside">
								<?php
								$sample_permalink_html = get_sample_permalink_html($object->ID);
								if ( !( 'pending' == $object->post_status && !current_user_can( 'publish_posts' ) ) ) { ?>
									<div id="edit-slug-box">
								<?php
									if ( ! empty($object->ID) && ! empty($sample_permalink_html) ) :
										echo $sample_permalink_html;
								endif; ?>
									</div>
								<?php
								} ?>
								</div>
							</div>
							
							<div id="postdivrich" class="postarea">
								<?php #the_editor(, "content", "titlediv", true); ?>
								<?php the_editor(stripslashes(stripslashes($object->post_content)) /*content*/, "content" /*id*/, "sample-permalink" /*prev_id*/, true /*media_buttons*/, 15 /*tab_index*/); ?>

								<table id="post-status-info" cellspacing="0">
									<tbody>
										<tr>
											<td id="wp-word-count"></td>
											<td class="autosave-info">
												<span id="autosave">&nbsp;</span>
											</td>
										</tr>
									</tbody>
								</table>

								<?php
								wp_nonce_field( 'autosave', 'autosavenonce', false );
								wp_nonce_field( 'closedpostboxes', 'closedpostboxesnonce', false );
								wp_nonce_field( 'getpermalink', 'getpermalinknonce', false );
								wp_nonce_field( 'samplepermalink', 'samplepermalinknonce', false );
								wp_nonce_field( 'meta-box-order', 'meta-box-order-nonce', false ); ?>
							</div>							
							
							<?php do_meta_boxes('object', 'normal', $object); ?>
							
						</div>
					</div>

				</div>
			</form>
		</div>	

<?php
	
}

function object_edit_page() {
	global $wpdb;
	$title = "Edit Objects";

	if($_GET['action'] == "delete") {
		object_delete_object($_GET['id']);
		return;
		echo("deleted");
	}

	wp_enqueue_script('inline-edit-post');
	$post_stati  = array(	//	array( adj, noun )
			'publish' => array(_x('Published', 'page'), __('Published pages'), _nx_noop('Published <span class="count">(%s)</span>', 'Published <span class="count">(%s)</span>', 'page')),
			'future' => array(_x('Scheduled', 'page'), __('Scheduled pages'), _nx_noop('Scheduled <span class="count">(%s)</span>', 'Scheduled <span class="count">(%s)</span>', 'page')),
			'pending' => array(_x('Pending Review', 'page'), __('Pending pages'), _nx_noop('Pending Review <span class="count">(%s)</span>', 'Pending Review <span class="count">(%s)</span>', 'page')),
			'draft' => array(_x('Draft', 'page'), _x('Drafts', 'manage posts header'), _nx_noop('Draft <span class="count">(%s)</span>', 'Drafts <span class="count">(%s)</span>', 'page')),
			'private' => array(_x('Private', 'page'), __('Private pages'), _nx_noop('Private <span class="count">(%s)</span>', 'Private <span class="count">(%s)</span>', 'page')),
			'trash' => array(_x('Trash', 'page'), __('Trash pages'), _nx_noop('Trash <span class="count">(%s)</span>', 'Trash <span class="count">(%s)</span>', 'page'))
		);

	if ( !EMPTY_TRASH_DAYS )
		unset($post_stati['trash']);

	$post_stati = apply_filters('page_stati', $post_stati);
	$totalobjects = wp_count_posts('page');

	if ( isset( $_GET['post_status'] )) {
		$post_status = $_GET['post_status'];
	}	else {
		$post_status = 'all';
	}

	$objects = get_posts( array('post_type' => 'physical-object', 'numberposts' => 20, 'post_status' => $post_status));

	
	?>
	<div class="wrap">
		<?php # screen_icon(); ?>
		<h2><?php echo esc_html( $title ); ?></h2>
		<ul class="subsubsub">
		<?php

		$avail_post_stati = get_available_post_statuses('physical-object');
		if ( empty($locked_post_status) ) :
		$status_links = array();
		$num_posts = wp_count_posts('physical-object', 'readable');
		$total_posts = array_sum( (array) $num_posts ) - $num_posts->trash;
		$class = empty($_GET['post_status']) ? ' class="current"' : '';
		$status_links[] = "<li><a href='admin.php?page=objects/objects.php'$class>" . sprintf( _nx( 'All <span class="count">(%s)</span>', 'All <span class="count">(%s)</span>', $total_posts, 'pages' ), number_format_i18n( $total_posts ) ) . '</a>';
		foreach ( $post_stati as $status => $label ) {
			$class = '';

			if ( !in_array($status, $avail_post_stati) || $num_posts->$status <= 0 )
				continue;

			if ( isset( $_GET['post_status'] ) && $status == $_GET['post_status'] )
				$class = ' class="current"';

			$status_links[] = "<li><a href='admin.php?page=objects/objects.php&post_status=$status'$class>" . sprintf( _nx( $label[2][0], $label[2][1], $num_posts->$status, $label[2][2] ), number_format_i18n( $num_posts->$status ) ) . '</a>';
		}
		echo implode( " |</li>\n", $status_links ) . '</li>';
		unset($status_links);
		endif;
		?>
		</ul>	

		
		<form class="search-form" action="" method="get">
		<p class="search-box">
			<label class="screen-reader-text" for="object-search-input"><?php _e( 'Search Objects' ); ?>:</label>
			<input type="text" id="object-search-input" name="s" value="<?php _admin_search_query(); ?>" />
			<input type="submit" value="<?php esc_attr_e( 'Search Objects' ); ?>" class="button" />
		</p>
		</form>
		<br class="clear" />
		
		
		<div class="tablenav">
			<?php
			$page_links = paginate_links( array(
				'base' => add_query_arg( 'paged', '%#%' ),
				'format' => '',
				'prev_text' => __('&laquo;'),
				'next_text' => __('&raquo;'),
				'total' => $totalobjects,
				'current' => $_GET['paged']
			));

			?>
			
			<?php if ( $page_links ) { ?>
			<div class="tablenav-pages"><?php $page_links_text = sprintf( '<span class="displaying-num">' . __( 'Displaying %s&#8211;%s of %s' ) . '</span>%s',
				number_format_i18n( ( $_GET['paged'] - 1 ) * $wp_query->query_vars['posts_per_page'] + 1 ),
				number_format_i18n( min( $_GET['paged'] * $wp_query->query_vars['posts_per_page'], $wp_query->found_posts ) ),
				number_format_i18n( $wp_query->found_posts ),
				$page_links
			); echo $page_links_text; ?>
			</div>
			<?php } ?>
			
			<?php if ($objects) : ?>
			<table class="widefat post fixed" cellspacing="0">
				<thead>
					<tr>
							<th scope="col" id="cb" class="manage-column column-cb check-column" style=""><input type="checkbox" /></th>
							<th scope="col"><?php _e("Title"); ?></th>
							<th scope="col"><?php _e("Number"); ?></th>
						</tr>
					</tr>
				</thead>

				<tfoot>
					<tr>
						<th scope="col" id="cb" class="manage-column column-cb check-column" style=""><input type="checkbox" /></th>
						<th scope="col"><?php _e("Title"); ?></th>
						<th scope="col"><?php _e("Number"); ?></th>
					</tr>					
				</tfoot>

				<tbody>
					<?php
					foreach($objects as $object) {
						$class = $i % 2 == 0 ? " class='alternate'" : "";
					?>
					<tr id="object-<?php echo $objects['id']; ?>">
						<th scope="row" class="check-column"><input type="checkbox" name="object[]" value="1" /></th>
						<td><a href="admin.php?page=object-edit&amp;id=<?php echo  $object->ID; ?>" class="row-title"><?php echo $object->post_title; ?></a>
							
							<?php $actions = array();
							if ( current_user_can('edit_page', $page->ID) && $post->post_status != 'trash' ) {
								$actions['edit'] = '<a href="' . $edit_link . '" title="' . esc_attr(__('Edit this page')) . '">' . __('Edit') . '</a>';
								$actions['inline'] = '<a href="#" class="editinline">' . __('Quick&nbsp;Edit') . '</a>';
							}
							#	$actions['edit'] = '<a href="admin.php?page=object-edit&id=' . $object->ID . '" title="' . esc_attr(__('Edit this post')) . '">' . __('Edit') . '</a>';
								$actions['delete'] = "<a class='submitdelete' title='" . esc_attr(__('Delete this post')) . "' href='" . wp_nonce_url("admin.php?page=objects/objects.php&amp;action=delete&amp;id=".$object->ID, 'delete-post_' . $object->ID) . "' onclick=\"if ( confirm('" . esc_js(sprintf(__("You are about to delete the object '%s'\n 'Cancel' to stop, 'OK' to delete."), $object->object_title )) . "') ) { return true;}return false;\">" . __('Delete') . "</a>";
							

								$actions = apply_filters('post_row_actions', $actions, $post);
								$action_count = count($actions);
								$j = 0;
								echo '<div class="row-actions">';
								foreach ( $actions as $action => $link ) {
									++$j;
									( $j == $action_count ) ? $sep = '' : $sep = ' | ';
									echo "<span class='$action'>$link$sep</span>";
								}
								echo '</div>';
							
							?>
							
						</td>
						<td><?php #echo $object['object_name']; ?></td>
					</tr>
					<?php } ?>
				</tbody>
			</table>
			<?php endif; ?>
		</div>
	</div>
	<?php
}

function object_new_page() {
	if ($_POST['object_title']) {
		object_process_object($_POST);		
	}
	
	object_edit_form();
}

function object_admin_pages() {
	add_object_page(__("Object"), __("Objects"), 2, plugin_basename(__FILE__), "object_edit_page");
	add_submenu_page(plugin_basename(__FILE__), __("Edit"), __("Edit"), 2, "objects/objects.php", "object_edit_page", plugin_basename(__FILE__));
	add_submenu_page(plugin_basename(__FILE__), __("Add New"), __("Add New"), 2, "object-edit", "object_new_page");

	add_meta_box('pagesubmitdiv', __('Save'), 'post_submit_meta_box', 'object', 'side', 'core');

	add_meta_box('commentstatusdiv', __('Discussion'), 'post_comment_status_meta_box', 'object', 'normal', 'core');
	
	add_meta_box('postthumbnaildiv', __('Object Image'), 'post_thumbnail_meta_box', 'object', 'side', 'low');

	
	do_action('do_meta_boxes', 'object', 'normal');
	do_action('do_meta_boxes', 'object', 'advanced');
	do_action('do_meta_boxes', 'object', 'side');	

	// all tag-style post taxonomies
	foreach ( get_object_taxonomies('post') as $tax_name ) {
		if ( !is_taxonomy_hierarchical($tax_name) ) {
			$taxonomy = get_taxonomy($tax_name);
			$label = isset($taxonomy->label) ? esc_attr($taxonomy->label) : $tax_name;
			$label = "Object Tags";

		}
	}
	
	
}

function object_process_object($post_data) {
	global $wpdb, $current_user, $id;
		
	$post_data['post_content'] = isset($post_data['content']) ? $post_data['content'] : '';
	$post_data['post_title'] = isset($post_data['object_title']) ? $post_data['object_title'] : '';

	$post_data['post_status'] = 'publish';
	$post_data['post_type'] = 'physical-object';

	if (!isset( $post_data['comment_status'] ))
		$post_data['comment_status'] = 'closed';

	if(empty($post_data['id'])) {
		$id = wp_insert_post($post_data);
	}
	else {
		$post_data['ID'] = (int) $post_data['id'];
		$id = wp_insert_post($post_data);
		
	}	
}

function object_delete_object($id=null) {
	global $wpdb;
	
	if($id == null)
		$id = $wpdb->escape($_POST['id']);
		
	wp_trash_post($id);
}


add_action('admin_menu', 'object_admin_pages');



#add_action('activate_objects', "objects_install");



?>