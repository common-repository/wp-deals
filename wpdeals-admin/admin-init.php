<?php
/**
 * WPDeals Admin
 * 
 * Main admin file which loads all settings panels and sets up admin menus.
 *
 * @author 		Tokokoo
 * @category 	Admin
 * @package 	WPDeals
 */

include_once( 'admin-install.php' );

function wpdeals_admin_init() {
	include_once( 'admin-settings-forms.php' );
	include_once( 'admin-settings.php' );
	include_once( 'admin-attributes.php' );
	include_once( 'admin-dashboard.php' );
	include_once( 'admin-import.php' );
	include_once( 'post-types/post-types-init.php' );
	include_once( 'admin-reports.php' );
	include_once( 'writepanels/writepanels-init.php' );	
}
add_action('admin_init', 'wpdeals_admin_init');

/**
 * Admin Menus
 * 
 * Sets up the admin menus in wordpress.
 */
function wpdeals_admin_menu() {
	global $menu, $wpdeals;
	
	if ( current_user_can( 'manage_deals' ) ) $menu[] = array( '', 'read', 'separator-wpdeals', '', 'wp-menu-separator wpdeals' );
	
        add_menu_page(__('WPDeals', 'wpdeals'), __('WPDeals', 'wpdeals'), 'manage_deals', 'wpdeals' , 'wpdeals_settings', $wpdeals->plugin_url() . '/wpdeals-assets/images/icons/menu_icon_wd.png', 55);
        add_submenu_page('wpdeals', __('WPDeals Settings', 'wpdeals'),  __('Settings', 'wpdeals') , 'manage_deals', 'wpdeals', 'wpdeals_settings');
        add_submenu_page('wpdeals', __('Reports', 'wpdeals'),  __('Reports', 'wpdeals') , 'manage_deals', 'wpdeals_reports', 'wpdeals_reports');
    
        $print_css_on = array( 'toplevel_page_wpdeals', 'wpdeals_page_wpdeals_reports', 'deal_page_wpdeals_attributes', 'edit-tags.php', 'edit.php', 'index.php', 'post-new.php', 'post.php' );

        foreach ($print_css_on as $page) add_action( 'admin_print_styles-'. $page, 'wpdeals_admin_css' ); 
}
add_action('admin_menu', 'wpdeals_admin_menu', 9);

/**
 * Admin Scripts
 */
