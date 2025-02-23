<?php
/**
 * Contains the main functions for WPDeals, stores variables, and handles error messages
 *
 * @class 		wpdeals
 * @package		WPDeals
 * @category	Class
 * @author		Tokokoo
 */
class wpdeals {
	
	var $_cache;
	var $errors = array(); // Stores store errors
	var $messages = array(); // Stores store messages
	var $attribute_taxonomies; // Stores the attribute taxonomies used in the store
	var $plugin_url;
	var $plugin_path;
	var $inline_js = '';
	
	// Class instances
	var $query;
	var $customer;
	var $shipping;
	var $cart;
	var $payment_gateways;
	var $countries;
		
	/** constructor */
	function __construct() {
		
		// Load class instances
		$this->payment_gateways                 = new wpdeals_payment_gateways();	// Payment gateways. Loads and stores payment methods, and handles incoming requests such as IPN
		$this->countries 			= new wpdeals_countries();			// Countries class
		
		// Non-admin and ajax requests
		if ( !is_admin() || defined('DOING_AJAX') ) :
			
			// Class instances
			$this->cart 			= new wpdeals_cart();				// Cart class, stores the cart contents
			$this->customer 		= new wpdeals_customer();			// Customer class, sorts out session data such as location
			$this->query			= new wpdeals_query();				// Query class, handles front-end queries and loops
			
			// Load messages
			$this->load_messages();
			
			// Hooks
			add_filter( 'wp_redirect', array(&$this, 'redirect'), 1, 2 );
			add_action( 'wpdeals_before_single_deals', array(&$this, 'show_messages'), 10);
			add_action( 'wpdeals_before_store_loop', array(&$this, 'show_messages'), 10);
			add_action( 'wp_footer', array(&$this, 'output_inline_js'), 25);
		
		else :
		endif;
		
		add_action( 'plugins_loaded', array( &$this->payment_gateways, 'init' ), 1); 	// Load payment methods - some more may be added by plugins
	}
	
    /*-----------------------------------------------------------------------------------*/
	/* Instance Loaders - loaded only when needed */
	/*-----------------------------------------------------------------------------------*/ 
				 
		/**
		 * Validation Class
		 */
		function validation() { 
			if ( !class_exists('wpdeals_validation') ) include_once( 'validation.class.php' );
			
			return new wpdeals_validation();
		}
		
		/**
		 * Checkout Class
		 */
		function checkout() { 
			if ( !class_exists('wpdeals_checkout') ) include_once( 'checkout.class.php' );
			
			return new wpdeals_checkout();
		}
		
		/**
		 * Logging Class
		 */
		function logger() { 
			if ( !class_exists('wpdeals_logger') ) include_once( 'wpdeals_logger.class.php' );
			
			return new wpdeals_logger();
		}
	
    /*-----------------------------------------------------------------------------------*/
	/* Helper functions */
	/*-----------------------------------------------------------------------------------*/ 

		/**
		 * Get the plugin url
		 */
		function plugin_url() { 
			if($this->plugin_url) return $this->plugin_url;
			
			if (is_ssl()) :
				return $this->plugin_url = str_replace('http://', 'https://', WP_PLUGIN_URL) . "/" . plugin_basename( dirname(dirname(__FILE__))); 
			else :
				return $this->plugin_url = WP_PLUGIN_URL . "/" . plugin_basename( dirname(dirname(__FILE__))); 
			endif;
		}
		
		/**
		 * Get the plugin path
		 */
		function plugin_path() { 	
			if($this->plugin_path) return $this->plugin_path;
			return $this->plugin_path = WP_PLUGIN_DIR . "/" . plugin_basename( dirname(dirname(__FILE__))); 
		 }
		 
		/**
		 * Return the URL with https if SSL is on
		 */
		function force_ssl( $url ) { 	
			if (is_ssl()) $url = str_replace('http:', 'https:', $url);
			return $url;
		 }
		
		/**
		 * Get an image size
		 *
		 * Variable is filtered by wpdeals_get_image_size_{image_size}
		 */
		function get_image_size( $image_size ) {
			$return = '';
			switch ($image_size) :
				case "store_thumbnail_image_width" : $return = get_option('wpdeals_thumbnail_image_width'); break;
				case "store_thumbnail_image_height" : $return = get_option('wpdeals_thumbnail_image_height'); break;
				case "store_catalog_image_width" : $return = get_option('wpdeals_catalog_image_width'); break;
				case "store_catalog_image_height" : $return = get_option('wpdeals_catalog_image_height'); break;
				case "store_single_image_width" : $return = get_option('wpdeals_single_image_width'); break;
				case "store_single_image_height" : $return = get_option('wpdeals_single_image_height'); break;
			endswitch;
			return apply_filters( 'wpdeals_get_image_size_'.$image_size, $return );
		}
	
