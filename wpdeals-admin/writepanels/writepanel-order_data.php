<?php
/**
 * Order Data
 * 
 * Functions for displaying the order data meta box
 *
 * @author 		Tokokoo
 * @category 	Admin Write Panels
 * @package 	WPDeals
 */

/**
 * Order data meta box
 * 
 * Displays the meta box
 */
function wpdeals_order_data_meta_box($post) {
	
	global $post, $wpdb, $thepostid, $sales_status, $wpdeals;
	
	$thepostid = $post->ID;
	
	$order = new wpdeals_order( $thepostid );
	
	add_action('admin_footer', 'wpdeals_meta_scripts');
	
	wp_nonce_field( 'wpdeals_save_data', 'wpdeals_meta_nonce' );
	
	// Custom user
	$customer_user = (int) get_post_meta($post->ID, '_customer_user', true);
	
	// Order status
	$sales_status = wp_get_post_terms($post->ID, 'deals_sales_status');
	if ($sales_status) :
		$sales_status = current($sales_status);
		$sales_status = $sales_status->slug;
	else :
		$sales_status = 'pending';
	endif;
	
	if (!isset($post->post_title) || empty($post->post_title)) :
		$order_title = 'Order';
	else :
		$order_title = $post->post_title;
	endif;
	?>
	<style type="text/css">
		#titlediv, #major-publishing-actions, #minor-publishing-actions, #visibility, #submitdiv { display:none }
	</style>
	<div class="panel-wrap wpdeals">
		<input name="post_title" type="hidden" value="<?php echo esc_attr( $order_title ); ?>" />
		<input name="post_status" type="hidden" value="publish" />
		<div id="order_data" class="panel">
		
			<div class="order_data_left">
				
				<h2><?php _e('Order Details', 'wpdeals'); ?></h2>
				
				<p class="form-field"><label for="sales_status"><?php _e('Order status:', 'wpdeals') ?></label>
				<select id="sales_status" name="sales_status" class="chosen_select">
					<?php
						$statuses = (array) get_terms('deals_sales_status', array('hide_empty' => 0, 'orderby' => 'id'));
						foreach ($statuses as $status) :
							echo '<option value="'.$status->slug.'" ';
							if ($status->slug==$sales_status) echo 'selected="selected"';
							echo '>'.__($status->name, 'wpdeals').'</option>';
						endforeach;
					?>
				</select></p>
	
				<p class="form-field form-field-wide"><label for="customer_user"><?php _e('Customer:', 'wpdeals') ?></label>
				<select id="customer_user" name="customer_user" class="chosen_select">
					<option value=""><?php _e('Guest', 'wpdeals') ?></option>
					<?php
						$users = new WP_User_Query( array( 'orderby' => 'display_name' ) );
						$users = $users->get_results();
						if ($users) foreach ( $users as $user ) :
							echo '<option value="'.$user->ID.'" '; selected($customer_user, $user->ID); echo '>' . $user->display_name . ' ('.$user->user_email.')</option>';
						endforeach;
					?>
				</select></p>
			
			</div>
			<div class="order_data_right">
				<div class="order_data">
                                    
                                    <?php $data = get_post_custom( $post->ID ); ?>
                                    <h2><?php _e('Order Total', 'wpdeals'); ?></h2>
                                    <ul class="totals">

                                            <li>         
                                                    <label><?php _e('Total:', 'wpdeals'); ?></label>
                                                    <input type="text" id="_order_total" name="_order_total" placeholder="0.00" value="<?php 
                                                    if (isset($data['_order_total'][0])) echo $data['_order_total'][0];
                                                    ?>" class="first" readonly="readonly" /> 
                                            </li>	
                                            <li>                    
                                                    <?php 
                                                    // get all available gateways payment.
                                                    $available_gateways = $wpdeals->payment_gateways->get_available_payment_gateways();
                                                    $selected   = '';

                                                    if(!empty($available_gateways)){ ?>
                                                    <label><?php _e('Payment Method:', 'wpdeals'); ?></label>
                                                    <select name="_payment_method" id="_payment_method">
                                                        <?php 
                                                            foreach($available_gateways as $key => $value): 
                                                            if($value->id == $data['_payment_method'][0] && isset($data['_payment_method'][0]) ) $selected = ' selected="selected"';
                                                            else $selected = '';
                                                        ?>
                                                        <option<?php echo $selected; ?> value="<?php echo $value->id; ?>"><?php echo $value->title; ?></option>
                                                        <?php endforeach; ?>
                                                    </select>
                                                    <?php } ?>
                                            </li>

                                    </ul>
				</div>
			</div>
			<div class="clear"></div>

		</div>
	</div>
	<?php
}

