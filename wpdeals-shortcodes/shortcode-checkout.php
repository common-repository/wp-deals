<?php
/**
 * Checkout Shortcode
 * 
 * Used on the checkout page, the checkout shortcode displays the checkout process.
 *
 * @package		WPDeals
 * @category	Shortcode
 * @author		Tokokoo
 */
 
function get_wpdeals_checkout( $atts ) {
	global $wpdeals;
	return $wpdeals->shortcode_wrapper('wpdeals_checkout', $atts);
}

function wpdeals_checkout( $atts ) {
	global $wpdeals, $wpdeals_checkout;
	$errors = array();
	$validation = $wpdeals->validation();
	
	// Process Discount Codes
	if (isset($_POST['apply_coupon']) && $_POST['apply_coupon'] && $wpdeals->verify_nonce('cart')) :
	
		$coupon_code = stripslashes(trim($_POST['coupon_code']));
		$wpdeals->cart->add_discount($coupon_code);
	
	// Remvoe Discount Codes
	elseif (isset($_GET['remove_discounts'])) :
		
		$wpdeals->cart->remove_coupons( $_GET['remove_discounts'] );
		
		// Re-calc price
		$wpdeals->cart->calculate_totals();
	
	endif;
	
	do_action('wpdeals_check_cart_items');
	
	$wpdeals->show_messages();
	
	if (sizeof($wpdeals->cart->get_cart())==0) :
		echo '<p>'.__('Your cart is currently empty.', 'wpdeals').'</p>';
		do_action('wpdeals_cart_is_empty');
		echo '<p><a class="button" href="'.get_permalink(get_option('wpdeals_store_page_id')).'">'.__('&larr; Return To Store', 'wpdeals').'</a></p>';
		return;
	endif;
	
	?>
	<form action="<?php echo esc_url( $wpdeals->cart->get_cart_url() ); ?>" method="post">
	<table class="store_table cart" cellspacing="0">
		<thead>
			<tr>
				<th class="daily-deals-remove"></th>
				<th class="daily-deals-thumbnail"></th>
				<th class="daily-deals-name"><span class="nobr"><?php _e('Deal Name', 'wpdeals'); ?></span></th>
				<th class="daily-deals-quantity"><?php _e('Quantity', 'wpdeals'); ?></th>
				<th class="daily-deals-subtotal"><?php _e('Price', 'wpdeals'); ?></th>
			</tr>
		</thead>
		<tbody>
			<?php
			if (sizeof($wpdeals->cart->get_cart())>0) : 
				foreach ($wpdeals->cart->get_cart() as $cart_item_key => $values) :
					$_deals = $values['data'];
					if ($_deals->exists() && $values['quantity']>0) :
					
						?>
						<tr>
							<td class="daily-deals-remove"><a href="<?php echo esc_url( $wpdeals->cart->get_remove_url($cart_item_key) ); ?>" class="remove" title="<?php _e('Remove this item', 'wpdeals'); ?>">&times;</a></td>
							<td class="daily-deals-thumbnail">
								<?php echo $_deals->get_image(); ?>
							</td>
							<td class="daily-deals-name">
								<?php echo $_deals->get_title(); ?>
								<?php echo $wpdeals->cart->get_item_data( $values ); //Meta data ?>
							</td>
							<td class="daily-deals-quantity"><input type="hidden" name="cart[<?php echo $cart_item_key; ?>][qty]" value="<?php echo esc_attr( $values['quantity'] ); ?>"/><?php echo esc_attr( $values['quantity'] ); ?></td>
							<td class="daily-deals-subtotal"><?php 

								echo $wpdeals->cart->get_deals_subtotal( $_deals, $values['quantity'] )	;
														
							?></td>
						</tr>
						<?php
					endif;
				endforeach; 
			endif;
			
			do_action( 'wpdeals_cart_contents' );
			?>
		</tbody>
	</table>
	</form>
	<div class="cart-collaterals">
		
		<?php do_action('wpdeals_cart_collaterals'); ?>
		
		<?php wpdeals_cart_totals(); ?>
		
	</div>
	<?php	
	
	if (!defined('WPDEALS_CHECKOUT')) define('WPDEALS_CHECKOUT', true);
	
	if (sizeof($wpdeals->cart->get_cart())==0) :
		wp_redirect(get_permalink(get_option('wpdeals_checkout_page_id')));
		exit;
	endif;
	
	$non_js_checkout = (isset($_POST['update_totals']) && $_POST['update_totals']) ? true : false;
	
	$wpdeals_checkout = $wpdeals->checkout();
	
	$wpdeals_checkout->process_checkout();
	
	do_action('wpdeals_check_cart_items');
	
	if ( $wpdeals->error_count()==0 && $non_js_checkout) $wpdeals->add_message( __('The order totals have been updated. Please confirm your order by pressing the Place Order button at the bottom of the page.', 'wpdeals') );
	
	$wpdeals->show_messages();
	
	wpdeals_get_template('checkout/form.php', false);
	
}