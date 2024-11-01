<?php
/**
 * PayPal Standard Payment Gateway
 * 
 * Provides a PayPal Standard Payment Gateway.
 *
 * @class 	wpdeals_paypal
 * @package	WPDeals
 * @category	Payment Gateways
 * @author	Tokokoo
 */
class wpdeals_paypal extends wpdeals_payment_gateway {
		
	public function __construct() { 
		global $wpdeals;
		
                $this->id		= 'paypal';
                $this->icon 		= apply_filters('wpdeals_paypal_icon', $wpdeals->plugin_url() . '/wpdeals-assets/images/icons/paypal.png');
                $this->has_fields 	= false;
                $this->liveurl 		= 'https://www.paypal.com/webscr';
		$this->testurl 		= 'https://www.sandbox.paypal.com/webscr';
        
		// Load the form fields.
		$this->init_form_fields();
		
		// Load the settings.
		$this->init_settings();
		
		// Define user set variables
		$this->title 		= $this->settings['title'];
		$this->description 	= $this->settings['description'];
		$this->email 		= $this->settings['email'];
		$this->testmode		= $this->settings['testmode'];
		$this->debug		= $this->settings['debug'];	
		
		// Logs
		if ($this->debug=='yes') $this->log = $wpdeals->logger();
		
		// Actions
		add_action( 'init', array(&$this, 'check_ipn_response') );
		add_action('valid-paypal-standard-ipn-request', array(&$this, 'successful_request') );
		add_action('wpdeals_receipt_paypal', array(&$this, 'receipt_page'));
		add_action('wpdeals_update_options_payment_gateways', array(&$this, 'process_admin_options'));
		
		if ( !$this->is_valid_for_use() ) $this->enabled = false;
    } 
    
     /**
     * Check if this gateway is enabled and available in the user's country
     */
    function is_valid_for_use() {
        if (!in_array(get_option('wpdeals_currency'), array('AUD', 'BRL', 'CAD', 'MXN', 'NZD', 'HKD', 'SGD', 'USD', 'EUR', 'JPY', 'TRY', 'NOK', 'CZK', 'DKK', 'HUF', 'ILS', 'MYR', 'PHP', 'PLN', 'SEK', 'CHF', 'TWD', 'THB', 'GBP'))) return false;

        return true;
    }
    
	/**
	 * Admin Panel Options 
	 * - Options for bits like 'title' and availability on a country-by-country basis
	 *
	 * @since 1.0.0
	 */
	public function admin_options() {

    	?>
    	<h3><?php _e('PayPal standard', 'wpdeals'); ?></h3>
    	<p><?php _e('PayPal standard works by sending the user to PayPal to enter their payment information.', 'wpdeals'); ?></p>
    	<table class="form-table">
    	<?php
    		if ( $this->is_valid_for_use() ) :
    	
    			// Generate the HTML For the settings form.
    			$this->generate_settings_html();
    		
    		else :
    		
    			?>
            		<div class="inline error"><p><strong><?php _e( 'Gateway Disabled', 'wpdeals' ); ?></strong>: <?php _e( 'PayPal does not support your store currency.', 'wpdeals' ); ?></p></div>
        		<?php
        		
    		endif;
    	?>
		</table><!--/.form-table-->
    	<?php
    } // End admin_options()
    
	/**
     * Initialise Gateway Settings Form Fields
     */
    function init_form_fields() {
    
    	$this->form_fields = array(
			'enabled' => array(
							'title' => __( 'Enable/Disable', 'wpdeals' ), 
							'type' => 'checkbox', 
							'label' => __( 'Enable PayPal standard', 'wpdeals' ), 
							'default' => 'yes'
						), 
			'title' => array(
							'title' => __( 'Title', 'wpdeals' ), 
							'type' => 'text', 
							'description' => __( 'This controls the title which the user sees during checkout.', 'wpdeals' ), 
							'default' => __( 'PayPal', 'wpdeals' )
						),
			'description' => array(
							'title' => __( 'Description', 'wpdeals' ), 
							'type' => 'textarea', 
							'description' => __( 'This controls the description which the user sees during checkout.', 'wpdeals' ), 
							'default' => __("Pay via PayPal; you can pay with your credit card if you don't have a PayPal account", 'wpdeals')
						),
			'email' => array(
							'title' => __( 'PayPal Email', 'wpdeals' ), 
							'type' => 'text', 
							'description' => __( 'Please enter your PayPal email address; this is needed in order to take payment.', 'wpdeals' ), 
							'default' => ''
						),
			'testmode' => array(
							'title' => __( 'PayPal sandbox', 'wpdeals' ), 
							'type' => 'checkbox', 
							'label' => __( 'Enable PayPal sandbox', 'wpdeals' ), 
							'default' => 'yes'
						),
			'debug' => array(
							'title' => __( 'Debug', 'wpdeals' ), 
							'type' => 'checkbox', 
							'label' => __( 'Enable logging (<code>wpdeals/wpdeals-logs/paypal.txt</code>)', 'wpdeals' ), 
							'default' => 'no'
						)
			);
    
    } // End init_form_fields()
    