function wpdeals_admin_scripts() {
	global $wpdeals,$pagenow;
	
	$suffix = defined('SCRIPT_DEBUG') && SCRIPT_DEBUG ? '' : '';
        
	// Register scripts
	if($pagenow == 'post-new.php' && isset($_GET['post_type']) && $_GET['post_type'] == 'daily-deals') {
		wp_deregister_script('jquery-ui-core');
		wp_register_script( 'jquery-ui-core',  $wpdeals->plugin_url() . '/wpdeals-assets/js/jquery-ui'.$suffix.'.js', array('jquery'), '1.8.16' );		
	}        
	wp_register_script( 'wpdeals_admin', $wpdeals->plugin_url() . '/wpdeals-assets/js/admin/wpdeals_admin'.$suffix.'.js', array('jquery', 'jquery-ui-widget', 'jquery-ui-core'), '1.0' );
	wp_register_script( 'jquery-ui-timepicker',  $wpdeals->plugin_url() . '/wpdeals-assets/js/admin/ui-timepicker.js', array('jquery','jquery-ui-core'), '1.0.2' );	
	wp_register_script( 'wpdeals_writepanel', $wpdeals->plugin_url() . '/wpdeals-assets/js/admin/write-panels'.$suffix.'.js', array('jquery', 'jquery-ui-datepicker') );
	wp_register_script( 'chosen', $wpdeals->plugin_url() . '/wpdeals-assets/js/chosen.jquery'.$suffix.'.js', array('jquery'), '1.0' );
	
	// Get admin screen id
        $screen = get_current_screen();
    
        // WPDeals admin pages
        if (in_array( $screen->id, array( 'toplevel_page_wpdeals', 'wpdeals_page_wpdeals_reports', 'edit-deals-sales', 'edit-store_coupon', 'store_coupon', 'deals-sales', 'edit-daily-deals', 'daily-deals' ))) :
    
                wp_enqueue_script( 'wpdeals_admin' );
                wp_enqueue_script('farbtastic');
                wp_enqueue_script('chosen');
                wp_enqueue_script('jquery-ui-sortable');

        endif;
    
    // Edit deals category pages
    if (in_array( $screen->id, array('edit-deal-category') )) :
    
		wp_enqueue_script( 'media-upload' );
		wp_enqueue_script( 'thickbox' );
		
	endif;

	// Deal/Coupon/Orders
	if (in_array( $screen->id, array( 'store_coupon', 'deals-sales', 'daily-deals', 'edit-daily-deals' ))) :
		
		global $post;
		
		wp_enqueue_script( 'wpdeals_writepanel' );
		wp_enqueue_script( 'jquery-ui-timepicker' );
		wp_enqueue_script( 'media-upload' );
		wp_enqueue_script( 'thickbox' );
		wp_enqueue_script('chosen');
		
		$wpdeals_witepanel_params = array( 
			'remove_item_notice' 			=> __("Remove this item? If you have previously reduced this item's stock, or this order was submitted by a customer, will need to manually restore the item's stock.", 'wpdeals'),
			'cart_total' 				=> __("Calculate totals based on order items?", 'wpdeals'),
			'ID' 					=> __('ID', 'wpdeals'),
			'item_name' 				=> __('Item Name', 'wpdeals'),
			'quantity' 				=> __('Quantity e.g. 2', 'wpdeals'),
			'cost_unit' 				=> __('Cost per unit e.g. 2.99', 'wpdeals'),
			'select_terms'				=> __('Select terms', 'wpdeals'),
			'no_customer_selected'			=> __('No customer selected', 'wpdeals'),
			'plugin_url' 				=> $wpdeals->plugin_url(),
			'ajax_url' 				=> admin_url('admin-ajax.php'),
			'add_order_item_nonce' 			=> wp_create_nonce("add-order-item"),
			'get_customer_details_nonce'            => wp_create_nonce("get-customer-details"),
			'upsell_crosssell_search_deals_nonce'   => wp_create_nonce("search-daily-deals"),
			'calendar_image'			=> $wpdeals->plugin_url().'/wpdeals-assets/images/calendar.png',
			'post_id'				=> $post->ID
		 );
					 
		wp_localize_script( 'wpdeals_writepanel', 'wpdeals_writepanel_params', $wpdeals_witepanel_params );
		
	endif;
	
	// Term ordering - only when sorting by menu_order (our custom meta)
	if (($screen->id=='edit-deal-category' || strstr($screen->id, 'edit-pa_')) && !isset($_GET['orderby'])) :
		
		wp_register_script( 'wpdeals_term_ordering', $wpdeals->plugin_url() . '/wpdeals-assets/js/admin/term-ordering.js', array('jquery-ui-sortable') );
		wp_enqueue_script( 'wpdeals_term_ordering' );
		
		$taxonomy = (isset($_GET['taxonomy'])) ? $_GET['taxonomy'] : '';
		
		$wpdeals_term_order_params = array( 
			'taxonomy' 			=>  $taxonomy
		 );
					 
		wp_localize_script( 'wpdeals_term_ordering', 'wpdeals_term_ordering_params', $wpdeals_term_order_params );
		
	endif;

	// Reports pages
    if ($screen->id=='wpdeals_page_wpdeals_reports') :

		wp_enqueue_script( 'jquery-ui-datepicker' );
		wp_enqueue_script( 'flot', $wpdeals->plugin_url() . '/wpdeals-assets/js/admin/jquery.flot'.$suffix.'.js', 'jquery', '1.0' );
		wp_enqueue_script( 'flot-resize', $wpdeals->plugin_url() . '/wpdeals-assets/js/admin/jquery.flot.resize'.$suffix.'.js', array('jquery', 'flot'), '1.0' );
	
	endif;
}
add_action('admin_enqueue_scripts', 'wpdeals_admin_scripts');

/**
 * Queue admin CSS
 */
