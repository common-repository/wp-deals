<?php
/**
 * Pay Shortcode
 * 
 * The pay page. Used for form based gateways to show payment forms and order info.
 *
 * @package		WPDeals
 * @category            Shortcode
 * @author		Tokokoo
 */
 
function get_wpdeals_pay( $atts ) {
	global $wpdeals;
	return $wpdeals->shortcode_wrapper('wpdeals_pay', $atts); 
}

/**
 * Outputs the pay page - payment gateways can hook in here to show payment forms etc
 **/
function wpdeals_pay() {
	global $wpdeals;
	
	if ( isset($_GET['pay_for_order']) && isset($_GET['order']) && isset($_GET['order_id']) ) :
		
		// Pay for existing order
		$order_key = urldecode( $_GET['order'] );
		$order_id = (int) $_GET['order_id'];
		$order = new wpdeals_order( $order_id );
		
		if ($order->id == $order_id && $order->order_key == $order_key && in_array($order->status, array('pending', 'failed'))) :
			
			// Pay form was posted - process payment
			if (isset($_POST['pay']) && $wpdeals->verify_nonce('pay')) :
			
				// Update payment method
				if ($order->order_total > 0 ) : 
					$payment_method 			= wpdeals_clean($_POST['payment_method']);
					
					$available_gateways = $wpdeals->payment_gateways->get_available_payment_gateways();
					
					// Update meta
					update_post_meta( $order_id, '_payment_method', $payment_method);
					if (isset($available_gateways) && isset($available_gateways[$payment_method])) :
						$payment_method_title = $available_gateways[$payment_method]->title;
					endif;
					update_post_meta( $order_id, '_payment_method_title', $payment_method_title);

					$result = $available_gateways[$payment_method]->process_payment( $order_id );

					// Redirect to success/confirmation/payment page
					if ($result['result']=='success') :
						wp_redirect( $result['redirect'] );
						exit;
					endif;
				else :
					
					// No payment was required for order
					$order->payment_complete();
					wp_safe_redirect( get_permalink(get_option('wpdeals_thanks_page_id')) );
					exit;
					
				endif;
	
			endif;
			
			// Show messages
			$wpdeals->show_messages();
			
			// Show form
			wpdeals_pay_for_existing_order( $order );
		
		elseif (!in_array($order->status, array('pending', 'failed'))) :
			
			$wpdeals->add_error( __('Your order has already been paid for. Please contact us if you need assistance.', 'wpdeals') );
			
			$wpdeals->show_messages();
			
		else :
		
			$wpdeals->add_error( __('Invalid order.', 'wpdeals') );
			
			$wpdeals->show_messages();
			
		endif;
		
	else :
		
		// Pay for order after checkout step
		if (isset($_GET['order'])) $order_id = $_GET['order']; else $order_id = 0;
		if (isset($_GET['key'])) $order_key = $_GET['key']; else $order_key = '';
		
		if ($order_id > 0) :
		
			$order = new wpdeals_order( $order_id );
		
			if ($order->order_key == $order_key && in_array($order->status, array('pending', 'failed'))) :
		
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
				
				<?php do_action( 'wpdeals_receipt_' . $order->payment_method, $order_id ); ?>
				
				<div class="clear"></div>
				<?php
				
			else :
			
				wp_safe_redirect( get_permalink(get_option('wpdeals_myaccount_page_id')) );
				exit;
				
			endif;
			
		else :
			
			wp_safe_redirect( get_permalink(get_option('wpdeals_myaccount_page_id')) );
			exit;
			
		endif;

	endif;
}

/**
 * Outputs the payment page when a user comes to pay from a link (for an existing/past created order)
 **/
function wpdeals_pay_for_existing_order( $pay_for_order ) {
	
	global $order;
	
	$order = $pay_for_order;
	
	wpdeals_get_template('checkout/pay_for_order.php');
	
}