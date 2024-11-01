<?php
/**
 * Admin functions for the store_deals post type
 *
 * @author 		Tokokoo
 * @category 	Admin
 * @package 	WPDeals
 */
 
/**
 * Columns for Deals page
 **/
add_filter('manage_edit-daily-deals_columns', 'wpdeals_edit_deals_columns');

function wpdeals_edit_deals_columns($columns){
	
	$columns = array();	
	$columns["cb"] = "<input type=\"checkbox\" />";
	$columns["thumb"] = __("Image", 'wpdeals');
	$columns["title"] = __("Name", 'wpdeals');
	$columns["deal-type"] = __("Type", 'wpdeals');
	$columns["deal-categories"] = __("Categories", 'wpdeals');
	$columns["price"] = __("Price", 'wpdeals');
	$columns["featured"] = __("Featured", 'wpdeals');
	$columns["status"] = __("Status", 'wpdeals');
	$columns["date"] = __("Date", 'wpdeals');
        
	return $columns;
}


/**
 * Custom Columns for Deals page
 **/
add_action('manage_daily-deals_posts_custom_column', 'wpdeals_custom_deals_columns', 2);

function wpdeals_custom_deals_columns($column) {
	global $post, $wpdeals;
	$deal = new wpdeals_deals($post->ID);

	switch ($column) {
		case "thumb" :
			if (has_post_thumbnail($post->ID)) :
                            the_post_thumbnail('store_thumbnail');
			endif;
		break;
		case "price":
			echo $deal->get_price_html();	
		break;
		case "deal-categories" :
			if (!$terms = get_the_term_list($post->ID, 'deal-categories', '', ', ','')) echo '<span class="na">&ndash;</span>'; else echo $terms;
		break;
		case "featured" :
			$url = wp_nonce_url( admin_url('admin-ajax.php?action=wpdeals-feature-daily-deals&deal_id=' . $post->ID), 'wpdeals-feature-daily-deals' );
			echo '<a href="'.$url.'" title="'.__('Change', 'wpdeals') .'">';
			if ($deal->is_featured()) echo '<a href="'.$url.'"><img src="'.$wpdeals->plugin_url().'/wpdeals-assets/images/success.gif" alt="yes" />';
			else echo '<img src="'.$wpdeals->plugin_url().'/wpdeals-assets/images/success-off.gif" alt="no" />';
			echo '</a>';
		break;
		case "status" :
			if ( !$deal->is_expired() ) :
				echo '<img src="'.$wpdeals->plugin_url().'/wpdeals-assets/images/success.gif" alt="yes" /> ';
			else :
				echo '<img src="'.$wpdeals->plugin_url().'/wpdeals-assets/images/success-off.gif" alt="no" /> ';
			endif;
		break;
		case "deal-type" :
                        $simple_type = '';
                        if( $deal->is_type('simple') )
                                $simple_type    = ($deal->is_downloadable())? __(' - Download', 'wpdeals'):__(' - Voucher', 'wpdeals');
			echo ucwords($deal->deal_type) . $simple_type ;
		break;
	}
}


/**
 * Filter the products in admin based on options
 *
 * @access public
 * @param mixed $query
 * @return void
 */
function wpdeals_admin_product_default_query( $query ) {
	global $typenow, $wp_query;

    if ( $typenow == 'daily-deals' ) {

        // Default orderby
        if ( !isset( $_GET['date'] ) && !isset( $_GET['orderby'] ) ) {
                $query->query_vars['orderby']   = 'date';
                $query->query_vars['order']     = 'DESC';
        }

    }

}

add_filter( 'parse_query', 'wpdeals_admin_product_default_query' );

/**
 * Make deals columns sortable
 * https://gist.github.com/906872
 **/
add_filter("manage_edit-daily-deals_sortable_columns", 'wpdeals_custom_deals_sort');

function wpdeals_custom_deals_sort($columns) {
	$custom = array(
		'status' 	=> '_is_expired',
		'price'		=> '_discount_price',
		'featured'	=> 'featured',
		'date'          => 'date'
	);
	return wp_parse_args($custom, $columns);
}


/**
 * Deal column orderby
 * http://scribu.net/wordpress/custom-sortable-columns.html#comment-4732
 **/
add_filter( 'request', 'wpdeals_custom_deals_orderby' );

function wpdeals_custom_deals_orderby( $vars ) {
	if (isset( $vars['orderby'] )) :
		if ( 'status' == $vars['orderby'] ) :
			$vars = array_merge( $vars, array(
				'meta_key' 	=> '_is_expired',
				'orderby' 	=> 'meta_value'
			) );
		endif;
		if ( 'price' == $vars['orderby'] ) :
			$vars = array_merge( $vars, array(
				'meta_key' 	=> '_discount_price',
				'orderby' 	=> 'meta_value_num'
			) );
		endif;
		if ( 'featured' == $vars['orderby'] ) :
			$vars = array_merge( $vars, array(
				'meta_key' 	=> 'featured',
				'orderby' 	=> 'meta_value'
			) );
		endif;
	endif;
	
	return $vars;
}

