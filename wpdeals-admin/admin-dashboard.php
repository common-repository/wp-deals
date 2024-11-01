<?php
/**
 * Functions used for displaying the WPDeals dashboard widgets
 *
 * @author 		Tokokoo
 * @category 	Admin
 * @package 	WPDeals
 */

// Only hook in admin parts if the user has admin access
if (current_user_can('manage_deals')) :
	add_action('right_now_content_table_end', 'wpdeals_content_right_now');
	add_action('right_now_table_end', 'wpdeals_right_now');
	add_action('wp_dashboard_setup', 'wpdeals_init_dashboard_widgets' );
	add_action('admin_footer', 'wpdeals_dashboard_sales_js');
endif;

/**
 * Right now widget hooks/content
 */
function wpdeals_content_right_now() {
	
	global $wpdeals;
	?>
	</table>
	<p class="sub wpdeals_sub"><?php _e('Store Content', 'wpdeals'); ?></p>
	<table>
		<tr>
			<td class="first b"><a href="edit.php?post_type=daily-deals"><?php
				$num_posts = wp_count_posts( 'daily-deals' );
				$num = number_format_i18n( $num_posts->publish );
				echo $num;
			?></a></td>
			<td class="t"><a href="edit.php?post_type=daily-deals"><?php _e('Deals', 'wpdeals'); ?></a></td>
		</tr>
		<tr>
			<td class="first b"><a href="edit-tags.php?taxonomy=deal-categories&post_type=daily-deals"><?php
				echo wp_count_terms('deal-categories');
			?></a></td>
			<td class="t"><a href="edit-tags.php?taxonomy=deal-categories&post_type=daily-deals"><?php _e('Deal Categories', 'wpdeals'); ?></a></td>
		</tr>
		<tr>
			<td class="first b"><a href="edit-tags.php?taxonomy=deal-tags&post_type=daily-deals"><?php
				echo wp_count_terms('deal-tags');
			?></a></td>
			<td class="t"><a href="edit-tags.php?taxonomy=deal-tags&post_type=daily-deals"><?php _e('Deal Tags', 'wpdeals'); ?></a></td>
		</tr>
	<?php
}

function wpdeals_right_now() {
	$pending_count 		= get_term_by( 'slug', 'pending', 'deals_sales_status' )->count;
	$completed_count  	= get_term_by( 'slug', 'completed', 'deals_sales_status' )->count;
	$on_hold_count    	= get_term_by( 'slug', 'on-hold', 'deals_sales_status' )->count;
	$processing_count 	= get_term_by( 'slug', 'processing', 'deals_sales_status' )->count;
	?>
	</table>
	<p class="sub wpdeals_sub"><?php _e('Orders', 'wpdeals'); ?></p>
	<table>
		<tr>
			<td class="b"><a href="edit.php?post_type=deals-sales&deals_sales_status=pending"><span class="total-count"><?php echo $pending_count; ?></span></a></td>
			<td class="last t"><a class="pending" href="edit.php?post_type=deals-sales&deals_sales_status=pending"><?php _e('Pending', 'wpdeals'); ?></a></td>
		</tr>
		<tr>
			<td class="b"><a href="edit.php?post_type=deals-sales&deals_sales_status=on-hold"><span class="total-count"><?php echo $on_hold_count; ?></span></a></td>
			<td class="last t"><a class="onhold" href="edit.php?post_type=deals-sales&deals_sales_status=on-hold"><?php _e('On-Hold', 'wpdeals'); ?></a></td>
		</tr>
		<tr>
			<td class="b"><a href="edit.php?post_type=deals-sales&deals_sales_status=processing"><span class="total-count"><?php echo $processing_count; ?></span></a></td>
			<td class="last t"><a class="processing" href="edit.php?post_type=deals-sales&deals_sales_status=processing"><?php _e('Processing', 'wpdeals'); ?></a></td>
		</tr>
		<tr>
			<td class="b"><a href="edit.php?post_type=deals-sales&deals_sales_status=completed"><span class="total-count"><?php echo $completed_count; ?></span></a></td>
			<td class="last t"><a class="complete" href="edit.php?post_type=deals-sales&deals_sales_status=completed"><?php _e('Completed', 'wpdeals'); ?></a></td>
		</tr>
	<?php
}

/**
 * Dashboard Widgets - init
 */
