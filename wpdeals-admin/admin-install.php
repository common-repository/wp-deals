<?php
/**
 * WPDeals Install
 * 
 * Plugin install script which adds default pages, taxonomies, and database tables
 *
 * @author 		Tokokoo
 * @category 	Admin
 * @package 	WPDeals
 */

/**
 * Activate wpdeals
 */
function activate_wpdeals() {
	
	install_wpdeals();        
	
	// Update installed variable
	update_option( "wpdeals_installed", 1 );
	update_option( 'skip_install_wpdeals_pages', 0 );
}

/**
 * Install wpdeals
 */
function install_wpdeals() {
	global $wpdeals_settings, $wpdeals;
	
	// Do install
	wpdeals_default_options();
	wpdeals_tables_install();
	wpdeals_default_taxonomies();
	wpdeals_populate_custom_fields();
        wpdeals_upgrade();
	
	// Install folder for uploading files and prevent hotlinking
	$upload_dir 	=  wp_upload_dir();
	$downloads_url 	= $upload_dir['basedir'] . '/deals_uploads';
	if ( wp_mkdir_p($downloads_url) && !file_exists($downloads_url.'/.htaccess') ) :
		if ($file_handle = fopen( $downloads_url . '/.htaccess', 'w' )) :
			fwrite($file_handle, 'deny from all');
			fclose($file_handle);
		endif;
	endif;
	
	// Install folder for logs
	$logs_url 		= WP_PLUGIN_DIR . "/" . plugin_basename( dirname(dirname(__FILE__))) . '/wpdeals-logs';
	if ( wp_mkdir_p($logs_url) && !file_exists($logs_url.'/.htaccess') ) :
		if ($file_handle = fopen( $logs_url . '/.htaccess', 'w' )) :
			fwrite($file_handle, 'deny from all');
			fclose($file_handle);
		endif;
	endif;
	
	// Clear transient cache (if this is an upgrade then wpdeals_class will be defined)
	if ( $wpdeals instanceof wpdeals ) $wpdeals->clear_deals_transients();
	
	// Update version
	update_option( "deals_db_version", WPDEALS_VERSION );
}

/**
 * Install wpdeals redirect
 */
add_action('admin_init', 'install_wpdeals_redirect');
function install_wpdeals_redirect() {
	global $pagenow, $wpdeals;

	if ( is_admin() && isset( $_GET['activate'] ) && ($_GET['activate'] == true) && $pagenow == 'plugins.php' && get_option( "wpdeals_installed" ) == 1 ) :
		
		// Clear transient cache
		$wpdeals->clear_deals_transients();
		
		// Unset installed flag
		update_option( "wpdeals_installed", 0 );
		
		// Flush rewrites
		flush_rewrite_rules( false );
		
		// Redirect to settings
		wp_redirect(admin_url('admin.php?page=wpdeals&installed=true'));
		exit;
		
	endif;
}

/**
 * Add required post meta so queries work
 */
function wpdeals_populate_custom_fields() {

	// Attachment exclusion
	$args = array( 
		'post_type' 	=> 'attachment', 
		'numberposts' 	=> -1, 
		'post_status' 	=> null, 
		'fields' 		=> 'ids'
	); 
	$attachments = get_posts($args);
	if ($attachments) foreach ($attachments as $id) :
		add_post_meta($id, '_wpdeals_exclude_image', 0, true);
	endforeach;
	
}

/**
 * Default options
 * 
 * Sets up the default options used on the settings page
 */
function wpdeals_default_options() {
	global $wpdeals_settings;
	
	// Include settings so that we can run through defaults
	include_once( 'admin-settings.php' );
	
	foreach ($wpdeals_settings as $section) :
	
		foreach ($section as $value) :
	
	        if (isset($value['std'])) :
	        
	        	if ($value['type']=='image_width') :
	        		
	        		add_option($value['id'].'_width', $value['std']);
	        		add_option($value['id'].'_height', $value['std']);
	        		
	        	else :
	        		
	        		add_option($value['id'], $value['std']);
	        	
	        	endif;
	        	
	        endif;
        
        endforeach;
        
    endforeach;

    add_option('wpdeals_store_slug', 'store');
}

/**
 * Create a page
 */
