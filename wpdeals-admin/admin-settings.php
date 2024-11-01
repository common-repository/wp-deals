<?php
/**
 * Functions for the settings page in admin.
 * 
 * The settings page contains options for the WPDeals plugin - this file contains functions to display
 * and save the list of options.
 *
 * @author 		Tokokoo
 * @category 	Admin
 * @package 	WPDeals
 */

/**
 * Define settings for the WPDeals settings pages
 */
global $wpdeals_settings;

$wpdeals_settings['general'] = apply_filters('wpdeals_general_settings', array(

	array( 'name' => __( 'General Options', 'wpdeals' ), 'type' => 'title', 'desc' => '', 'id' => 'general_options' ),

	array(  
		'name' => __( 'Base Country/Region', 'wpdeals' ),
		'desc' 		=> __( 'This is the base country for your business.', 'wpdeals' ),
		'id' 		=> 'wpdeals_default_country',
		'css' 		=> 'min-width:300px;',
		'std' 		=> 'GB',
		'type' 		=> 'single_select_country'
	),
	
	array(  
		'name' => __( 'Currency', 'wpdeals' ),
		'desc' 		=> __("This controls what currency prices are listed at in the deals and which currency gateways will take payments in.", 'wpdeals' ),
		'tip' 		=> '',
		'id' 		=> 'wpdeals_currency',
		'css' 		=> 'min-width:300px;',
		'std' 		=> 'GBP',
		'type' 		=> 'select',
		'class'		=> 'chosen_select',
		'options' => array_unique(apply_filters('wpdeals_currencies', array( 
			'USD' => __( 'US Dollars (&#36;)', 'wpdeals' ),
			'EUR' => __( 'Euros (&euro;)', 'wpdeals' ),
			'GBP' => __( 'Pounds Sterling (&pound;)', 'wpdeals' ),
			'AUD' => __( 'Australian Dollars (&#36;)', 'wpdeals' ),
			'BRL' => __( 'Brazilian Real (&#36;)', 'wpdeals' ),
			'CAD' => __( 'Canadian Dollars (&#36;)', 'wpdeals' ),
			'CZK' => __( 'Czech Koruna (&#75;&#269;)', 'wpdeals' ),
			'DKK' => __( 'Danish Krone', 'wpdeals' ),
			'HKD' => __( 'Hong Kong Dollar (&#36;)', 'wpdeals' ),
			'HUF' => __( 'Hungarian Forint', 'wpdeals' ),
			'IDR' => __( 'Indonesia (IDR)', 'wpdeals' ),
			'ILS' => __( 'Israeli Shekel', 'wpdeals' ),
			'JPY' => __( 'Japanese Yen (&yen;)', 'wpdeals' ),
			'MYR' => __( 'Malaysian Ringgits', 'wpdeals' ),
			'MXN' => __( 'Mexican Peso (&#36;)', 'wpdeals' ),
			'NZD' => __( 'New Zealand Dollar (&#36;)', 'wpdeals' ),
			'NOK' => __( 'Norwegian Krone', 'wpdeals' ),
			'PHP' => __( 'Philippine Pesos', 'wpdeals' ),
			'PLN' => __( 'Polish Zloty', 'wpdeals' ),
			'SGD' => __( 'Singapore Dollar (&#36;)', 'wpdeals' ),
			'SEK' => __( 'Swedish Krona', 'wpdeals' ),
			'CHF' => __( 'Swiss Franc', 'wpdeals' ),
			'TWD' => __( 'Taiwan New Dollars', 'wpdeals' ),
			'THB' => __( 'Thai Baht', 'wpdeals' ), 
			'TRY' => __( 'Turkish Lira (TL)', 'wpdeals' ),
			'ZAR' => __( 'South African rand (R)', 'wpdeals' ),
			))
		)
	),
	
	array(  
		'name' => __( 'Checkout', 'wpdeals' ),
		'desc' 		=> __( 'Allow users to create an account and login from the checkout page', 'wpdeals' ),
		'id' 		=> 'wpdeals_enable_signup_and_login_from_checkout',
		'std' 		=> 'yes',
		'type' 		=> 'checkbox',
		'checkboxgroup'		=> 'start'
	),
	
	array(  
		'desc' 		=> __( 'Show order comments section', 'wpdeals' ),
		'id' 		=> 'wpdeals_enable_order_comments',
		'std' 		=> 'yes',
		'type' 		=> 'checkbox',
		'checkboxgroup'		=> ''
	),
	
	array(  
		'desc' 		=> __( 'Force <abbr title="Secure Sockets Layer, a computing protocol that ensures the security of data sent via the Internet by using encryption">SSL</abbr>/HTTPS (an SSL Certificate is required)', 'wpdeals' ),
		'id' 		=> 'wpdeals_force_ssl_checkout',
		'std' 		=> 'no',
		'type' 		=> 'checkbox',
		'checkboxgroup'		=> ''
	),
	
	array(  
		'desc' 		=> __( 'Un-force <abbr title="Secure Sockets Layer, a computing protocol that ensures the security of data sent via the Internet by using encryption">SSL</abbr>/HTTPS when leaving the checkout', 'wpdeals' ),
		'id' 		=> 'wpdeals_unforce_ssl_checkout',
		'std' 		=> 'no',
		'type' 		=> 'checkbox',
		'checkboxgroup'		=> 'end'
	),
	
	array(  
		'name' => __( 'Customer Accounts', 'wpdeals' ),
		'desc' 		=> __( 'Allow unregistered users to register from the My Account page', 'wpdeals' ),
		'id' 		=> 'wpdeals_enable_myaccount_registration',
		'std' 		=> 'no',
		'type' 		=> 'checkbox',
		'checkboxgroup'		=> 'start'
	),
	
	array(  
		'desc' 		=> __( 'Clear cart when logging out', 'wpdeals' ),
		'id' 		=> 'wpdeals_clear_cart_on_logout',
		'std' 		=> 'no',
		'type' 		=> 'checkbox',
		'checkboxgroup'		=> ''
	),
	
	array(  
		'desc' 		=> __( 'Prevent customers from accessing WordPress admin', 'wpdeals' ),
		'id' 		=> 'wpdeals_lock_down_admin',
		'std' 		=> 'no',
		'type' 		=> 'checkbox',
		'checkboxgroup'		=> 'end'
	),
	
	array(  
		'name' => __( 'Styling', 'wpdeals' ),
		'desc' 		=> __( 'Enable WPDeals CSS styles', 'wpdeals' ),
		'id' 		=> 'wpdeals_frontend_css',
		'std' 		=> 'yes',
		'type' 		=> 'checkbox',
		'checkboxgroup'		=> 'start'
	),
	
	array(  
		'desc' 		=> __( 'Enable the "Demo Store" notice on your site', 'wpdeals' ),
		'id' 		=> 'wpdeals_demo_store',
		'std' 		=> 'no',
		'type' 		=> 'checkbox',
		'checkboxgroup'	=> 'end'
	),
	
	array(  
		'name' => __( 'Scripts', 'wpdeals' ),
		'desc' 		=> __( 'Enable WPDeals lightbox on the deals page', 'wpdeals' ),
		'id' 		=> 'wpdeals_enable_lightbox',
		'std' 		=> 'yes',
		'type' 		=> 'checkbox',
		'checkboxgroup'		=> 'start'
	),
	
	array(  
		'desc' 		=> __( 'Enable jQuery UI (used by the price slider widget)', 'wpdeals' ),
		'id' 		=> 'wpdeals_enable_jquery_ui',
		'std' 		=> 'yes',
		'type' 		=> 'checkbox',
		'checkboxgroup'		=> ''
	),
	
	array(  
	    'desc'     => __( 'Output WPDeals JavaScript in the footer (<code>wp_footer</code>)', 'wpdeals' ),
	    'id'     => 'wpdeals_scripts_position',
	    'std'     => 'yes',
	    'type'     => 'checkbox',
	    'checkboxgroup'		=> 'end'
	),

	array(  
		'name' => __('File download method', 'wpdeals'),
		'desc' 		=> __('Forcing downloads will keep URLs hidden, but some servers may serve large files unreliably. If supported, <code>X-Accel-Redirect</code>/ <code>X-Sendfile</code> can be used to serve downloads instead (server requires <code>mod_xsendfile</code>).', 'wpdeals'),
		'id' 		=> 'wpdeals_file_download_method',
		'type' 		=> 'select',
		'class'		=> 'chosen_select',
		'css' 		=> 'min-width:300px;',
		'std'		=> 'force',
		'options' => array(  
			'force'  	=> __( 'Force Downloads', 'wpdeals' ),
			'xsendfile' => __( 'X-Accel-Redirect/X-Sendfile', 'wpdeals' ),
			'redirect'  => __( 'Redirect only', 'wpdeals' ),	
		)
	),
	
	array( 'type' => 'sectionend', 'id' => 'general_options'),
	
	array( 'name' => __( 'ShareThis', 'wpdeals' ), 'type' => 'title', 'desc' => '', 'id' => 'share_this' ),

	array(  
		'name' => __( 'Share Button', 'wpdeals' ),
		'desc' 		=> __( 'Display share button on single deal', 'wpdeals' ),
		'id' 		=> 'wpdeals_show_share',
		'std' 		=> 'yes',
		'type' 		=> 'checkbox'
	),
	array(  
		'name' => __( 'ShareThis Publisher ID', 'wpdeals' ),
		'desc' 		=> sprintf( __( 'Enter your %1$sShareThis publisher ID%2$s to show social sharing buttons on deals pages.', 'wpdeals' ), '<a href="http://sharethis.com/account/">', '</a>' ),
		'id' 		=> 'wpdeals_sharethis',
		'type' 		=> 'text',
		'std' 		=> '',
                'css' 		=> 'min-width:300px;',
	),
	
	array( 'type' => 'sectionend', 'id' => 'share_this'),
	
	array( 'name' => __( 'Google Analytics', 'wpdeals' ), 'type' => 'title', 'desc' => '', 'id' => 'google_analytics' ),
	
	array(  
		'name' => __('Google Analytics ID', 'wpdeals'),
		'desc' 		=> __('Log into your google analytics account to find your ID. e.g. <code>UA-XXXXX-X</code>', 'wpdeals'),
		'id' 		=> 'wpdeals_ga_id',
		'type' 		=> 'text',
        'css' 		=> 'min-width:300px;',
	),
	
	array(  
		'name' => __('Tracking code', 'wpdeals'),
		'desc' 		=> __('Add tracking code to your site\'s footer. You don\'t need to enable this if using a 3rd party analytics plugin.', 'wpdeals'),
		'id' 		=> 'wpdeals_ga_standard_tracking_enabled',
		'type' 		=> 'checkbox',
		'checkboxgroup'		=> 'start'
	),
	
	array(  
		'name' => __('Tracking code', 'wpdeals'),
		'desc' 		=> __('Add eCommerce tracking code to the thankyou page', 'wpdeals'),
		'id' 		=> 'wpdeals_ga_ecommerce_tracking_enabled',
		'type' 		=> 'checkbox',
		'checkboxgroup'		=> 'end'
	),
					
	array( 'type' => 'sectionend', 'id' => 'google_analytics'),

)); // End general settings

