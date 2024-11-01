<?php
/**
 * Cheque Payment Gateway
 * 
 * Provides a Cheque Payment Gateway, mainly for testing purposes.
 *
 * @class 		wpdeals_cheque
 * @package		WPDeals
 * @category	Payment Gateways
 * @author		Tokokoo
 */
class wpdeals_cheque extends wpdeals_payment_gateway {
		
	public function __construct() { 
        $this->id				= 'cheque';
        $this->icon 			= apply_filters('wpdeals_cheque_icon', '');
        $this->has_fields 		= false;
		
		// Load the form fields.
		$this->init_form_fields();
		
		// Load the settings.
		$this->init_settings();
		
		// Define user set variables
		$this->title = $this->settings['title'];
		$this->description = $this->settings['description'];
		
		// Actions
		add_action('wpdeals_update_options_payment_gateways', array(&$this, 'process_admin_options'));
    	add_action('wpdeals_thankyou_cheque', array(&$this, 'thankyou_page'));
    	
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
							'label' => __( 'Enable Cheque Payment', 'wpdeals' ), 
							'default' => 'yes'
						), 
			'title' => array(
							'title' => __( 'Title', 'wpdeals' ), 
							'type' => 'text', 
							'description' => __( 'This controls the title which the user sees during checkout.', 'wpdeals' ), 
							'default' => __( 'Cheque Payment', 'wpdeals' )
						),
			'description' => array(
							'title' => __( 'Customer Message', 'wpdeals' ), 
							'type' => 'textarea', 
							'description' => __( 'Let the customer know the payee and where they should be sending the cheque to and that their order won\'t be shipping until you receive it.', 'wpdeals' ), 
							'default' => 'Please send your cheque to Store Name, Store Street, Store Town, Store State / County, Store Postcode.'
						)
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
    	<h3><?php _e('Cheque Payment', 'wpdeals'); ?></h3>
    	<p><?php _e('Allows cheque payments. Why would you take cheques in this day and age? Well you probably wouldn\'t but it does allow you to make test purchases for testing order emails and the \'success\' pages etc.', 'wpdeals'); ?></p>
    	<table class="form-table">
    	<?php
    		// Generate the HTML For the settings form.
    		$this->generate_settings_html();
    	?>
		</table><!--/.form-table-->
    	<?php
    } // End admin_options()

	/**
	* There are no payment fields for cheques, but we want to show the description if set.
	**/
	function payment_fields() {
		if ($this->description) echo wpautop(wptexturize($this->description));
	}
	
	function thankyou_page() {
		if ($this->description) echo wpautop(wptexturize($this->description));
	}
	
	function email_instructions( $order, $sent_to_admin ) {
    	if ( $sent_to_admin ) return;
    	
    	if ( $order->status !== 'on-hold') return;
    	
    	if ( $order->payment_method !== 'cheque') return;
    	
		if ($this->description) echo wpautop(wptexturize($this->description));
	}
	
	/**
	 * Process the payment and return the result
	 **/
	function process_payment( $order_id ) {
		global $wpdeals;
		
		$order = new wpdeals_order( $order_id );
		
		// Mark as on-hold (we're awaiting the cheque)
		$order->update_status('on-hold', __('Awaiting cheque payment', 'wpdeals'));
		
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
function add_cheque_gateway( $methods ) {
	$methods[] = 'wpdeals_cheque'; return $methods;
}

add_filter('wpdeals_payment_gateways', 'add_cheque_gateway' );
