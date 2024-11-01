<?php
/**
 * Checkout
 * 
 * The WPDeals checkout class handles the checkout process, collecting user data and processing the payment.
 *
 * @class 		wpdeals_checkout
 * @package		WPDeals
 * @category	Class
 * @author		Tokokoo
 */
class wpdeals_checkout {
	
	var $posted;
	var $billing_fields;
	var $shipping_fields;
	var $must_create_account;
	var $creating_account;
	
	/** constructor */
	function __construct () {
		
		add_action('wpdeals_checkout_form',array(&$this,'checkout_form_billing'));
		
		$this->must_create_account = true;
		
		if (get_option('wpdeals_enable_guest_checkout')=='yes' || is_user_logged_in()) $this->must_create_account = false;
		
	}
		
	/** Output the billing information form */
	function checkout_form_billing() {
		global $wpdeals;
				
		// Registration Form Fields
		if (!is_user_logged_in() && get_option('wpdeals_enable_signup_and_login_from_checkout')=="yes") :
		
			if (get_option('wpdeals_enable_guest_checkout')=='yes') :
				
				echo '<p class="form-row"><input class="input-checkbox" id="createaccount" '.checked($this->get_value('createaccount'), true).' type="checkbox" name="createaccount" value="1" /> <label for="createaccount" class="checkbox">'.__('Create an account?', 'wpdeals').'</label></p>';
				
			endif;
			
			echo '<div class="create-account">';
			
			echo '<p>'.__('Create an account by entering the information below. If you are a returning customer please login with your username at the top of the page.', 'wpdeals').'</p>'; 
			
			$this->checkout_form_field( 'account_username', array( 
				'type' => 'text', 
				'label' => __('Account username', 'wpdeals'), 
				'placeholder' => __('Username', 'wpdeals'),
				'class' => array('form-row-first') 
				));
			$this->checkout_form_field( 'account_email', array( 
				'type' => 'email', 
				'label' => __('Email', 'wpdeals'), 
				'placeholder' => __('Email', 'wpdeals'),
				'class' => array('form-row-last') 
				));
			$this->checkout_form_field( 'account_password', array( 
				'type' => 'password', 
				'label' => __('Account password', 'wpdeals'), 
				'placeholder' => __('Password', 'wpdeals'),
				'class' => array('form-row-first')
				));
			$this->checkout_form_field( 'account_password-2', array( 
				'type' => 'password', 
				'label' => __('Account password', 'wpdeals'), 
				'placeholder' => __('Password', 'wpdeals'),
				'class' => array('form-row-last'), 
				'label_class' => array('hidden')
				));
			
			echo '</div>';
							
		endif;
		
	}
	
	/**
	 * Outputs a checkout form field
	 */
	function checkout_form_field( $key, $args ) {
		global $wpdeals;
		
		$defaults = array(
			'type' => 'text',
			'label' => '',
			'placeholder' => '',
			'required' => false,
			'class' => array(),
			'label_class' => array(),
			'rel' => '',
			'return' => false
		);
		
		$args = wp_parse_args( $args, $defaults );

		if ($args['required']) $required = ' <span class="required">*</span>'; else $required = '';
		
		if (in_array('form-row-last', $args['class'])) $after = '<div class="clear"></div>'; else $after = '';
		
		$field = '';
		
		switch ($args['type']) :
			case "checkbox" :
				
				$field = '<p class="form-row '.implode(' ', $args['class']).'">
					<input type="'.$args['type'].'" class="input-checkbox" name="'.$key.'" id="'.$key.'" value="1" '.checked($this->get_value( $key ), 1, false).' />
					<label for="'.$key.'" class="checkbox '.implode(' ', $args['label_class']).'">'.$args['label'].$required.'</label>
				</p>'.$after;
				
                            break;
			case "email" :
			
				$field = '<p class="form-row '.implode(' ', $args['class']).'">
					<label for="'.$key.'" class="'.implode(' ', $args['label_class']).'">'.$args['label'].$required.'</label>
					<input type="email" class="input-text" name="'.$key.'" id="'.$key.'" placeholder="'.$args['placeholder'].'" value="'. $this->get_value( $key ).'" />
				</p>'.$after;
				
                            break;
			default :
			
				$field = '<p class="form-row '.implode(' ', $args['class']).'">
					<label for="'.$key.'" class="'.implode(' ', $args['label_class']).'">'.$args['label'].$required.'</label>
					<input type="text" class="input-text" name="'.$key.'" id="'.$key.'" placeholder="'.$args['placeholder'].'" value="'. $this->get_value( $key ).'" />
				</p>'.$after;
				
                            break;
		endswitch;
		
		if ($args['return']) return $field; else echo $field;
	}


