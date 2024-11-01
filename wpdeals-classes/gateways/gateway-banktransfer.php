<?php
/**
 * Bank Transfer Payment Gateway
 * 
 * Provides a Bank Transfer Payment Gateway. Based on code by Mike Pepper.
 *
 * @class 		wpdeals_bacs
 * @package		WPDeals
 * @category	Payment Gateways
 * @author		Tokokoo
 */
class wpdeals_bacs extends wpdeals_payment_gateway {

    public function __construct() { 
		$this->id				= 'bacs';
		$this->icon 			= apply_filters('wpdeals_bacs_icon', '');
		$this->has_fields 		= false;
		
		// Load the form fields.
		$this->init_form_fields();
		
		// Load the settings.
		$this->init_settings();
		
		// Define user set variables
		$this->title 		= $this->settings['title'];
		$this->description      = $this->settings['description'];
		$this->account_name     = $this->settings['account_name'];
		$this->account_number   = $this->settings['account_number'];
		$this->sort_code        = $this->settings['sort_code'];
		$this->bank_name        = $this->settings['bank_name'];
		$this->iban             = $this->settings['iban'];
		$this->bic              = $this->settings['bic'];  
		
		// Actions
		add_action('wpdeals_update_options_payment_gateways', array(&$this, 'process_admin_options'));
                add_action('wpdeals_thankyou_bacs', array(&$this, 'thankyou_page'));

                // Customer Emails
                add_action('wpdeals_email_before_order_table', array(&$this, 'email_instructions'), 10, 2);
    } 

	/**
     * Initialise Gateway Settings Form Fields
     */
    function init_form_fields() {
    
    	$this->form_fields = array(
			'enabled' => array(
							'title' => __( 'Enable/Disable', 'wpdeals' ), 
							'type' => 'checkbox', 
							'label' => __( 'Enable Bank Transfer', 'wpdeals' ), 
							'default' => 'yes'
						), 
			'title' => array(
							'title' => __( 'Title', 'wpdeals' ), 
							'type' => 'text', 
							'description' => __( 'This controls the title which the user sees during checkout.', 'wpdeals' ), 
							'default' => __( 'Direct Bank Transfer', 'wpdeals' )
						),
			'description' => array(
							'title' => __( 'Customer Message', 'wpdeals' ), 
							'type' => 'textarea', 
							'description' => __( 'Give the customer instructions for paying via BACS, and let them know that their order won\'t be shipping until the money is received.', 'wpdeals' ), 
							'default' => __('Make your payment directly into our bank account. Please use your Order ID as the payment reference. Your order wont be shipped until the funds have cleared in our account.', 'wpdeals')
						),
			'account_name' => array(
							'title' => __( 'Account Name', 'wpdeals' ), 
							'type' => 'text', 
							'description' => '', 
							'default' => ''
						),
			'account_number' => array(
							'title' => __( 'Account Number', 'wpdeals' ), 
							'type' => 'text', 
							'description' => '', 
							'default' => ''
						),
			'sort_code' => array(
							'title' => __( 'Sort Code', 'wpdeals' ), 
							'type' => 'text', 
							'description' => '', 
							'default' => ''
						),
			'bank_name' => array(
							'title' => __( 'Bank Name', 'wpdeals' ), 
							'type' => 'text', 
							'description' => '', 
							'default' => ''
						),
			'iban' => array(
							'title' => __( 'IBAN', 'wpdeals' ), 
							'type' => 'text', 
							'description' => __('Your bank may require this for international payments','wpdeals'), 
							'default' => ''
						),
			'bic' => array(
							'title' => __( 'BIC (formerly Swift)', 'wpdeals' ), 
							'type' => 'text', 
							'description' => __('Your bank may require this for international payments','wpdeals'), 
							'default' => ''
						),

			);
    
    } // End init_form_fields()
    
