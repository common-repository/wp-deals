<?php
/**
 * WPDeals Emails
 * 
 * Email handling for important store events.
 *
 * @package		WPDeals
 * @category	Emails
 * @author		Tokokoo
 */

/**
 * Mail from name/email
 **/
function wpdeals_mail_from_name( $name ) {
	return get_option('wpdeals_email_from_name');
}
function wpdeals_mail_from( $email ) {
	return get_option('wpdeals_email_from_address');
}

/**
 * HTML emails from WPDeals
 **/
function wpdeals_mail( $to, $subject, $message, $headers = "Content-Type: text/html\r\n", $attachments = "" ) {
	
	add_filter( 'wp_mail_from', 'wpdeals_mail_from' );
	add_filter( 'wp_mail_from_name', 'wpdeals_mail_from_name' );
	add_filter( 'wp_mail_content_type', 'wpdeals_email_content_type' );
	
	// Send the mail	
	wp_mail( $to, $subject, $message, $headers, $attachments );
	
	// Unhook
	remove_filter( 'wp_mail_from', 'wpdeals_mail_from' );
	remove_filter( 'wp_mail_from_name', 'wpdeals_mail_from_name' );
	remove_filter( 'wp_mail_content_type', 'wpdeals_email_content_type' );
}

/**
 * Wraps a message in the wpdeals mail template
 **/
function wpdeals_mail_template( $heading, $message ) {
	global $email_heading;
	
	$email_heading = $heading;
	
	// Buffer
	ob_start();

	do_action('wpdeals_email_header');
	
	echo wpautop(wptexturize( $message ));
	
	do_action('wpdeals_email_footer');
	
	// Get contents
	$message = ob_get_clean();
	
	return $message;
}

/**
 * Email Header
 **/
add_action('wpdeals_email_header', 'wpdeals_email_header');

function wpdeals_email_header() {
	wpdeals_get_template('emails/email_header.php', false);
}


/**
 * Email Footer
 **/
add_action('wpdeals_email_footer', 'wpdeals_email_footer');

function wpdeals_email_footer() {
	wpdeals_get_template('emails/email_footer.php', false);
}	
	
/**
 * HTML email type
 **/
function wpdeals_email_content_type($content_type){
	return 'text/html';
}


/**
 * Fix recieve password mail links
 **/
function wpdeals_retrieve_password_message($content){
	return htmlspecialchars($content);
}
	

/**
 * Hooks for emails
 **/
add_action('wpdeals_low_stock_notification', 'wpdeals_low_stock_notification');
add_action('wpdeals_no_stock_notification', 'wpdeals_no_stock_notification');
add_action('wpdeals_deals_on_backorder_notification', 'wpdeals_deals_on_backorder_notification', 1, 2);
 
 
/**
 * New order notification email template
 **/
add_action('wpdeals_sales_status_pending_to_processing', 'wpdeals_new_order_notification');
add_action('wpdeals_sales_status_pending_to_completed', 'wpdeals_new_order_notification');
add_action('wpdeals_sales_status_pending_to_on-hold', 'wpdeals_new_order_notification');
add_action('wpdeals_sales_status_failed_to_processing', 'wpdeals_new_order_notification');
add_action('wpdeals_sales_status_failed_to_completed', 'wpdeals_new_order_notification');

function wpdeals_new_order_notification( $id ) {
	
	global $order_id, $email_heading;
	
	$order_id = $id;
	
	$email_heading = __('New Customer Order', 'wpdeals');
	
	$subject = sprintf(__('[%s] New Customer Order (# %s)', 'wpdeals'), get_bloginfo('name'), $order_id);
	
	// Buffer
	ob_start();
	
	// Get mail template
	wpdeals_get_template('emails/new_order.php', false);
	
	// Get contents
	$message = ob_get_clean();

	// Send the mail	
	wpdeals_mail( get_option('wpdeals_new_order_email_recipient'), $subject, $message );
}


/**
 * Processing order notification email template
 **/
add_action('wpdeals_sales_status_pending_to_processing', 'wpdeals_processing_order_customer_notification');
add_action('wpdeals_sales_status_pending_to_on-hold', 'wpdeals_processing_order_customer_notification');
 
function wpdeals_processing_order_customer_notification( $id ) {
	
	global $order_id, $email_heading;
	
	$order_id = $id;
	
	$order = new wpdeals_order( $order_id );
	
	$email_heading = __('Order Received', 'wpdeals');
	
	$subject = sprintf(__('[%s] Order Received', 'wpdeals'), get_bloginfo('name'));
	
	// Buffer
	ob_start();
	
	// Get mail template
	wpdeals_get_template('emails/customer_processing_order.php', false);
	
	// Get contents
	$message = ob_get_clean();

	// Send the mail	
	wpdeals_mail( $order->user_email, $subject, $message );
}


