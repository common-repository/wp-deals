<?php
/*
Plugin Name: WP Deals
Plugin URI: http://wpdeals.me/
Description: Deals plugin for wordpress.
Version: 2.0.1.4
Author: Tokokoo
Author URI: http://wpdeals.me
Requires at least: 3.1
Tested up to: 3.5
*/

if (!session_id()) session_start();

/**
 * Constants
 **/ 
define("WPDEALS_VERSION", "2.0.1.1");
if (!defined('WPDEALS_TEMPLATE_URL')) define('WPDEALS_TEMPLATE_URL', 'wpdeals/');	

/**
 * Localisation
 **/
load_plugin_textdomain('wpdeals', false, dirname( plugin_basename( __FILE__ ) ) . '/wpdeals-languages');
load_plugin_textdomain('wpdeals', false, dirname( plugin_basename( __FILE__ ) ) . '/../../languages/wpdeals');

/**
 * Admin init + activation hooks
 **/
if ( is_admin() ) :

	require_once( 'wpdeals-admin/admin-init.php' );

	register_activation_hook( __FILE__, 'activate_wpdeals' );
	
	if (get_option('deals_db_version') != WPDEALS_VERSION) : add_action('init', 'install_wpdeals', 0); endif;

endif;

/**
 * Include core files
 **/
if (defined('DOING_AJAX')) :
	include_once( 'wpdeals_ajax.php' );					// Ajax functions for admin and the front-end
endif;

if ( !is_admin() || defined('DOING_AJAX') ) :
	include_once( 'wpdeals_template_actions.php' );		// Template actions used on the front-end
	include_once( 'wpdeals-shortcodes/shortcodes-init.php' );		// Init the shortcodes
	include_once( 'wpdeals-classes/wpdeals_query.class.php' );	// The main store queries
	add_action( 'init', 'include_template_functions', 99 );	// Defer loading template functions so functions are pluggable by themes
endif;

include_once( 'wpdeals-classes/cart.class.php' );				// The main cart class
include_once( 'wpdeals-classes/coupons.class.php' );			// Coupons class
include_once( 'wpdeals-classes/customer.class.php' ); 			// Customer class
include_once( 'wpdeals_templates.php' );			// Loads template files - used in admin and front-end
include_once( 'wpdeals_taxonomy.php' );				// Defines post formats and taxonomies
include_once( 'wpdeals-widgets/widgets-init.php' );				// Widget classes
include_once( 'wpdeals_actions.php' );				// Contains action hooks and functions for various events
include_once( 'wpdeals_emails.php' );				// Email template handlers
include_once( 'wpdeals-classes/countries.class.php' );			// Defines countries and states
include_once( 'wpdeals-classes/order.class.php' );				// Single order class
include_once( 'wpdeals-classes/daily-deals.class.php' );			// Deal class
include_once( 'wpdeals-classes/deal-variations.class.php' );	// Deal variation class
include_once( 'wpdeals-classes/wpdeals.class.php' );		// Main WPDeals class

/**
 * Include shipping modules and gateways
 */
include_once( 'wpdeals-classes/wpdeals_settings_api.class.php' );
include_once( 'wpdeals-classes/gateways/gateways.class.php' );
include_once( 'wpdeals-classes/gateways/gateway.class.php' );
include_once( 'wpdeals-classes/gateways/gateway-banktransfer.php' );
include_once( 'wpdeals-classes/gateways/gateway-cheque.php' );
include_once( 'wpdeals-classes/gateways/gateway-paypal.php' );

/**
 * Function used to Init WPDeals Template Functions - This makes them pluggable by plugins and themes
 **/
function include_template_functions() {
	include_once( 'wpdeals_template_functions.php' );
}

/**
 * Init wpdeals class
 */
global $wpdeals;
$wpdeals = new wpdeals();

/**
 * Init WPDeals
 **/
add_action('init', 'wpdeals_init', 0);