function wpdeals_admin_css() {
	global $wpdeals, $typenow, $post;

	if ($typenow=='post' && isset($_GET['post']) && !empty($_GET['post'])) $typenow = $post->post_type;
	
	if ( $typenow=='' || $typenow=="daily-deals" || $typenow=="deals-sales" || $typenow=="store_coupon" ) :
		wp_enqueue_style( 'thickbox' );
		wp_enqueue_style( 'wpdeals_admin_styles', $wpdeals->plugin_url() . '/wpdeals-assets/css/admin.css' );
		wp_enqueue_style( 'jquery-ui-style', 'http://ajax.googleapis.com/ajax/libs/jqueryui/1.8.2/themes/smoothness/jquery-ui.css' );
	endif;
	
	wp_enqueue_style('farbtastic');
	
	do_action('wpdeals_admin_css');
}

/**
 * Order admin menus
 */
function wpdeals_admin_menu_order( $menu_order ) {
	
	// Initialize our custom order array
	$wpdeals_menu_order = array();

	// Get the index of our custom separator
	$wpdeals_separator = array_search( 'separator-wpdeals', $menu_order );
	
	// Get index of deals menu
	$wpdeals_deals = array_search( 'edit.php?post_type=daily-deals', $menu_order );

	// Loop through menu order and do some rearranging
	foreach ( $menu_order as $index => $item ) :

		if ( ( ( 'wpdeals' ) == $item ) ) :
			$wpdeals_menu_order[] = 'separator-wpdeals';
			$wpdeals_menu_order[] = $item;
			$wpdeals_menu_order[] = 'edit.php?post_type=daily-deals';
			unset( $menu_order[$wpdeals_separator] );
			unset( $menu_order[$wpdeals_deals] );
		elseif ( !in_array( $item, array( 'separator-wpdeals' ) ) ) :
			$wpdeals_menu_order[] = $item;
		endif;

	endforeach;
	
	// Return order
	return $wpdeals_menu_order;
}
add_action('menu_order', 'wpdeals_admin_menu_order');

function wpdeals_admin_custom_menu_order() {
	if ( !current_user_can( 'manage_deals' ) ) return false;
	return true;
}
add_action('custom_menu_order', 'wpdeals_admin_custom_menu_order');

/**
 * Admin Head
 * 
 * Outputs some styles in the admin <head> to show icons on the wpdeals admin pages
 */
function wpdeals_admin_head() {
	global $wpdeals, $pagenow;
	
	if ( !current_user_can( 'manage_deals' ) ) return false;
	?>
	<style type="text/css">
		<?php if ( $pagenow == 'post-new.php' && $_GET['post_type']=='daily-deals' ) : ?>
			.icon32-posts-daily-deals { background-position: -13px -5px !important; }
		<?php elseif ( isset($_GET['taxonomy']) && $_GET['taxonomy']=='deal-categories' ) : ?>
			.icon32-posts-daily-deals { background-position: -185px -5px !important; }
		<?php elseif ( isset($_GET['taxonomy']) && $_GET['taxonomy']=='deal-tags' ) : ?>
			.icon32-posts-daily-deals { background-position: -185px -5px !important; }
		<?php endif; ?>
	</style>
	<?php
}
add_action('admin_head', 'wpdeals_admin_head');

/**
 * Prevent non-admin access to backend
 */
if (get_option('wpdeals_lock_down_admin')=='yes') add_action('admin_init', 'wpdeals_prevent_admin_access');

function wpdeals_prevent_admin_access() {
	
	if ( is_admin() && !is_ajax() && !current_user_can('edit_posts') ) :
		wp_safe_redirect(get_permalink(get_option('wpdeals_myaccount_page_id')));
		exit;
	endif;
	
}

/**
 * Fix 'insert into post' buttons for images
 **/
add_filter('get_media_item_args', 'wpdeals_allow_img_insertion');

function wpdeals_allow_img_insertion($vars) {
    $vars['send'] = true; // 'send' as in "Send to Editor"
    return($vars);
}

/**
 * Duplicate a deals action
 *
 * Based on 'Duplicate Post' (http://www.lopo.it/duplicate-post-plugin/) by Enrico Battocchi
 */
add_action('admin_action_duplicate_deals', 'wpdeals_duplicate_deals_action');

