<?php
/**
 * Coupon Data
 * 
 * Functions for displaying the coupon data meta box
 *
 * @author 		Tokokoo
 * @category 	Admin Write Panels
 * @package 	WPDeals
 */

/**
 * Coupon data meta box
 * 
 * Displays the meta box
 */
function wpdeals_coupon_data_meta_box($post) {
	global $wpdeals;
	
	wp_nonce_field( 'wpdeals_save_data', 'wpdeals_meta_nonce' );
	
	?>
	<style type="text/css">
		#edit-slug-box { display:none }
	</style>
	<div id="coupon_options" class="panel wpdeals_options_panel">
		<?php

			// Type
    		wpdeals_wp_select( array( 'id' => 'discount_type', 'label' => __('Discount type', 'wpdeals'), 'options' => $wpdeals->get_coupon_discount_types() ) );
				
			// Amount
			wpdeals_wp_text_input( array( 'id' => 'coupon_amount', 'label' => __('Coupon amount', 'wpdeals'), 'placeholder' => __('0.00', 'wpdeals'), 'description' => __('Enter an amount e.g. 2.99', 'wpdeals') ) );
				
			// Individual use
			wpdeals_wp_checkbox( array( 'id' => 'individual_use', 'label' => __('Individual use', 'wpdeals'), 'description' => __('Check this box if the coupon cannot be used in conjunction with other coupons', 'wpdeals') ) );
			
			// Apply before tax
			wpdeals_wp_checkbox( array( 'id' => 'apply_before_tax', 'label' => __('Apply before tax', 'wpdeals'), 'description' => __('Check this box if the coupon should be applied before calculating cart tax', 'wpdeals') ) );
			
			// Free Shipping
			wpdeals_wp_checkbox( array( 'id' => 'free_shipping', 'label' => __('Enable free shipping', 'wpdeals'), 'description' => sprintf(__('Check this box if the coupon enables free shipping (see <a href="%s">Free Shipping</a>)', 'wpdeals'), admin_url('admin.php?page=wpdeals&tab=shipping_methods&subtab=shipping-free_shipping')) ) );
			
			// Deal ids
			wpdeals_wp_text_input( array( 'id' => 'deal_ids', 'label' => __('Deal IDs', 'wpdeals'), 'placeholder' => __('N/A', 'wpdeals'), 'description' => __('(optional) Comma separate IDs which need to be in the cart to use this coupon or, for "Deal Discounts", which deals are discounted.', 'wpdeals') ) );
			
			// Exclude Deal ids
			wpdeals_wp_text_input( array( 'id' => 'exclude_deals_ids', 'label' => __('Exclude Deal IDs', 'wpdeals'), 'placeholder' => __('N/A', 'wpdeals'), 'description' => __('(optional) Comma separate IDs which must not be in the cart to use this coupon or, for "Deal Discounts", which deals are not discounted.', 'wpdeals') ) );
			
			// Usage limit
			wpdeals_wp_text_input( array( 'id' => 'usage_limit', 'label' => __('Usage limit', 'wpdeals'), 'placeholder' => __('Unlimited usage', 'wpdeals'), 'description' => __('(optional) How many times this coupon can be used before it is void', 'wpdeals') ) );
				
			// Expiry date
			wpdeals_wp_text_input( array( 'id' => 'expiry_date', 'label' => __('Expiry date', 'wpdeals'), 'placeholder' => __('Never expire', 'wpdeals'), 'description' => __('(optional) The date this coupon will expire, <code>YYYY-MM-DD</code>', 'wpdeals'), 'class' => 'short date-picker' ) );
			
			do_action('wpdeals_coupon_options');
			
		?>
	</div>
	<?php	
}

/**
 * Coupon Data Save
 * 
 * Function for processing and storing all coupon data.
 */
add_action('wpdeals_process_store_coupon_meta', 'wpdeals_process_store_coupon_meta', 1, 2);

function wpdeals_process_store_coupon_meta( $post_id, $post ) {
	global $wpdb;
	
	$wpdeals_errors = array();
	
	// Add/Replace data to array
		$type 			= strip_tags(stripslashes( $_POST['discount_type'] ));
		$amount 		= strip_tags(stripslashes( $_POST['coupon_amount'] ));
		$deal_ids 	= strip_tags(stripslashes( $_POST['deal_ids'] ));
		$exclude_deals_ids = strip_tags(stripslashes( $_POST['exclude_deals_ids'] ));
		$usage_limit 	= (isset($_POST['usage_limit']) && $_POST['usage_limit']>0) ? (int) $_POST['usage_limit'] : '';
		$individual_use = isset($_POST['individual_use']) ? 'yes' : 'no';
		$expiry_date 	= strip_tags(stripslashes( $_POST['expiry_date'] ));
		$apply_before_tax = isset($_POST['apply_before_tax']) ? 'yes' : 'no';
		$free_shipping = isset($_POST['free_shipping']) ? 'yes' : 'no';
	
	// Save
		update_post_meta( $post_id, 'discount_type', $type );
		update_post_meta( $post_id, 'coupon_amount', $amount );
		update_post_meta( $post_id, 'individual_use', $individual_use );
		update_post_meta( $post_id, 'deal_ids', $deal_ids );
		update_post_meta( $post_id, 'exclude_deals_ids', $exclude_deals_ids );
		update_post_meta( $post_id, 'usage_limit', $usage_limit );
		update_post_meta( $post_id, 'expiry_date', $expiry_date );
		update_post_meta( $post_id, 'apply_before_tax', $apply_before_tax );
		update_post_meta( $post_id, 'free_shipping', $free_shipping );
		
		do_action('wpdeals_coupon_options');
	
	// Error Handling
		if (sizeof($wpdeals_errors)>0) update_option('wpdeals_errors', $wpdeals_errors);
}