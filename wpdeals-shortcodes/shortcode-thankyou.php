<?php
/**
 * Thankyou Shortcode
 * 
 * The thankyou page displays after successful checkout and can be hooked into by payment gateways.
 *
 * @package	WPDeals
 * @category	Shortcode
 * @author	Tokokoo
 */
 
function get_wpdeals_thankyou( $atts ) {
	global $wpdeals;
	return $wpdeals->shortcode_wrapper('wpdeals_thankyou', $atts); 
}

/**
 * Outputs the order received page
 **/
function wpdeals_thankyou( $atts ) {
	global $wpdeals;

	// Pay for order after checkout step
	if (isset($_GET['order'])) $order_id = $_GET['order']; else $order_id = 0;
	if (isset($_GET['key'])) $order_key = $_GET['key']; else $order_key = '';
	
	// Empty awaiting payment session
	unset($_SESSION['order_awaiting_payment']);
	
	if ($order_id > 0) :
	
		$order = new wpdeals_order( $order_id );
		
		if ($order->order_key == $order_key) :
		
			if (in_array($order->status, array('failed'))) :
				
				echo '<p>' . __('Unfortunately your order cannot be processed as the originating bank/merchant has declined your transaction.', 'wpdeals') . '</p>';

				echo '<p>';

					if (is_user_logged_in()) :
						_e('Please attempt your purchase again or go to your account page.', 'wpdeals');
					else :
						_e('Please attempt your purchase again.', 'wpdeals');
					endif;
				
				echo '</p>';
				
				echo '<a href="'.esc_url( $order->get_checkout_payment_url() ).'" class="button pay">'.__('Pay', 'wpdeals').'</a> ';
				
				if (is_user_logged_in()) :
					echo '<a href="'.esc_url( get_permalink(get_option('wpdeals_myaccount_page_id')) ).'" class="button pay">'.__('My Account', 'wpdeals').'</a>';
				endif;

			else :
				
				echo '<p>' . __('Thank you. Your order has been received.', 'wpdeals') . '</p>';
				
				?>
				<ul class="order_details">
					<li class="order">
						<?php _e('Order:', 'wpdeals'); ?>
						<strong># <?php echo $order->id; ?></strong>
					</li>
					<li class="date">
						<?php _e('Date:', 'wpdeals'); ?>
						<strong><?php echo date(get_option('date_format'), strtotime($order->order_date)); ?></strong>
					</li>
					<li class="total">
						<?php _e('Total:', 'wpdeals'); ?>
						<strong><?php echo wpdeals_price($order->order_total); ?></strong>
					</li>
					<li class="method">
						<?php _e('Payment method:', 'wpdeals'); ?>
						<strong><?php 
							echo $order->payment_method_title;
						?></strong>
					</li>
				</ul>
				<div class="clear"></div>
				<?php
			
			endif;
			
			do_action( 'wpdeals_thankyou_' . $order->payment_method, $order_id );
			do_action( 'wpdeals_thankyou', $order_id );
			
		endif;
	
	else :
	
		echo '<p>' . __('Thank you. Your order has been received.', 'wpdeals') . '</p>';
		
	endif;
	
}