function wpdeals_create_page( $slug, $option, $page_title = '', $page_content = '', $post_parent = 0 ) {
	global $wpdb;
	 
	$option_value = get_option($option); 
	 
	if ($option_value>0) :
		if (get_post( $option_value )) :
			// Page exists
			return;
		endif;
	endif;
	
	$page_found = $wpdb->get_var("SELECT ID FROM " . $wpdb->posts . " WHERE post_name = '$slug' LIMIT 1;");
	if ($page_found) :
		// Page exists
		if (!$option_value)  update_option($option, $page_found);
		return;
	endif;
	
	$page_data = array(
        'post_status' => 'publish',
        'post_type' => 'page',
        'post_author' => 1,
        'post_name' => $slug,
        'post_title' => $page_title,
        'post_content' => $page_content,
        'post_parent' => $post_parent,
        'comment_status' => 'closed'
    );
    $page_id = wp_insert_post($page_data);
    
    update_option($option, $page_id);
}
 
/**
 * Create pages
 * 
 * Creates pages that the plugin relies on, storing page id's in variables.
 */
function wpdeals_create_pages() {
	
    // Store page
    wpdeals_create_page( esc_sql( _x('daily-deals', 'page_slug', 'wpdeals') ), 'wpdeals_store_page_id', __('Daily Deals', 'wpdeals'), '' );
    
    // Store page
    wpdeals_create_page( esc_sql( _x('featured-deals', 'page_slug', 'wpdeals') ), 'wpdeals_featured_page_id', __('Featured Deals', 'wpdeals'), '' );
    
    // Checkout page
    wpdeals_create_page( esc_sql( _x('checkout', 'page_slug', 'wpdeals') ), 'wpdeals_checkout_page_id', __('Checkout', 'wpdeals'), '[wpdeals_checkout]' );
	
    // My Account page
    wpdeals_create_page( esc_sql( _x('my-account', 'page_slug', 'wpdeals') ), 'wpdeals_myaccount_page_id', __('My Account', 'wpdeals'), '[wpdeals_my_account]' );
    
    // View order page
    wpdeals_create_page( esc_sql( _x('view-order', 'page_slug', 'wpdeals') ), 'wpdeals_view_order_page_id', __('View Order', 'wpdeals'), '[wpdeals_view_order]', get_option('wpdeals_myaccount_page_id') );

    // Change password page
    wpdeals_create_page( esc_sql( _x('change-password', 'page_slug', 'wpdeals') ), 'wpdeals_change_password_page_id', __('Change Password', 'wpdeals'), '[wpdeals_change_password]', get_option('wpdeals_myaccount_page_id') );

    // Pay page
    wpdeals_create_page( esc_sql( _x('pay', 'page_slug', 'wpdeals') ), 'wpdeals_pay_page_id', __('Checkout &rarr; Pay', 'wpdeals'), '[wpdeals_pay]', get_option('wpdeals_checkout_page_id') );
    
    // Thanks page
    wpdeals_create_page( esc_sql( _x('order-received', 'page_slug', 'wpdeals') ), 'wpdeals_thanks_page_id', __('Order Received', 'wpdeals'), '[wpdeals_thankyou]', get_option('wpdeals_checkout_page_id') );
    
}

/**
 * Table Install
 * 
 * Sets up the database tables which the plugin needs to function.
 */
