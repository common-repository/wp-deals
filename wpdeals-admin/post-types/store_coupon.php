<?php
/**
 * Admin functions for the store_coupon post type
 *
 * @author 		Tokokoo
 * @category 	Admin
 * @package 	WPDeals
 */
 
/**
 * Columns for Coupons page
 **/
add_filter('manage_edit-store_coupon_columns', 'wpdeals_edit_coupon_columns');

function wpdeals_edit_coupon_columns($columns){
	
	$columns = array();
	
	$columns["cb"] 			= "<input type=\"checkbox\" />";
	$columns["title"] 		= __("Code", 'wpdeals');
	$columns["type"] 		= __("Coupon type", 'wpdeals');
	$columns["amount"] 		= __("Coupon amount", 'wpdeals');
	$columns["daily-deals"]	= __("Deal IDs", 'wpdeals');
	$columns["usage_limit"] = __("Usage limit", 'wpdeals');
	$columns["usage_count"] = __("Usage count", 'wpdeals');
	$columns["expiry_date"] = __("Expiry date", 'wpdeals');

	return $columns;
}


/**
 * Custom Columns for Coupons page
 **/
add_action('manage_store_coupon_posts_custom_column', 'wpdeals_custom_coupon_columns', 2);

function wpdeals_custom_coupon_columns($column) {
	global $post, $wpdeals;
	
	$type 			= get_post_meta($post->ID, 'discount_type', true);
	$amount 		= get_post_meta($post->ID, 'coupon_amount', true);
	$individual_use = get_post_meta($post->ID, 'individual_use', true);
	$deal_ids 	= (get_post_meta($post->ID, 'deal_ids', true)) ? explode(',', get_post_meta($post->ID, 'deal_ids', true)) : array();
	$usage_limit 	= get_post_meta($post->ID, 'usage_limit', true);
	$usage_count 	= (int) get_post_meta($post->ID, 'usage_count', true);
	$expiry_date 	= get_post_meta($post->ID, 'expiry_date', true);

	switch ($column) {
		case "type" :
			echo $wpdeals->get_coupon_discount_type($type);			
		break;
		case "amount" :
			echo $amount;
		break;
		case "daily-deals" :
			if (sizeof($deal_ids)>0) echo implode(', ', $deal_ids); else echo '&ndash;';
		break;
		case "usage_limit" :
			if ($usage_limit) echo $usage_limit; else echo '&ndash;';
		break;
		case "usage_count" :
			echo $usage_count;
		break;
		case "expiry_date" :
			if ($expiry_date) echo date('F j, Y', strtotime($expiry_date)); else echo '&ndash;';
		break;
	}
}