/**
 * Completed order notification email template - this one includes download links for downloadable deals
 **/
add_action('wpdeals_sales_status_completed', 'wpdeals_completed_order_customer_notification');
 
function wpdeals_completed_order_customer_notification( $id ) {
	
	global $order_id, $email_heading;
	
	$order_id = $id;
	
	$order = new wpdeals_order( $order_id );
	
	if ($order->has_downloadable_item()) :
		$subject		= __('[%s] Order Complete/Download Links', 'wpdeals');
		$email_heading 	= __('Order Complete/Download Links', 'wpdeals');
	else :
		$subject		= __('[%s] Order Complete', 'wpdeals');
		$email_heading 	= __('Order Complete', 'wpdeals');
	endif;
	
	$email_heading = apply_filters('wpdeals_completed_order_customer_notification_subject', $email_heading);

	$subject = sprintf($subject, get_bloginfo('name'));
	
	// Buffer
	ob_start();
	
	// Get mail template
	wpdeals_get_template('emails/customer_completed_order.php', false);
	
	// Get contents
	$message = ob_get_clean();

	// Send the mail	
	wpdeals_mail( $order->user_email, $subject, $message );
}


/**
 * Pay for order notification email template - this one includes a payment link
 **/
function wpdeals_pay_for_order_customer_notification( $the_order ) {
	
	global $order_id, $order, $email_heading;
	
	$order = $the_order;
	$order_id = $order->id;
	
	$email_heading = sprintf(__('Invoice for Order #%s', 'wpdeals'), $order_id);

	$subject = sprintf(__('[%s] Pay for Order', 'wpdeals'), get_bloginfo('name'));

	// Buffer
	ob_start();
	
	// Get mail template
	wpdeals_get_template('emails/customer_pay_for_order.php', false);
	
	// Get contents
	$message = ob_get_clean();

	// Send the mail	
	wpdeals_mail( $order->user_email, $subject, $message );
}

/**
 * Customer note notification
 **/
add_action('wpdeals_new_customer_note', 'wpdeals_customer_note_notification', 10, 2);

function wpdeals_customer_note_notification( $id, $note ) {
	
	global $order_id, $email_heading, $customer_note;
	
	$order_id = $id;
	$customer_note = $note;
	
	$order = new wpdeals_order( $order_id );
	
	if (!$customer_note) return;
	
	$email_heading = __('A note has been added to your order', 'wpdeals');
	
	$subject = sprintf(__('[%s] A note has been added to your order', 'wpdeals'), get_bloginfo('name'));
	
	// Buffer
	ob_start();
	
	// Get mail template
	wpdeals_get_template('emails/customer_note_notification.php', false);
	
	// Get contents
	$message = ob_get_clean();

	// Send the mail	
	wpdeals_mail( $order->user_email, $subject, $message );
}


/**
 * Low stock notification email
 **/
function wpdeals_low_stock_notification( $deal ) {
	$_deals = new wpdeals_deals($deal);

	$subject = '[' . get_bloginfo('name') . '] ' . __('Deal low in stock', 'wpdeals');
	
	$message = wpdeals_mail_template( 
		__('Deal low in stock', 'wpdeals'),
		'#' . $_deals->id .' '. $_deals->get_title() .' ' . __('is low in stock.', 'wpdeals') .
                '[<a href="'.get_edit_post_link( $_deals->id ).'" target="_blank">'.__('Edit here', 'wpdeals').'</a>]'
	);

	// Send the mail
	wpdeals_mail( get_option('wpdeals_stock_email_recipient'), $subject, $message );
}

/**
 * No stock notification email
 **/
function wpdeals_no_stock_notification( $deal ) {
	$_deals = new wpdeals_deals($deal);
	
	$subject = '[' . get_bloginfo('name') . '] ' . __('Deal out of stock', 'wpdeals');
	
	$message = wpdeals_mail_template( 
		__('Deal out of stock', 'wpdeals'),
		'#' . $_deals->id .' '. $_deals->get_title() . __('is out of stock.', 'wpdeals')
	);

	// Send the mail
	wpdeals_mail( get_option('wpdeals_stock_email_recipient'), $subject, $message );
}


/**
 * Backorder notification email
 **/
function wpdeals_deals_on_backorder_notification( $deal, $amount ) {
	$_deals = new wpdeals_deals($deal);
	
	$subject = '[' . get_bloginfo('name') . '] ' . __('Deal Backorder', 'wpdeals');

	$message = wpdeals_mail_template( 
		__('Deal Backorder', 'wpdeals'),
		$amount . __(' units of #', 'wpdeals') . $_deals->id .' '. $_deals->get_title() . ' ' . __('have been backordered.', 'wpdeals')
	);

	// Send the mail
	wpdeals_mail( get_option('wpdeals_stock_email_recipient'), $subject, $message );
}