$store_page_id = get_option('wpdeals_store_page_id');
$base_slug = ($store_page_id > 0 && get_page( $store_page_id )) ? get_page_uri( $store_page_id ) : 'store';	
	
$wpdeals_settings['pages'] = apply_filters('wpdeals_page_settings', array(

	array( 'name' => __( 'Page Setup', 'wpdeals' ), 'type' => 'title', 'desc' => '', 'id' => 'page_options' ),
	
	array(  
		'name' => __( 'Store Base Page', 'wpdeals' ),
		'desc' 		=> sprintf( __( 'This sets the base page of your store.', 'wpdeals' ), '<a target="_blank" href="options-permalink.php">', '</a>' ),
		'id' 		=> 'wpdeals_store_page_id',
		'type' 		=> 'single_select_page',
		'std' 		=> '',
		'class'		=> 'chosen_select_nostd',
		'css' 		=> 'min-width:300px;',
	),
	
	array(  
		'name' => __( 'Base Page Title', 'wpdeals' ),
		'desc' 		=> __( 'This title to show on the store base page. Leave blank to use the page title.', 'wpdeals' ),
		'id' 		=> 'wpdeals_store_page_title',
		'type' 		=> 'text',
		'css' 		=> 'min-width:300px;',
		'std' 		=> 'All Deals' // Default value for the page title - changed in settings
	),

	array(  
		'name' => __( 'Terms page ID', 'wpdeals' ),
		'desc' 		=> __( 'If you define a "Terms" page the customer will be asked if they accept them when checking out.', 'wpdeals' ),
		'tip' 		=> '',
		'id' 		=> 'wpdeals_terms_page_id',
		'std' 		=> '',
		'class'		=> 'chosen_select_nostd',
		'css' 		=> 'min-width:300px;',
		'type' 		=> 'single_select_page'
	),
	
	array( 'type' => 'sectionend', 'id' => 'page_options' ),
	
	array( 'name' => __( 'Permalinks', 'wpdeals' ), 'type' => 'title', 'desc' => '', 'id' => 'permalink_options' ),
	
	array(  
		'name' => __( 'Taxonomy base page', 'wpdeals' ),
		'desc' 		=> sprintf(__( 'Prepend store categories/tags with store base page (<code>%s</code>)', 'wpdeals' ), $base_slug),
		'id' 		=> 'wpdeals_prepend_store_page_to_urls',
		'std' 		=> 'no',
		'type' 		=> 'checkbox',
	),
	
	array(  
		'name' => __( 'Deal category slug', 'wpdeals' ),
		'desc' 		=> __( 'Shows in the deals category URLs. Leave blank to use the default slug.', 'wpdeals' ),
		'id' 		=> 'wpdeals_deal_category_slug',
		'type' 		=> 'text',
		'css' 		=> 'min-width:300px;',
		'std' 		=> ''
	),
	
	array(  
		'name' => __( 'Deal tag slug', 'wpdeals' ),
		'desc' 		=> __( 'Shows in the deals tag URLs. Leave blank to use the default slug.', 'wpdeals' ),
		'id' 		=> 'wpdeals_deal_tags_slug',
		'type' 		=> 'text',
		'css' 		=> 'min-width:300px;',
		'std' 		=> ''
	),
	
	array(  
		'name' => __( 'Deal base page', 'wpdeals' ),
		'desc' 		=> sprintf(__( 'Prepend deals permalinks with store base page (<code>%s</code>)', 'wpdeals' ), $base_slug),
		'id' 		=> 'wpdeals_prepend_store_page_to_deals',
		'std' 		=> 'yes',
		'type' 		=> 'checkbox',
		'checkboxgroup'		=> 'start'
	),
	
	array(  
		'name' => __( 'Deal base category', 'wpdeals' ),
		'desc' 		=> __( 'Prepend deals permalinks with deals category', 'wpdeals' ),
		'id' 		=> 'wpdeals_prepend_category_to_deals',
		'std' 		=> 'no',
		'type' 		=> 'checkbox',
		'checkboxgroup'		=> 'end'
	),
	
	array( 'type' => 'sectionend', 'id' => 'permalink_options' ),
	
	array( 'name' => __( 'Store Pages', 'wpdeals' ), 'type' => 'title', 'desc' => __( 'The following pages need selecting so that WPDeals knows which are which. These pages should have been created upon installation of the plugin.', 'wpdeals' ) ),
		
	array(  
		'name' => __( 'Featured Page', 'wpdeals' ),
		'desc' 		=> '',
		'id' 		=> 'wpdeals_featured_page_id',
		'type' 		=> 'single_select_page',
		'std' 		=> '',
		'class'		=> 'chosen_select_nostd',
		'css' 		=> 'min-width:300px;',
	),
	
	array(  
		'name' => __( 'Checkout Page', 'wpdeals' ),
		'desc' 		=> __( 'Page contents: [wpdeals_checkout]', 'wpdeals' ),
		'id' 		=> 'wpdeals_checkout_page_id',
		'type' 		=> 'single_select_page',
		'std' 		=> '',
		'class'		=> 'chosen_select_nostd',
		'css' 		=> 'min-width:300px;',
	),
	
	array(  
		'name' => __( 'Pay Page', 'wpdeals' ),
		'desc' 		=> __( 'Page contents: [wpdeals_pay] Parent: "Checkout"', 'wpdeals' ),
		'id' 		=> 'wpdeals_pay_page_id',
		'type' 		=> 'single_select_page',
		'std' 		=> '',
		'class'		=> 'chosen_select_nostd',
		'css' 		=> 'min-width:300px;',
	),
	
	array(  
		'name' => __('Thanks Page', 'wpdeals'),
		'desc' 		=> __( 'Page contents: [wpdeals_thankyou] Parent: "Checkout"', 'wpdeals' ),
		'id' 		=> 'wpdeals_thanks_page_id',
		'type' 		=> 'single_select_page',
		'std' 		=> '',
		'class'		=> 'chosen_select_nostd',
		'css' 		=> 'min-width:300px;',
	),
	
	array(  
		'name' => __( 'My Account Page', 'wpdeals' ),
		'desc' 		=> __( 'Page contents: [wpdeals_my_account]', 'wpdeals' ),
		'id' 		=> 'wpdeals_myaccount_page_id',
		'type' 		=> 'single_select_page',
		'std' 		=> '',
		'class'		=> 'chosen_select_nostd',
		'css' 		=> 'min-width:300px;',
	),
    
	array(  
		'name' => __( 'View Order Page', 'wpdeals' ),
		'desc' 		=> __( 'Page contents: [wpdeals_view_order] Parent: "My Account"', 'wpdeals' ),
		'id' 		=> 'wpdeals_view_order_page_id',
		'type' 		=> 'single_select_page',
		'std' 		=> '',
		'class'		=> 'chosen_select_nostd',
		'css' 		=> 'min-width:300px;',
	),
	
	array(  
		'name' => __( 'Change Password Page', 'wpdeals' ),
		'desc' 		=> __( 'Page contents: [wpdeals_change_password] Parent: "My Account"', 'wpdeals' ),
		'id' 		=> 'wpdeals_change_password_page_id',
		'type' 		=> 'single_select_page',
		'std' 		=> '',
		'class'		=> 'chosen_select_nostd',
		'css' 		=> 'min-width:300px;',
	),	
	
	array( 'type' => 'sectionend', 'id' => 'page_options'),

)); // End pages settings