	/**
	 * Admin Panel Options 
	 * - Options for bits like 'title' and availability on a country-by-country basis
	 *
	 * @since 1.0.0
	 */
	public function admin_options() {
    	?>
    	<h3><?php _e('BACS Payment', 'wpdeals'); ?></h3>
    	<p><?php _e('Allows payments by BACS (Bank Account Clearing System), more commonly known as direct bank/wire transfer.', 'wpdeals'); ?></p>
    	<table class="form-table">
    	<?php
    		// Generate the HTML For the settings form.
    		$this->generate_settings_html();
    	?>
		</table><!--/.form-table-->
    	<?php
    } // End admin_options()


    /**
    * There are no payment fields for bacs, but we want to show the description if set.
    **/
    function payment_fields() {
      if ($this->description) echo wpautop(wptexturize($this->description));
    }

    function thankyou_page() {
		if ($this->description) echo wpautop(wptexturize($this->description));
		
		?><h2><?php _e('Our Details', 'wpdeals') ?></h2><ul class="order_details bacs_details"><?php
		
		$fields = array(
			'account_name' 	=> __('Account Name', 'wpdeals'), 
			'account_number'=> __('Account Number', 'wpdeals'),  
			'sort_code'		=> __('Sort Code', 'wpdeals'),  
			'bank_name'		=> __('Bank Name', 'wpdeals'),  
			'iban'			=> __('IBAN', 'wpdeals'), 
			'bic'			=> __('BIC', 'wpdeals')
		);
		
		foreach ($fields as $key=>$value) :
		    if(!empty($this->$key)) :
		    	echo '<li class="'.$key.'">'.$value.': <strong>'.wptexturize($this->$key).'</strong></li>';
		    endif;
		endforeach;
		
		?></ul><?php
    }
    
    /**
    * Add text to user email
    **/
    function email_instructions( $order, $sent_to_admin ) {
    	
    	if ( $sent_to_admin ) return;
    	
    	if ( $order->status !== 'on-hold') return;
    	
    	if ( $order->payment_method !== 'bacs') return;
    	
		if ($this->description) echo wpautop(wptexturize($this->description));
		
		?><h2><?php _e('Our Details', 'wpdeals') ?></h2><ul class="order_details bacs_details"><?php
		
		$fields = array(
			'account_name'          => __('Account Name', 'wpdeals'), 
			'account_number'        => __('Account Number', 'wpdeals'),  
			'sort_code'		=> __('Sort Code', 'wpdeals'),  
			'bank_name'		=> __('Bank Name', 'wpdeals'),  
			'iban'			=> __('IBAN', 'wpdeals'), 
			'bic'			=> __('BIC', 'wpdeals')
		);
		
		foreach ($fields as $key=>$value) :
		    if(!empty($this->$key)) :
		    	echo '<li class="'.$key.'">'.$value.': <strong>'.wptexturize($this->$key).'</strong></li>';
		    endif;
		endforeach;
		
		?></ul><?php
    }

    /**
    * Process the payment and return the result
    **/
    function process_payment( $order_id ) {
    	global $wpdeals;
    	
		$order = new wpdeals_order( $order_id );
		
		// Mark as on-hold (we're awaiting the payment)
		$order->update_status('on-hold', __('Awaiting BACS payment', 'wpdeals'));
		
		// Reduce stock levels
		$order->reduce_order_stock();

		// Remove cart
		$wpdeals->cart->empty_cart();
		
		// Empty awaiting payment session
		unset($_SESSION['order_awaiting_payment']);
		
		// Return thankyou redirect
		return array(
			'result' 	=> 'success',
			'redirect'	=> add_query_arg('key', $order->order_key, add_query_arg('order', $order_id, get_permalink(get_option('wpdeals_thanks_page_id'))))
		);
    }

}

/**
 * Add the gateway to WPDeals
 **/
function add_bacs_gateway( $methods ) {
	$methods[] = 'wpdeals_bacs'; return $methods;
}

add_filter('wpdeals_payment_gateways', 'add_bacs_gateway' );
