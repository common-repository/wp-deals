<?php do_action('wpdeals_before_checkout_form');

global $wpdeals;

// If checkout registration is disabled and not logged in, the user cannot checkout
if (get_option('wpdeals_enable_signup_and_login_from_checkout')=="no" && !is_user_logged_in()) :
	echo apply_filters('wpdeals_checkout_must_be_logged_in_message', __('You must be logged in to checkout.', 'wpdeals'));
	return;
endif;

// filter hook for include new pages inside the payment method
$get_checkout_url = apply_filters( 'wpdeals_get_checkout_url', $wpdeals->cart->get_checkout_url() ); ?>

<form name="checkout" method="post" class="checkout" action="<?php echo esc_url( $get_checkout_url ); ?>">
    
        <?php do_action('wpdeals_checkout_form'); ?>
		
	<h3 id="order_review_heading"><?php _e('Payment methods', 'wpdeals'); ?></h3>
	
	<?php do_action('wpdeals_checkout_order_review'); ?>
	
</form>

<?php do_action('wpdeals_after_checkout_form'); ?>