function wpdeals_init_dashboard_widgets() {

	global $current_month_offset;
						
	$current_month_offset = (int) date('m');
	
	if (isset($_GET['month'])) $current_month_offset = (int) $_GET['month'];
	
	$sales_heading = '';
	
	if ($current_month_offset!=date('m')) : 
		$sales_heading .= '<a href="index.php?month='.($current_month_offset+1).'" class="next">'.date('F', strtotime('01-'.($current_month_offset+1).'-2011')).' &rarr;</a>';
	endif;
	
	$sales_heading .= '<a href="index.php?month='.($current_month_offset-1).'" class="previous">&larr; '.date('F', strtotime('01-'.($current_month_offset-1).'-2011')).'</a><span>'.__('Monthly Sales', 'wpdeals').'</span>';

	wp_add_dashboard_widget('wpdeals_dashboard_sales', $sales_heading, 'wpdeals_dashboard_sales');
	wp_add_dashboard_widget('wpdeals_dashboard_recent_sales', __('WPDeals recent sales', 'wpdeals'), 'wpdeals_dashboard_recent_sales');
	wp_add_dashboard_widget('wpdeals_dashboard_recent_reviews', __('WPDeals recent reviews', 'wpdeals'), 'wpdeals_dashboard_recent_reviews');
} 
				     		
/**
 * Recent sales widget
 */
function wpdeals_dashboard_recent_sales() {

	$args = array(
	    'numberposts'     => 8,
	    'orderby'         => 'post_date',
	    'order'           => 'DESC',
	    'post_type'       => 'deals-sales',
	    'post_status'     => 'publish' 
	);
	$sales = get_posts( $args );
	if ($sales) :
		echo '<ul class="recent-sales">';
		foreach ($sales as $order) :
			
			$this_order = new wpdeals_order( $order->ID );
			
			echo '
			<li>
				<span class="order-status '.sanitize_title($this_order->status).'">'.ucwords($this_order->status).'</span> <a href="'.admin_url('post.php?post='.$order->ID).'&action=edit">'.date_i18n('l jS \of F Y h:i:s A', strtotime($this_order->order_date)).'</a><br />
				<small>'.sizeof($this_order->items).' '._n('item', 'items', sizeof($this_order->items), 'wpdeals').' <span class="order-cost">'.__('Total:', 'wpdeals') . ' ' . wpdeals_price($this_order->order_total).'</span></small>
			</li>';

		endforeach;
		echo '</ul>';
	endif;
} 

/**
 * Recent reviews widget
 */