/**
 * Order items meta box
 * 
 * Displays the order items meta box - for showing individual items in the order
 */
function wpdeals_order_items_meta_box($post) {
	global $wpdeals;
	
	$order_items 	= (array) maybe_unserialize( get_post_meta($post->ID, '_order_items', true) );
	?>
	<div class="wpdeals_order_items_wrapper">
		<table cellpadding="0" cellspacing="0" class="wpdeals_order_items">
			<thead>
				<tr>
					<th class="daily-deals-id" width="1%"><?php _e('ID', 'wpdeals'); ?></th>
					<th class="name"><?php _e('Name', 'wpdeals'); ?></th>
					<?php do_action('wpdeals_admin_order_item_headers'); ?>
					<th class="quantity"><?php _e('Quantity', 'wpdeals'); ?></th>
                                        <th class="cost"><?php _e('Base&nbsp;Price', 'wpdeals'); ?>&nbsp;<a class="tips" tip="<?php _e('Cost before discounts. Up to 2 decimals are allowed for precision.', 'wpdeals'); ?>" href="#">[?]</a></th>
                                        <th class="cost"><?php _e('Sale&nbsp;Price', 'wpdeals'); ?>&nbsp;<a class="tips" tip="<?php _e('Cost after discounts. Up to 2 decimals are allowed for precision.', 'wpdeals'); ?>" href="#">[?]</a></th>
					<th class="center" width="1%"><?php _e('Remove', 'wpdeals'); ?></th>
				</tr>
			</thead>
			<tbody id="order_items_list">	
				
				<?php $loop = 0; if (sizeof($order_items)>0 && isset($order_items[0]['id'])) foreach ($order_items as $item) : 
				
					if (isset($item['variation_id']) && $item['variation_id'] > 0) :
						$_deals = new wpdeals_deals_variation( $item['variation_id'] );
					else :
						$_deals = new wpdeals_deals( $item['id'] );
					endif;

					?>
					<tr class="item" rel="<?php echo $loop; ?>">
						<td class="daily-deals-id">
							<img class="tips" tip="<?php
								echo '<strong>'.__('Deal ID:', 'wpdeals').'</strong> '. $item['id'];
								echo '<br/><strong>'.__('Variation ID:', 'wpdeals').'</strong> '; if ($item['variation_id']) echo $item['variation_id']; else echo '-';
							?>" src="<?php echo $wpdeals->plugin_url(); ?>/wpdeals-assets/images/tip.png" />
						</td>
						<td class="name">
							<a href="<?php echo esc_url( admin_url('post.php?post='. $_deals->id .'&action=edit') ); ?>"><?php echo $item['name']; ?></a>
							<?php
								if (isset($_deals->variation_data)) echo '<br/>' . wpdeals_get_formatted_variation( $_deals->variation_data, true );
							?>
						</td>
						<?php do_action('wpdeals_admin_order_item_values', $_deals, $item); ?>
						
						<td class="quantity">
								<input type="text" name="item_quantity[<?php echo $loop; ?>]" placeholder="<?php _e('0', 'wpdeals'); ?>" value="<?php echo esc_attr( $item['qty'] ); ?>" />
						</td>
						
						<td class="cost">
								<input type="text" name="base_item_cost[<?php echo $loop; ?>]" placeholder="<?php _e('0.00', 'wpdeals'); ?>" value="<?php if (isset($item['base_cost'])) echo esc_attr( $item['base_cost'] ); else echo esc_attr( $item['cost'] ); ?>" />
						</td>
						
						<td class="cost">
								<input type="text" name="discount_item_cost[<?php echo $loop; ?>]" placeholder="<?php _e('0.00', 'wpdeals'); ?>" value="<?php if (isset($item['discount_cost'])) echo esc_attr( $item['discount_cost'] ); else echo esc_attr( $item['cost'] ); ?>" />
						</td>
						
						<td class="center">
							<button type="button" class="remove_row button">&times;</button>
							<input type="hidden" name="item_id[<?php echo $loop; ?>]" value="<?php echo esc_attr( $item['id'] ); ?>" />
							<input type="hidden" name="item_name[<?php echo $loop; ?>]" value="<?php echo esc_attr( $item['name'] ); ?>" />
							<input type="hidden" name="item_variation[<?php echo $loop; ?>]" value="<?php echo esc_attr( $item['variation_id'] ); ?>" />
						</td>
						
					</tr>
				<?php $loop++; endforeach; ?>
			</tbody>
		</table>
	</div>
	
	<p class="buttons">
		<select name="add_item_id" class="add_item_id chosen_select_nostd" data-placeholder="<?php _e('Choose an item&hellip;', 'wpdeals') ?>">
			<?php
				echo '<option value=""></option>';
				
				$args = array(
					'post_type' 		=> 'daily-deals',
					'posts_per_page' 	=> -1,
					'post_status'		=> 'publish',
					'post_parent'		=> 0,
					'order'			=> 'ASC',
					'orderby'		=> 'title'
				);
				$deals = get_posts( $args );
				
				if ($deals) foreach ($deals as $deal) :
					
					echo '<option value="'.$deal->ID.'">'.$deal->post_title.'</option>';
					
					$args_get_children = array(
						'post_type' => array( 'deal-variations', 'daily-deals' ),
						'posts_per_page' 	=> -1,
						'order'			=> 'ASC',
						'orderby'		=> 'title',
						'post_parent'		=> $deal->ID
					);	
						
					if ( $children_deals =& get_children( $args_get_children ) ) :
		
						foreach ($children_deals as $child) :
							
							echo '<option value="'.$child->ID.'">&nbsp;&nbsp;&mdash;&nbsp;'.$child->post_title.'</option>';
							
						endforeach;
						
					endif;
					
				endforeach;
			?>
		</select>
		
		<button type="button" class="button button-primary add_deals_sales_item"><?php _e('Add item', 'wpdeals'); ?></button>
	</p>
	<p class="buttons buttons-alt">
		<button type="button" class="button button calc_totals"><?php _e('Calculate totals', 'wpdeals'); ?></button>
	</p>	
	<div class="clear"></div>
	<?php
	
}

