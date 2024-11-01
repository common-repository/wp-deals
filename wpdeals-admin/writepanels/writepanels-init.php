<?php
/**
 * WPDeals Write Panels
 * 
 * Sets up the write panels used by deals and sales (custom post types)
 *
 * @author 		Tokokoo
 * @category 	Admin Write Panels
 * @package 	WPDeals
 */

require_once('writepanel-daily-deals_data.php');
require_once('writepanel-order_data.php');
require_once('writepanel-order_notes.php');
//require_once('writepanel-coupon_data.php');

/**
 * Init the meta boxes
 * 
 * Inits the write panels for both deals and sales. Also removes unused default write panels.
 */
add_action( 'add_meta_boxes', 'wpdeals_meta_boxes' );

function wpdeals_meta_boxes() {
	
	// Deals
	add_meta_box( 'wpdeals-daily-deals-type', __('Daily Deals Data', 'wpdeals'), 'wpdeals_deals_type_box', 'daily-deals', 'normal', 'high' );
	
	// Orders
	add_meta_box( 'wpdeals-order-data', __('Order Data', 'wpdeals'), 'wpdeals_order_data_meta_box', 'deals-sales', 'normal', 'high' );
	add_meta_box( 'wpdeals-order-items', __('Order Items <small>&ndash; Note: if you edit quantities or remove items from the order you will need to manually change the item\'s stock levels.</small>', 'wpdeals'), 'wpdeals_order_items_meta_box', 'deals-sales', 'normal', 'high');
	add_meta_box( 'wpdeals-order-notes', __('Order Notes', 'wpdeals'), 'wpdeals_order_notes_meta_box', 'deals-sales', 'side', 'default');
	add_meta_box( 'wpdeals-order-actions', __('Order Actions', 'wpdeals'), 'wpdeals_order_actions_meta_box', 'deals-sales', 'side', 'high');
	
	remove_meta_box( 'commentsdiv', 'deals-sales' , 'normal' );
	remove_meta_box( 'wpdeals-settings', 'deals-sales' , 'normal' );
	remove_meta_box( 'commentstatusdiv', 'deals-sales' , 'normal' );
	remove_meta_box( 'slugdiv', 'deals-sales' , 'normal' );
	
	// Coupons
	add_meta_box( 'wpdeals-coupon-data', __('Coupon Data', 'wpdeals'), 'wpdeals_coupon_data_meta_box', 'store_coupon', 'normal', 'high');
	
	remove_meta_box( 'wpdeals-settings', 'store_coupon' , 'normal' );
	remove_meta_box( 'commentstatusdiv', 'store_coupon' , 'normal' );
	remove_meta_box( 'slugdiv', 'store_coupon' , 'normal' );
	
	//remove_meta_box('pageparentdiv', 'deal-variations', 'side');
	//add_meta_box('deal-variations-parent', __('Deal', 'wpdeals'), 'variations_deals_meta_box', 'deal-variations', 'side', 'default');
}

/**
 * Title boxes
 */
add_filter('enter_title_here', 'wpdeals_enter_title_here', 1, 2);

function wpdeals_enter_title_here( $text, $post ) {
	if ($post->post_type=='store_coupon') return __('Coupon code', 'wpdeals');
	if ($post->post_type=='daily-deals') return __('Deal name', 'wpdeals');
	return $text;
}

/**
 * Save meta boxes
 * 
 * Runs when a post is saved and does an action which the write panel save scripts can hook into.
 */
add_action( 'save_post', 'wpdeals_meta_boxes_save', 1, 2 );

function wpdeals_meta_boxes_save( $post_id, $post ) {
	global $wpdb;
	
	if ( !$_POST ) return $post_id;
	if ( defined('DOING_AUTOSAVE') && DOING_AUTOSAVE ) return $post_id;
	if ( !isset($_POST['wpdeals_meta_nonce']) || (isset($_POST['wpdeals_meta_nonce']) && !wp_verify_nonce( $_POST['wpdeals_meta_nonce'], 'wpdeals_save_data' ))) return $post_id;
	if ( !current_user_can( 'edit_post', $post_id )) return $post_id;
	if ( $post->post_type != 'daily-deals' && $post->post_type != 'deals-sales' && $post->post_type != 'store_coupon' ) return $post_id;
	
	do_action( 'wpdeals_process_'.$post->post_type.'_meta', $post_id, $post );
}

/**
 * Deal data
 * 
 * Forces certain deals data based on the deals's type, e.g. grouped deals cannot have a parent.
 */
add_filter('wp_insert_post_data', 'wpdeals_deals_data');

function wpdeals_deals_data( $data ) {
	global $post;
	if ($data['post_type']=='daily-deals' && isset($_POST['daily-deals-type'])) {
		$deal_type = stripslashes( $_POST['daily-deals-type'] );
		switch($deal_type) :
			case "grouped" :
			case "variable" :
				$data['post_parent'] = 0;
			break;
		endswitch;
	}
	return $data;
}

