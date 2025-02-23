<?php
/**
 * WPDeals Ajax Handlers
 * 
 * Handles AJAX requests via wp_ajax hook (both admin and front-end events)
 *
 * @author 		Tokokoo
 * @category 	AJAX
 * @package 	WPDeals
 */

/*-----------------------------------------------------------------------------------*/
/* Frontend */
/*-----------------------------------------------------------------------------------*/ 

	/**
	 * AJAX update order review on checkout
	 */
	add_action('wp_ajax_wpdeals_update_order_review', 'wpdeals_ajax_update_order_review');
	add_action('wp_ajax_nopriv_wpdeals_update_order_review', 'wpdeals_ajax_update_order_review');
	
	function wpdeals_ajax_update_order_review() {
		global $wpdeals;
		
		check_ajax_referer( 'update-order-review', 'security' );
		
		if (!defined('WPDEALS_CHECKOUT')) define('WPDEALS_CHECKOUT', true);
		
		if (sizeof($wpdeals->cart->get_cart())==0) :
			echo '<p class="error">'.__('Sorry, your session has expired.', 'wpdeals').' <a href="'.home_url().'">'.__('Return to homepage &rarr;', 'wpdeals').'</a></p>';
			die();
		endif;
		
		do_action('wpdeals_checkout_update_order_review', $_POST['post_data']);
		
		if (isset($_POST['shipping_method'])) $_SESSION['_chosen_shipping_method'] = $_POST['shipping_method'];
		if (isset($_POST['country'])) $wpdeals->customer->set_country( $_POST['country'] );
		if (isset($_POST['state'])) $wpdeals->customer->set_state( $_POST['state'] );
		if (isset($_POST['postcode'])) $wpdeals->customer->set_postcode( $_POST['postcode'] );
		if (isset($_POST['s_country'])) $wpdeals->customer->set_shipping_country( $_POST['s_country'] );
		if (isset($_POST['s_state'])) $wpdeals->customer->set_shipping_state( $_POST['s_state'] );
		if (isset($_POST['s_postcode'])) $wpdeals->customer->set_shipping_postcode( $_POST['s_postcode'] );
		
		$wpdeals->cart->calculate_totals();
		
		do_action('wpdeals_checkout_order_review'); // Display review order table
	
		die();
	}
	
	/**
	 * AJAX add to cart
	 */
	add_action('wp_ajax_wpdeals_add_to_cart', 'wpdeals_ajax_add_to_cart');
	add_action('wp_ajax_nopriv_wpdeals_add_to_cart', 'wpdeals_ajax_add_to_cart');
	
	function wpdeals_ajax_add_to_cart() {
		
		global $wpdeals;
		
		check_ajax_referer( 'buy-this', 'security' );
		
		$deal_id = (int) $_POST['deal_id'];
	
		if ($wpdeals->cart->add_to_cart($deal_id, 1)) :
			// Return html fragments
			$data = apply_filters('add_to_cart_fragments', array());
		else :
			// Return error
			$data = array(
				'error' => $wpdeals->errors[0]
			);
			$wpdeals->clear_messages();
		endif;
		
		echo json_encode( $data );
		
		die();
	}

	/**
	 * Process ajax checkout form
	 */
	add_action('wp_ajax_wpdeals-checkout', 'wpdeals_process_checkout');
	add_action('wp_ajax_nopriv_wpdeals-checkout', 'wpdeals_process_checkout');
	
	function wpdeals_process_checkout () {
		global $wpdeals, $wpdeals_checkout;
		
		$wpdeals_checkout = $wpdeals->checkout();
		$wpdeals_checkout->process_checkout();
		
		die(0);
	}
        
	/**
	 * Process ajax expired deals
	 */
	add_action('wp_ajax_wpdeals_expired_deals', 'wpdeals_expired_deals');
	add_action('wp_ajax_nopriv_wpdeals_expired_deals', 'wpdeals_expired_deals');
	
	function wpdeals_expired_deals () {
		global $wpdeals;
                
                check_ajax_referer( 'expired-deals', 'security' );
                		
		$deal_id = (int) $_POST['post_id'];
                
                if($deal_id)
                    update_post_meta ($deal_id, '_is_expired', 'yes');
		
		die(0);
	}

