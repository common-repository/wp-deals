<?php
/**
 * WPDeals Templates
 * 
 * Handles template usage so that we can use our own templates instead of the theme's.
 *
 * Templates are in the 'templates' folder. wpdeals looks for theme 
 * overides in /theme/wpdeals/ by default  but this can be overwritten with WPDEALS_TEMPLATE_URL
 *
 * @package		WPDeals
 * @category	Core
 * @author		Tokokoo
 */
function wpdeals_template_loader( $template ) {
	global $wpdeals;
	
	if ( is_single() && get_post_type() == 'daily-deals' ) {
		
		$template = locate_template( array( 'single-daily-deals.php', WPDEALS_TEMPLATE_URL . 'single-daily-deals.php' ) );
		
		if ( ! $template ) $template = $wpdeals->plugin_path() . '/wpdeals-templates/single-daily-deals.php';
		
	}
	elseif ( is_tax('deal-categories') ) {
		
		$template = locate_template(  array( 'taxonomy-deal-categories.php', WPDEALS_TEMPLATE_URL . 'taxonomy-deal-categories.php' ) );
		
		if ( ! $template ) $template = $wpdeals->plugin_path() . '/wpdeals-templates/taxonomy-deal-categories.php';
	}
	elseif ( is_tax('deal-tags') ) {
		
		$template = locate_template( array( 'taxonomy-deal-tags.php', WPDEALS_TEMPLATE_URL . 'taxonomy-deal-tags.php' ) );
		
		if ( ! $template ) $template = $wpdeals->plugin_path() . '/wpdeals-templates/taxonomy-deal-tags.php';
	}
	elseif ( is_post_type_archive('daily-deals') ||  is_page( get_option('wpdeals_store_page_id') )) {

		$template = locate_template( array( 'archive-daily-deals.php', WPDEALS_TEMPLATE_URL . 'archive-daily-deals.php' ) );
		
		if ( ! $template ) $template = $wpdeals->plugin_path() . '/wpdeals-templates/archive-daily-deals.php';
		
	}
        elseif ( is_page( get_option('wpdeals_featured_page_id') ) ) {
		
		$template = locate_template( array( 'featured-store.php', WPDEALS_TEMPLATE_URL . 'featured-store.php' ) );
		
		if ( ! $template ) $template = $wpdeals->plugin_path() . '/wpdeals-templates/featured-store.php';
                
		
	}
	
	return $template;

}
add_filter( 'template_include', 'wpdeals_template_loader' );

/**
 * Get template part (for templates like loop)
 */
function wpdeals_get_template_part( $slug, $name = '' ) {
	global $wpdeals, $post;
	if ($name=='store') :
		if (!locate_template(array( $slug.'-store.php', WPDEALS_TEMPLATE_URL . $slug.'-store.php' ))) :
			load_template( $wpdeals->plugin_path() . '/wpdeals-templates/'.$slug.'-store.php',false );
			return;
		endif;
	endif;
	get_template_part( WPDEALS_TEMPLATE_URL . $slug, $name );
}

/**
 * Get the reviews template (comments)
 */
function wpdeals_comments_template($template) {
	global $wpdeals;
		
	if(get_post_type() !== 'daily-deals') return $template;
	
	if (file_exists( STYLESHEETPATH . '/' . WPDEALS_TEMPLATE_URL . 'single-daily-deals-reviews.php' ))
		return STYLESHEETPATH . '/' . WPDEALS_TEMPLATE_URL . 'single-daily-deals-reviews.php'; 
	else
		return $wpdeals->plugin_path() . '/deals-templates/single-daily-deals-reviews.php';
}

add_filter('comments_template', 'wpdeals_comments_template' );


/**
 * Get other templates (e.g. deals attributes)
 */
function wpdeals_get_template($template_name, $require_once = true) {
	global $wpdeals;
	if (file_exists( STYLESHEETPATH . '/' . WPDEALS_TEMPLATE_URL . $template_name )) load_template( STYLESHEETPATH . '/' . WPDEALS_TEMPLATE_URL . $template_name, $require_once ); 
	elseif (file_exists( STYLESHEETPATH . '/' . $template_name )) load_template( STYLESHEETPATH . '/' . $template_name , $require_once); 
	else load_template( $wpdeals->plugin_path() . '/wpdeals-templates/' . $template_name , $require_once);
}