/**
 * Filter deals by category, uses slugs for option values. Code adapted by Andrew Benbow - chromeorange.co.uk
 **/
add_action('restrict_manage_posts','wpdeals_deals_by_category');

function wpdeals_deals_by_category() {
	global $typenow, $wp_query;
    if ($typenow=='daily-deals') :
    	wpdeals_deals_dropdown_categories();
    endif;
}


/**
 * Filter deals by type
 **/
add_action('restrict_manage_posts', 'wpdeals_deals_by_type');

function wpdeals_deals_by_type() {
    global $typenow, $wp_query;
    if ($typenow=='daily-deals') :
    	
    	// Types
		$terms = get_terms('deal-type');
		$output = "<select name='deal-type' id='dropdown_deals_type'>";
		$output .= '<option value="">'.__('Show all deals types', 'wpdeals').'</option>';
		foreach($terms as $term) :
			$output .="<option value='$term->slug' ";
			if ( isset( $wp_query->query['deal-type'] ) ) $output .=selected($term->slug, $wp_query->query['deal-type'], false);
			$output .=">".ucfirst($term->name)." ($term->count)</option>";
		endforeach;
		$output .="</select>";
		
		echo $output;
    endif;
}


/**
 * Add functionality to the image uploader on deals pages to exlcude an image
 **/
add_filter('attachment_fields_to_edit', 'wpdeals_exclude_image_from_deals_page_field', 1, 2);
add_filter('attachment_fields_to_save', 'wpdeals_exclude_image_from_deals_page_field_save', 1, 2);

function wpdeals_exclude_image_from_deals_page_field( $fields, $object ) {
	
	if (!$object->post_parent) return $fields;
	
	$parent = get_post( $object->post_parent );
	
	if ($parent->post_type!=='daily-deals') return $fields;
	
	$exclude_image = (int) get_post_meta($object->ID, '_wpdeals_exclude_image', true);
	
	$label = __('Exclude image', 'wpdeals');
	
	$html = '<input type="checkbox" '.checked($exclude_image, 1, false).' name="attachments['.$object->ID.'][wpdeals_exclude_image]" id="attachments['.$object->ID.'][wpdeals_exclude_image" />';
	
	$fields['wpdeals_exclude_image'] = array(
			'label' => $label,
			'input' => 'html',
			'html' =>  $html,
			'value' => '',
			'helps' => __('Enabling this option will hide it from the deals page image gallery.', 'wpdeals')
	);
	
	return $fields;
}

function wpdeals_exclude_image_from_deals_page_field_save( $post, $attachment ) {

	if (isset($_REQUEST['attachments'][$post['ID']]['wpdeals_exclude_image'])) :
		delete_post_meta( (int) $post['ID'], '_wpdeals_exclude_image' );
		update_post_meta( (int) $post['ID'], '_wpdeals_exclude_image', 1);
	else :
		delete_post_meta( (int) $post['ID'], '_wpdeals_exclude_image' );
		update_post_meta( (int) $post['ID'], '_wpdeals_exclude_image', 0);
	endif;
		
	return $post;
				
}


/**
 * Search by SKU or ID for deals. Adapted from code by BenIrvin (Admin Search by ID)
 */
if (is_admin()) :
	add_action( 'parse_request', 'wpdeals_admin_deals_search' );
	add_filter( 'get_search_query', 'wpdeals_admin_deals_search_label' );
endif;

function wpdeals_admin_deals_search( $wp ) {
    global $pagenow, $wpdb;
	
	if( 'edit.php' != $pagenow ) return;
	if( !isset( $wp->query_vars['s'] ) ) return;
	if ($wp->query_vars['post_type']!='daily-deals') return;

	if( '#' == substr( $wp->query_vars['s'], 0, 1 ) ) :

		$id = absint( substr( $wp->query_vars['s'], 1 ) );
			
		if( !$id ) return; 
		
		unset( $wp->query_vars['s'] );
		$wp->query_vars['p'] = $id;
				
	endif;
}

function wpdeals_admin_deals_search_label($query) {
	global $pagenow, $typenow, $wp;

    if ( 'edit.php' != $pagenow ) return $query;
    if ( $typenow!='daily-deals' ) return $query;
	
	$s = get_query_var( 's' );
	if ($s) return $query;
		
	$p = get_query_var( 'p' );
	if ($p) {
		$post_type = get_post_type_object($wp->query_vars['post_type']);
		return sprintf(__("[%s with ID of %d]", 'wpdeals'), $post_type->labels->singular_name, $p);
	}
	
	return $query;
}