function wpdeals_duplicate_deals_action() {

	if (! ( isset( $_GET['post']) || isset( $_POST['post'])  || ( isset($_REQUEST['action']) && 'duplicate_post_save_as_new_page' == $_REQUEST['action'] ) ) ) {
		wp_die(__('No deals to duplicate has been supplied!', 'wpdeals'));
	}

	// Get the original page
	$id = (isset($_GET['post']) ? $_GET['post'] : $_POST['post']);
	check_admin_referer( 'wpdeals-duplicate-daily-deals_' . $id );
	$post = wpdeals_get_deals_to_duplicate($id);

	// Copy the page and insert it
	if (isset($post) && $post!=null) {
		$new_id = wpdeals_create_duplicate_from_deals($post);

		// If you have written a plugin which uses non-WP database tables to save
		// information about a page you can hook this action to dupe that data.
		do_action( 'wpdeals_duplicate_deals', $new_id, $post );

		// Redirect to the edit screen for the new draft page
		wp_redirect( admin_url( 'post.php?action=edit&post=' . $new_id ) );
		exit;
	} else {
		wp_die(__('Deal creation failed, could not find original deals:', 'wpdeals') . ' ' . $id);
	}
}

/**
 * Duplicate a deals link on deals list
 */
add_filter('post_row_actions', 'wpdeals_duplicate_deals_link_row',10,2);
add_filter('page_row_actions', 'wpdeals_duplicate_deals_link_row',10,2);
	
function wpdeals_duplicate_deals_link_row($actions, $post) {
	
	if (function_exists('duplicate_post_plugin_activation')) return $actions;
	
	if (!current_user_can('manage_deals')) return $actions;
	
	if ($post->post_type!='daily-deals') return $actions;
	
	$actions['duplicate'] = '<a href="' . wp_nonce_url( admin_url( 'admin.php?action=duplicate_deals&amp;post=' . $post->ID ), 'wpdeals-duplicate-daily-deals_' . $post->ID ) . '" title="' . __("Make a duplicate from this deals", 'wpdeals')
		. '" rel="permalink">' .  __("Duplicate", 'wpdeals') . '</a>';

	return $actions;
}

/**
 *  Duplicate a deals link on edit screen
 */
add_action( 'post_submitbox_start', 'wpdeals_duplicate_deals_post_button' );

function wpdeals_duplicate_deals_post_button() {
	global $post;
	
	if (function_exists('duplicate_post_plugin_activation')) return;
	
	if (!current_user_can('manage_deals')) return;
	
	if( !is_object( $post ) ) return;
	
	if ($post->post_type!='daily-deals') return;
	
	if ( isset( $_GET['post'] ) ) :
		$notifyUrl = wp_nonce_url( admin_url( "admin.php?action=duplicate_deals&post=" . $_GET['post'] ), 'wpdeals-duplicate-daily-deals_' . $_GET['post'] );
		?>
		<div id="duplicate-action"><a class="submitduplicate duplication"
			href="<?php echo esc_url( $notifyUrl ); ?>"><?php _e('Copy to a new draft', 'wpdeals'); ?></a>
		</div>
		<?php
	endif;
}

/**
 * Get a deals from the database
 */
function wpdeals_get_deals_to_duplicate($id) {
	global $wpdb;
	$post = $wpdb->get_results("SELECT * FROM $wpdb->posts WHERE ID=$id");
	if ($post->post_type == "revision"){
		$id = $post->post_parent;
		$post = $wpdb->get_results("SELECT * FROM $wpdb->posts WHERE ID=$id");
	}
	return $post[0];
}

/**
 * Function to create the duplicate
 */