/*-----------------------------------------------------------------------------------*/
/* Admin */
/*-----------------------------------------------------------------------------------*/ 

	/**
	 * Feature a deals from admin
	 */
	function wpdeals_feature_deals() {
	
		if( !is_admin() ) die;
		
		if( !current_user_can('edit_posts') ) wp_die( __('You do not have sufficient permissions to access this page.', 'wpdeals') );
		
		if( !check_admin_referer('wpdeals-feature-daily-deals')) wp_die( __('You have taken too long. Please go back and retry.', 'wpdeals') );
		
		$post_id = isset($_GET['deal_id']) && (int)$_GET['deal_id'] ? (int)$_GET['deal_id'] : '';
		
		if(!$post_id) die;
		
		$post = get_post($post_id);
		if(!$post) die;
		
		if($post->post_type !== 'daily-deals') die;
		
		$deal = new wpdeals_deals($post->ID);
	
		if ($deal->is_featured()) update_post_meta($post->ID, 'featured', 'no');
		else update_post_meta($post->ID, 'featured', 'yes');
		
		$sendback = remove_query_arg( array('trashed', 'untrashed', 'deleted', 'ids'), wp_get_referer() );
		wp_safe_redirect( $sendback );
	
	}
	add_action('wp_ajax_wpdeals-feature-daily-deals', 'wpdeals_feature_deals');
	
	/**
	 * Mark an order as complete
	 */
	function wpdeals_mark_order_complete() {
	
		if( !is_admin() ) die;
		if( !current_user_can('edit_posts') ) wp_die( __('You do not have sufficient permissions to access this page.', 'wpdeals') );
		if( !check_admin_referer()) wp_die( __('You have taken too long. Please go back and retry.', 'wpdeals') );
		$order_id = isset($_GET['order_id']) && (int) $_GET['order_id'] ? (int) $_GET['order_id'] : '';
		if(!$order_id) die;
		
		$order = new wpdeals_order( $order_id );
		$order->update_status( 'completed' );
		
		wp_safe_redirect( wp_get_referer() );
	
	}
	add_action('wp_ajax_wpdeals-mark-order-complete', 'wpdeals_mark_order_complete');
	
	/**
	 * Mark an order as processing
	 */
	function wpdeals_mark_order_processing() {
	
		if( !is_admin() ) die;
		if( !current_user_can('edit_posts') ) wp_die( __('You do not have sufficient permissions to access this page.', 'wpdeals') );
		if( !check_admin_referer()) wp_die( __('You have taken too long. Please go back and retry.', 'wpdeals') );
		$order_id = isset($_GET['order_id']) && (int) $_GET['order_id'] ? (int) $_GET['order_id'] : '';
		if(!$order_id) die;
		
		$order = new wpdeals_order( $order_id );
		$order->update_status( 'processing' );
		
		wp_safe_redirect( wp_get_referer() );
	
	}
	add_action('wp_ajax_wpdeals-mark-order-processing', 'wpdeals_mark_order_processing');
	
	/**
	 * Delete variation via ajax function
	 */
	add_action('wp_ajax_wpdeals_remove_variation', 'wpdeals_remove_variation');
	
	function wpdeals_remove_variation() {
		
		check_ajax_referer( 'delete-variation', 'security' );
		$variation_id = intval( $_POST['variation_id'] );
		$variation = get_post($variation_id);
		if ($variation && $variation->post_type=="deal-variations") wp_delete_post( $variation_id );
		die();
		
	}
	
	/**
	 * Add variation via ajax function
	 */
	add_action('wp_ajax_wpdeals_add_variation', 'wpdeals_add_variation');
	
	function wpdeals_add_variation() {
		
		check_ajax_referer( 'add-variation', 'security' );
		
		$post_id = intval( $_POST['post_id'] );
	
		$variation = array(
			'post_title' => 'Deal #' . $post_id . ' Variation',
			'post_content' => '',
			'post_status' => 'publish',
			'post_author' => get_current_user_id(),
			'post_parent' => $post_id,
			'post_type' => 'deal-variations'
		);
		$variation_id = wp_insert_post( $variation );
		
		echo $variation_id;
		
		die();
		
	}
	
	/**
	 * Link all variations via ajax function
	 */
	add_action('wp_ajax_wpdeals_link_all_variations', 'wpdeals_link_all_variations');
	
	function wpdeals_link_all_variations() {
		
		check_ajax_referer( 'link-variations', 'security' );
		
		$post_id = intval( $_POST['post_id'] );
		
		if (!$post_id) die();
		
		$variations = array();
		
		$_deals = new wpdeals_deals( $post_id );
			
		// Put variation attributes into an array
		foreach ($_deals->get_attributes() as $attribute) :
									
			if ( !$attribute['is_variation'] ) continue;
			
			$attribute_field_name = 'attribute_' . sanitize_title($attribute['name']);
			
			if ($attribute['is_taxonomy']) :
				$post_terms = wp_get_post_terms( $post_id, $attribute['name'] );
				$options = array();
				foreach ($post_terms as $term) :
					$options[] = $term->slug;
				endforeach;
			else :
				$options = explode('|', $attribute['value']);
			endif;
			
			$options = array_map('trim', $options);
			
			$variations[$attribute_field_name] = $options;
			
		endforeach;
		
		// Quit out if none were found
		if (sizeof($variations)==0) die();
		
		// Get existing variations so we don't create duplicated
	    $available_variations = array();
	    
	    foreach($_deals->get_children() as $child_id) {
	    	$child = $_deals->get_child( $child_id );
	        if($child instanceof wpdeals_deals_variation) {
	            $available_variations[] = $child->get_variation_attributes();
	        }
	    }
		
		// Created posts will all have the following data
		$variation_post_data = array(
			'post_title' => 'Deal #' . $post_id . ' Variation',
			'post_content' => '',
			'post_status' => 'publish',
			'post_author' => get_current_user_id(),
			'post_parent' => $post_id,
			'post_type' => 'deal-variations'
		);
			
		// Now find all combinations and create posts
		if (!function_exists('array_cartesian')) {
			function array_cartesian($input) {
			    $result = array();
			 
			    while (list($key, $values) = each($input)) {
			        // If a sub-array is empty, it doesn't affect the cartesian deals
			        if (empty($values)) {
			            continue;
			        }
			 
			        // Special case: seeding the deals array with the values from the first sub-array
			        if (empty($result)) {
			            foreach($values as $value) {
			                $result[] = array($key => $value);
			            }
			        }
			        else {
			            // Second and subsequent input sub-arrays work like this:
			            //   1. In each existing array inside $deal, add an item with
			            //      key == $key and value == first item in input sub-array
			            //   2. Then, for each remaining item in current input sub-array,
			            //      add a copy of each existing array inside $deal with
			            //      key == $key and value == first item in current input sub-array
			 
			            // Store all items to be added to $deal here; adding them on the spot
			            // inside the foreach will result in an infinite loop
			            $append = array();
			            foreach($result as &$deal) {
			                // Do step 1 above. array_shift is not the most efficient, but it
			                // allows us to iterate over the rest of the items with a simple
			                // foreach, making the code short and familiar.
			                $deal[$key] = array_shift($values);
			 
			                // $deal is by reference (that's why the key we added above
			                // will appear in the end result), so make a copy of it here
			                $copy = $deal;
			 
			                // Do step 2 above.
			                foreach($values as $item) {
			                    $copy[$key] = $item;
			                    $append[] = $copy;
			                }
			 
			                // Undo the side effecst of array_shift
			                array_unshift($values, $deal[$key]);
			            }
			 
			            // Out of the foreach, we can add to $results now
			            $result = array_merge($result, $append);
			        }
			    }
			    
			    return $result;
			}
		}
		
		$variation_ids = array();
		$possible_variations = array_cartesian( $variations );
		
		foreach ($possible_variations as $variation) :
			
			// Check if variation already exists
			if (in_array($variation, $available_variations)) continue;
			
			$variation_id = wp_insert_post( $variation_post_data );
			
			$variation_ids[] = $variation_id;
			
			foreach ($variation as $key => $value) :
				
				update_post_meta( $variation_id, $key, $value );
				
			endforeach;
			
		endforeach;
		
		echo 1;
			
		die();
		
	}
	
	/**
	 * Get customer details via ajax
	 */
	add_action('wp_ajax_wpdeals_get_customer_details', 'wpdeals_get_customer_details');
	
	function wpdeals_get_customer_details() {
		
		global $wpdeals;
	
		check_ajax_referer( 'get-customer-details', 'security' );
	
		$user_id = (int) trim(stripslashes($_POST['user_id']));
		$type_to_load = esc_attr(trim(stripslashes($_POST['type_to_load'])));
		
		$customer_data = array(
			$type_to_load . '_first_name' => get_user_meta( $user_id, $type_to_load . '_first_name', true ),
			$type_to_load . '_last_name' => get_user_meta( $user_id, $type_to_load . '_last_name', true ),
			$type_to_load . '_company' => get_user_meta( $user_id, $type_to_load . '_company', true ),
			$type_to_load . '_address_1' => get_user_meta( $user_id, $type_to_load . '_address_1', true ),
			$type_to_load . '_address_2' => get_user_meta( $user_id, $type_to_load . '_address_2', true ),
			$type_to_load . '_city' => get_user_meta( $user_id, $type_to_load . '_city', true ),
			$type_to_load . '_postcode' => get_user_meta( $user_id, $type_to_load . '_postcode', true ),
			$type_to_load . '_country' => get_user_meta( $user_id, $type_to_load . '_country', true ),
			$type_to_load . '_state' => get_user_meta( $user_id, $type_to_load . '_state', true ),
			$type_to_load . '_email' => get_user_meta( $user_id, $type_to_load . '_email', true ),
			$type_to_load . '_phone' => get_user_meta( $user_id, $type_to_load . '_phone', true ),
		);
		
		echo json_encode($customer_data);
		
		// Quit out
		die();
		
	}
	
	/**
	 * Add order item via ajax
	 */
	add_action('wp_ajax_wpdeals_add_order_item', 'wpdeals_add_order_item');
	
	function wpdeals_add_order_item() {
		global $wpdeals;
	
		check_ajax_referer( 'add-order-item', 'security' );
		
		global $wpdb;
		
		$index = trim(stripslashes($_POST['index']));
		$item_to_add = trim(stripslashes($_POST['item_to_add']));
		
		$post = '';
		
		// Find the item
		if (is_numeric($item_to_add)) :
			$post = get_post( $item_to_add );
		endif;
		
		if (!$post || ($post->post_type!=='daily-deals' && $post->post_type!=='deal-variations')) :
			$post_id = $wpdb->get_var($wpdb->prepare("
				SELECT post_id
				FROM $wpdb->posts
				LEFT JOIN $wpdb->postmeta ON ($wpdb->posts.ID = $wpdb->postmeta.post_id)
				WHERE $wpdb->posts.post_status = 'publish'
				AND $wpdb->posts.post_type = 'deals-sales'
				AND $wpdb->postmeta.meta_value = %s
				LIMIT 1
			"), $item_to_add );
			$post = get_post( $post_id );
		endif;
		
		if (!$post || ($post->post_type!=='daily-deals' && $post->post_type!=='deal-variations')) :
			die();
		endif;
		
		if ($post->post_type=="daily-deals") :
			$_deals = new wpdeals_deals( $post->ID );
		else :
			$_deals = new wpdeals_deals_variation( $post->ID );
		endif;
		?>
		<tr class="item" rel="<?php echo $index; ?>">
			<td class="daily-deals-id">
				<img class="tips" tip="<?php
					echo '<strong>'.__('Deal ID:', 'wpdeals').'</strong> '. $_deals->id;
					echo '<br/><strong>'.__('Variation ID:', 'wpdeals').'</strong> '; if ($_deals->variation_id) echo $_deals->variation_id; else echo '-';
				?>" src="<?php echo $wpdeals->plugin_url(); ?>/wpdeals-assets/images/tip.png" />
			</td>
			<td class="name">
				<a href="<?php echo esc_url( admin_url('post.php?post='. $_deals->id .'&action=edit') ); ?>"><?php echo $_deals->get_title(); ?></a>
				<?php
					if (isset($_deals->variation_data)) :
						echo '<br/>' . wpdeals_get_formatted_variation( $_deals->variation_data, true );
					endif;
				?>
			</td>
			<?php do_action('wpdeals_admin_order_item_values', $_deals); ?>
			<td class="quantity"><input type="text" name="item_quantity[<?php echo $index; ?>]" placeholder="<?php _e('0', 'wpdeals'); ?>" value="1" /></td>
			<td class="cost"><input type="text" name="base_item_cost[<?php echo $index; ?>]" placeholder="<?php _e('0.00', 'wpdeals'); ?>" value="<?php echo esc_attr( $_deals->get_price( false ) ); ?>" /></td>
			<td class="cost"><input type="text" name="discount_item_cost[<?php echo $index; ?>]" placeholder="<?php _e('0.00', 'wpdeals'); ?>" value="<?php echo esc_attr( $_deals->get_sale( false ) ); ?>" /></td>
			<td class="center">
				<input type="hidden" name="item_id[<?php echo $index; ?>]" value="<?php echo esc_attr( $_deals->id ); ?>" />
				<input type="hidden" name="item_name[<?php echo $index; ?>]" value="<?php echo esc_attr( $_deals->get_title() ); ?>" />
				<input type="hidden" name="item_variation[<?php echo $index; ?>]" value="<?php if (isset($_deals->variation_id)) echo $_deals->variation_id; ?>" />
				<button type="button" class="remove_row button">&times;</button>
			</td>
		</tr>
		<?php
		
		// Quit out
		die();
		
	}
	
	/**
	 * Add order note via ajax
	 */
	add_action('wp_ajax_wpdeals_add_order_note', 'wpdeals_add_order_note');
	
	function wpdeals_add_order_note() {
		
		global $wpdeals;
	
		check_ajax_referer( 'add-order-note', 'security' );
		
		$post_id 	= (int) $_POST['post_id'];
		$note		= strip_tags(wpdeals_clean($_POST['note']));
		$note_type	= $_POST['note_type'];
		
		$is_customer_note = ($note_type=='customer') ? 1 : 0;
		
		if ($post_id>0) :
			$order = new wpdeals_order( $post_id );
			$comment_id = $order->add_order_note( $note, $is_customer_note );
			
			echo '<li rel="'.$comment_id.'" class="note ';
			if ($is_customer_note) echo 'customer-note';
			echo '"><div class="note_content">';
			echo wpautop(wptexturize($note));
			echo '</div><p class="meta">'. sprintf(__('added %s ago', 'wpdeals'), human_time_diff(current_time('timestamp'))) .' - <a href="#" class="delete_note">'.__('Delete note', 'wpdeals').'</a></p>';
			echo '</li>';
			
		endif;
		
		// Quit out
		die();
	}
	
	/**
	 * Delete order note via ajax
	 */
	add_action('wp_ajax_wpdeals_delete_order_note', 'wpdeals_delete_order_note');
	
	function wpdeals_delete_order_note() {
		
		global $wpdeals;
	
		check_ajax_referer( 'delete-order-note', 'security' );
		
		$note_id 	= (int) $_POST['note_id'];
		
		if ($note_id>0) :
			wp_delete_comment( $note_id );
		endif;
		
		// Quit out
		die();
	}
	
	/**
	 * Search for deals for upsells/crosssells
	 */
	add_action('wp_ajax_wpdeals_upsell_crosssell_search_deals', 'wpdeals_upsell_crosssell_search_deals');
	
	function wpdeals_upsell_crosssell_search_deals() {
		
		check_ajax_referer( 'search-daily-deals', 'security' );
		
		$search = (string) urldecode(stripslashes(strip_tags($_POST['search'])));
		$name = (string) urldecode(stripslashes(strip_tags($_POST['name'])));
		
		if (empty($search)) die();
		
		if (is_numeric($search)) :
			
			$args = array(
				'post_type'	=> 'daily-deals',
				'post_status' => 'publish',
				'posts_per_page' => 15,
				'post__in' => array(0, $search)
			);
			
		else :
		
			$args = array(
				'post_type'	=> 'daily-deals',
				'post_status' => 'publish',
				'posts_per_page' => 15,
				's' => $search
			);
		
		endif;
		
		$posts = get_posts( $args );
		
		if ($posts) : foreach ($posts as $post) : 
			
			?>
			<li rel="<?php echo $post->ID; ?>"><button type="button" name="Add" class="button add_crosssell" title="Add"><?php _e('Cross-sell', 'wpdeals'); ?> &rarr;</button><button type="button" name="Add" class="button add_upsell" title="Add"><?php _e('Up-sell', 'wpdeals'); ?> &rarr;</button><strong><?php echo $post->post_title; ?></strong> &ndash; #<?php echo $post->ID; ?> <input type="hidden" class="deal_id" value="0" /></li>
			<?php
							
		endforeach; else : 
		
			?><li><?php _e('No deals found', 'wpdeals'); ?></li><?php 
			
		endif; 
		
		die();
		
	}
	
	/**
	 * Ajax request handling for categories ordering
	 */
	function wpdeals_term_ordering() {
		global $wpdb;
		
		$id = (int) $_POST['id'];
		$next_id  = isset($_POST['nextid']) && (int) $_POST['nextid'] ? (int) $_POST['nextid'] : null;
		$taxonomy = isset($_POST['thetaxonomy']) ? esc_attr( $_POST['thetaxonomy'] ) : null;
		$term = get_term_by('id', $id, $taxonomy);
		
		if( !$id || !$term || !$taxonomy ) die(0);
		
		wpdeals_order_terms( $term, $next_id, $taxonomy );
		
		$children = get_terms($taxonomy, "child_of=$id&menu_order=ASC&hide_empty=0");
		
		if( $term && sizeof($children) ) {
			echo 'children';
			die;	
		}
	}
	add_action('wp_ajax_wpdeals-term-ordering', 'wpdeals_term_ordering');
