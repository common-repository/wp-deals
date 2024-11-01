<?php
/**
 * Functions used for taxonomies in admin 
 *
 * These functions control admin interface bits like category ordering.
 *
 * @author 		Tokokoo
 * @category 	Admin
 * @package 	WPDeals
 */

/**
 * Category thumbnails
 */
add_action('deal_category_add_form_fields', 'wpdeals_add_category_thumbnail_field');
add_action('deal_category_edit_form_fields', 'wpdeals_edit_category_thumbnail_field', 10,2);

function wpdeals_add_category_thumbnail_field() {
	global $wpdeals;
	?>
	<div class="form-field">
		<label><?php _e('Thumbnail', 'wpdeals'); ?></label>
		<div id="deal_category_thumbnail" style="float:left;margin-right:10px;"><img src="<?php echo $wpdeals->plugin_url().'/wpdeals-assets/images/placeholder.png' ?>" width="60px" height="60px" /></div>
		<div style="line-height:60px;">
			<input type="hidden" id="deal_category_thumbnail_id" name="deal_category_thumbnail_id" />
			<button type="submit" class="upload_image_button button"><?php _e('Upload/Add image', 'wpdeals'); ?></button>
			<button type="submit" class="remove_image_button button"><?php _e('Remove image', 'wpdeals'); ?></button>
		</div>
		<script type="text/javascript">
			
				window.send_to_termmeta = function(html) {
					
					jQuery('body').append('<div id="temp_image">' + html + '</div>');
					
					var img = jQuery('#temp_image').find('img');
					
					imgurl 		= img.attr('src');
					imgclass 	= img.attr('class');
					imgid		= parseInt(imgclass.replace(/\D/g, ''), 10);
					
					jQuery('#deal_category_thumbnail_id').val(imgid);
					jQuery('#deal_category_thumbnail img').attr('src', imgurl);
					jQuery('#temp_image').remove();
					
					tb_remove();
				}
				
				jQuery('.upload_image_button').live('click', function(){
					var post_id = 0;
					
					window.send_to_editor = window.send_to_termmeta;
					
					tb_show('', 'media-upload.php?post_id=' + post_id + '&amp;type=image&amp;TB_iframe=true');
					return false;
				});
				
				jQuery('.remove_image_button').live('click', function(){
					jQuery('#deal_category_thumbnail img').attr('src', '<?php echo $wpdeals->plugin_url().'/wpdeals-assets/images/placeholder.png'; ?>');
					jQuery('#deal_category_thumbnail_id').val('');
					return false;
				});
			
		</script>
		<div class="clear"></div>
	</div>
	<?php
}

function wpdeals_edit_category_thumbnail_field( $term, $taxonomy ) {
	global $wpdeals;
	
	$image 			= '';
	$thumbnail_id 	= get_wpdeals_term_meta( $term->term_id, 'thumbnail_id', true );
	if ($thumbnail_id) :
		$image = wp_get_attachment_url( $thumbnail_id );
	else :
		$image = $wpdeals->plugin_url().'/wpdeals-assets/images/placeholder.png';
	endif;
	?>
	<tr class="form-field">
		<th scope="row" valign="top"><label><?php _e('Thumbnail', 'wpdeals'); ?></label></th>
		<td>
			<div id="deal_category_thumbnail" style="float:left;margin-right:10px;"><img src="<?php echo $image; ?>" width="60px" height="60px" /></div>
			<div style="line-height:60px;">
				<input type="hidden" id="deal_category_thumbnail_id" name="deal_category_thumbnail_id" value="<?php echo $thumbnail_id; ?>" />
				<button type="submit" class="upload_image_button button"><?php _e('Upload/Add image', 'wpdeals'); ?></button>
				<button type="submit" class="remove_image_button button"><?php _e('Remove image', 'wpdeals'); ?></button>
			</div>
			<script type="text/javascript">
				
				window.send_to_termmeta = function(html) {
					
					jQuery('body').append('<div id="temp_image">' + html + '</div>');
					
					var img = jQuery('#temp_image').find('img');
					
					imgurl 		= img.attr('src');
					imgclass 	= img.attr('class');
					imgid		= parseInt(imgclass.replace(/\D/g, ''), 10);
					
					jQuery('#deal_category_thumbnail_id').val(imgid);
					jQuery('#deal_category_thumbnail img').attr('src', imgurl);
					jQuery('#temp_image').remove();
					
					tb_remove();
				}
				
				jQuery('.upload_image_button').live('click', function(){
					var post_id = 0;
					
					window.send_to_editor = window.send_to_termmeta;
					
					tb_show('', 'media-upload.php?post_id=' + post_id + '&amp;type=image&amp;TB_iframe=true');
					return false;
				});
				
				jQuery('.remove_image_button').live('click', function(){
					jQuery('#deal_category_thumbnail img').attr('src', '<?php echo $wpdeals->plugin_url().'/wpdeals-assets/images/placeholder.png'; ?>');
					jQuery('#deal_category_thumbnail_id').val('');
					return false;
				});
				
			</script>
			<div class="clear"></div>
		</td>
	</tr>
	<?php
}

add_action('created_term', 'wpdeals_category_thumbnail_field_save', 10,3);
add_action('edit_term', 'wpdeals_category_thumbnail_field_save', 10,3);

function wpdeals_category_thumbnail_field_save( $term_id, $tt_id, $taxonomy ) {
	if (isset($_POST['deal_category_thumbnail_id'])) {
		update_wpdeals_term_meta($term_id, 'thumbnail_id', $_POST['deal_category_thumbnail_id']);
    }
}