$wpdeals_settings['deals'] = apply_filters('wpdeals_deals_settings', array(
	
	array( 'type' => 'sectionend', 'id' => 'deals_options' ),
	
	array(	'name' => __( 'Pricing Options', 'wpdeals' ), 'type' => 'title','desc' => '', 'id' => 'pricing_options' ),
	
	array(  
		'name' => __( 'Currency Position', 'wpdeals' ),
		'desc' 		=> __( 'This controls the position of the currency symbol.', 'wpdeals' ),
		'tip' 		=> '',
		'id' 		=> 'wpdeals_currency_pos',
		'css' 		=> 'min-width:150px;',
		'std' 		=> 'left',
		'type' 		=> 'select',
		'options' => array( 
			'left' => __( 'Left', 'wpdeals' ),
			'right' => __( 'Right', 'wpdeals' ),
			'left_space' => __( 'Left (with space)', 'wpdeals' ),
			'right_space' => __( 'Right (with space)', 'wpdeals' )
		)
	),
	
	array(  
		'name' => __( 'Thousand separator', 'wpdeals' ),
		'desc' 		=> __( 'This sets the thousand separator of displayed prices.', 'wpdeals' ),
		'tip' 		=> '',
		'id' 		=> 'wpdeals_price_thousand_sep',
		'css' 		=> 'width:30px;',
		'std' 		=> ',',
		'type' 		=> 'text',
	),
	
	array(  
		'name' => __( 'Decimal separator', 'wpdeals' ),
		'desc' 		=> __( 'This sets the decimal separator of displayed prices.', 'wpdeals' ),
		'tip' 		=> '',
		'id' 		=> 'wpdeals_price_decimal_sep',
		'css' 		=> 'width:30px;',
		'std' 		=> '.',
		'type' 		=> 'text',
	),
	
	array(  
		'name' => __( 'Number of decimals', 'wpdeals' ),
		'desc' 		=> __( 'This sets the number of decimal points shown in displayed prices.', 'wpdeals' ),
		'tip' 		=> '',
		'id' 		=> 'wpdeals_price_num_decimals',
		'css' 		=> 'width:30px;',
		'std' 		=> '2',
		'type' 		=> 'text',
	),
	
	array(  
		'name'		=> __( 'Trim zeros', 'wpdeals' ),
		'desc' 		=> __( 'Trim zeros after the decimal point when displaying prices', 'wpdeals' ),
		'id' 		=> 'wpdeals_price_trim_zeros',
		'std' 		=> 'yes',
		'type' 		=> 'checkbox'
	),
	
	array( 'type' => 'sectionend', 'id' => 'pricing_options' ),
	
	array(	'name' => __( 'Image Options', 'wpdeals' ), 'type' => 'title','desc' => sprintf(__('These settings affect the actual dimensions of images in your deals - the display on the front-end will still be affected by CSS styles. After changing these settings you may need to <a href="%s">regenerate your thumbnails</a>.', 'wpdeals'), 'http://wordpress.org/extend/plugins/regenerate-thumbnails/'), 'id' => 'image_options' ),
	
	array(  
		'name' => __( 'Deals Images', 'wpdeals' ),
		'desc' 		=> __('This size is usually used in deals listings', 'wpdeals'),
		'id' 		=> 'wpdeals_deals_image',
		'css' 		=> '',
		'type' 		=> 'image_width',
		'std' 		=> '200'
	),

	array(  
		'name' => __( 'Single Deal Image', 'wpdeals' ),
		'desc' 		=> __('This is the size used by the main image on the deals page.', 'wpdeals'),
		'id' 		=> 'wpdeals_single_image',
		'css' 		=> '',
		'type' 		=> 'image_width',
		'std' 		=> '300'
	),
	
	array(  
		'name' => __( 'Deal Thumbnails', 'wpdeals' ),
		'desc' 		=> __('This size is usually used for the gallery of images on the deals page.', 'wpdeals'),
		'id' 		=> 'wpdeals_thumbnail_image',
		'css' 		=> '',
		'type' 		=> 'image_width',
		'std' 		=> '90'
	),
	
	array( 'type' => 'sectionend', 'id' => 'image_options' ),

)); // End deals settings