    /*-----------------------------------------------------------------------------------*/
	/* Messages */
	/*-----------------------------------------------------------------------------------*/ 
    
	    /**
		 * Load Messages
		 */
		function load_messages() { 
			if (isset($_SESSION['errors'])) $this->errors = $_SESSION['errors'];
			if (isset($_SESSION['messages'])) $this->messages = $_SESSION['messages'];
			
			unset($_SESSION['messages']);
			unset($_SESSION['errors']);
		}

		/**
		 * Add an error
		 */
		function add_error( $error ) { $this->errors[] = $error; }
		
		/**
		 * Add a message
		 */
		function add_message( $message ) { $this->messages[] = $message; }
		
		/** Clear messages and errors from the session data */
		function clear_messages() {
			$this->errors = $this->messages = array();
			unset($_SESSION['messages']);
			unset($_SESSION['errors']);
		}
		
		/**
		 * Get error count
		 */
		function error_count() { return sizeof($this->errors); }
		
		/**
		 * Get message count
		 */
		function message_count() { return sizeof($this->messages); }
		
		/**
		 * Output the errors and messages
		 */
		function show_messages() {
		
			if (isset($this->errors) && sizeof($this->errors)>0) :
				echo '<div class="wpdeals_error">'.$this->errors[0].'</div>';
				$this->clear_messages();
				return true;
			elseif (isset($this->messages) && sizeof($this->messages)>0) :
				echo '<div class="wpdeals_message">'.$this->messages[0].'</div>';
				$this->clear_messages();
				return true;
			else :
				return false;
			endif;
		}
		
		/**
		 * Redirection hook which stores messages into session data
		 *
		 * @param   location
		 * @param   status
		 * @return  location
		 */
		function redirect( $location, $status ) {
			global $is_IIS;

			// IIS fix
			if ($is_IIS) session_write_close();
		
			$_SESSION['errors'] = $this->errors;
			$_SESSION['messages'] = $this->messages;
			
			return $location;
		}
		
    /*-----------------------------------------------------------------------------------*/
	/* Attributes */
	/*-----------------------------------------------------------------------------------*/ 
	
	    /**
		 * Get attribute taxonomies
		 */
		function get_attribute_taxonomies() { 
			global $wpdb;
			if (!$this->attribute_taxonomies) :
				$this->attribute_taxonomies = $wpdb->get_results("SELECT * FROM ".$wpdb->prefix."wpdeals_attribute_taxonomies;");
			endif;
			return $this->attribute_taxonomies;
		}
	    
	    /**
		 * Get a deals attributes name
		 */
		function attribute_taxonomy_name( $name ) { 
			return 'pa_'.sanitize_title($name);
		}
		
		/**
		 * Get a deals attributes label
		 */
		function attribute_label( $name ) { 
			global $wpdb;
			
			if (strstr( $name, 'pa_' )) :
				$name = str_replace( 'pa_', '', sanitize_title( $name ) );
	
				$label = $wpdb->get_var( $wpdb->prepare( "SELECT attribute_label FROM ".$wpdb->prefix."wpdeals_attribute_taxonomies WHERE attribute_name = %s;", $name ) );
				
				if ($label) return $label; else return ucfirst($name);
			else :
				return $name;
			endif;
	
			
		}
		
    /*-----------------------------------------------------------------------------------*/
	/* Coupons */
	/*-----------------------------------------------------------------------------------*/ 
		
		/**
		 * Get coupon types
		 */
		function get_coupon_discount_types() { 
			if (!isset($this->coupon_discount_types)) :
				$this->coupon_discount_types = apply_filters('wpdeals_coupon_discount_types', array(
	    			'fixed_cart' 	=> __('Cart Discount', 'wpdeals'),
	    			'percent' 		=> __('Cart % Discount', 'wpdeals'),
	    			'fixed_deals'	=> __('Deal Discount', 'wpdeals'),
	    			'percent_deals'	=> __('Deal % Discount', 'wpdeals')
	    		));
    		endif;
    		return $this->coupon_discount_types;
    	}
    	
    	/**
		 * Get a coupon type's name
		 */
		function get_coupon_discount_type( $type = '' ) { 
			$types = (array) $this->get_coupon_discount_types();
			if (isset($types[$type])) return $types[$type];
    	}
	
    /*-----------------------------------------------------------------------------------*/
	/* Nonce Field Helpers */
	/*-----------------------------------------------------------------------------------*/ 
	