function wpdeals_init() {

	global $wpdeals;
        
	ob_start();
	
	wpdeals_post_type();

	// Image sizes
	$store_thumbnail_crop 	= (get_option('wpdeals_thumbnail_image_crop')==1) ? true : false;
	$store_catalog_crop 	= (get_option('wpdeals_catalog_image_crop')==1) ? true : false;
	$store_single_crop 	= (get_option('wpdeals_single_image_crop')==1) ? true : false;

	add_image_size( 'store_thumbnail', $wpdeals->get_image_size('store_thumbnail_image_width'), $wpdeals->get_image_size('store_thumbnail_image_height'), $store_thumbnail_crop );
	add_image_size( 'store_catalog', $wpdeals->get_image_size('store_catalog_image_width'), $wpdeals->get_image_size('store_catalog_image_height'), $store_catalog_crop );
	add_image_size( 'store_single', $wpdeals->get_image_size('store_single_image_width'), $wpdeals->get_image_size('store_single_image_height'), $store_single_crop );

	$suffix = defined('SCRIPT_DEBUG') && SCRIPT_DEBUG ? '' : '';
	
        if (!is_admin()) :

            // Optional front end css	
            if ((defined('WPDEALS_USE_CSS') && WPDEALS_USE_CSS) || (!defined('WPDEALS_USE_CSS') && get_option('wpdeals_frontend_css') == 'yes')) :
                    $css = file_exists(get_stylesheet_directory() . '/wpdeals/style.css') ? get_stylesheet_directory_uri() . '/wpdeals/style.css' : $wpdeals->plugin_url() . '/wpdeals-assets/css/wpdeals.css';
                    wp_register_style('wpdeals_frontend_styles', $css );
                    wp_enqueue_style( 'wpdeals_frontend_styles' );
            endif;

            if (get_option('wpdeals_enable_lightbox')=='yes') wp_enqueue_style( 'wpdeals_fancybox_styles', $wpdeals->plugin_url() . '/wpdeals-assets/css/fancybox'.$suffix.'.css' );
        endif;       
        
}

/**
 * Init WPDeals Thumbnails after theme setup
 **/
add_action('after_setup_theme', 'wpdeals_init_post_thumbnails');

function wpdeals_init_post_thumbnails() {
	// Post thumbnail support
	if ( !current_theme_supports( 'post-thumbnails' ) ) :
		add_theme_support( 'post-thumbnails' );
		remove_post_type_support( 'post', 'thumbnail' );
		remove_post_type_support( 'page', 'thumbnail' );
	else :
		add_post_type_support( 'daily-deals', 'thumbnail' );
	endif;
}

/**
 * Output generator to aid debugging
 **/
add_action('wp_head', 'wpdeals_generator');

function wpdeals_generator() {
	echo "\n\n" . '<!-- WPDeals Version -->' . "\n" . '<meta name="generator" content="WPDeals ' . WPDEALS_VERSION . '" />' . "\n\n";
}

/**
 * Set up Roles & Capabilities
 **/
add_action('init', 'wpdeals_init_roles');

function wpdeals_init_roles() {
	global $wp_roles;

	if (class_exists('WP_Roles')) if ( ! isset( $wp_roles ) ) $wp_roles = new WP_Roles();	
	
	if (is_object($wp_roles)) :
		
		// Customer role
		add_role('customer', __('Customer', 'wpdeals'), array(
		    'read' 				=> true,
		    'edit_posts' 			=> false,
		    'delete_posts' 			=> false
		));
	
		// Store manager role
		add_role('deals_manager', __('Deals Manager', 'wpdeals'), array(
		    'read'                              => true,
		    'read_private_pages'		=> true,
		    'read_private_posts'		=> true,
		    'edit_posts' 			=> true,
		    'edit_pages' 			=> true,
		    'edit_published_posts'		=> true,
		    'edit_published_pages'		=> true,
		    'edit_private_pages'		=> true,
		    'edit_private_posts'		=> true,
		    'edit_others_posts' 		=> true,
		    'edit_others_pages' 		=> true,
		    'publish_posts' 			=> true,
		    'publish_pages'			=> true,
		    'delete_posts' 			=> true,
		    'delete_pages' 			=> true,
		    'delete_private_pages'		=> true,
		    'delete_private_posts'		=> true,
		    'delete_published_pages'            => true,
		    'delete_published_posts'            => true,
		    'delete_others_posts' 		=> true,
		    'delete_others_pages' 		=> true,
		    'manage_categories' 		=> true,
		    'manage_links'			=> true,
		    'moderate_comments'			=> true,
		    'unfiltered_html'			=> true,
		    'upload_files'			=> true,
		   	'export'			=> true,
			'import'			=> true,
			'manage_deals'                  => true
		));
		
		// Main Store capabilities for admin
		$wp_roles->add_cap( 'administrator', 'manage_deals' );
	endif;
}

/**
 * Enqueue frontend scripts
 **/