/**
 * Order actions meta box
 * 
 * Displays the order actions meta box - buttons for managing order stock and sending the customer an invoice.
 */
function wpdeals_order_actions_meta_box($post) {
	?>
	<ul class="order_actions">
		<li><input type="submit" class="button button-primary tips" name="save" value="<?php _e('Save Order', 'wpdeals'); ?>" tip="<?php _e('Save/update the order', 'wpdeals'); ?>" /></li>

		<li><input type="submit" class="button tips" name="reduce_stock" value="<?php _e('Reduce stock', 'wpdeals'); ?>" tip="<?php _e('Reduces stock for each item in the order; useful after manually creating an order or manually marking an order as paid.', 'wpdeals'); ?>" /></li>
		
		<li><input type="submit" class="button tips" name="restore_stock" value="<?php _e('Restore stock', 'wpdeals'); ?>" tip="<?php _e('Restores stock for each item in the order; useful after refunding or canceling the entire order.', 'wpdeals'); ?>" /></li>
		
		<li><input type="submit" class="button tips" name="invoice" value="<?php _e('Email invoice', 'wpdeals'); ?>" tip="<?php _e('Email the order to the customer. Unpaid sales will include a payment link.', 'wpdeals'); ?>" /></li>
		
		<?php do_action('wpdeals_order_actions', $post->ID); ?>
		
		<li class="wide">
		<?php
		if ( current_user_can( "delete_post", $post->ID ) ) {
			if ( !EMPTY_TRASH_DAYS )
				$delete_text = __('Delete Permanently', 'wpdeals');
			else
				$delete_text = __('Move to Trash', 'wpdeals');
			?>
		<a class="submitdelete deletion" href="<?php echo esc_url( get_delete_post_link($post->ID) ); ?>"><?php echo $delete_text; ?></a><?php
		} ?>
		</li>
	</ul>
	<?php
}

/**
 * Order Data Save
 * 
 * Function for processing and storing all order data.
 */
add_action('wpdeals_process_deals-sales_meta', 'wpdeals_process_deals_sales_meta', 1, 2);