/**
 * Preview Emails
 **/
add_action('admin_init', 'wpdeals_preview_emails');

function wpdeals_preview_emails() {
	if (isset($_GET['preview_wpdeals_mail'])) :
		$nonce = $_REQUEST['_wpnonce'];
		if (!wp_verify_nonce($nonce, 'preview-mail') ) die('Security check'); 
		
		global $email_heading;
	
		$email_heading = __('Email preview', 'wpdeals');
		
		do_action('wpdeals_email_header');
		
		echo '<h2>WPDeals sit amet</h2>';
		
		echo wpautop('Ut ut est qui euismod parum. Dolor veniam tation nihil assum mazim. Possim fiant habent decima et claritatem. Erat me usus gothica laoreet consequat. Clari facer litterarum aliquam insitam dolor. 

Gothica minim lectores demonstraverunt ut soluta. Sequitur quam exerci veniam aliquip litterarum. Lius videntur nisl facilisis claritatem nunc. Praesent in iusto me tincidunt iusto. Dolore lectores sed putamus exerci est. ');
		
		do_action('wpdeals_email_footer');
		
		exit;
		
	endif;
}

/**
 * Add order meta to email templates
 **/
add_action('wpdeals_email_after_order_table', 'wpdeals_email_order_meta', 10, 2);

function wpdeals_email_order_meta( $order, $sent_to_admin ) {
	
	$meta = array();
	$show_fields = apply_filters('wpdeals_email_order_meta_keys', array('coupons'), $sent_to_admin);

	if ($order->customer_note) :
		$meta[__('Note:', 'wpdeals')] = wptexturize($order->customer_note);
	endif;
	
	if ($show_fields) foreach ($show_fields as $field) :
		
		$value = get_post_meta( $order->id, $field, true );
		if ($value) $meta[ucwords(esc_attr($field))] = wptexturize($value);
		
	endforeach;
	
	if (sizeof($meta)>0) :
		echo '<h2>'.__('Order information', 'wpdeals').'</h2>';
		foreach ($meta as $key=>$value) :
			echo '<p><strong>'.$key.':</strong> '.$value.'</p>';
		endforeach;
	endif;
}


/**
 * Customer new account welcome email
 **/
function wpdeals_customer_new_account( $user_id, $plaintext_pass ) {
	global $email_heading, $user_login, $user_pass, $blogname;
	
	if ( empty($plaintext_pass) ) return;
	
	$user = new WP_User($user_id);
	
	$user_login = stripslashes($user->user_login);
	$user_email = stripslashes($user->user_email);
	$user_pass 	= $plaintext_pass;
	 
	$blogname = wp_specialchars_decode(get_option('blogname'), ENT_QUOTES);
	
	$subject		= sprintf(__('Your account on %s', 'wpdeals'), $blogname);
	$email_heading 	= __('Your account details', 'wpdeals');

	// Buffer
	ob_start();
	
	// Get mail template
	wpdeals_get_template('emails/customer_new_account.php', false);
	
	// Get contents
	$message = ob_get_clean();

	// Send the mail	
	wpdeals_mail( $user_email, $subject, $message );
}


/* --------------------------------------------------------------- */
/* Voucher
/* --------------------------------------------------------------- */

/**
 * Voucher Header
 **/
add_action('wpdeals_voucher_header', 'wpdeals_voucher_header');

function wpdeals_voucher_header() {
	wpdeals_get_template('voucher/voucher_header.php', false);
}


/**
 * Voucher Footer
 **/
add_action('wpdeals_voucher_footer', 'wpdeals_voucher_footer');

function wpdeals_voucher_footer() {
	wpdeals_get_template('voucher/voucher_footer.php', false);
}


/**
 * Download Voucher
 **/
add_action('init', 'wpdeals_download_voucher');

function wpdeals_download_voucher() {
	if (isset($_GET['wpdeals-download-voucher']) ) :

		$nonce = $_REQUEST['_wpnonce'];
		if (!wp_verify_nonce($nonce, 'download-voucher') ) die('Security check'); 
				
		global $voucher;
                
		$voucher['heading']     = apply_filters('wpdeals_voucher_heading', __('Voucher Detail', 'wpdeals'));
		$voucher['code']        = (isset($_GET['code']) && $_GET['code'] != '' )? $_GET['code']:'';
		if (isset($_GET['deal-id']) && $_GET['deal-id'] != '' ) 
                    $voucher['id']   = $_GET['deal-id'];
                else
                    return;
		
                // Get mail template
                wpdeals_get_template('voucher/download_voucher.php', false);
                
		exit;
		
	endif;
}