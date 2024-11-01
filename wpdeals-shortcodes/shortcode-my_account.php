<?php
/**
 * My Account Shortcode
 * 
 * Shows the 'my account' section where the customer can view past sales and update their information.
 *
 * @package		WPDeals
 * @category	Shortcode
 * @author		Tokokoo
 */
function get_wpdeals_my_account ( $atts ) {
	global $wpdeals;
	return $wpdeals->shortcode_wrapper('wpdeals_my_account', $atts); 
}	
function wpdeals_my_account( $atts ) {
	global $wpdeals;
	
	extract(shortcode_atts(array(
    	'recent_sales' => 10
	), $atts));

  	$recent_sales = ('all' == $recent_sales) ? -1 : $recent_sales;
	
	global $post, $current_user;

	get_currentuserinfo();
	
	$wpdeals->show_messages();
	
	if (is_user_logged_in()) :
	
		?>
		<p><?php echo sprintf( __('Hello, <strong>%s</strong>. From your account dashboard you can view your recent sales, download deals, view your voucher code and <a href="%s">change your password</a>.', 'wpdeals'), $current_user->display_name, get_permalink(get_option('wpdeals_change_password_page_id'))); ?></p>
		
		<?php do_action('wpdeals_before_my_account'); ?>
		
		<?php if ($downloads = $wpdeals->customer->get_downloadable_deals()) : ?>
		<h2><?php _e('Available downloads', 'wpdeals'); ?></h2>
		<table class="store_table digital-downloads">
                    <thead>
                        <td><?php _e('Deals', 'wpdeals'); ?></td>
                        <td><?php _e('Files', 'wpdeals'); ?></td>
                        <td><?php _e('Download', 'wpdeals'); ?></td>
                    </thead>
			<?php foreach ($downloads as $download) : ?>
                    <tbody>
                        <td><?php echo $download['download_name']; ?></td>
                        <td><a href="<?php echo esc_url( $download['download_url'] ); ?>"><?php _e('Download'); ?></a></td>
                        <td><?php if (is_numeric($download['downloads_remaining'])) : ?><span class="count"><?php echo $download['downloads_remaining'] . _n(' download Remaining', ' downloads Remaining', $download['downloads_remaining'], 'wpdeals'); ?></span><?php else: _e('Unlimited Download', 'wpdeals'); endif; ?></td>
                    </tbody>
			<?php endforeach; ?>
		</table>
		<?php endif; ?>	
                
		<?php if ($coupons = $wpdeals->customer->get_coupons_deals()) : ?>
		<h2><?php _e('Available Vouchers', 'wpdeals'); ?></h2>		
		<table class="store_table voucher-deals">
                    <thead>
                        <td><?php _e('Deals', 'wpdeals'); ?></td>
                        <td><?php _e('Voucher', 'wpdeals'); ?></td>
                        <td><?php _e('How To Use', 'wpdeals'); ?></td>
                        <td><?php _e('Print', 'wpdeals'); ?></td>
                    </thead>
			<?php foreach ($coupons as $coupon) : ?>
                    <tbody>
                        <td><?php echo $coupon['voucher_name']; ?></td>
                        <td><?php echo $coupon['voucher_value']; ?></td>
                        <td><a href="#howto-<?php echo $coupon['deal_id']; ?>" class="zoom"><?php _e('View', 'wpdeals'); ?></a>
                            <div class="voucher-how-to" id="howto-<?php echo $coupon['deal_id']; ?>">
                                <?php echo stripslashes(get_post_meta($coupon['deal_id'], 'how_to_use', true)); ?>
                            </div>
                        </td>
                        <td><a href="#" onclick="window.open('<?php echo wp_nonce_url('?wpdeals-download-voucher=true&deal-id='.$coupon['deal_id'].'&code='.$coupon['voucher_value'], 'download-voucher'); ?>', 'popupwindow', 'scrollbars=yes,width=650,height=650,location=no');return true"><?php _e('Click', 'wpdeals'); ?></a></td>
                    </tbody>
			<?php endforeach; ?>
		</table>
		<?php endif; ?>	
		
		
		<h2><?php _e('Recent Orders', 'wpdeals'); ?></h2>
		<?php
		$args = array(
		    'numberposts'   => $recent_sales,
		    'meta_key'      => '_customer_user',
		    'meta_value'    => get_current_user_id(),
		    'post_type'     => 'deals-sales',
		    'post_status'   => 'publish' 
		);
		$customer_sales = get_posts($args);
		if ($customer_sales) :
		?>
			<table class="store_table my_account_sales">
			
				<thead>
					<tr>
						<th><span class="nobr"><?php _e('#', 'wpdeals'); ?></span></th>
						<th><span class="nobr"><?php _e('Date', 'wpdeals'); ?></span></th>
						<th><span class="nobr"><?php _e('Total', 'wpdeals'); ?></span></th>
						<th colspan="2"><span class="nobr"><?php _e('Status', 'wpdeals'); ?></span></th>
					</tr>
				</thead>
				
				<tbody><?php
					foreach ($customer_sales as $customer_order) :
						$order = new wpdeals_order();
						$order->populate($customer_order);
						?><tr class="order">
							<td><?php echo $order->id; ?></td>
							<td><time title="<?php echo esc_attr( strtotime($order->order_date) ); ?>"><?php echo date(get_option('date_format'), strtotime($order->order_date)); ?></time></td>
							<td><?php echo wpdeals_price($order->order_total); ?></td>
							<td><?php 
								$status = get_term_by('slug', $order->status, 'deals_sales_status');
								echo __($status->name, 'wpdeals'); 
							?></td>
							<td style="text-align:right; white-space:nowrap;">
								<?php if (in_array($order->status, array('pending', 'failed'))) : ?>
									<a href="<?php echo esc_url( $order->get_checkout_payment_url() ); ?>" class="button pay"><?php _e('Pay', 'wpdeals'); ?></a>
									<a href="<?php echo esc_url( $order->get_cancel_order_url() ); ?>" class="button cancel"><?php _e('Cancel', 'wpdeals'); ?></a>
								<?php endif; ?>
								<a href="<?php echo esc_url( add_query_arg('order', $order->id, get_permalink(get_option('wpdeals_view_order_page_id'))) ); ?>" class="button"><?php _e('View', 'wpdeals'); ?></a>
							</td>
						</tr><?php
					endforeach;
				?></tbody>
			
			</table>
		<?php
		else : 
			_e('You have no recent sales.', 'wpdeals');
		endif;
                
		do_action('wpdeals_after_my_account');
		
	else :
		
		// Login/register template
		wpdeals_get_template( 'myaccount/login.php' );
		
	endif;
		
}

