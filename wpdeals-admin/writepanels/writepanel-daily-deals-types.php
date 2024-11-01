<?php
/**
 * Deal Type
 * 
 * Function for displaying the deals type meta (specific) meta boxes
 *
 * @author 		Tokokoo
 * @category 	Admin Write Panels
 * @package 	WPDeals
 */

include_once('writepanel-daily-deals-type-downloadable.php');
include_once('writepanel-daily-deals-type-variable.php');

/**
 * Deal type meta box
 * 
 * Display the deals type meta box which contains a hook for deals types to hook into and show their options
 *
 * @since 		1.0
 */
function wpdeals_deals_type_options_box() {

	global $post;
	?>
	<div id="simple_deals_options" class="panel wpdeals_options_panel">
		<?php
			_e('Simple deals have no specific options.', 'wpdeals');
		?>
	</div>
	<?php 
	do_action('wpdeals_deals_type_options_box');
}

/**
 * Virtual Deal Type - Deal Options
 * 
 * Deal Options for the virtual deals type
 */
function virtual_deals_type_options() {
	?>
	<div id="virtual_deals_options">
		<?php
			_e('Virtual deals have no specific options.', 'wpdeals');
		?>
	</div>
	<?php
}
add_action('wpdeals_deals_type_options_box', 'virtual_deals_type_options');

/**
 * Grouped Deal Type - Deal Options
 * 
 * Deal Options for the grouped deals type
 *
 * @since 		1.0
 */
function grouped_deals_type_options() {
	?>
	<div id="grouped_deals_options">
		<?php
			_e('Grouped deals have no specific options &mdash; you can add simple deals to this grouped deals by editing them and setting their <code>parent deals</code> option.', 'wpdeals');
		?>
	</div>
	<?php
}
add_action('wpdeals_deals_type_options_box', 'grouped_deals_type_options');


/**
 * Deal Type selectors
 * 
 * Adds a deals type to the deals type selector in the deals options meta box
 */
add_filter('deal_type_selector', 'virtual_deals_type_selector', 1, 2);
add_filter('deal_type_selector', 'grouped_deals_type_selector', 1, 2);

function virtual_deals_type_selector( $types, $deal_type ) {
	$types['virtual'] = __('Virtual', 'wpdeals');
	return $types;
}

function grouped_deals_type_selector( $types, $deal_type ) {
	$types['grouped'] = __('Grouped', 'wpdeals');
	return $types;
}