function wpdeals_process_deals_sales_meta( $post_id, $post ) {
	global $wpdb;
	
	$wpdeals_errors = array();
	
	// Add key
		add_post_meta( $post_id, '_order_key', uniqid('order_') );

	// Update post data
		update_post_meta( $post_id, '_payment_method', stripslashes( $_POST['_payment_method'] ));
		update_post_meta( $post_id, '_order_total', stripslashes( $_POST['_order_total'] ));
		update_post_meta( $post_id, '_customer_user', (int) $_POST['customer_user'] );
	
	// Order items
		$order_items = array();
	
		if (isset($_POST['item_id'])) :
			 $item_id		= $_POST['item_id'];
			 $item_variation	= $_POST['item_variation'];
			 $item_name 		= $_POST['item_name'];
			 $item_quantity 	= $_POST['item_quantity'];
			 $base_item_cost	= $_POST['base_item_cost'];
			 $discount_item_cost	= $_POST['discount_item_cost'];
	
			 for ($i=0; $i<sizeof($item_id); $i++) :
			 	
			 	if (!isset($item_id[$i])) continue;
			 	if (!isset($item_name[$i])) continue;
			 	if (!isset($item_quantity[$i]) || $item_quantity[$i] < 1) continue;
                                			 	
			 	// Add to array	 	
			 	$order_items[] = apply_filters('update_order_item', array(
			 		'id' 			=> htmlspecialchars(stripslashes($item_id[$i])),
			 		'variation_id'          => (int) $item_variation[$i],
			 		'name' 			=> htmlspecialchars(stripslashes($item_name[$i])),
			 		'qty' 			=> (int) $item_quantity[$i],
			 		'cost' 			=> rtrim(rtrim(number_format(wpdeals_clean($item_cost[$i]), 4, '.', ''), '0'), '.'),
			 		'base_cost'		=> rtrim(rtrim(number_format(wpdeals_clean($base_item_cost[$i]), 4, '.', ''), '0'), '.'),
			 		'discount_cost'		=> rtrim(rtrim(number_format(wpdeals_clean($discount_item_cost[$i]), 4, '.', ''), '0'), '.')
			 	));
			 	
			 endfor; 
		endif;	
                
	
		update_post_meta( $post_id, '_order_items', $order_items );

	// Give a password - not used, but can protect the content/comments from theme functions
		if ($post->post_password=='') :
			$order_post = array();
			$order_post['ID'] = $post_id;
			$order_post['post_password'] = uniqid('order_');
			wp_update_post( $order_post );
		endif;
		
	// Order data saved, now get it so we can manipulate status
		$order = new wpdeals_order( $post_id );
		
	// Order status
		$order->update_status( $_POST['sales_status'] );
	
	// Handle button actions
	
		if (isset($_POST['reduce_stock']) && $_POST['reduce_stock'] && sizeof($order_items)>0) :
			
			$order->add_order_note( __('Manually reducing stock.', 'wpdeals') );
			
			foreach ($order_items as $order_item) :
						
				$_deals = $order->get_deals_from_item( $order_item );
				
				if ($_deals->exists) :
				
				 	if ($_deals->managing_stock()) :
						
						$old_stock = $_deals->_stock;
						
						$new_quantity = $_deals->reduce_stock( $order_item['qty'] );
						
						$order->add_order_note( sprintf( __('Item #%s stock reduced from %s to %s.', 'wpdeals'), $order_item['id'], $old_stock, $new_quantity) );
							
						if ($new_quantity<0) :
							do_action('wpdeals_deals_on_backorder_notification', $order_item['id'], $values['quantity']);
						endif;
						
						// stock status notifications
						if (get_option('wpdeals_notify_no_stock_amount') && get_option('wpdeals_notify_no_stock_amount')>=$new_quantity) :
							do_action('wpdeals_no_stock_notification', $order_item['id']);
						elseif (get_option('wpdeals_notify_low_stock_amount') && get_option('wpdeals_notify_low_stock_amount')>=$new_quantity) :
							do_action('wpdeals_low_stock_notification', $order_item['id']);
						endif;
						
					endif;
				
				else :
					
					$order->add_order_note( sprintf( __('Item %s %s not found, skipping.', 'wpdeals'), $order_item['id'], $order_item['name'] ) );
					
				endif;
			 	
			endforeach;
			
			$order->add_order_note( __('Manual stock reduction complete.', 'wpdeals') );
			
		elseif (isset($_POST['restore_stock']) && $_POST['restore_stock'] && sizeof($order_items)>0) :
		
			$order->add_order_note( __('Manually restoring stock.', 'wpdeals') );
			
			foreach ($order_items as $order_item) :
						
				$_deals = $order->get_deals_from_item( $order_item );
				
				if ($_deals->exists) :
				
				 	if ($_deals->managing_stock()) :
						
						$old_stock = $_deals->_stock;
						
						$new_quantity = $_deals->increase_stock( $order_item['qty'] );
						
						$order->add_order_note( sprintf( __('Item #%s stock increased from %s to %s.', 'wpdeals'), $order_item['id'], $old_stock, $new_quantity) );
						
					endif;
				
				else :
					
					$order->add_order_note( sprintf( __('Item %s %s not found, skipping.', 'wpdeals'), $order_item['id'], $order_item['name'] ) );
					
				endif;
			 	
			endforeach;
			
			$order->add_order_note( __('Manual stock restore complete.', 'wpdeals') );
		
		elseif (isset($_POST['invoice']) && $_POST['invoice']) :
			
			// Mail link to customer
			wpdeals_pay_for_order_customer_notification( $order );
			
		endif;
	
	// Error Handling
		if (sizeof($wpdeals_errors)>0) update_option('wpdeals_errors', $wpdeals_errors);
}