/**
 * Category/Term ordering
 */

/**
 * Reorder on term insertion
 */
add_action("create_term", 'wpdeals_create_term', 5, 3);

function wpdeals_create_term( $term_id, $tt_id, $taxonomy ) {
	
	if (!$taxonomy=='deal-categories' && !strstr($taxonomy, 'pa_')) return;
	
	$next_id = null;
	
	$term = get_term($term_id, $taxonomy);
	
	// gets the sibling terms
	$siblings = get_terms($taxonomy, "parent={$term->parent}&menu_order=ASC&hide_empty=0");
	
	foreach ($siblings as $sibling) {
		if( $sibling->term_id == $term_id ) continue;
		$next_id =  $sibling->term_id; // first sibling term of the hierarchy level
		break;
	}

	// reorder
	wpdeals_order_terms( $term, $next_id, $taxonomy );
}

/**
 * Delete terms metas on deletion
 */
add_action("delete_deals_term", 'wpdeals_delete_term', 5, 3);

function wpdeals_delete_term( $term_id, $tt_id, $taxonomy ) {
	
	$term_id = (int) $term_id;
	
	if(!$term_id) return;
	
	global $wpdb;
	$wpdb->query("DELETE FROM {$wpdb->wpdeals_termmeta} WHERE `wpdeals_term_id` = " . $term_id);
	
}

/**
 * Move a term before the a	given element of its hierarchy level
 *
 * @param object $the_term
 * @param int $next_id the id of the next slibling element in save hierachy level
 * @param int $index
 * @param int $terms
 */
function wpdeals_order_terms( $the_term, $next_id, $taxonomy, $index=0, $terms=null ) {
	
	if( ! $terms ) $terms = get_terms($taxonomy, 'menu_order=ASC&hide_empty=0&parent=0');
	if( empty( $terms ) ) return $index;
	
	$id	= $the_term->term_id;
	
	$term_in_level = false; // flag: is our term to order in this level of terms
	
	foreach ($terms as $term) {
		
		if( $term->term_id == $id ) { // our term to order, we skip
			$term_in_level = true;
			continue; // our term to order, we skip
		}
		// the nextid of our term to order, lets move our term here
		if(null !== $next_id && $term->term_id == $next_id) { 
			$index++;
			$index = wpdeals_set_term_order($id, $index, $taxonomy, true);
		}		
		
		// set order
		$index++;
		$index = wpdeals_set_term_order($term->term_id, $index, $taxonomy);
		
		// if that term has children we walk through them
		$children = get_terms($taxonomy, "parent={$term->term_id}&menu_order=ASC&hide_empty=0");
		if( !empty($children) ) {
			$index = wpdeals_order_terms( $the_term, $next_id, $taxonomy, $index, $children );	
		}
	}
	
	// no nextid meaning our term is in last position
	if( $term_in_level && null === $next_id )
		$index = wpdeals_set_term_order($id, $index+1, $taxonomy, true);
	
	return $index;
	
}

/**
 * Set the sort order of a term
 * 
 * @param int $term_id
 * @param int $index
 * @param bool $recursive
 */
function wpdeals_set_term_order($term_id, $index, $taxonomy, $recursive=false) {
	global $wpdb;
	
	$term_id 	= (int) $term_id;
	$index 		= (int) $index;
	
	// Meta name
	if (strstr($taxonomy, 'pa_')) :
		$meta_name =  'order_' . esc_attr($taxonomy);
	else :
		$meta_name = 'order';
	endif;
	
	update_wpdeals_term_meta( $term_id, $meta_name, $index );
	
	if( ! $recursive ) return $index;
	
	$children = get_terms($taxonomy, "parent=$term_id&menu_order=ASC&hide_empty=0");

	foreach ( $children as $term ) {
		$index ++;
		$index = wpdeals_set_term_order($term->term_id, $index, $taxonomy, true);		
	}
	
	return $index;
}


/**
 * Description for deal-categories page
 */
add_action('deal_category_pre_add_form', 'wpdeals_deal_category_description');

function wpdeals_deal_category_description() {

	echo wpautop(__('Deal categories for your store can be managed here. To change the order of categories on the front-end you can drag and drop to sort them. To see more categories listed click the "screen options" link at the top of the page.', 'wpdeals'));

}


/**
 * Fix for per_page option
 * Trac: http://core.trac.wordpress.org/ticket/19465
 */
add_filter('edit_posts_per_page', 'wpdeals_fix_edit_posts_per_page', 1, 2);

function wpdeals_fix_edit_posts_per_page( $per_page, $post_type ) {
	
	if ($post_type!=='daily-deals') return $per_page;
	
	$screen = get_current_screen();
	
	if (strstr($screen->id, '-')) {
	
		$option = 'edit_' . str_replace('edit-', '', $screen->id) . '_per_page';
		
		if (isset($_POST['wp_screen_options']['option']) && $_POST['wp_screen_options']['option'] == $option ) :
			
			update_user_meta( get_current_user_id(), $option, $_POST['wp_screen_options']['value'] );
			
			wp_redirect( remove_query_arg( array('pagenum', 'apage', 'paged'), wp_get_referer() ) );
			exit;

		endif;
		
		$user_per_page = (int) get_user_meta( get_current_user_id(), $option, true );
		
		if ($user_per_page) $per_page = $user_per_page;
		
	}
	
	return $per_page;
	
}