    /**
	 * There are no payment fields for paypal, but we want to show the description if set.
	 **/
    function payment_fields() {
    	if ($this->description) echo wpautop(wptexturize($this->description));
    }
    
	/**
	 * Generate the paypal button link
	 **/
    public function generate_paypal_form( $order_id ) {
		global $wpdeals;
		
		$order = new wpdeals_order( $order_id );
		
		if ( $this->testmode == 'yes' ):
			$paypal_adr = $this->testurl . '?test_ipn=1&';		
		else :
			$paypal_adr = $this->liveurl . '?';		
		endif;
		
		if ($this->debug=='yes') $this->log->add( 'paypal', 'Generating payment form for order #' . $order_id . '. Notify URL: ' . trailingslashit(home_url()).'?paypalListener=paypal_standard_IPN');
		
		
		$paypal_args = array(
                        'cmd' 			=> '_cart',
                        'business' 		=> $this->email,
                        'no_note' 		=> 1,
                        'currency_code' 	=> get_option('wpdeals_currency'),
                        'charset' 		=> 'UTF-8',
                        'rm' 			=> 2,
                        'upload' 		=> 1,
                        'return' 		=> $this->get_return_url( $order ),
                        'cancel_return'		=> $order->get_cancel_order_url(),
                        'no_shipping'           => 1,

                        // Order key
                        'custom'		=> $order_id,

                        // IPN
                        'notify_url'		=> trailingslashit(home_url()).'?paypalListener=paypal_standard_IPN',

                        // Payment Info
                        'invoice' 		=> $order->order_key
                );
                
                // Cart Contents
                $item_loop = 0;
                if (sizeof($order->items)>0) : foreach ($order->items as $item) :
                        if ($item['qty']) :

                                $item_loop++;

                                $item_name = $item['name'];

                                $item_meta = new order_item_meta( $item['item_meta'] );					
                                if ($meta = $item_meta->display( true, true )) :
                                        $item_name .= ' ('.$meta.')';
                                endif;

                                $paypal_args['item_name_'.$item_loop] = $item_name;
                                $paypal_args['quantity_'.$item_loop] = $item['qty'];
                                $paypal_args['amount_'.$item_loop] = number_format($item['cost'], 2, '.', '');

                        endif;
                endforeach; endif;
		
		$paypal_args_array = array();

		foreach ($paypal_args as $key => $value) {
			$paypal_args_array[] = '<input type="hidden" name="'.esc_attr( $key ).'" value="'.esc_attr( $value ).'" />';
		}
		
		$wpdeals->add_inline_js('
			jQuery("body").block({ 
					message: "<img src=\"'.esc_url( $wpdeals->plugin_url() ).'/wpdeals-assets/images/ajax-loader.gif\" alt=\"Redirecting...\" style=\"float:left; margin-right: 10px;\" />'.__('Thank you for your order. We are now redirecting you to PayPal to make payment.', 'wpdeals').'", 
					overlayCSS: 
					{ 
						background: "#fff", 
						opacity: 0.6 
					},
					css: { 
				        padding:        20, 
				        textAlign:      "center", 
				        color:          "#555", 
				        border:         "3px solid #aaa", 
				        backgroundColor:"#fff", 
				        cursor:         "wait",
				        lineHeight:		"32px"
				    } 
				});
			jQuery("#submit_paypal_payment_form").click();
		');
		
		return '<form action="'.esc_url( $paypal_adr ).'" method="post" id="paypal_payment_form">
				' . implode('', $paypal_args_array) . '
				<input type="submit" class="button-alt" id="submit_paypal_payment_form" value="'.__('Pay via PayPal', 'wpdeals').'" /> <a class="button cancel" href="'.esc_url( $order->get_cancel_order_url() ).'">'.__('Cancel order &amp; restore cart', 'wpdeals').'</a>
			</form>';
		
	}
	
	/**
	 * Process the payment and return the result
	 **/
	function process_payment( $order_id ) {
		
		$order = new wpdeals_order( $order_id );
		
		return array(
			'result' 	=> 'success',
			'redirect'	=> add_query_arg('order', $order->id, add_query_arg('key', $order->order_key, get_permalink(get_option('wpdeals_pay_page_id'))))
		);
		
	}
	
	/**
	 * receipt_page
	 **/
	function receipt_page( $order ) {
		
		echo '<p>'.__('Thank you for your order, please click the button below to pay with PayPal.', 'wpdeals').'</p>';
		
		echo $this->generate_paypal_form( $order );
		
	}
	
	/**
	 * Check PayPal IPN validity
	 **/
	function check_ipn_request_is_valid() {
		global $wpdeals;
		
		if ($this->debug=='yes') $this->log->add( 'paypal', 'Checking IPN response is valid...' );
    
    	 // Add cmd to the post array
        $_POST['cmd'] = '_notify-validate';

        // Send back post vars to paypal
        $params = array( 
        	'body' => $_POST,
        	'sslverify' => false
        );

        // Get url
       	if ( $this->testmode == 'yes' ):
			$paypal_adr = $this->testurl;		
		else :
			$paypal_adr = $this->liveurl;		
		endif;
		
		// Post back to get a response
        $response = wp_remote_post( $paypal_adr, $params );
		
		 // Clean
        unset($_POST['cmd']);
        
        // check to see if the request was valid
        if ( !is_wp_error($response) && $response['response']['code'] >= 200 && $response['response']['code'] < 300 && (strcmp( $response['body'], "VERIFIED") == 0)) {
            if ($this->debug=='yes') $this->log->add( 'paypal', 'Received valid response from PayPal' );
            return true;
        } 
        
        if ($this->debug=='yes') :
        	$this->log->add( 'paypal', 'Received invalid response from PayPal' );
        	if (is_wp_error($response)) :
        		$this->log->add( 'paypal', 'Error response: ' . $result->get_error_message() );
        	endif;
        endif;
        
        return false;
    }
	
	/**
	 * Check for PayPal IPN Response
	 **/
	function check_ipn_response() {
			
		if (isset($_GET['paypalListener']) && $_GET['paypalListener'] == 'paypal_standard_IPN'):
		
        	$_POST = stripslashes_deep($_POST);
        	
        	if ($this->check_ipn_request_is_valid()) :
        	
            	do_action("valid-paypal-standard-ipn-request", $_POST);

       		endif;
       		
       	endif;
			
	}
	
	/**
	 * Successful Payment!
	 **/
	function successful_request( $posted ) {
		
		// Custom holds post ID
	    if ( !empty($posted['custom']) && !empty($posted['invoice']) ) {
	
			$order = new wpdeals_order( (int) $posted['custom'] );
	        if ($order->order_key!==$posted['invoice']) exit;
	        
	        // Sandbox fix
	        if ($posted['test_ipn']==1 && $posted['payment_status']=='Pending') $posted['payment_status'] = 'completed';
	        
	        // We are here so lets check status and do actions
	        switch (strtolower($posted['payment_status'])) :
	            case 'completed' :
	            	
	            	// Check order not already completed
	            	if ($order->status == 'completed') exit;
	            	
	            	// Check valid txn_type
	            	$accepted_types = array('cart', 'instant', 'express_checkout', 'web_accept', 'masspay', 'send_money');
					if (!in_array(strtolower($posted['txn_type']), $accepted_types)) exit;
	            	
	            	// Payment completed
	                $order->add_order_note( __('IPN payment completed', 'wpdeals') );
	                $order->payment_complete();
	                
	                // Store PP Details
	                update_post_meta( (int) $posted['custom'], 'Payer PayPal address', $posted['payer_email']);
	                update_post_meta( (int) $posted['custom'], 'Transaction ID', $posted['txn_id']);
	                update_post_meta( (int) $posted['custom'], 'Payer first name', $posted['first_name']);
	                update_post_meta( (int) $posted['custom'], 'Payer last name', $posted['last_name']);
	                update_post_meta( (int) $posted['custom'], 'Payment type', $posted['payment_type']); 
	                
	            break;
	            case 'denied' :
	            case 'expired' :
	            case 'failed' :
	            case 'voided' :
	                // Order failed
	                $order->update_status('failed', sprintf(__('Payment %s via IPN.', 'wpdeals'), strtolower($posted['payment_status']) ) );
	            break;
	            case "refunded" :
	            case "reversed" :
	            case "chargeback" :
	            	
	            	// Mark order as refunded
	            	$order->update_status('refunded', sprintf(__('Payment %s via IPN.', 'wpdeals'), strtolower($posted['payment_status']) ) );
	            	
					$message = wpdeals_mail_template( 
						__('Order refunded/reversed', 'wpdeals'),
						sprintf(__('Order #%s has been marked as refunded - PayPal reason code: %s', 'wpdeals'), $order->id, $posted['reason_code'] )
					);
				
					// Send the mail
					wpdeals_mail( get_option('wpdeals_new_order_email_recipient'), sprintf(__('Payment for order #%s refunded/reversed', 'wpdeals'), $order->id), $message );
	            	
	            break;
	            default:
	            	// No action
	            break;
	        endswitch;
			
			exit;
			
	    }
		
	}

}

/**
 * Add the gateway to WPDeals
 **/
function add_paypal_gateway( $methods ) {
	$methods[] = 'wpdeals_paypal'; return $methods;
}

add_filter('wpdeals_payment_gateways', 'add_paypal_gateway' );
