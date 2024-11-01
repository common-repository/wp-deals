<?php
/**
 * Widgets init
 * 
 * Init the widgets.
 *
 * @package		WPDeals
 * @category	Widgets
 * @author		Tokokoo
 */
 
include_once('widget-featured-deals.php');
include_once('widget-deals-categories.php');
include_once('widget-deals-search.php');
include_once('widget-deals-tags-cloud.php');
include_once('widget-recent-deals.php');
include_once('widget-login.php');

function wpdeals_register_widgets() {
	register_widget('WPDeals_Widget_Recent_Deals');
	register_widget('WPDeals_Widget_Featured_Deals');
	register_widget('WPDeals_Widget_Deal_Categories');
	register_widget('WPDeals_Widget_Deal_Tag_Cloud');
	register_widget('WPDeals_Widget_Deal_Search');
	register_widget('WPDeals_Widget_Login');
}
add_action('widgets_init', 'wpdeals_register_widgets');