function wpdeals_dashboard_recent_reviews() {
	global $wpdb;
	$comments = $wpdb->get_results("SELECT *, SUBSTRING(comment_content,1,100) AS comment_excerpt
	FROM $wpdb->comments
	LEFT JOIN $wpdb->posts ON ($wpdb->comments.comment_post_ID = $wpdb->posts.ID)
	WHERE comment_approved = '1' 
	AND comment_type = '' 
	AND post_password = ''
	AND post_type = 'daily-deals'
	ORDER BY comment_date_gmt DESC
	LIMIT 5" );
	
	if ($comments) : 
		echo '<ul>';
		foreach ($comments as $comment) :
			
			echo '<li>';
			
			echo get_avatar($comment->comment_author, '32');
			
			$rating = get_comment_meta( $comment->comment_ID, 'rating', true );
			
			echo '<div class="star-rating" title="'.$rating.'">
				<span style="width:'.($rating*10).'px">'.$rating.' '.__('out of 5', 'wpdeals').'</span></div>';
				
			echo '<h4 class="meta"><a href="'.get_permalink($comment->ID).'#comment-'.$comment->comment_ID .'">'.$comment->post_title.'</a> reviewed by ' .strip_tags($comment->comment_author) .'</h4>';
			echo '<blockquote>'.strip_tags($comment->comment_excerpt).' [...]</blockquote></li>';
			
		endforeach;
		echo '</ul>';
	else :
		echo '<p>'.__('There are no deals reviews yet.', 'wpdeals').'</p>';
	endif;
}

/**
 * Orders this month filter function
 */
function sales_this_month( $where = '' ) {
	global $current_month_offset;
	
	$month = $current_month_offset;
	$year = (int) date('Y');
	
	$first_day = strtotime("{$year}-{$month}-01");
	//$last_day = strtotime('-1 second', strtotime('+1 month', $first_day));
	$last_day = strtotime('+1 month', $first_day);
	
	$after = date('Y-m-d', $first_day);
	$before = date('Y-m-d', $last_day);
	
	$where .= " AND post_date > '$after'";
	$where .= " AND post_date < '$before'";
	
	return $where;
}
	
/**
 * Sales widget
 */
function wpdeals_dashboard_sales() {
		
	?><div id="placeholder" style="width:100%; height:300px; position:relative;"></div><?php
}

/**
 * Sales widget javascript
 */
function wpdeals_dashboard_sales_js() {
	
	global $wpdeals;
	
	$screen = get_current_screen();
	
	if (!$screen || $screen->id!=='dashboard') return;
	
	global $current_month_offset;
	
	// Get sales to display in widget
	add_filter( 'posts_where', 'sales_this_month' );

	$args = array(
	    'numberposts'     => -1,
	    'orderby'         => 'post_date',
	    'order'           => 'DESC',
	    'post_type'       => 'deals-sales',
	    'post_status'     => 'publish' ,
	    'suppress_filters' => false,
	    'tax_query' => array(
	    	array(
		    	'taxonomy' => 'deals_sales_status',
				'terms' => array('completed', 'processing', 'on-hold'),
				'field' => 'slug',
				'operator' => 'IN'
			)
	    )
	);
	$sales = get_posts( $args );
	
	$order_counts = array();
	$order_amounts = array();
		
	// Blank date ranges to begin
	$month = $current_month_offset;
	$year = (int) date('Y');
	
	$first_day = strtotime("{$year}-{$month}-01");
	$last_day = strtotime('-1 second', strtotime('+1 month', $first_day));
	
	if ((date('m') - $current_month_offset)==0) :
		$up_to = date('d', strtotime('NOW'));
	else :
		$up_to = date('d', $last_day);
	endif;
	$count = 0;
	
	while ($count < $up_to) :
		
		$time = strtotime(date('Ymd', strtotime('+ '.$count.' DAY', $first_day))).'000';
		
		$order_counts[$time] = 0;
		$order_amounts[$time] = 0;

		$count++;
	endwhile;
	
	if ($sales) :
		foreach ($sales as $order) :
			
			$order_data = new wpdeals_order($order->ID);
			
			if ($order_data->status=='cancelled' || $order_data->status=='refunded') continue;
			
			$time = strtotime(date('Ymd', strtotime($order->post_date))).'000';
			
			if (isset($order_counts[$time])) :
				$order_counts[$time]++;
			else :
				$order_counts[$time] = 1;
			endif;
			
			if (isset($order_amounts[$time])) :
				$order_amounts[$time] = $order_amounts[$time] + $order_data->order_total;
			else :
				$order_amounts[$time] = (float) $order_data->order_total;
			endif;
			
		endforeach;
	endif;
	
	remove_filter( 'posts_where', 'sales_this_month' );
	
	/* Script variables */
	$params = array( 
		'currency_symbol' 				=> get_wpdeals_currency_symbol() 
	);
	
	$order_counts_array = array();
	foreach ($order_counts as $key => $count) :
		$order_counts_array[] = array($key, $count);
	endforeach;
	
	$order_amounts_array = array();
	foreach ($order_amounts as $key => $amount) :
		$order_amounts_array[] = array($key, $amount);
	endforeach;
	
	$order_data = array( 'order_counts' => $order_counts_array, 'order_amounts' => $order_amounts_array );

	$params['order_data'] = json_encode($order_data);	
	
	// Queue scripts
	$suffix = defined('SCRIPT_DEBUG') && SCRIPT_DEBUG ? '' : '';
	
	wp_register_script( 'wpdeals_dashboard_sales', $wpdeals->plugin_url() . '/wpdeals-assets/js/admin/dashboard_sales'.$suffix.'.js', 'jquery', '1.0' );
	wp_register_script( 'flot', $wpdeals->plugin_url() . '/wpdeals-assets/js/admin/jquery.flot'.$suffix.'.js', 'jquery', '1.0' );
	wp_register_script( 'flot-resize', $wpdeals->plugin_url() . '/wpdeals-assets/js/admin/jquery.flot.resize'.$suffix.'.js', 'jquery', '1.0' );
	
	wp_localize_script( 'wpdeals_dashboard_sales', 'params', $params );
	
	wp_print_scripts('flot');
	wp_print_scripts('flot-resize');
	wp_print_scripts('wpdeals_dashboard_sales');
	
}