function wpdeals_tables_install() {
	global $wpdb;
	
	$wpdb->hide_errors();

	$collate = '';
    if($wpdb->supports_collation()) {
		if(!empty($wpdb->charset)) $collate = "DEFAULT CHARACTER SET $wpdb->charset";
		if(!empty($wpdb->collate)) $collate .= " COLLATE $wpdb->collate";
    }
    
    require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
    
    // Table for storing attribute taxonomies - these are user defined
    $sql = "CREATE TABLE ". $wpdb->prefix . "wpdeals_attribute_taxonomies" ." (
        attribute_id 		mediumint(9) NOT NULL AUTO_INCREMENT,
        attribute_name		varchar(200) NOT NULL,
        attribute_label		longtext NULL,
        attribute_type		varchar(200) NOT NULL,
        PRIMARY KEY id (attribute_id)) $collate;";
    dbDelta($sql);
    
    // Table for storing user and guest download permissions
    $downloadable_deals_table = $wpdb->prefix . "wpdeals_permissions";
   
    // Drop primary key first
    if ($wpdb->get_var("SHOW TABLES LIKE '$downloadable_deals_table'") == $downloadable_deals_table) {
		$wpdb->query("ALTER TABLE $downloadable_deals_table DROP PRIMARY KEY");
	}

    // Now create it
    $sql = "CREATE TABLE ". $downloadable_deals_table ." (
        deal_id 			mediumint(9) NOT NULL,
        order_id			mediumint(9) NOT NULL DEFAULT 0,
        order_key			varchar(200) NOT NULL,
        user_email			varchar(200) NOT NULL,
        user_id				mediumint(9) NULL,
        downloads_remaining             varchar(9) NULL,
        vouchers                        varchar(20) NULL,
        PRIMARY KEY id (deal_id,order_id,order_key)) $collate;";
    dbDelta($sql);
    
    // Term meta table - sadly WordPress does not have termmeta so we need our own
    $sql = "CREATE TABLE ". $wpdb->prefix . "wpdeals_termmeta" ." (
		meta_id 			bigint(20) NOT NULL AUTO_INCREMENT,
      	wpdeals_term_id bigint(20) NOT NULL,
      	meta_key 			varchar(255) NULL,
      	meta_value 			longtext NULL,
      	PRIMARY KEY id (meta_id)) $collate;";
    dbDelta($sql);
    
    // Update wpdeals_permissions table to include order ID's as well as keys
    $results = $wpdb->get_results( $wpdb->prepare( "SELECT * FROM ".$wpdb->prefix."wpdeals_permissions WHERE order_id = 0;" ) );
	
	if ($results) foreach ($results as $result) :
		
		if (!$result->order_key) continue;
		
		$order_id = $wpdb->get_var( $wpdb->prepare("SELECT post_id FROM ".$wpdb->postmeta." WHERE meta_key = '_order_key' AND meta_value = '%s' LIMIT 1;", $result->order_key) );
		
		if ($order_id) :
		
			$wpdb->update( $wpdb->prefix . "wpdeals_permissions", array( 
				'order_id' => $order_id, 
			), array( 
				'deal_id' => $result->deal_id,
				'order_key' => $result->order_key
			), array( '%s' ), array( '%s', '%s' ) );
		
		endif;
		
	endforeach;
	
	$wpdb->show_errors();
}

/**
 * Default taxonomies
 * 
 * Adds the default terms for taxonomies - deals types and order statuses. Modify at your own risk.
 */
function wpdeals_default_taxonomies() {
	
	if (!post_type_exists('daily-deals')) :
		register_post_type('daily-deals',
			array(
				'public' => true,
				'show_ui' => true,
				'capability_type' => 'post',
				'publicly_queryable' => true,
				'exclude_from_search' => false,
				'hierarchical' => true,
				'query_var' => true,			
				'supports' => array( 'title', 'editor', 'thumbnail', 'comments' ),
				'show_in_nav_menus' => false,
			)
		);
	endif;
	
	if (!taxonomy_exists('deal-type')) :
		register_taxonomy( 'deal-type', array('post', 'daily-deals'));
		register_taxonomy( 'deals_sales_status', array('post', 'daily-deals'));
	endif;
	
	$deal_types = array(
		'simple',
		'variable',
		'external'
	);
	
	foreach($deal_types as $type) {
		if (!get_term_by( 'slug', sanitize_title($type), 'deal-type')) {
			wp_insert_term($type, 'deal-type');
		}
	}
	
	$sales_status = array(
		'pending',
		'failed',
		'on-hold',
		'processing',
		'completed',
		'refunded',
		'cancelled'
	);
	
	foreach($sales_status as $status) {
		if (!get_term_by( 'slug', sanitize_title($status), 'deals_sales_status')) {
			wp_insert_term($status, 'deals_sales_status');
		}
	}

}


/**
 * Upgrade WP Deals
 */
function wpdeals_upgrade(){
	global $wpdb;
	
	$wpdb->hide_errors();

	$collate = '';
        if($wpdb->supports_collation()) {
                    if(!empty($wpdb->charset)) $collate = "DEFAULT CHARACTER SET $wpdb->charset";
                    if(!empty($wpdb->collate)) $collate .= " COLLATE $wpdb->collate";
        }

        require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
    
        // Update wpdeals_permissions table to include order ID's as well as keys
        $results = $wpdb->get_results( $wpdb->prepare( "SELECT * FROM ".$wpdb->prefix."postmeta WHERE meta_key = '_end_time';" ) );
	
	if ($results) foreach ($results as $result) :
		
		if (!$result->meta_value || count(explode( ' ', $result->meta_value)) != 2) continue;               
                		
                $wpdb->update( $wpdb->prefix . "postmeta", array( 
                        'meta_value' => strtotime($result->meta_value), 
                ), array( 
                        'post_id' => $result->post_id,
                        'meta_key' => $result->meta_key
                ));
		
	endforeach;
	
	$wpdb->show_errors();
    
}