$wpdeals_settings['inventory'] = apply_filters('wpdeals_inventory_settings', array(

	array(	'name' => __( 'Inventory Options', 'wpdeals' ), 'type' => 'title','desc' => '', 'id' => 'inventory_options' ),
		
	array(  
		'name' => __( 'Notifications', 'wpdeals' ),
		'desc' 		=> __( 'Enable low stock notifications', 'wpdeals' ),
		'id' 		=> 'wpdeals_notify_low_stock',
		'std' 		=> 'yes',
		'type' 		=> 'checkbox',
		'checkboxgroup' => 'start'
	),
	
	array(  
		'desc' 		=> __( 'Enable out of stock notifications', 'wpdeals' ),
		'id' 		=> 'wpdeals_notify_no_stock',
		'std' 		=> 'yes',
		'type' 		=> 'checkbox',
		'checkboxgroup' => 'end'
	),
	
	array(  
		'name' => __( 'Low stock threshold', 'wpdeals' ),
		'desc' 		=> '',
		'tip' 		=> '',
		'id' 		=> 'wpdeals_notify_low_stock_amount',
		'css' 		=> 'width:30px;',
		'type' 		=> 'text',
		'std' 		=> '2'
	),
	
	array(  
		'name' => __( 'Out of stock threshold', 'wpdeals' ),
		'desc' 		=> '',
		'tip' 		=> '',
		'id' 		=> 'wpdeals_notify_no_stock_amount',
		'css' 		=> 'width:30px;',
		'type' 		=> 'text',
		'std' 		=> '0'
	),
	
	array(  
		'name' => __( 'Out of stock visibility', 'wpdeals' ),
		'desc' 		=> __('Hide out of stock items from the deals', 'wpdeals'),
		'id' 		=> 'wpdeals_hide_out_of_stock_items',
		'std' 		=> 'no',
		'type' 		=> 'checkbox'
	),
	
	array( 'type' => 'sectionend', 'id' => 'inventory_options'),

)); // End inventory settings