function wpdeals_create_duplicate_from_deals($post, $parent = 0) {
	global $wpdb;

	$new_post_author 	= wp_get_current_user();
	$new_post_date 		= current_time('mysql');
	$new_post_date_gmt 	= get_gmt_from_date($new_post_date);
	
	if ($parent>0) :
		$post_parent		= $parent;
		$suffix 		= '';
		$post_status     	= 'publish';
	else :
		$post_parent		= $post->post_parent;
		$post_status     	= 'draft';
		$suffix 		= __(" (Copy)", 'wpdeals');
	endif;
	
	$new_post_type 		= $post->post_type;
	$post_content    	= str_replace("'", "''", $post->post_content);
	$post_content_filtered  = str_replace("'", "''", $post->post_content_filtered);
	$post_excerpt    	= str_replace("'", "''", $post->post_excerpt);
	$post_title      	= str_replace("'", "''", $post->post_title).$suffix;
	$post_name       	= str_replace("'", "''", $post->post_name);
	$comment_status  	= str_replace("'", "''", $post->comment_status);
	$ping_status     	= str_replace("'", "''", $post->ping_status);

	// Insert the new template in the post table
	$wpdb->query(
			"INSERT INTO $wpdb->posts
			(post_author, post_date, post_date_gmt, post_content, post_content_filtered, post_title, post_excerpt,  post_status, post_type, comment_status, ping_status, post_password, to_ping, pinged, post_modified, post_modified_gmt, post_parent, menu_order, post_mime_type)
			VALUES
			('$new_post_author->ID', '$new_post_date', '$new_post_date_gmt', '$post_content', '$post_content_filtered', '$post_title', '$post_excerpt', '$post_status', '$new_post_type', '$comment_status', '$ping_status', '$post->post_password', '$post->to_ping', '$post->pinged', '$new_post_date', '$new_post_date_gmt', '$post_parent', '$post->menu_order', '$post->post_mime_type')");

	$new_post_id = $wpdb->insert_id;

	// Copy the taxonomies
	wpdeals_duplicate_post_taxonomies($post->ID, $new_post_id, $post->post_type);

	// Copy the meta information
	wpdeals_duplicate_post_meta($post->ID, $new_post_id);
	
	// Copy the children (variations)
	if ( $children_deals =& get_children( 'post_parent='.$post->ID.'&post_type=deal-variations' ) ) :

		if ($children_deals) foreach ($children_deals as $child) :
			
			wpdeals_create_duplicate_from_deals(wpdeals_get_deals_to_duplicate($child->ID), $new_post_id);
			
		endforeach;

	endif;

	return $new_post_id;
}

/**
 * Copy the taxonomies of a post to another post
 */
function wpdeals_duplicate_post_taxonomies($id, $new_id, $post_type) {
	global $wpdb;
	$taxonomies = get_object_taxonomies($post_type); //array("category", "post_tag");
	foreach ($taxonomies as $taxonomy) {
		$post_terms = wp_get_object_terms($id, $taxonomy);
		for ($i=0; $i<count($post_terms); $i++) {
			wp_set_object_terms($new_id, $post_terms[$i]->slug, $taxonomy, true);
		}
	}
}

/**
 * Copy the meta information of a post to another post
 */
function wpdeals_duplicate_post_meta($id, $new_id) {
	global $wpdb;
	$post_meta_infos = $wpdb->get_results("SELECT meta_key, meta_value FROM $wpdb->postmeta WHERE post_id=$id");

	if (count($post_meta_infos)!=0) {
		$sql_query = "INSERT INTO $wpdb->postmeta (post_id, meta_key, meta_value) ";
		foreach ($post_meta_infos as $meta_info) {
			$meta_key = $meta_info->meta_key;
			$meta_value = addslashes($meta_info->meta_value);
			$sql_query_sel[]= "SELECT $new_id, '$meta_key', '$meta_value'";
		}
		$sql_query.= implode(" UNION ALL ", $sql_query_sel);
		$wpdb->query($sql_query);
	}
}


/**
 * Deleting deals sync
 * 
 * Removes variations etc belonging to a deleted post
 */
add_action('delete_post', 'wpdeals_delete_deals_sync', 10);

function wpdeals_delete_deals_sync( $id ) {
	
	if (!current_user_can('delete_posts')) return;
	
	if ( $id > 0 ) :
	
		if ( $children_deals =& get_children( 'post_parent='.$id.'&post_type=deal-variations' ) ) :
	
			if ($children_deals) :
			
				foreach ($children_deals as $child) :
					
					wp_delete_post( $child->ID, true );
					
				endforeach;
			
			endif;
	
		endif;
	
	endif;
	
}

/**
 * Directory for uploads
 */
add_filter('upload_dir', 'wpdeals_downloads_upload_dir');

function wpdeals_downloads_upload_dir( $pathdata ) {

	if (isset($_POST['type']) && $_POST['type'] == 'downloadable_deals') :
		
		// Uploading a downloadable file
		$subdir = '/deals_uploads'.$pathdata['subdir'];
	 	$pathdata['path'] = str_replace($pathdata['subdir'], $subdir, $pathdata['path']);
	 	$pathdata['url'] = str_replace($pathdata['subdir'], $subdir, $pathdata['url']);
		$pathdata['subdir'] = str_replace($pathdata['subdir'], $subdir, $pathdata['subdir']);
		return $pathdata;
		
	endif;
	
	return $pathdata;

}

add_action('media_upload_downloadable_deals', 'wpdeals_media_upload_downloadable_deals');

function wpdeals_media_upload_downloadable_deals() {
	do_action('media_upload_file');
}

/**
 * Shortcode button in post editor
 **/
add_action( 'init', 'wpdeals_add_shortcode_button' );
add_filter( 'tiny_mce_version', 'wpdeals_refresh_mce' );

function wpdeals_add_shortcode_button() {
	if ( ! current_user_can('edit_posts') && ! current_user_can('edit_pages') ) return;
	if ( get_user_option('rich_editing') == 'true') :
		add_filter('mce_external_plugins', 'wpdeals_add_shortcode_tinymce_plugin');
		add_filter('mce_buttons', 'wpdeals_register_shortcode_button');
	endif;
}

function wpdeals_register_shortcode_button($buttons) {
	array_push($buttons, "|", "wpdeals_shortcodes_button");
	return $buttons;
}

function wpdeals_add_shortcode_tinymce_plugin($plugin_array) {
	global $wpdeals;
	$plugin_array['WPDealsShortcodes'] = $wpdeals->plugin_url() . '/wpdeals-assets/js/admin/editor_plugin.js';
	return $plugin_array;
}

function wpdeals_refresh_mce($ver) {
	$ver += 3;
	return $ver;
}


/**
 * Export sales this month into CSV
 */
add_action('admin_init', 'wpdeals_export_to_csv_this_month');
function wpdeals_export_to_csv_this_month(){
    
	global $wpdb;
        
        if(is_admin() && isset ($_REQUEST['page']) && $_REQUEST['page'] == 'wpdeals_reports' &&
                isset ($_REQUEST['tab']) && $_REQUEST['tab'] == 'sales' &&
                isset ($_REQUEST['chart']) && $_REQUEST['chart'] == '6'):
           
            
                $today = getdate();
                $args = array(
                    'year'              => $today["year"],
                    'monthnum'          => $today["mon"],
                    'post_type'         => 'deals-sales',
                    'numberposts'       => -1,
                    'orderby'           => 'post_date',
                    'order'             => 'ASC',
                    'post_type'         => 'deals-sales',
                    'post_status'       => 'publish' ,
                    'suppress_filters'  => 0                    
                );
                $sales = get_posts( $args );
                
                $header  = "Order ID, Date, Order Total, Status, Payment Method\n";
                $data    = apply_filters('wpdeals_export_csv_sales_header', $header);  
                foreach ($sales as $item):
                    $data   .= __('Order #').$item->ID.',';
                    $date    = str_getcsv ( $item->post_date , ",", "\"" , "\\");
                    $data   .= $date[0].',';
                    $data   .= get_post_meta($item->ID, '_order_total', true).',';
                    $status  = get_the_terms($item->ID, 'deals_sales_status');
                    $order   = new wpdeals_order( $item->ID );
                    foreach ($status as $stat)
                        $data   .= ucfirst($stat->name).',';
                    $data   .= esc_html( $order->payment_method_title ).'';
                    $data   .= "\n";                    
                endforeach;
                                
                $data    = apply_filters('wpdeals_export_csv_sales_data', $data);  
                
                
                $csv_file_name = __('Sales_', 'wpdeals').date('Ymd_His').".csv"; # CSV FILE NAME WILL BE table_name_yyyymmdd_hhmmss.csv

                header("Content-type: text/x-csv"); # DECLARING FILE TYPE
                header("Content-Transfer-Encoding: binary");
                header("Content-Disposition: attachment; filename=".$csv_file_name); # EXPORT GENERATED CSV FILE
                header("Pragma: no-cache");
                header("Expires: 0");

                echo $data;
                exit;
        
        endif;
        
}