/**
 * Front page archive/store template applied to main loop
 */
if (!function_exists('wpdeals_front_page_archive')) {
	function wpdeals_front_page_archive( $query ) {
			
		global $paged, $wpdeals, $wp_the_query, $wp_query;
		
		if ( defined('SHOP_IS_ON_FRONT') ) :
		
			wp_reset_query();
			
			// Only apply to front_page
			if ( $query === $wp_the_query ) :
				
				if (get_query_var('paged')) :
					$paged = get_query_var('paged'); 
				else :
					$paged = (get_query_var('page')) ? get_query_var('page') : 1;
				endif;
	
				// Filter the query
				add_filter( 'parse_query', array( &$wpdeals->query, 'parse_query') );
				
				// Query the deals
				$wp_query->query( array( 'page_id' => '', 'p' => '', 'post_type' => 'daily-deals', 'paged' => $paged ) );
				
				// get deals in view (for use by widgets)
				$wpdeals->query->get_deals_in_view();
				
				// Remove the query manipulation
				remove_filter( 'parse_query', array( &$wpdeals->query, 'parse_query') ); 
				remove_action('loop_start', 'wpdeals_front_page_archive', 1);
	
			endif;
		
		endif;
	}
}
add_action('loop_start', 'wpdeals_front_page_archive', 1);

/**
 * Detect frontpage store and fix pagination on static front page
 **/
function wpdeals_front_page_archive_paging_fix() {
		
	if ( is_front_page() && is_page( get_option('wpdeals_store_page_id') )) :
		
		if (get_query_var('paged')) :
			$paged = get_query_var('paged'); 
		else :
			$paged = (get_query_var('page')) ? get_query_var('page') : 1;
		endif;
			
		query_posts( array( 'page_id' => get_option('wpdeals_store_page_id'), 'is_paged' => true, 'paged' => $paged ) );
		
		define('SHOP_IS_ON_FRONT', true);
		
	endif;
}
add_action('wp', 'wpdeals_front_page_archive_paging_fix', 1);

/**
 * Detect frontpage store and fix pagination on static front page
 **/
function wpdeals_front_page_featured_paging_fix() {
		
	if ( is_front_page() && is_page( get_option('wpdeals_featured_page_id') )) :
		
		query_posts( array( 'page_id' => get_option('wpdeals_featured_page_id'), 'is_paged' => true ) );
		
		define('FEATURED_IS_ON_FRONT', true);
		
	endif;
}
add_action('wp', 'wpdeals_front_page_archive_paging_fix', 1);

/**
 * Add Body classes based on page/template
 **/
global $wpdeals_body_classes;

function wpdeals_page_body_classes() {
	
	global $wpdeals_body_classes;
	
	$wpdeals_body_classes = (array) $wpdeals_body_classes;
	
	if (is_wpdeals()) $wpdeals_body_classes[] = 'wpdeals';
	
	if (is_checkout()) $wpdeals_body_classes[] = 'wpdeals-checkout';
	
	if (is_account_page()) $wpdeals_body_classes[] = 'wpdeals-account';
	
	if (is_wpdeals() || is_checkout() || is_account_page() || get_page(get_option('wpdeals_order_tracking_page_id')) || get_page(get_option('wpdeals_thanks_page_id'))) $wpdeals_body_classes[] = 'wpdeals-page';
	
}
add_action('wp_head', 'wpdeals_page_body_classes');

function wpdeals_body_class($classes) {
	
	global $wpdeals_body_classes;
	
	$wpdeals_body_classes = (array) $wpdeals_body_classes;
	
	$classes = array_merge($classes, $wpdeals_body_classes);
	
	return $classes;
}
add_filter('body_class','wpdeals_body_class');

/**
 * Fix active class in nav for store page
 **/