$wpdeals_settings['email'] = apply_filters('wpdeals_email_settings', array(
	
	array(	'name' => __( 'Email Recipient Options', 'wpdeals' ), 'type' => 'title', '', 'id' => 'email_recipient_options' ),
	
	array(  
		'name' => __( 'New order notifications', 'wpdeals' ),
		'desc' 		=> __( 'The recipient of new order emails. Defaults to the admin email.', 'wpdeals' ),
		'id' 		=> 'wpdeals_new_order_email_recipient',
		'type' 		=> 'text',
		'css' 		=> 'min-width:300px;',
		'std' 		=> esc_attr(get_option('admin_email'))
	),
	
	array(  
		'name' => __( 'Inventory notifications', 'wpdeals' ),
		'desc' 		=> __( 'The recipient of stock emails. Defaults to the admin email.', 'wpdeals' ),
		'id' 		=> 'wpdeals_stock_email_recipient',
		'type' 		=> 'text',
		'css' 		=> 'min-width:300px;',
		'std' 		=> esc_attr(get_option('admin_email'))
	),
	
	array( 'type' => 'sectionend', 'id' => 'email_recipient_options' ),
	
	array(	'name' => __( 'Email Sender Options', 'wpdeals' ), 'type' => 'title', '', 'id' => 'email_options' ),
	
	array(  
		'name' => __( '"From" name', 'wpdeals' ),
		'desc' 		=> __( 'The sender name for WPDeals emails.', 'wpdeals' ),
		'id' 		=> 'wpdeals_email_from_name',
		'type' 		=> 'text',
		'css' 		=> 'min-width:300px;',
		'std' 		=> esc_attr(get_bloginfo('name'))
	),
	
	array(  
		'name' => __( '"From" email address', 'wpdeals' ),
		'desc' 		=> __( 'The sender email address for WPDeals emails.', 'wpdeals' ),
		'id' 		=> 'wpdeals_email_from_address',
		'type' 		=> 'text',
		'css' 		=> 'min-width:300px;',
		'std' 		=> get_option('admin_email')
	),
	
	array( 'type' => 'sectionend', 'id' => 'email_options' ),
	
	array(	'name' => __( 'Email template', 'wpdeals' ), 'type' => 'title', 'desc' => sprintf(__('This section lets you customise the WPDeals emails. <a href="%s" target="_blank">Click here to preview your email template</a>. For more advanced control copy <code>wpdeals/deals-templates/emails/</code> to <code>yourtheme/wpdeals/emails/</code>.', 'wpdeals'), wp_nonce_url(admin_url('?preview_wpdeals_mail=true'), 'preview-mail')), 'id' => 'email_template_options' ),
	
	array(  
		'name' => __( 'Header image', 'wpdeals' ),
		'desc' 		=> sprintf(__( 'Enter a URL to an image you want to show in the email\'s header. Upload your image using the <a href="%s">media uploader</a>.', 'wpdeals' ), admin_url('media-new.php')),
		'id' 		=> 'wpdeals_email_header_image',
		'type' 		=> 'text',
		'css' 		=> 'min-width:300px;',
		'std' 		=> ''
	),
	
	array(  
		'name' => __( 'Email footer text', 'wpdeals' ),
		'desc' 		=> __( 'The text to appear in the footer of WPDeals emails.', 'wpdeals' ),
		'id' 		=> 'wpdeals_email_footer_text',
		'css' 		=> 'width:100%; height: 75px;',
		'type' 		=> 'textarea',
		'std' 		=> get_bloginfo('name') . ' - ' . __('Powered by WPDeals', 'wpdeals')
	),
	
	array(  
		'name' => __( 'Base colour', 'wpdeals' ),
		'desc' 		=> __( 'The base colour for WPDeals email templates. Default <code>#336666</code>.', 'wpdeals' ),
		'id' 		=> 'wpdeals_email_base_color',
		'type' 		=> 'color',
		'css' 		=> 'width:6em;',
		'std' 		=> '#336666'
	),
	
	array(  
		'name' => __( 'Background colour', 'wpdeals' ),
		'desc' 		=> __( 'The background colour for WPDeals email templates. Default <code>#eeeeee</code>.', 'wpdeals' ),
		'id' 		=> 'wpdeals_email_background_color',
		'type' 		=> 'color',
		'css' 		=> 'width:6em;',
		'std' 		=> '#eeeeee'
	),
	
	array(  
		'name' => __( 'Email body background colour', 'wpdeals' ),
		'desc' 		=> __( 'The main body background colour. Default <code>#fdfdfd</code>.', 'wpdeals' ),
		'id' 		=> 'wpdeals_email_body_background_color',
		'type' 		=> 'color',
		'css' 		=> 'width:6em;',
		'std' 		=> '#fdfdfd'
	),
	
	array(  
		'name' => __( 'Email body text colour', 'wpdeals' ),
		'desc' 		=> __( 'The main body text colour. Default <code>#505050</code>.', 'wpdeals' ),
		'id' 		=> 'wpdeals_email_text_color',
		'type' 		=> 'color',
		'css' 		=> 'width:6em;',
		'std' 		=> '#505050'
	),
	
	array( 'type' => 'sectionend', 'id' => 'email_template_options' ),

)); // End email settings

