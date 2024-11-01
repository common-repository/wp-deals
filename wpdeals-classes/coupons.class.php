<?php
/**
 * WPDeals coupons
 * 
 * The WPDeals coupons class gets coupon data from storage
 *
 * @class 		wpdeals_coupon
 * @package		WPDeals
 * @category	Class
 * @author		Tokokoo
 */
class wpdeals_coupon {
	
	var $code;
	var $id;
	var $type;
	var $amount;
	var $individual_use;
	var $deal_ids;
	var $usage_limit;
	var $usage_count;
	var $expiry_date;
	var $apply_before_tax;
	var $free_shipping;
	
	/** get coupon with $code */
	function wpdeals_coupon( $code ) {
		
		$this->code = $code;
		
		$coupon = get_page_by_title( $this->code, 'OBJECT', 'store_coupon' );
		
		if ($coupon && $coupon->post_status == 'publish') :
			
			$this->id					= $coupon->ID;
			$this->type 				= get_post_meta($coupon->ID, 'discount_type', true);
			$this->amount 				= get_post_meta($coupon->ID, 'coupon_amount', true);
			$this->individual_use 		= get_post_meta($coupon->ID, 'individual_use', true);
			$this->deal_ids 			= array_filter(array_map('trim', explode(',', get_post_meta($coupon->ID, 'deal_ids', true))));
			$this->exclude_deals_ids	= array_filter(array_map('trim', explode(',', get_post_meta($coupon->ID, 'exclude_deals_ids', true))));
			$this->usage_limit 			= get_post_meta($coupon->ID, 'usage_limit', true);
			$this->usage_count 			= (int) get_post_meta($coupon->ID, 'usage_count', true);
			$this->expiry_date 			= ($expires = get_post_meta($coupon->ID, 'expiry_date', true)) ? strtotime($expires) : '';
			$this->apply_before_tax 	= get_post_meta($coupon->ID, 'apply_before_tax', true);
			$this->free_shipping 		= get_post_meta($coupon->ID, 'free_shipping', true);
			
			return true;
			
		endif;
		
		return false;
	}
	
	/** Check if coupon needs applying before tax **/
	function apply_before_tax() {
		if ($this->apply_before_tax=='yes') return true; else return false;
	}
	
	function enable_free_shipping() {
		if ($this->free_shipping=='yes') return true; else return false;
	}
	
	/** Increase usage count */
	function inc_usage_count() {
		$this->usage_count++;
		update_post_meta($this->id, 'usage_count', $this->usage_count);
	}
	
	/** Check coupon is valid */
	function is_valid() {
		
		global $wpdeals;
				
		if ($this->id) :
			
			if ($this->usage_limit>0) :
				if ($this->usage_count>=$this->usage_limit) :
					return false;
				endif;
			endif;
			
			if ($this->expiry_date) :
				if (strtotime('NOW')>$this->expiry_date) :
					return false;
				endif;
			endif;
			
			// Deal ids - If a deals included is found in the cart then its valid
			if (sizeof( $this->deal_ids )>0) :
				$valid = false;
				if (sizeof($wpdeals->cart->get_cart())>0) : foreach ($wpdeals->cart->get_cart() as $cart_item_key => $cart_item) :
					if (in_array($cart_item['deal_id'], $this->deal_ids) || in_array($cart_item['variation_id'], $this->deal_ids)) :
						$valid = true;
					endif;
				endforeach; endif;
				if (!$valid) return false;
			endif;
			
			// Cart discounts cannot be added if non-eligble deals is found in cart
			if ($this->type!='fixed_deals' && $this->type!='percent_deals') : 

				if (sizeof( $this->exclude_deals_ids )>0) :
					$valid = true;
					if (sizeof($wpdeals->cart->get_cart())>0) : foreach ($wpdeals->cart->get_cart() as $cart_item_key => $cart_item) :
						if (in_array($cart_item['deal_id'], $this->exclude_deals_ids) || in_array($cart_item['variation_id'], $this->exclude_deals_ids)) :
							$valid = false;
						endif;
					endforeach; endif;
					if (!$valid) return false;
				endif;
			
			endif;
			
			$valid = apply_filters('wpdeals_coupon_is_valid', true, $this);
			if (!$valid) return false;
			
			return true;
		
		endif;
		
		return false;
	}
}