function wpdeals_frontend_scripts() {
	global $wpdeals;
	
	$suffix = defined('SCRIPT_DEBUG') && SCRIPT_DEBUG ? '' : '';
	$lightbox_en = (get_option('wpdeals_enable_lightbox')=='yes') ? true : false;
	$jquery_ui_en = (get_option('wpdeals_enable_jquery_ui')=='yes') ? true : false;
	$scripts_position = (get_option('wpdeals_scripts_position') == 'yes') ? true : false;

	wp_register_script( 'wpdeals', $wpdeals->plugin_url() . '/wpdeals-assets/js/wpdeals'.$suffix.'.js', 'jquery', '1.0', $scripts_position );
	wp_register_script( 'wpdeals_plugins', $wpdeals->plugin_url() . '/wpdeals-assets/js/wpdeals_plugins'.$suffix.'.js', 'jquery', '1.0', $scripts_position );
	wp_register_script( 'jquery-countdown', $wpdeals->plugin_url() . '/wpdeals-assets/js/countdown.min.js', 'jquery', '1.0', $scripts_position );
	
	wp_enqueue_script( 'jquery' );
	wp_enqueue_script( 'wpdeals_plugins' );
	wp_enqueue_script( 'wpdeals' );
	wp_enqueue_script( 'jquery-countdown' );
	
	if ($lightbox_en) :
		wp_register_script( 'fancybox', $wpdeals->plugin_url() . '/wpdeals-assets/js/fancybox'.$suffix.'.js', 'jquery', '1.0', $scripts_position );
		wp_enqueue_script( 'fancybox' );
	endif;
	
	if ($jquery_ui_en) :
		wp_register_script( 'jqueryui', $wpdeals->plugin_url() . '/wpdeals-assets/js/jquery-ui'.$suffix.'.js', 'jquery', '1.0', $scripts_position );
		wp_register_script( 'wd_price_slider', $wpdeals->plugin_url() . '/wpdeals-assets/js/price_slider'.$suffix.'.js', 'jqueryui', '1.0', $scripts_position );
		
		wp_enqueue_script( 'jqueryui' );
		wp_enqueue_script( 'wd_price_slider' );
		
		$wpdeals_price_slider_params = array(
			'currency_symbol' 				=> get_wpdeals_currency_symbol(),
			'currency_pos'           		=> get_option('wpdeals_currency_pos'), 
		);
		
		if (isset($_SESSION['min_price'])) $wpdeals_price_slider_params['min_price'] = $_SESSION['min_price'];
		if (isset($_SESSION['max_price'])) $wpdeals_price_slider_params['max_price'] = $_SESSION['max_price'];
		
		wp_localize_script( 'wd_price_slider', 'wpdeals_price_slider_params', $wpdeals_price_slider_params );
	endif;
    	
	/* Script variables */
	$states = json_encode( $wpdeals->countries->states );
	
	$wpdeals_params = array(
		'countries' 				=> $states,
		'select_state_text' 			=> __('Select a state&hellip;', 'wpdeals'),
		'state_text' 				=> __('state', 'wpdeals'),
		'plugin_url' 				=> $wpdeals->plugin_url(),
		'ajax_url' 				=> (!is_ssl()) ? str_replace('https', 'http', admin_url('admin-ajax.php')) : admin_url('admin-ajax.php'),
		'get_variation_nonce' 			=> wp_create_nonce("get-variation"),
		'add_to_cart_nonce' 			=> wp_create_nonce("buy-this"),
		'update_order_review_nonce'             => wp_create_nonce("update-order-review"),
		'option_guest_checkout'			=> get_option('wpdeals_enable_guest_checkout'),
		'checkout_url'				=> admin_url('admin-ajax.php?action=wpdeals-checkout'),
		'option_ajax_add_to_cart'		=> get_option('wpdeals_enable_ajax_add_to_cart')
	);
	
	$wpdeals_params['is_checkout'] = ( is_page(get_option('wpdeals_checkout_page_id')) ) ? 1 : 0;
	$wpdeals_params['is_pay_page'] = ( is_page(get_option('wpdeals_pay_page_id')) ) ? 1 : 0;
	
	wp_localize_script( 'wpdeals', 'wpdeals_params', $wpdeals_params );
	
}
add_action('template_redirect', 'wpdeals_frontend_scripts');

/**
 * WPDeals conditionals
 **/
