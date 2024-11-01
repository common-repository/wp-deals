<?php
/**
 * WPDeals Template Actions
 * 
 * Actions used in the template files to output content.
 *
 * @package	WPDeals
 * @category	Core
 * @author	Tokokoo
 */

/* Content Wrappers */
add_action( 'wpdeals_before_main_content', 'wpdeals_output_content_wrapper', 10);
add_action( 'wpdeals_after_main_content', 'wpdeals_output_content_wrapper_end', 10);

/* Sidebar */
add_action( 'wpdeals_sidebar', 'wpdeals_get_sidebar', 10);

/* Deals Loop */
add_action( 'wpdeals_before_store_loop_item_title', 'wpdeals_template_loop_deals_thumbnail', 10, 2);
add_action( 'wpdeals_after_store_loop_item_title', 'wpdeals_template_loop_price', 10, 2);
add_action( 'wpdeals_after_store_loop_item_title', 'wpdeals_template_loop_countdown', 10, 2);

/* Subcategories */
add_action( 'wpdeals_before_subcategory_title', 'wpdeals_subcategory_thumbnail', 10);

/* Before Single Deals */
add_action( 'wpdeals_before_single_deals', 'wpdeals_before_single_deals', 10, 2);

/* Before Single Deals Summary Div */
add_action( 'wpdeals_before_single_deals_summary', 'wpdeals_show_deals_images', 20);
add_action( 'wpdeals_deals_thumbnails', 'wpdeals_show_deals_thumbnails', 20 );

/* After Single Deals Summary Div */
add_action( 'wpdeals_after_single_deals_summary', 'wpdeals_deals_description', 10);
add_action( 'wpdeals_after_single_deals_summary', 'wpdeals_template_single_add_to_cart', 20, 2 );
add_action( 'wpdeals_after_single_deals_summary', 'wpdeals_single_meta_content', 30, 2 );
add_action( 'wpdeals_after_single_deals_summary', 'wpdeals_output_related_deals', 40);
add_action( 'wpdeals_after_single_deals_summary', 'wpdeals_deals_comments', 50);

/* Deal Summary Box */
add_action( 'wpdeals_single_deals_summary', 'wpdeals_template_single_add_to_cart', 10, 2 );
add_action( 'wpdeals_single_deals_summary', 'wpdeals_template_single_price', 20, 2);
add_action( 'wpdeals_single_deals_summary', 'wpdeals_template_single_bought', 30, 2);
add_action( 'wpdeals_single_deals_summary', 'wpdeals_template_single_countdown', 40, 2);
add_action( 'wpdeals_single_deals_summary', 'wpdeals_template_single_sharing', 50, 2);

/* Deal Buy now */
add_action( 'wpdeals_simple_add_to_cart', 'wpdeals_simple_add_to_cart', 20, 2 ); 
add_action( 'wpdeals_variable_add_to_cart', 'wpdeals_variable_add_to_cart', 30, 2 ); 
add_action( 'wpdeals_external_add_to_cart', 'wpdeals_external_add_to_cart', 40, 2 );

/* Deal Add to Cart forms */
add_action( 'wpdeals_add_to_cart_form', 'wpdeals_add_to_cart_form_nonce', 10);

/* Pagination in loop-store */
add_action( 'wpdeals_pagination', 'wpdeals_pagination', 10 );
add_action( 'wpdeals_pagination', 'wpdeals_catalog_ordering', 20 );

/* Checkout */
add_action( 'wpdeals_before_checkout_form', 'wpdeals_checkout_login_form', 10 );
add_action( 'wpdeals_checkout_order_review', 'wpdeals_order_review', 10 );

/* Remove the singular class for wpdeals single deals */
add_action( 'after_setup_theme', 'wpdeals_body_classes_check' );

function wpdeals_body_classes_check () {
	if( has_filter( 'body_class', 'twentyeleven_body_classes' ) ) add_filter( 'body_class', 'wpdeals_body_classes' );
}

/* Footer */
add_action( 'wp_footer', 'wpdeals_demo_store' );

/* Order details */
add_action( 'wpdeals_view_order', 'wpdeals_order_details_table', 10 );
add_action( 'wpdeals_thankyou', 'wpdeals_order_details_table', 10 );