/**
 * Settings page
 * 
 * Handles the display of the main wpdeals settings page in admin.
 */
if (!function_exists('wpdeals_settings')) {
function wpdeals_settings() {
    global $wpdeals, $wpdeals_settings;
    
    $current_tab = (isset($_GET['tab'])) ? $_GET['tab'] : 'general';
    
    if( isset( $_POST ) && $_POST ) :
    	if ( ! wp_verify_nonce( $_REQUEST['_wpnonce'], 'wpdeals-settings' ) ) die( __( 'Action failed. Please refresh the page and retry.', 'wpdeals' ) ); 
    	
    	switch ( $current_tab ) :
			case "general" :
			case "pages" :
			case "deals" :
			case "inventory" :
			case "email" :
				wpdeals_update_options( $wpdeals_settings[$current_tab] );
			break;
		endswitch;
		
		do_action( 'wpdeals_update_options' );
		do_action( 'wpdeals_update_options_' . $current_tab );
		flush_rewrite_rules( false );
		wp_redirect( add_query_arg( 'subtab', esc_attr(str_replace('#', '', $_POST['subtab'])), add_query_arg( 'saved', 'true', admin_url( 'admin.php?page=wpdeals&tab=' . $current_tab ) )) );
    endif;
    
    if (isset($_GET['saved']) && $_GET['saved']) :
    	echo '<div id="message" class="updated fade"><p><strong>' . __( 'Your settings have been saved.', 'wpdeals' ) . '</strong></p></div>';
        flush_rewrite_rules( false );
    endif;
    
    // Install/page installer
    $install_complete = false;
    $show_page_installer = false;
    
    // Add pages button
    if (isset($_GET['install_wpdeals_pages']) && $_GET['install_wpdeals_pages']) :
	    	
    	wpdeals_create_pages();
    	update_option('skip_install_wpdeals_pages', 1);
    	$install_complete = true;
	
	// Skip button
    elseif (isset($_GET['skip_install_wpdeals_pages']) && $_GET['skip_install_wpdeals_pages']) :
    	
    	update_option('skip_install_wpdeals_pages', 1);
    	$install_complete = true;
    	
    // If we have just activated WPDeals...
    elseif (isset($_GET['installed']) && $_GET['installed']) :
    	
        flush_rewrite_rules( false );

        if (get_option('wpdeals_store_page_id')) :
                $install_complete = true;
        else :
                $show_page_installer = true;
        endif;
		
    // If we havn't just installed, but page installed has not been skipped and store page does not exist...
    elseif (!get_option('skip_install_wpdeals_pages') && !get_option('wpdeals_store_page_id')) :

            $show_page_installer = true;

    endif;
	
    if ($show_page_installer) :

        echo '<div id="message" class="updated fade">
                <p><strong>' . __( 'Welcome to WPDeals!', 'wpdeals' ) . '</strong></p>
                <p>'. __('WPDeals requires several WordPress pages containing shortcodes in order to work correctly; these include Store, Checkout and My Account. To add these pages automatically please click the \'Automatically add pages\' button below, otherwise you can set them up manually. See the \'Pages\' tab in settings for more information.', 'wpdeals') .'</p>
                <p><a href="'.remove_query_arg('installed', add_query_arg('install_wpdeals_pages', 'true')).'" class="button button-primary">'. __('Automatically add pages', 'wpdeals') .'</a> <a href="'.remove_query_arg('installed', add_query_arg('skip_install_wpdeals_pages', 'true')).'" class="button">'. __('Skip setup', 'wpdeals') .'</a></p>
        </div>';

    elseif ($install_complete) :

        echo '<div id="message" class="updated fade">
                <p style="float:right;">' . __( 'Like WPDeals? <a href="http://wordpress.org/extend/plugins/wp-deals/" target="_blank">Support us by leaving a rating!</a>', 'wpdeals' ) . '</p>
                <p><strong>' . __( 'WPDeals has been installed and setup. Enjoy :)', 'wpdeals' ) . '</strong></p>
        </div>';

        flush_rewrite_rules( false );

    endif;
    ?>
	<div class="wrap wpdeals">
		<form method="post" id="mainform" action="">
			<div class="icon32 icon32-wpdeals-settings" id="icon-wpdeals"><br></div><h2 class="nav-tab-wrapper wpdeals-nav-tab-wrapper">
				<?php
					$tabs = array(
						'general' => __( 'General', 'wpdeals' ),
						'pages' => __( 'Pages', 'wpdeals' ),
						'deals' => __( 'Deals', 'wpdeals' ),
						'inventory' => __( 'Inventory', 'wpdeals' ),
						'payment_gateways' => __( 'Payment Gateways', 'wpdeals' ),
						'email' => __( 'Emails', 'wpdeals' ),
					);
					
					$tabs = apply_filters('wpdeals_settings_tabs_array', $tabs);
					
					foreach ($tabs as $name => $label) :
						echo '<a href="' . admin_url( 'admin.php?page=wpdeals&tab=' . $name ) . '" class="nav-tab ';
						if( $current_tab==$name ) echo 'nav-tab-active';
						echo '">' . $label . '</a>';
					endforeach;
					
					do_action( 'wpdeals_settings_tabs' ); 
				?>
			</h2>
			<?php wp_nonce_field( 'wpdeals-settings', '_wpnonce', true, true ); ?>
			<?php
				switch ($current_tab) :
					case "general" :
					case "pages" :
					case "deals" :
					case "inventory" :
					case "email" :
						wpdeals_admin_fields( $wpdeals_settings[$current_tab] );
					break;
            	
					break;
					case "payment_gateways" : 	
					
						$links = array( '<a href="#gateway-order">'.__('Payment Gateways', 'wpdeals').'</a>' );
            			
		            	foreach ($wpdeals->payment_gateways->payment_gateways() as $gateway) :
		            		$title = ( isset( $gateway->method_title ) && $gateway->method_title) ? ucwords($gateway->method_title) : ucwords($gateway->id);
		            		$links[] = '<a href="#gateway-'.$gateway->id.'">'.$title.'</a>';
						endforeach;
						
						echo '<div class="subsubsub_section"><ul class="subsubsub"><li>' . implode(' | </li><li>', $links) . '</li></ul><br class="clear" />';
		            	
		            	// Gateway ordering
		            	echo '<div class="section" id="gateway-order">';
		            	
		            	?>
		            	<h3><?php _e('Payment Gateways', 'wpdeals'); ?></h3>
		            	<p><?php _e('Your activated payment gateways are listed below. Drag and drop rows to re-order them for display on the checkout.', 'wpdeals'); ?></p>
		            	<table class="wd_gateways widefat" cellspacing="0">
		            		<thead>
		            			<tr>
		            				<th width="1%"><?php _e('Default', 'wpdeals'); ?></th>
		            				<th><?php _e('Gateway', 'wpdeals'); ?></th>
		            				<th><?php _e('Status', 'wpdeals'); ?></th>
		            			</tr>
		            		</thead>
		            		<tbody>
				            	<?php
				            	foreach ( $wpdeals->payment_gateways->payment_gateways() as $gateway ) :
				            		
				            		$default_gateway = get_option('wpdeals_default_gateway');
				            		
				            		echo '<tr>
				            			<td width="1%" class="radio">
				            				<input type="radio" name="default_gateway" value="'.$gateway->id.'" '.checked($default_gateway, $gateway->id, false).' />
				            				<input type="hidden" name="gateway_order[]" value="'.$gateway->id.'" />
				            			</td>
				            			<td>
				            				<p><strong>'.$gateway->title.'</strong><br/>
				            				<small>'.__('Gateway ID', 'wpdeals').': '.$gateway->id.'</small></p>
				            			</td>
				            			<td>';
				            		
				            		if ($gateway->enabled == 'yes') 
				            			echo '<img src="'.$wpdeals->plugin_url().'/wpdeals-assets/images/success.gif" alt="yes" />';
									else 
										echo '<img src="'.$wpdeals->plugin_url().'/wpdeals-assets/images/success-off.gif" alt="no" />';	
				            			
				            		echo '</td>
				            		</tr>';
				            		
				            	endforeach; 
				            	?>
		            		</tbody>
		            	</table>
		            	<?php
		            	
		            	echo '</div>';
		            	
		            	// Specific gateway options
		            	foreach ( $wpdeals->payment_gateways->payment_gateways() as $gateway ) :
		            		echo '<div class="section" id="gateway-'.$gateway->id.'">';
		            		$gateway->admin_options();
		            		echo '</div>';
		            	endforeach; 
		            	
		            	echo '</div>';
            	
					break;
					default :
						do_action( 'wpdeals_settings_tabs_' . $current_tab );
					break;
				endswitch;
			?>
	        <p class="submit">
	        	<input name="save" class="button-primary" type="submit" value="<?php _e( 'Save changes', 'wpdeals' ); ?>" />
	        	<input type="hidden" name="subtab" id="last_tab" />
	        </p>
		</form>
		
		<script type="text/javascript">
			jQuery(window).load(function(){
			
				// Subsubsub tabs
				jQuery('ul.subsubsub li a:eq(0)').addClass('current');
				jQuery('.subsubsub_section .section:gt(0)').hide();
				
				jQuery('ul.subsubsub li a').click(function(){
					jQuery('a', jQuery(this).closest('ul.subsubsub')).removeClass('current');
					jQuery(this).addClass('current');
					jQuery('.section', jQuery(this).closest('.subsubsub_section')).hide();
					jQuery( jQuery(this).attr('href') ).show();
					jQuery('#last_tab').val( jQuery(this).attr('href') );
					return false;
				});
				
				<?php if (isset($_GET['subtab']) && $_GET['subtab']) echo 'jQuery("ul.subsubsub li a[href=#'.$_GET['subtab'].']").click();'; ?>
				
				// Countries
				jQuery('select#wpdeals_allowed_countries').change(function(){
					if (jQuery(this).val()=="specific") {
						jQuery(this).parent().parent().next('tr').show();
					} else {
						jQuery(this).parent().parent().next('tr').hide();
					}
				}).change();
				
				// Color picker
				jQuery('.colorpick').each(function(){
					jQuery('.colorpickdiv', jQuery(this).parent()).farbtastic(this);
					jQuery(this).click(function() {
						if ( jQuery(this).val() == "" ) jQuery(this).val('#');
						jQuery('.colorpickdiv', jQuery(this).parent() ).show();
					});	
				});
				jQuery(document).mousedown(function(){
					jQuery('.colorpickdiv').hide();
				});
				
				// Edit prompt
				jQuery(function(){
					var changed = false;
					
					jQuery('input, textarea, select, checkbox').change(function(){
						changed = true;
					});
					
					jQuery('.wpdeals-nav-tab-wrapper a').click(function(){
						if (changed) {
							window.onbeforeunload = function() {
							    return '<?php echo __( 'The changes you made will be lost if you navigate away from this page.', 'wpdeals' ); ?>';
							}
						} else {
							window.onbeforeunload = '';
						}
					});
					
					jQuery('.submit input').click(function(){
						window.onbeforeunload = '';
					});
				});
				
				// Sorting
				jQuery('table.wd_gateways tbody').sortable({
					items:'tr',
					cursor:'move',
					axis:'y',
					handle: 'td',
					scrollSensitivity:40,
					helper:function(e,ui){
						ui.children().each(function(){
							jQuery(this).width(jQuery(this).width());
						});
						ui.css('left', '0');
						return ui;
					},
					start:function(event,ui){
						ui.item.css('background-color','#f6f6f6');
					},
					stop:function(event,ui){
						ui.item.removeAttr('style');
					}
				});
				
				// Chosen selects
				jQuery("select.chosen_select").chosen();
				
				jQuery("select.chosen_select_nostd").chosen({
					allow_single_deselect: 'true'
				});
				
			});
		</script>
	</div>
	<?php
}
}