/**
 * Order data
 * 
 * Forces the order posts to have a title in a certain format (containing the date)
 */
add_filter('wp_insert_post_data', 'wpdeals_order_data');

function wpdeals_order_data( $data ) {
	global $post;
	if ($data['post_type']=='deals-sales' && isset($data['post_date'])) {
		
		$order_title = 'Order';
		if ($data['post_date']) $order_title.= ' &ndash; '.date('F j, Y @ h:i A', strtotime($data['post_date']));
		
		$data['post_title'] = $order_title;
	}
	return $data;
}


/**
 * Save errors
 * 
 * Stores error messages in a variable so they can be displayed on the edit post screen after saving.
 */
add_action( 'admin_notices', 'wpdeals_meta_boxes_save_errors' );

function wpdeals_meta_boxes_save_errors() {
	$wpdeals_errors = maybe_unserialize(get_option('wpdeals_errors'));
    if ($wpdeals_errors && sizeof($wpdeals_errors)>0) :
    	echo '<div id="wpdeals_errors" class="error fade">';
    	foreach ($wpdeals_errors as $error) :
    		echo '<p>'.$error.'</p>';
    	endforeach;
    	echo '</div>';
    	update_option('wpdeals_errors', '');
    endif; 
}

/**
 * Meta scripts
 * 
 * Outputs JavaScript used by the meta panels.
 */
function wpdeals_meta_scripts() {
	?>
	<script type="text/javascript">
		jQuery(function(){
			<?php do_action('wpdeals_deals_write_panel_js'); ?>
		});
	</script>
	<?php
}

/**
 * Output write panel form elements
 */
function wpdeals_wp_text_input( $field ) {
	global $thepostid, $post;
	
	if (!$thepostid) $thepostid = $post->ID;
	if (!isset($field['placeholder'])) $field['placeholder'] = '';
	if (!isset($field['class'])) $field['class'] = 'short';
	if (!isset($field['value'])) $field['value'] = get_post_meta($thepostid, $field['id'], true);
	
	echo '<p class="form-field '.$field['id'].'_field"><label for="'.$field['id'].'">'.$field['label'].'</label><input type="text" class="'.$field['class'].'" name="'.$field['id'].'" id="'.$field['id'].'" value="'.esc_attr( $field['value'] ).'" placeholder="'.$field['placeholder'].'" /> ';
	
	if (isset($field['description'])) echo '<span class="description">' .$field['description'] . '</span>';
		
	echo '</p>';
}

function wpdeals_wp_text_area( $field ) {
	global $thepostid, $post;
	
	if (!$thepostid) $thepostid = $post->ID;
	if (!isset($field['class'])) $field['class'] = 'short';
	if (!isset($field['value'])) $field['value'] = get_post_meta($thepostid, $field['id'], true);
	
	echo '<p class="form-field '.$field['id'].'_field"><label for="'.$field['id'].'">'.$field['label'].'</label><textarea type="text" class="'.$field['class'].'" name="'.$field['id'].'" id="'.$field['id'].'" />'.esc_attr( $field['value'] ).'</textarea>';
	
	if (isset($field['description'])) echo '<span class="description">' .$field['description'] . '</span>';
		
	echo '</p>';
}

function wpdeals_wp_checkbox( $field ) {
	global $thepostid, $post;
	
	if (!$thepostid) $thepostid = $post->ID;
	if (!isset($field['class'])) $field['class'] = 'checkbox';
	if (!isset($field['wrapper_class'])) $field['wrapper_class'] = '';
	if (!isset($field['value'])) $field['value'] = get_post_meta($thepostid, $field['id'], true);
	
	echo '<p class="form-field '.$field['id'].'_field '.$field['wrapper_class'].'"><label for="'.$field['id'].'">'.$field['label'].'</label><input type="checkbox" class="'.$field['class'].'" name="'.$field['id'].'" id="'.$field['id'].'" ';
	
	checked($field['value'], 'yes');
	
	echo ' /> ';
	
	if (isset($field['description'])) echo '<span class="description">' .$field['description'] . '</span>';
		
	echo '</p>';
}

function wpdeals_wp_select( $field ) {
	global $thepostid, $post;
	
	if (!$thepostid) $thepostid = $post->ID;
	if (!isset($field['class'])) $field['class'] = 'select short';
	if (!isset($field['value'])) $field['value'] = get_post_meta($thepostid, $field['id'], true);
	
	echo '<p class="form-field '.$field['id'].'_field"><label for="'.$field['id'].'">'.$field['label'].'</label><select id="'.$field['id'].'" name="'.$field['id'].'" class="'.$field['class'].'">';
	
	foreach ($field['options'] as $key => $value) :
		
		echo '<option value="'.$key.'" ';
		selected($field['value'], $key);
		echo '>'.$value.'</option>';
		
	endforeach;
	
	echo '</select> ';
	
	if (isset($field['description'])) echo '<span class="description">' .$field['description'] . '</span>';
		
	echo '</p>';
}