		/**
		 * Return a nonce field
		 */
		function nonce_field ($action, $referer = true , $echo = true) { return wp_nonce_field('wpdeals-' . $action, '_n', $referer, $echo); }
		
		/**
		 * Return a url with a nonce appended
		 */
		function nonce_url ($action, $url = '') { return add_query_arg( '_n', wp_create_nonce( 'wpdeals-' . $action ), $url); }
		
		/**
		 * Check a nonce and sets wpdeals error in case it is invalid
		 * To fail silently, set the error_message to an empty string
		 * 
		 * @param 	string $name the nonce name
		 * @param	string $action then nonce action
		 * @param   string $method the http request method _POST, _GET or _REQUEST
		 * @param   string $error_message custom error message, or false for default message, or an empty string to fail silently
		 * 
		 * @return   bool
		 */
		function verify_nonce($action, $method='_POST', $error_message = false) {
			
			$name = '_n';
			$action = 'wpdeals-' . $action;
			
			if( $error_message === false ) $error_message = __('Action failed. Please refresh the page and retry.', 'wpdeals'); 
			
			if(!in_array($method, array('_GET', '_POST', '_REQUEST'))) $method = '_POST';
			
			if ( isset($_REQUEST[$name]) && wp_verify_nonce($_REQUEST[$name], $action) ) return true;
			
			if( $error_message ) $this->add_error( $error_message );
			
			return false;
		}
	
    /*-----------------------------------------------------------------------------------*/
	/* Cache Helpers */
	/*-----------------------------------------------------------------------------------*/ 
	
		/**
		 * Cache API
		 */
		function cache ( $id, $data, $args=array() ) {
	
			if( ! isset($this->_cache[ $id ]) ) $this->_cache[ $id ] = array();
			
			if( empty($args) ) $this->_cache[ $id ][0] = $data;
			else $this->_cache[ $id ][ serialize($args) ] = $data;
			
			return $data;
			
		}
		function cache_get ( $id, $args=array() ) {
	
			if( ! isset($this->_cache[ $id ]) ) return null;
			
			if( empty($args) && isset($this->_cache[ $id ][0]) ) return $this->_cache[ $id ][0];
			elseif ( isset($this->_cache[ $id ][ serialize($args) ] ) ) return $this->_cache[ $id ][ serialize($args) ];
		}
		
		/**
		 * Shortcode cache
		 */
		function shortcode_wrapper($function, $atts=array()) {
			if( $content = $this->cache_get( $function . '-shortcode', $atts ) ) return $content;
			
			ob_start();
			call_user_func($function, $atts);
			return $this->cache( $function . '-shortcode', ob_get_clean(), $atts);
		}
		
		
    /*-----------------------------------------------------------------------------------*/
	/* Transients */
	/*-----------------------------------------------------------------------------------*/ 
	
		/**
		 * Clear Deal Transients
		 */
		function clear_deals_transients( $post_id = 0 ) {
			global $wpdb;
			
			delete_transient('wpdeals_deals_onsale');
			delete_transient('wpdeals_hidden_deals_ids');
			delete_transient('wpdeals_hidden_from_search_deals_ids');
			
			$wpdb->query("DELETE FROM `$wpdb->options` WHERE `option_name` LIKE ('_transient_wpdeals_unfiltered_deals_ids_%')");
			$wpdb->query("DELETE FROM `$wpdb->options` WHERE `option_name` LIKE ('_transient_wpdeals_layered_nav_count_%')");

			if ($post_id>0) :
				$post_id = (int) $post_id;
				delete_transient('wpdeals_deals_total_stock_'.$post_id);
				delete_transient('wpdeals_deals_children_ids_'.$post_id);
			else :
				$wpdb->query("DELETE FROM `$wpdb->options` WHERE `option_name` LIKE ('_transient_wpdeals_deals_children_ids_%')");
				$wpdb->query("DELETE FROM `$wpdb->options` WHERE `option_name` LIKE ('_transient_wpdeals_deals_total_stock_%')");
			endif;
		}

    /*-----------------------------------------------------------------------------------*/
	/* Inline JavaScript Helper (for adding it to the footer) */
	/*-----------------------------------------------------------------------------------*/ 
		
		function add_inline_js( $code ) {
		
			$this->inline_js .= "\n" . $code . "\n";
		
		}
		
		function output_inline_js() {
			
			if ($this->inline_js) :
				
				echo "<!-- WPDeals JavaScript-->\n<script type=\"text/javascript\">\njQuery(document).ready(function($) {";
				
				echo $this->inline_js;
				
				echo "});\n</script>\n";
				
			endif;
			
		}
		
}