function wpdeals_nav_menu_item_classes( $menu_items, $args ) {
	
	if (!is_wpdeals()) return $menu_items;
	
	$store_page 		= (int) get_option('wpdeals_store_page_id');
	$page_for_posts = (int) get_option( 'page_for_posts' );

	foreach ( (array) $menu_items as $key => $menu_item ) :

		$classes = (array) $menu_item->classes;

		// Unset active class for blog page
		if ( $page_for_posts == $menu_item->object_id ) :
			$menu_items[$key]->current = false;
			unset( $classes[ array_search('current_page_parent', $classes) ] );
			unset( $classes[ array_search('current-menu-item', $classes) ] );

		// Set active state if this is the store page link
		elseif ( is_store() && $store_page == $menu_item->object_id ) :
			$menu_items[$key]->current = true;
			$classes[] = 'current-menu-item';
			$classes[] = 'current_page_item';
		
		endif;

		$menu_items[$key]->classes = array_unique( $classes );
	
	endforeach;

	return $menu_items;
}
add_filter( 'wp_nav_menu_objects',  'wpdeals_nav_menu_item_classes', 2, 20 );

/**
 * Fix active class in wp_list_pages for store page
 *
 * Suggested by jessor - https://github.com/wpdeals/wpdeals/issues/177
 **/
function wpdeals_list_pages($pages){
    global $post;

    if (is_wpdeals() || is_checkout() || is_page(get_option('wpdeals_thanks_page_id'))) {
        $pages = str_replace( 'current_page_parent', '', $pages); // remove current_page_parent class from any item
        $store_page = 'page-item-' . get_option('wpdeals_store_page_id'); // find store_page_id through wpdeals options
        
        if (is_store()) :
        	$pages = str_replace($store_page, $store_page . ' current_page_item', $pages); // add current_page_item class to store page
    	else :
    		$pages = str_replace($store_page, $store_page . ' current_page_parent', $pages); // add current_page_parent class to store page
    	endif;
    }
    return $pages;
}

add_filter('wp_list_pages', 'wpdeals_list_pages');




/**
 * Filter the products in admin based on options
 *
 * @access public
 * @param mixed $query
 * @return void
 */
function wpdeals_featured_deals_query( $q ) {
    global $typenow, $wp_query;

    if ( is_page( get_option('wpdeals_featured_page_id') ) ) {
        
        // Ordering query vars
        $q->set( 'orderby', 'rand' );

        // Query vars that affect posts shown
        $q->set( 'post_type', 'daily-deals' );
        $q->set( 'ignore_sticky_posts', 1 );
        $q->set( 'posts_per_page', 1 );
        $q->set( 'meta_query', array(            
                array(
                        'key' => 'featured',
                        'value' => 'yes',
                        'compare' 	=> '='
                ),
                array(
                        'key' => '_is_expired',
                        'value' => 'no',
                        'compare' 	=> '='
                )
        ) );

    }

}


/**
 * Featured page template applied
 */
function wpdeals_featured_page( $query ) {

        global $wpdeals, $wp_the_query, $wp_query;

        if ( is_page( get_option('wpdeals_featured_page_id') ) || defined('FEATURED_IS_ON_FRONT') ) :

                wp_reset_query();

                // Only apply to front_page
                if ( $query === $wp_the_query ) :

                        // Filter the query
                        add_filter( 'parse_query', 'wpdeals_featured_deals_query' );

                        // Query the deals
                        $wp_query->query( array( 
                            'meta_query' => array(            
                                    array(
                                            'key' => 'featured',
                                            'value' => 'yes',
                                            'compare' 	=> '='
                                    ),
                                    array(
                                            'key' => '_is_expired',
                                            'value' => 'no',
                                            'compare' 	=> '='
                                    )
                            ),
                            'posts_per_page' => 1, 
                            'orderby' => 'rand',
                            'page_id' => '', 
                            'p' => '', 
                            'post_type' => 'daily-deals' ) );

                        // Remove the query manipulation
                        remove_filter( 'parse_query', 'wpdeals_featured_deals_query' ); 
                        remove_action('loop_start', 'wpdeals_featured_page', 1);

                endif;


        endif;
}
add_action('loop_start', 'wpdeals_featured_page', 1);