<?php
/**
 * Shortcodes init
 * 
 * Init main shortcodes, and add a few others such as recent deals.
 *
 * @package	WPDeals
 * @category	Shortcode
 * @author	Tokokoo
 */

include_once('shortcode-checkout.php');
include_once('shortcode-my_account.php');
include_once('shortcode-pay.php');
include_once('shortcode-thankyou.php');

/**
 * List deals in a category shortcode
 **/
function wpdeals_deals_category($atts){
	global $wpdeals_loop;
	
  	if (empty($atts)) return;
  
	extract(shortcode_atts(array(
		'per_page' 		=> '12',
		'columns' 		=> '3',
	  	'orderby'               => 'title',
	  	'order'                 => 'asc',
	  	'category'		=> ''
		), $atts));
		
	if (!$category) return;
		
	$wpdeals_loop['columns'] = $columns;
	
  	$args = array(
		'post_type'	=> 'daily-deals',
		'post_status' => 'publish',
		'ignore_sticky_posts'	=> 1,
		'orderby' => $orderby,
		'order' => $order,
		'posts_per_page' => $per_page,
		'tax_query' => array(
                        array(
                                'taxonomy' => 'deal-categories',
				'terms' => array( esc_attr($category) ),
				'field' => 'slug',
				'operator' => 'IN'
			)
	    )
	);
	
  	query_posts($args);
	
  	ob_start();
	wpdeals_get_template_part( 'loop', 'store' );
	wp_reset_query();
	return ob_get_clean();
}

/**
 * Recent Deals shortcode
 **/
function wpdeals_recent_deals( $atts ) {
	
	global $wpdeals_loop;
	
	extract(shortcode_atts(array(
		'per_page' 	=> '12',
		'columns' 	=> '4',
		'orderby' => 'date',
		'order' => 'desc'
	), $atts));
	
	$wpdeals_loop['columns'] = $columns;
	
	$args = array(
		'post_type'	=> 'daily-deals',
		'post_status' => 'publish',
		'ignore_sticky_posts'	=> 1,
		'posts_per_page' => $per_page,
		'orderby' => $orderby,
		'order' => $order
	);
	
	query_posts($args);
	ob_start();
	wpdeals_get_template_part( 'loop', 'store' );
	wp_reset_query();
	
	return ob_get_clean();
}

/**
 * List multiple deals shortcode
 **/
function wpdeals_deals($atts){
	global $wpdeals_loop;
	
  	if (empty($atts)) return;
  
	extract(shortcode_atts(array(
		'columns' 	=> '3',
	  	'orderby'   => 'title',
	  	'order'     => 'asc'
		), $atts));
		
	$wpdeals_loop['columns'] = $columns;
	
  	$args = array(
		'post_type'	=> 'daily-deals',
		'post_status' => 'publish',
		'ignore_sticky_posts'	=> 1,
		'orderby' => $orderby,
		'order' => $order
	);
		
	if(isset($atts['ids'])){
		$ids = explode(',', $atts['ids']);
	  	array_walk($ids, create_function('&$val', '$val = trim($val);'));
    	$args['post__in'] = $ids;
	}
	
  	query_posts($args);
	
  	ob_start();
	wpdeals_get_template_part( 'loop', 'store' );
	wp_reset_query();
	return ob_get_clean();
}

/**
 * Display a single prodcut
 **/
function wpdeals_deal($atts){
  	if (empty($atts)) return;
  
  	$args = array(
            'post_type' => 'daily-deals',
            'posts_per_page' => 1,
            'post_status' => 'publish'
  	);
  
  	if(isset($atts['id'])){
    	$args['p'] = $atts['id'];
  	}
  
  	query_posts($args);
	
  	ob_start();
	wpdeals_get_template_part( 'loop', 'store' );
	wp_reset_query();
	return ob_get_clean();  
}


/**
 * Display a single prodcut price + cart button
 **/
function wpdeals_deals_add_to_cart($atts){
  	if (empty($atts)) return;
  	
  	global $wpdb;
  	
  	if (!isset($atts['style'])) $atts['style'] = '';
  	
  	if ($atts['id']) :
  		$deal_data = get_post( $atts['id'] );
	else :
		return;
	endif;
	
	if ($deal_data->post_type!=='daily-deals') return;
	
	$_deals = new wpdeals_deals( $deal_data->ID ); 
		
	if (!$_deals->is_visible( true )) return; 
	
	ob_start();
	?>
	<div class="daily-deals" style="<?php echo $atts['style']; ?>">
		
		<?php wpdeals_template_single_add_to_cart( $deal_data, $_deals ); ?>
					
	</div><?php 
	
	return ob_get_clean();  
}


/**
 * Get the add to cart URL for a deals
 **/
function wpdeals_deals_add_to_cart_url( $atts ){
  	if (empty($atts)) return;
  	
  	global $wpdb;
  	  	
  	if ($atts['id']) :
  		$deal_data = get_post( $atts['id'] );
	else :
		return;
	endif;
	
	if ($deal_data->post_type!=='daily-deals') return;
	
	$_deals = new wpdeals_deals( $deal_data->ID ); 
		
	return esc_url( $_deals->add_to_cart_url() );
}


/**
 * Output featured deals
 **/
function wpdeals_featured_deals( $atts ) {
	
	global $wpdeals_loop;
	
	extract(shortcode_atts(array(
		'per_page' 	=> '12',
		'columns' 	=> '3',
		'orderby' => 'date',
		'order' => 'desc'
	), $atts));
	
	$wpdeals_loop['columns'] = $columns;
	
	$args = array(
		'post_type'	=> 'daily-deals',
		'post_status' => 'publish',
		'ignore_sticky_posts'	=> 1,
		'posts_per_page' => $per_page,
		'orderby' => $orderby,
		'order' => $order,
		'meta_query' => array(
			array(
				'key' => 'featured',
				'value' => 'yes'
			)
		)
	);
	query_posts($args);
	ob_start();
	wpdeals_get_template_part( 'loop', 'store' );
	wp_reset_query();
	
	return ob_get_clean();
}

/**
 * Shortcode creation
 **/
add_shortcode('deal_category', 'wpdeals_deals_category');
add_shortcode('add_to_cart', 'wpdeals_deals_add_to_cart');
add_shortcode('add_to_cart_url', 'wpdeals_deals_add_to_cart_url');
add_shortcode('daily-deals', 'wpdeals_deals');
add_shortcode('recent_deals', 'wpdeals_recent_deals');
add_shortcode('featured_deals', 'wpdeals_featured_deals');
add_shortcode('wpdeals_cart', 'get_wpdeals_cart');
add_shortcode('wpdeals_checkout', 'get_wpdeals_checkout');
add_shortcode('wpdeals_my_account', 'get_wpdeals_my_account');
add_shortcode('wpdeals_change_password', 'get_wpdeals_change_password');
add_shortcode('wpdeals_view_order', 'get_wpdeals_view_order');
add_shortcode('wpdeals_pay', 'get_wpdeals_pay');
add_shortcode('wpdeals_thankyou', 'get_wpdeals_thankyou');
