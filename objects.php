<?php
/*
Plugin Name: Objects
Description: Manage your object collection with Wordpress
Version: 0.1
Author: Frankie Roberto
Author URI: http://www.frankieroberto.com
Tags: museums, collection, objects
*/

// Plugin DB Installation
function objects_install() {
#	get_currentuserinfo();
	
	global $wpdb;
	$table_name = $wpdb->prefix."objects";

	// Check if DB exists and add it if necessary
//	if($wpdb->get_var("SHOW TABLES LIKE '$table_name'") != $table_name) {
		$sql = "CREATE TABLE ".$table_name."(
			ID bigint(9) NOT NULL AUTO_INCREMENT,
			object_title text NOT NULL,
			object_content longtext NOT NULL,
			object_date datetime NOT NULL,
			object_modified datetime NOT NULL,
			object_name varchar(200) NOT NULL,
			object_status varchar(20) NOT NULL,
			comment_status varchar(20) NOT NULL,
			PRIMARY KEY  id (id)
		);";
	
		require_once(ABSPATH.'wp-admin/upgrade-functions.php');
		dbDelta($sql);
//	}


}
register_activation_hook(__FILE__, "objects_install");

function add_object_urls() {
	add_rewrite_rule('(collection)/([0-9]+)$', 'index.php?object_id=$matches[1]');
  add_rewrite_tag('%object_id%', '[0-9]+');	
}
add_action('init', 'add_object_urls');

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



function object_edit_form() {
	global $wpdb; 
	global $id;
	if (isset($_GET['id'])) {
		$id = $_GET['id'];
	} elseif (isset($_POST['id'])) {
		$id = $_POST['id'];
		$message = "Object updated.";
	}	
	
	if (isset($id)) {
		
		$sql = "SELECT * "
		."FROM " . $wpdb->prefix."objects"
		." WHERE ID=".$wpdb->escape($id) .";";
		$objects = $wpdb->get_results($sql, ARRAY_A);
		$object = $objects[0];
		$object_id = $object['ID'];

	}
	get_currentuserinfo();
	global $user_ID;	

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
									<input type="text" name="object_title" size="30" tabindex="1" value="<?php echo esc_attr( htmlspecialchars( $object['object_title'] ) ); ?>" id="title" autocomplete="off" />			
								</div>
								<div class="inside">
									<div id="edit-slug-box">
										<strong>Permalink:</strong>
										<span id="sample-permalink">
											http://www.blah.com/objects/<?php echo $object['object_name']; ?>
										</span>
									</div>
								</div>
							</div>
							
							<div id="postdivrich" class="postarea">
								<?php the_editor($object['object_content'], "content", "titlediv", true); ?>

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

	// Check how many objects there are
	$sql = "SELECT ID FROM ".$wpdb->prefix."objects";
	$wpdb->query($sql);
	$totalobjects = $wpdb->num_rows;

	// Get objects for this page
	$sql = "SELECT id, object_title, object_name, object_status"
		 ." FROM ".$wpdb->prefix."objects"
		 ." ORDER BY object_date DESC";
		$wpdb->show_errors();
	$objects = $wpdb->get_results($sql, ARRAY_A);
	
	
	?>
	<div class="wrap">
		<?php # screen_icon(); ?>
		<h2><?php echo esc_html( $title ); ?></h2>
	
	
		<ul class="subsubsub">
		<?php
			$status_links = array();
			$num_posts = $totalobjects;
			$total_posts = array_sum( (array) $num_posts );
			$class = empty( $_GET['object_status'] ) ? ' class="current"' : '';
			$status_links[] = "<li><a href='admin.php?page=objects/objects.php' $class>" . sprintf( _nx( 'All <span class="count">(%s)</span>', 'All <span class="count">(%s)</span>', $total_posts, 'posts' ), number_format_i18n( $total_posts ) ) . '</a>';

			$status_links[] = "<li><a href='admin.php?page=objects/objects.php&amp;post_status=draft'>" . sprintf( _n("Draft", "Drafts", 10), number_format_i18n(10) ) . '</a>';
			

			echo implode( " |</li>\n", $status_links ) . '</li>';
			unset( $status_links );
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
						<td><a href="admin.php?page=object-edit&amp;id=<?php echo  $object['id'] ?>" class="row-title"><?php echo $object['object_title']; ?></a>
							
							<?php $actions = array();
								$actions['edit'] = '<a href="admin.php?page=object-edit&id=' . $object['id'] . '" title="' . esc_attr(__('Edit this post')) . '">' . __('Edit') . '</a>';
								$actions['delete'] = "<a class='submitdelete' title='" . esc_attr(__('Delete this post')) . "' href='" . wp_nonce_url("admin.php?page=objects/objects.php&amp;action=delete&amp;id=".$object['id'], 'delete-post_' . $object['id']) . "' onclick=\"if ( confirm('" . esc_js(sprintf(__("You are about to delete the object '%s'\n 'Cancel' to stop, 'OK' to delete."), $object['object_title'] )) . "') ) { return true;}return false;\">" . __('Delete') . "</a>";
							

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
						<td><?php echo $object['object_name']; ?></td>
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

	add_meta_box('pagesubmitdiv', __('Save'), 'object_submit_meta_box', 'object', 'side', 'core');
	
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

function object_process_object($postvars) {
	global $wpdb, $current_user;
	
	$object_title = addslashes($postvars['object_title']);
	$object_content = addslashes($postvars['content']);
	
	$tbl_name = $wpdb->prefix."objects";

	if(empty($postvars['id'])) {
		$insert = "INSERT INTO ".$tbl_name.
				  " (object_title, object_content, object_date, object_modified) ".
				  "VALUES('".$wpdb->escape($object_title)."',
				  		'".$wpdb->escape($object_content)."',
						  '".$wpdb->escape($create_mod_time)."',
						  '".$wpdb->escape($create_mod_time)."');";
		$results = $wpdb->query($insert);
	}
	else {
		$update = "UPDATE ".$tbl_name.
			  	  " SET object_title='".$wpdb->escape($object_title)."',".
				  "object_content='".$wpdb->escape($object_content)."',".
				  "object_modified='".$wpdb->escape($create_mod_time)."' ".
				  "WHERE id=".$wpdb->escape($postvars['id']);
		$results = $wpdb->query($update);
		
		$id = $wpdb->insert_id;
		
	}	
}

function object_delete_object($id=null) {
	global $wpdb;
	
	if($id == null)
		$id = $wpdb->escape($_POST['id']);
		
	$tbl_name = $wpdb->prefix."objects";
	
	$sql = "DELETE FROM ".$tbl_name." WHERE id=".$id;
	$wpdb->query($sql);
}


add_action('admin_menu', 'object_admin_pages');



#add_action('activate_objects', "objects_install");



?>