	/**
	 * Process the checkout after the confirm order button is pressed
	 */
	function process_checkout() {
		global $wpdb, $wpdeals;
		$validation = $wpdeals->validation();
		
		if (!defined('WPDEALS_CHECKOUT')) define('WPDEALS_CHECKOUT', true);

		do_action('wpdeals_before_checkout_process');
		
		if (isset($_POST) && $_POST && !isset($_POST['login'])) :

			$wpdeals->verify_nonce('process_checkout');

			if (sizeof($wpdeals->cart->get_cart())==0) :
				$wpdeals->add_error( sprintf(__('Sorry, your session has expired. <a href="%s">Return to homepage &rarr;</a>', 'wpdeals'), home_url()) );
			endif;
			
			do_action('wpdeals_checkout_process');
						
			// Checkout fields (non-shipping/billing)
			$this->posted['terms'] 			= isset($_POST['terms']) ? 1 : 0;
			$this->posted['createaccount'] 		= isset($_POST['createaccount']) ? 1 : 0;
			$this->posted['payment_method'] 	= isset($_POST['payment_method']) ? wpdeals_clean($_POST['payment_method']) : '';
			$this->posted['order_comments'] 	= isset($_POST['order_comments']) ? wpdeals_clean($_POST['order_comments']) : '';
			$this->posted['account_username']	= isset($_POST['account_username']) ? wpdeals_clean($_POST['account_username']) : '';
			$this->posted['account_password'] 	= isset($_POST['account_password']) ? wpdeals_clean($_POST['account_password']) : '';
			$this->posted['account_password-2']     = isset($_POST['account_password-2']) ? wpdeals_clean($_POST['account_password-2']) : '';
			$this->posted['account_email']          = isset($_POST['account_email']) ? wpdeals_clean($_POST['account_email']) : '';
			
			// Update cart totals
			$wpdeals->cart->calculate_totals();

			if (is_user_logged_in()) :
				$this->creating_account = false;
			elseif (isset($this->posted['createaccount']) && $this->posted['createaccount']) :
				$this->creating_account = true;
			elseif ($this->must_create_account) :
				$this->creating_account = true;
			else :
				$this->creating_account = false;
			endif;
			
			if ($this->creating_account) :
			
				if ( empty($this->posted['account_username']) ) $wpdeals->add_error( __('Please enter an account username.', 'wpdeals') );
				if ( empty($this->posted['account_password']) ) $wpdeals->add_error( __('Please enter an account password.', 'wpdeals') );
				if ( $this->posted['account_password-2'] !== $this->posted['account_password'] ) $wpdeals->add_error( __('Passwords do not match.', 'wpdeals') );
			
				// Check the username
				if ( !validate_username( $this->posted['account_username'] ) ) :
					$wpdeals->add_error( __('Invalid email/username.', 'wpdeals') );
				elseif ( username_exists( $this->posted['account_username'] ) ) :
					$wpdeals->add_error( __('An account is already registered with that username. Please choose another.', 'wpdeals') );
				endif;
				
				// Check the e-mail address
				if ( email_exists( $this->posted['account_email'] ) ) :
					$wpdeals->add_error( __('An account is already registered with your email address. Please login.', 'wpdeals') );
				endif;
			endif;
			
			// Terms
			if (!isset($_POST['update_totals']) && empty($this->posted['terms']) && get_option('wpdeals_terms_page_id')>0 ) $wpdeals->add_error( __('You must accept our Terms &amp; Conditions.', 'wpdeals') );
						
			if ($wpdeals->cart->needs_payment()) :
			
				// Payment Method
				$available_gateways = $wpdeals->payment_gateways->get_available_payment_gateways();
				if (!isset($available_gateways[$this->posted['payment_method']])) :
					$wpdeals->add_error( __('Invalid payment method.', 'wpdeals') );
				else :
					// Payment Method Field Validation
					$available_gateways[$this->posted['payment_method']]->validate_fields();
				endif;
			endif;
			
			do_action( 'wpdeals_after_checkout_validation', $this->posted );
					
			if (!isset($_POST['update_totals']) && $wpdeals->error_count()==0) :
				
				$user_id = get_current_user_id();
				
				while (1) :
					
					// Create customer account and log them in
					if ($this->creating_account && !$user_id) :
				
						$reg_errors = new WP_Error();
						do_action('register_post', $this->posted['account_username'], $this->posted['account_email'], $reg_errors);
						$errors = apply_filters( 'registration_errors', $reg_errors, $this->posted['account_username'], $this->posted['account_email'] );
				
                                                // if there are no errors, let's create the user account
						if ( !$reg_errors->get_error_code() ) :
		
                                                $user_pass = $this->posted['account_password'];
                                                $user_id = wp_create_user( $this->posted['account_username'], $user_pass, $this->posted['account_email'] );
                                                if ( !$user_id ) {
                                                        $wpdeals->add_error( sprintf(__('<strong>ERROR</strong>: Couldn&#8217;t register you... please contact the <a href="mailto:%s">webmaster</a> !', 'wpdeals'), get_option('admin_email')));
                                                        break;
                                                }

                                                // Change role
                                                wp_update_user( array ('ID' => $user_id, 'role' => 'customer') ) ;

                                                // send the user a confirmation and their login details
                                                wpdeals_customer_new_account( $user_id, $user_pass );
                                                //wp_new_user_notification( $user_id, $user_pass );

                                                // set the WP login cookie
                                                $secure_cookie = is_ssl() ? true : false;
                                                wp_set_auth_cookie($user_id, true, $secure_cookie);
						
						else :
							$wpdeals->add_error( $reg_errors->get_error_message() );
                                                        break;                    
						endif;
						
					endif;
						
                                        // Hook for meta user
                                        do_action('wpdeals_checkout_update_user_meta', $user_id, $this->posted);						
					
					$order_data = array(
						'post_type' => 'deals-sales',
						'post_title' => 'Order &ndash; '.date('F j, Y @ h:i A'),
						'post_status' => 'publish',
						'post_excerpt' => $this->posted['order_comments'],
						'post_author' => 1
					);

					// Cart items
					$order_items = array();
					
					foreach ($wpdeals->cart->get_cart() as $cart_item_key => $values) :
						
						$_deals = $values['data'];
						
						// Store any item meta data - item meta class lets plugins add item meta in a standardized way
						$item_meta = new order_item_meta();
						
						$item_meta->new_order_item( $values );
						
						// Store variation data in meta so admin can view it
						if ($values['variation'] && is_array($values['variation'])) :
							foreach ($values['variation'] as $key => $value) :
								$item_meta->add( esc_attr(str_replace('attribute_', '', $key)), $value );
							endforeach;
						endif;
						
                                                $cost = $wpdeals->cart->get_discounted_price( $values, $_deals->get_sale() );
						
						$order_items[] = apply_filters('new_order_item', array(
					 		'id' 			=> $values['deal_id'],
					 		'variation_id' 	=> $values['variation_id'],
					 		'name' 		=> $_deals->get_title(),
					 		'qty' 		=> (int) $values['quantity'],
					 		'base_cost' 	=> $_deals->get_price(),
					 		'discount_cost' => $_deals->get_sale(),
					 		'cost'		=> rtrim(rtrim(number_format($cost, 2, '.', ''), '0'), '.')
					 	), $values);
					 	
					 	// Check cart items for errors
					 	do_action('wpdeals_check_cart_items');
					 	
					endforeach;
					
					if ($wpdeals->error_count()>0) break;
					
					// Insert or update the post data
					$create_new_order = true;
					
					if (isset($_SESSION['order_awaiting_payment']) && $_SESSION['order_awaiting_payment'] > 0) :
						
						$order_id = (int) $_SESSION['order_awaiting_payment'];
						
						/* Check order is unpaid */
						$order = new wpdeals_order( $order_id );
						
						if ( $order->status == 'pending' ) :
							
							// Resume the unpaid order
							$order_data['ID'] = $order_id;
							wp_update_post( $order_data );
							do_action('wpdeals_resume_order', $order_id);
							
							$create_new_order = false;
						
						endif;
						
					endif;
					
					if ($create_new_order) :
						$order_id = wp_insert_post( $order_data );
						
						if (is_wp_error($order_id)) :
							$wpdeals->add_error( 'Error: Unable to create order. Please try again.' );
			                break;
						else :
							// Inserted successfully 
							do_action('wpdeals_new_order', $order_id);
						endif;
					endif;                                        
					
					// Get better formatted shipping method (title/label)
					$payment_method = $this->posted['payment_method'];
					if (isset($available_gateways) && isset($available_gateways[$this->posted['payment_method']])) :
						$payment_method = $available_gateways[$this->posted['payment_method']]->title;
					endif;
                                        
					update_post_meta( $order_id, '_payment_method', 	$this->posted['payment_method']);
					update_post_meta( $order_id, '_payment_method_title', 	$payment_method);
					update_post_meta( $order_id, '_order_total', 		number_format($wpdeals->cart->total, 2, '.', ''));
					update_post_meta( $order_id, '_order_key', 		uniqid('order_') );
					update_post_meta( $order_id, '_customer_user', 		(int) $user_id );
					update_post_meta( $order_id, '_order_items', 		$order_items );
					
					do_action('wpdeals_checkout_update_order_meta', $order_id, $this->posted);
					
					// Order status
					wp_set_object_terms( $order_id, 'pending', 'deals_sales_status' );
						
					// Discount code meta
					if ($applied_coupons = $wpdeals->cart->get_applied_coupons()) update_post_meta($order_id, 'coupons', implode(', ', $applied_coupons));
					
					// Order is saved
					do_action('wpdeals_checkout_order_processed', $order_id, $this->posted);
					
					// Process payment
					$order = new wpdeals_order($order_id);
					
					if ($wpdeals->cart->needs_payment()) :
						
						// Store Order ID in session so it can be re-used after payment failure
						$_SESSION['order_awaiting_payment'] = $order_id;
					
						// Process Payment
						$result = $available_gateways[$this->posted['payment_method']]->process_payment( $order_id );
						
						// Redirect to success/confirmation/payment page
						if ($result['result']=='success') :
						
							if (is_ajax()) : 
								ob_clean();
								echo json_encode($result);
								exit;
							else :
								wp_safe_redirect( $result['redirect'] );
								exit;
							endif;
							
						endif;
					
					else :
					
						// No payment was required for order
						$order->payment_complete();
						
						// Empty the Cart
						$wpdeals->cart->empty_cart();
						
						// Redirect to success/confirmation/payment page
						if (is_ajax()) : 
							ob_clean();
							echo json_encode( array('redirect'	=> get_permalink(get_option('wpdeals_thanks_page_id'))) );
							exit;
						else :
							wp_safe_redirect( get_permalink(get_option('wpdeals_thanks_page_id')) );
							exit;
						endif;
						
					endif;
					
					// Break out of loop
					break;
				
				endwhile;
	
			endif;
			
			// If we reached this point then there were errors
			if (is_ajax()) : 
				ob_clean();
				$wpdeals->show_messages();
				exit;
			else :
				$wpdeals->show_messages();
			endif;
		
		endif;
	}
	
	/** Gets the value either from the posted data, or from the users meta data */
	function get_value( $input ) {
		global $wpdeals;
		
		if (isset( $this->posted[$input] ) && !empty($this->posted[$input])) :
			return $this->posted[$input];
		elseif (is_user_logged_in()) :
			if (get_user_meta( get_current_user_id(), $input, true )) return get_user_meta( get_current_user_id(), $input, true );
			
			$current_user = wp_get_current_user();

			switch ( $input ) :
				
				case "account_email" :
					return $current_user->user_email;
				break;
				
			endswitch;
			
		endif;
	}
}