function get_wpdeals_change_password() {
	global $wpdeals;
	return $wpdeals->shortcode_wrapper('wpdeals_change_password'); 
}	
function wpdeals_change_password() {
	global $wpdeals;
	
	$user_id = get_current_user_id();
	
	if (is_user_logged_in()) :
		
		if ($_POST) :
			
			if ($user_id>0 && $wpdeals->verify_nonce('change_password')) :
				
				if ( $_POST['password-1'] && $_POST['password-2']  ) :
					
					if ( $_POST['password-1']==$_POST['password-2'] ) :
	
						wp_update_user( array ('ID' => $user_id, 'user_pass' => $_POST['password-1']) ) ;
						
						wp_safe_redirect( get_permalink(get_option('wpdeals_myaccount_page_id')) );
						exit;
						
					else :
					
						$wpdeals->add_error( __('Passwords do not match.', 'wpdeals') );
					
					endif;
				
				else :
				
					$wpdeals->add_error( __('Please enter your password.', 'wpdeals') );
					
				endif;			
				
			endif;
		
		endif;
		
		$wpdeals->show_messages();

		?>
		<form action="<?php echo esc_url( get_permalink(get_option('wpdeals_change_password_page_id')) ); ?>" method="post">
	
			<p class="form-row form-row-first">
				<label for="password-1"><?php _e('New password', 'wpdeals'); ?> <span class="required">*</span></label>
				<input type="password" class="input-text" name="password-1" id="password-1" />
			</p>
			<p class="form-row form-row-last">
				<label for="password-2"><?php _e('Re-enter new password', 'wpdeals'); ?> <span class="required">*</span></label>
				<input type="password" class="input-text" name="password-2" id="password-2" />
			</p>
			<div class="clear"></div>
			<?php $wpdeals->nonce_field('change_password')?>
			<p><input type="submit" class="button" name="save_password" value="<?php _e('Save', 'wpdeals'); ?>" /></p>
	
		</form>
		<?php
		
	else :
	
		wp_safe_redirect( get_permalink(get_option('wpdeals_myaccount_page_id')) );
		exit;
		
	endif;
	
}

function get_wpdeals_view_order () {
	global $wpdeals;
	return $wpdeals->shortcode_wrapper('wpdeals_view_order'); 
}	

function wpdeals_view_order() {
	global $wpdeals;
	
	$user_id = get_current_user_id();
	
	if (is_user_logged_in()) :
	
		if (isset($_GET['order'])) $order_id = (int) $_GET['order']; else $order_id = 0;
	
		$order = new wpdeals_order( $order_id );
		
		if ( $order_id>0 && $order->user_id == get_current_user_id() ) :
			
			echo '<p>' . sprintf( __('Order <mark>#%s</mark> made on <mark>%s</mark>', 'wpdeals'), $order->id, date(get_option('date_format'), strtotime($order->order_date)) );
			
			$status = get_term_by('slug', $order->status, 'deals_sales_status');
			
			echo sprintf( __('. Order status: <mark>%s</mark>', 'wpdeals'), __($status->name, 'wpdeals') );
			
			echo '.</p>';

			$notes = $order->get_customer_order_notes();
			if ($notes) :
				?>
				<h2><?php _e('Order Updates', 'wpdeals'); ?></h2>
				<ol class="commentlist notes">	
					<?php foreach ($notes as $note) : ?>
					<li class="comment note">
						<div class="comment_container">			
							<div class="comment-text">
								<p class="meta"><?php echo date_i18n('l jS \of F Y, h:ia', strtotime($note->comment_date)); ?></p>
								<div class="description">
									<?php echo wpautop(wptexturize($note->comment_content)); ?>
								</div>
				  				<div class="clear"></div>
				  			</div>
							<div class="clear"></div>			
						</div>
					</li>
					<?php endforeach; ?>
				</ol>
				<?php
			endif;
			
			do_action( 'wpdeals_view_order', $order_id );
		
		else :
		
			wp_safe_redirect( get_permalink(get_option('wpdeals_myaccount_page_id')) );
			exit;
			
		endif;
		
	else :
	
		wp_safe_redirect( get_permalink(get_option('wpdeals_myaccount_page_id')) );
		exit;
		
	endif;
}