function is_wpdeals() {
	// Returns true if on a page which uses WPDeals templates (cart and checkout are standard pages with shortcodes and thus are not included)
	if (is_store() || is_deal_category() || is_deal_tag() || is_deals()) return true; else return false;
}
if (!function_exists('is_store')) {
	function is_store() {
		if (is_post_type_archive( 'daily-deals' ) || is_page(get_option('wpdeals_store_page_id'))) return true; else return false;
	}
}
if (!function_exists('is_deal_category')) {
	function is_deal_category() {
		return is_tax( 'deal-categories' );
	}
}
if (!function_exists('is_deal_tag')) {
	function is_deal_tag() {
		return is_tax( 'deal-tags' );
	}
}
if (!function_exists('is_deals')) {
	function is_deals() {
		return is_singular( array('daily-deals') );
	}
}
if (!function_exists('is_checkout')) {
	function is_checkout() {
		if (is_page(get_option('wpdeals_checkout_page_id')) || is_page(get_option('wpdeals_pay_page_id'))) return true; else return false;
	}
}
if (!function_exists('is_account_page')) {
	function is_account_page() {
		if ( is_page(get_option('wpdeals_myaccount_page_id')) || is_page(get_option('wpdeals_edit_address_page_id')) || is_page(get_option('wpdeals_view_order_page_id')) || is_page(get_option('wpdeals_change_password_page_id')) ) return true; else return false;
		return is_page(get_option('wpdeals_myaccount_page_id'));
	}
}
if (!function_exists('is_ajax')) {
	function is_ajax() {
		if ( defined('DOING_AJAX') ) return true;
		if ( isset($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest' ) return true; else return false;
	}
}

/**
 * Force SSL (if enabled)
 **/
if (!is_admin() && get_option('wpdeals_force_ssl_checkout')=='yes') add_action( 'wp', 'wpdeals_force_ssl');

function wpdeals_force_ssl() {
	if (is_checkout() && !is_ssl()) :
		wp_safe_redirect( str_replace('http:', 'https:', get_permalink(get_option('wpdeals_checkout_page_id'))), 301 );
		exit;
	// Break out of SSL if we leave the checkout (anywhere but thanks page)
	elseif (get_option('wpdeals_unforce_ssl_checkout')=='yes' && is_ssl() && $_SERVER['REQUEST_URI'] && !is_checkout() && !is_page(get_option('wpdeals_thanks_page_id')) && !is_ajax()) :
		wp_safe_redirect( str_replace('https:', 'http:', home_url($_SERVER['REQUEST_URI']) ) );
		exit;
	endif;
}

/**
 * Force SSL for images
 **/
add_filter('post_thumbnail_html', 'wpdeals_force_ssl_images');
add_filter('widget_text', 'wpdeals_force_ssl_images');
add_filter('wp_get_attachment_url', 'wpdeals_force_ssl_images');
add_filter('wp_get_attachment_image_attributes', 'wpdeals_force_ssl_images');
add_filter('wp_get_attachment_url', 'wpdeals_force_ssl_images');

function wpdeals_force_ssl_images( $content ) {
	if (is_ssl()) :
		if (is_array($content)) :
			$content = array_map('wpdeals_force_ssl_images', $content);
		else :
			$content = str_replace('http:', 'https:', $content);
		endif;
	endif;
	return $content;
}

/**
 * Force SSL for stylsheet/script urls etc. Modified code by Chris Black (http://cjbonline.org)
 **/
add_filter('option_siteurl', 'wpdeals_force_ssl_urls');
add_filter('option_home', 'wpdeals_force_ssl_urls');
add_filter('option_url', 'wpdeals_force_ssl_urls');
add_filter('option_wpurl', 'wpdeals_force_ssl_urls');
add_filter('option_stylesheet_url', 'wpdeals_force_ssl_urls');
add_filter('option_template_url', 'wpdeals_force_ssl_urls');
add_filter('script_loader_src', 'wpdeals_force_ssl_urls');
add_filter('style_loader_src', 'wpdeals_force_ssl_urls');

function wpdeals_force_ssl_urls( $url ) {
	if (is_ssl()) :
		$url = str_replace('http:', 'https:', $url);
	endif;
	return $url;
}


/**
 * IIS compatability fix/fallback
 **/
if (!isset($_SERVER['REQUEST_URI'])) {
	$_SERVER['REQUEST_URI'] = substr($_SERVER['PHP_SELF'],1 );
	if (isset($_SERVER['QUERY_STRING'])) { $_SERVER['REQUEST_URI'].='?'.$_SERVER['QUERY_STRING']; }
}

/**
 * Currency
 **/
function get_wpdeals_currency_symbol() {
	$currency = get_option('wpdeals_currency');
	$currency_symbol = '';
	switch ($currency) :
		case 'AUD' :
		case 'BRL' :
		case 'CAD' :
		case 'MXN' :
		case 'NZD' :
		case 'HKD' :
		case 'SGD' :
		case 'USD' : $currency_symbol = '&#36;'; break;
		case 'EUR' : $currency_symbol = '&euro;'; break;
		case 'IDR' : $currency_symbol = 'IDR'; break;
		case 'JPY' : $currency_symbol = '&yen;'; break;
		case 'TRY' : $currency_symbol = 'TL'; break;
		case 'NOK' : $currency_symbol = 'kr'; break;
		case 'ZAR' : $currency_symbol = 'R'; break;
		case 'CZK' : $currency_symbol = '&#75;&#269;'; break;

		case 'DKK' :
		case 'HUF' :
		case 'ILS' :
		case 'MYR' :
		case 'PHP' :
		case 'PLN' :
		case 'SEK' :
		case 'CHF' :
		case 'TWD' :
		case 'THB' : $currency_symbol = $currency; break;
		
		case 'GBP' : 
		default    : $currency_symbol = '&pound;'; break;
	endswitch;
	return apply_filters('wpdeals_currency_symbol', $currency_symbol, $currency);
}

/**
 * Price Formatting
 **/
function wpdeals_price( $price ) {
	global $wpdeals;
	
	$return = '';
	$num_decimals = (int) get_option('wpdeals_price_num_decimals');
	$currency_pos = get_option('wpdeals_currency_pos');
	$currency_symbol = get_wpdeals_currency_symbol();
	$price = number_format( (double) $price, $num_decimals, get_option('wpdeals_price_decimal_sep'), get_option('wpdeals_price_thousand_sep') );
	
	if (get_option('wpdeals_price_trim_zeros')=='yes') :
		$trimmed_price = rtrim(rtrim($price, '0'), get_option('wpdeals_price_decimal_sep'));
		$after_decimal = explode(get_option('wpdeals_price_decimal_sep'), $trimmed_price);
		if (!isset($after_decimal[1]) || (isset($after_decimal[1]) && (strlen($after_decimal[1]) == 0 && strlen($after_decimal[1]) == $num_decimals))) $price = $trimmed_price;
	endif;
	
	switch ($currency_pos) :
		case 'left' :
			$return = $currency_symbol . $price;
		break;
		case 'right' :
			$return = $price . $currency_symbol;
		break;
		case 'left_space' :
			$return = $currency_symbol . ' ' . $price;
		break;
		case 'right_space' :
			$return = $price . ' ' . $currency_symbol;
		break;
	endswitch;
	
	return $return;
}	
	
/**
 * Clean variables
 **/
function wpdeals_clean( $var ) {
	return trim(strip_tags(stripslashes($var)));
}

/**
 * Rating field for comments
 **/
function wpdeals_add_comment_rating($comment_id) {
	if ( isset($_POST['rating']) ) :
		global $post;
		if (!$_POST['rating'] || $_POST['rating'] > 5 || $_POST['rating'] < 0) $_POST['rating'] = 5; 
		add_comment_meta( $comment_id, 'rating', esc_attr($_POST['rating']), true );
		delete_transient( esc_attr($post->ID) . '_wpdeals_average_rating' );
	endif;
}
add_action( 'comment_post', 'wpdeals_add_comment_rating', 1 );

function wpdeals_check_comment_rating($comment_data) {
	
	global $wpdeals;
	
	// If posting a comment (not trackback etc) and not logged in
	if ( isset($_POST['rating']) && !$wpdeals->verify_nonce('comment_rating') )
		wp_die( __('You have taken too long. Please go back and refresh the page.', 'wpdeals') );
		
	elseif ( isset($_POST['rating']) && empty($_POST['rating']) && $comment_data['comment_type']== '' ) {
		wp_die( __('Please rate the deals.',"wpdeals") );
		exit;
	}
	return $comment_data;
}
add_filter('preprocess_comment', 'wpdeals_check_comment_rating', 0);	

/**
 * Review comments template
 **/
function wpdeals_comments($comment, $args, $depth) {
	$GLOBALS['comment'] = $comment; global $post; ?>
	
	<li itemprop="reviews" itemscope itemtype="http://schema.org/Review" <?php comment_class(); ?> id="li-comment-<?php comment_ID() ?>">
		<div id="comment-<?php comment_ID(); ?>" class="comment_container">

  			<?php echo get_avatar( $comment, $size='60' ); ?>
			
			<div class="comment-text">
			
				<div itemprop="reviewRating" itemscope itemtype="http://schema.org/Rating" class="star-rating" title="<?php echo esc_attr( get_comment_meta( $comment->comment_ID, 'rating', true ) ); ?>">
					<span style="width:<?php echo get_comment_meta( $comment->comment_ID, 'rating', true )*16; ?>px"><span itemprop="ratingValue"><?php echo get_comment_meta( $comment->comment_ID, 'rating', true ); ?></span> <?php _e('out of 5', 'wpdeals'); ?></span>
				</div>
				
				<?php if ($comment->comment_approved == '0') : ?>
					<p class="meta"><em><?php _e('Your comment is awaiting approval', 'wpdeals'); ?></em></p>
				<?php else : ?>
					<p class="meta">
						<?php _e('Rating by', 'wpdeals'); ?> <strong itemprop="author"><?php comment_author(); ?></strong> <?php _e('on', 'wpdeals'); ?> <time itemprop="datePublished" time datetime="<?php echo get_comment_date('c'); ?>"><?php echo get_comment_date('M jS Y'); ?></time>:
					</p>
				<?php endif; ?>
				
  				<div itemprop="description" class="description"><?php comment_text(); ?></div>
  				<div class="clear"></div>
  			</div>
			<div class="clear"></div>			
		</div>
	<?php
}


/**
 * Exclude order comments from queries
 *
 * This code should exclude deals-sales comments from queries. Some queries (like the recent comments widget on the dashboard) are hardcoded
 * and are not filtered, however, the code current_user_can( 'read_post', $comment->comment_post_ID ) should keep them safe since only admin and
 * store managers can view sales anyway.
 *
 * The frontend view order pages get around this filter by using remove_filter('comments_clauses', 'wpdeals_exclude_order_comments');
 **/
function wpdeals_exclude_order_comments( $clauses ) {
	global $wpdb, $typenow;
	
	if (is_admin() && $typenow=='deals-sales') return $clauses; // Don't hide when viewing sales in admin
	
	$clauses['join'] = "LEFT JOIN $wpdb->posts ON $wpdb->comments.comment_post_ID = $wpdb->posts.ID";
	
	if ($clauses['where']) $clauses['where'] .= ' AND ';
	
	$clauses['where'] .= "
		$wpdb->posts.post_type NOT IN ('deals-sales')
	";
	
	return $clauses;	

}
add_filter( 'comments_clauses', 'wpdeals_exclude_order_comments', 10, 1);


/**
 * Exclude order comments from comments RSS
 **/
function wpdeals_exclude_order_comments_from_feed( $where ) {
	global $wpdb;
	
    if ($where) $where .= ' AND ';
	
	$where .= "$wpdb->posts.post_type NOT IN ('deals-sales')";
    
    return $where;
}
add_action( 'comment_feed_where', 'wpdeals_exclude_order_comments_from_feed' );


/**
 * readfile_chunked
 *
 * Reads file in chunks so big downloads are possible without changing PHP.INI - http://codeigniter.com/wiki/Download_helper_for_large_files/
 *
 * @access   public
 * @param    string    file
 * @param    boolean    return bytes of file
 * @return   void
 */
if ( ! function_exists('readfile_chunked')) {
    function readfile_chunked($file, $retbytes=TRUE) {
    
		$chunksize = 1 * (1024 * 1024);
		$buffer = '';
		$cnt = 0;
		
		$handle = fopen($file, 'r');
		if ($handle === FALSE) return FALSE;
				
		while (!feof($handle)) :
		   $buffer = fread($handle, $chunksize);
		   echo $buffer;
		   ob_flush();
		   flush();
		
		   if ($retbytes) $cnt += strlen($buffer);
		endwhile;
		
		$status = fclose($handle);
		
		if ($retbytes AND $status) return $cnt;
		
		return $status;
    }
}

/**
 * Cache
 **/
function wpdeals_prevent_sidebar_cache() {
	echo '<!--mfunc get_sidebar() --><!--/mfunc-->';
}
add_action('get_sidebar', 'wpdeals_prevent_sidebar_cache');

/**
 * Hex darker/lighter/contrast functions for colours
 **/
if (!function_exists('wpdeals_hex_darker')) {
	function wpdeals_hex_darker( $color, $factor = 30 ) {
		$color = str_replace('#', '', $color);
		
		$base['R'] = hexdec($color{0}.$color{1});
		$base['G'] = hexdec($color{2}.$color{3});
		$base['B'] = hexdec($color{4}.$color{5});
		
		$color = '#';
		
		foreach ($base as $k => $v) :
	        $amount = $v / 100;
	        $amount = round($amount * $factor);
	        $new_decimal = $v - $amount;
	
	        $new_hex_component = dechex($new_decimal);
	        if(strlen($new_hex_component) < 2) :
	        	$new_hex_component = "0".$new_hex_component;
	        endif;
	        $color .= $new_hex_component;
		endforeach;
		        
		return $color;        
	}
}
if (!function_exists('wpdeals_hex_lighter')) {
	function wpdeals_hex_lighter( $color, $factor = 30 ) {
		$color = str_replace('#', '', $color);
		
		$base['R'] = hexdec($color{0}.$color{1});
		$base['G'] = hexdec($color{2}.$color{3});
		$base['B'] = hexdec($color{4}.$color{5});
		
		$color = '#';
	     
	    foreach ($base as $k => $v) :
	        $amount = 255 - $v; 
	        $amount = $amount / 100; 
	        $amount = round($amount * $factor); 
	        $new_decimal = $v + $amount; 
	     
	        $new_hex_component = dechex($new_decimal); 
	        if(strlen($new_hex_component) < 2) :
	        	$new_hex_component = "0".$new_hex_component;
	        endif;
	        $color .= $new_hex_component; 
	   	endforeach;
	         
	   	return $color;          
	}
}
if (!function_exists('wpdeals_light_or_dark')) {
	function wpdeals_light_or_dark( $color, $dark = '#000000', $light = '#FFFFFF' ) {
	    return (hexdec($color) > 0xffffff/2) ? $dark : $light;
	}
}

/**
 * Variation Formatting
 *
 * Gets a formatted version of variation data or item meta
 **/
function wpdeals_get_formatted_variation( $variation = '', $flat = false ) {
	global $wpdeals;

	if (is_array($variation)) :

		if (!$flat) $return = '<dl class="variation">'; else $return = '';

		$variation_list = array();

		foreach ($variation as $name => $value) :

			if (!$value) continue;

			// If this is a term slug, get the term's nice name
            if (taxonomy_exists(esc_attr(str_replace('attribute_', '', $name)))) :
            	$term = get_term_by('slug', $value, esc_attr(str_replace('attribute_', '', $name)));
            	if (!is_wp_error($term) && $term->name) :
            		$value = $term->name;
            	endif;
            else :
            	$value = ucfirst($value);
            endif;

			if ($flat) :
				$variation_list[] = $wpdeals->attribute_label(str_replace('attribute_', '', $name)).': '.$value;
			else :
				$variation_list[] = '<dt>'.$wpdeals->attribute_label(str_replace('attribute_', '', $name)).':</dt><dd>'.$value.'</dd>';
			endif;

		endforeach;

		if ($flat) :
			$return .= implode(', ', $variation_list);
		else :
			$return .= implode('', $variation_list);
		endif;

		if (!$flat) $return .= '</dl>';

		return $return;

	endif;
}

/**
 * add wp schedule for checking deals expired
 */
if ( ! wp_next_scheduled('wpdeals_check_deals_cron') ) {
	wp_schedule_event( time(), 'hourly', 'wpdeals_check_deals_cron' ); // hourly, daily and twicedaily
}
add_action( 'wpdeals_check_deals_cron', 'wpdeals_check_deals', 1000 );
function wpdeals_check_deals() {
	global $wpdb;
                        
	$wpdb->hide_errors();
        
        // Update wpdeals_permissions table to include order ID's as well as keys
	$datenow    = current_time('timestamp');
        $results    = $wpdb->get_results( $wpdb->prepare( "SELECT post_id FROM ".$wpdb->prefix."postmeta WHERE meta_key = '_end_time' AND meta_value < $datenow;" ) );
        
        foreach ($results as $key => $value) 
                update_post_meta($value->post_id, '_is_expired', 'yes');
        
	$wpdb->show_errors();
        
}
