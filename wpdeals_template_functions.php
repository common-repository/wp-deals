<?php
/**
 * WPDeals Template Functions
 *
 * Functions used in the template files to output content - in most cases hooked in via the template actions.
 *
 * @package		WPDeals
 * @category	Core
 * @author		Tokokoo
 */

/**
 * Content Wrappers
 **/
if (!function_exists('wpdeals_output_content_wrapper')) {
	function wpdeals_output_content_wrapper() {
		if ( get_option('template') === 'twentyeleven' ) :
			echo '<div id="primary"><div id="content" role="main">';
		elseif ( get_option('template') === 'twentytwelve' ) :
			echo '<div id="primary" class="site-content"><div id="content" role="main">';
		else :
			echo '<div id="container"><div id="content" role="main">';
		endif;
	}
}
if (!function_exists('wpdeals_output_content_wrapper_end')) {
	function wpdeals_output_content_wrapper_end() {
		if ( get_option('template') === 'twentyeleven' ) :
			echo  '</div></div>';
		else :
			echo '</div></div>';
		endif;
	}
}

/**
 * Compatibility (for globals)
 *
 * Genisis shows deals via an action, so ensure the $_deals variable is set
 **/
function wpdeals_before_single_deals( $post, $deal ) {
	global $_deals;
	if (is_null($_deals)) $_deals = $deal;
}

/**
 * Sidebar
 **/
if (!function_exists('wpdeals_get_sidebar')) {
	function wpdeals_get_sidebar() {
		get_sidebar('deals');
	}
}

/**
 * Deals Loop
 **/
if (!function_exists('wpdeals_template_loop_add_to_cart')) {
	function wpdeals_template_loop_add_to_cart( $post, $_deals ) {

		// No price set - so no button
		if( $_deals->get_price() === '' && $_deals->deal_type!=='external') return;

		if (!$_deals->is_in_stock()) :
			echo '<a href="'.get_permalink($post->ID).'" class="button">'. apply_filters('out_of_stock_add_to_cart_text', __('See this', 'wpdeals')).'</a>';
			return;
		endif;

		switch ($_deals->deal_type) :
			case "variable" :
				$link 	= get_permalink($post->ID);
				$label 	= apply_filters('variable_add_to_cart_text', __('Select deals', 'wpdeals'));
			break;
			case "external" :
				$link 	= get_permalink($post->ID);
				$label 	= apply_filters('external_add_to_cart_text', __('See this', 'wpdeals'));
			break;
			default :
				$link 	= esc_url( $_deals->add_to_cart_url() );
				$label 	= apply_filters('add_to_cart_text', __('Buy now', 'wpdeals'));
			break;
		endswitch;

		echo sprintf('<a href="%s" data-daily-deals_id="%s" class="button add_to_cart_button deal_type_%s">%s</a>', $link, $_deals->id, $_deals->deal_type, $label);
	}
}
if (!function_exists('wpdeals_template_loop_deals_thumbnail')) {
	function wpdeals_template_loop_deals_thumbnail( $post, $_deals ) {
		echo wpdeals_get_deals_thumbnail();
	}
}
if (!function_exists('wpdeals_template_loop_price')) {
	function wpdeals_template_loop_price( $post, $_deals ) {
		$price_html = $_deals->get_price_html();
		if (!$price_html) return;
		?><span class="price"><?php echo $price_html; ?></span><?php
	}
}

if (!function_exists('wpdeals_template_loop_countdown')) {
	function wpdeals_template_loop_countdown( $post, $_deals ) {
            
                $post_id    = $post->ID;
                $date_end   = date('Y-m-d H:i:s', get_post_meta($post_id, '_end_time', true));
                $deals_end  = explode('-', str_replace(array('-', ' ', ':'), '-', $date_end)); // e.g. "2012-07-12 22:30"
                
                if( !$_deals->is_in_stock() && !$_deals->is_expired() ):
                        echo '<div class="deals-countdown deal-expired">' . __('Sold Out!', 'wpdeals') .'</div>';                    
                elseif( $_deals->is_expired() ):
                        echo '<div class="deals-countdown deal-expired">' . __('Deals Expired', 'wpdeals') .'</div>';
                else:
                
                ?>
                        <div id="deals-<?php echo $post_id; ?>" class="deals-countdown"></div>
                        <script type="text/javascript">
                                // jQuery Countdown
                                jQuery(document).ready(function() { 
                                    var expiredDate = new Date(
                                        <?php echo $deals_end[0]; // year  ?>,
                                        <?php echo ($deals_end[1] - 1); // month - 1 ?>,
                                        <?php echo $deals_end[2]; // date  ?>,  
                                        <?php echo $deals_end[3]; // hour  ?>,
                                        <?php echo $deals_end[4]; // minute  ?>);
                                    jQuery('#deals-<?php echo $post_id; ?>').countdown({
                                        until: expiredDate, 
                                        onExpiry: dealExpired<?php echo $post_id; ?>,
                                        expiryText:'<div class="deal-expired"><?php _e('Deals Expired', 'wpdeals'); ?></div>'
                                    }); 

                                });

                                // expired deals proccess
                                function dealExpired<?php echo $post_id; ?>() {

                                    var data = {
                                            action: 		'wpdeals_expired_deals',
                                            post_id:		'<?php echo $post_id; ?>',
                                            security: 		'<?php echo wp_create_nonce("expired-deals"); ?>'
                                    };
                                    jQuery.post( '<?php echo admin_url('admin-ajax.php'); ?>', data, function(response) {

                                    });

                                    return false;
                                }
                        </script>
                <?php
                endif;
	}
}

if (!function_exists('wpdeals_template_single_countdown')) {
	function wpdeals_template_single_countdown( $post, $_deals ) {
            
                $post_id    = $_deals->id;
                $date_end   = date('Y-m-d H:i:s', get_post_meta($post_id, '_end_time', true));
                $deals_end  = explode('-', str_replace(array('-', ' ', ':'), '-', $date_end)); // e.g. "2012-07-12 22:30"
                                
                if( !$_deals->is_in_stock() && !$_deals->is_expired() ):
                        echo '<div class="deals-countdown deal-expired">' . __('Sold Out!', 'wpdeals') .'</div>';                    
                elseif( $_deals->is_expired() ):
                        echo '<div class="deals-countdown deal-expired">' . __('Deals Expired', 'wpdeals') .'</div>';
                else:
                
                ?>
                        <div id="deals-<?php echo $post_id; ?>" class="deals-countdown"></div>
                        <script type="text/javascript">
                                // jQuery Countdown
                                jQuery(document).ready(function() { 
                                    var expiredDate = new Date(
                                        <?php echo $deals_end[0]; // year  ?>,
                                        <?php echo ($deals_end[1] - 1); // month - 1 ?>,
                                        <?php echo $deals_end[2]; // date  ?>,  
                                        <?php echo $deals_end[3]; // hour  ?>,
                                        <?php echo $deals_end[4]; // minute  ?>);
                                    jQuery('#deals-<?php echo $post_id; ?>').countdown({
                                        until: expiredDate, 
                                        onExpiry: dealExpired<?php echo $post_id; ?>,
                                        expiryText:'<div class="deal-expired"><?php _e('Deals Expired', 'wpdeals'); ?></div>'
                                    }); 

                                });

                                // expired deals proccess
                                function dealExpired<?php echo $post_id; ?>() {

                                    var data = {
                                            action: 		'wpdeals_expired_deals',
                                            post_id:		'<?php echo $post_id; ?>',
                                            security: 		'<?php echo wp_create_nonce("expired-deals"); ?>'
                                    };
                                    jQuery.post( '<?php echo admin_url('admin-ajax.php'); ?>', data, function(response) {

                                    });

                                    return false;
                                }
                        </script>
                <?php
                endif;
	}
}



/**
 * Check deals visibility in loop
 **/
if (!function_exists('wpdeals_check_deals_visibility')) {
	function wpdeals_check_deals_visibility( $post, $_deals ) {
	
		if (!$_deals->is_visible( true ) && $post->post_parent > 0) : wp_safe_redirect(get_permalink($post->post_parent)); exit; endif;
		if (!$_deals->is_visible( true )) : wp_safe_redirect(home_url()); exit; endif;
		
	}
}

/**
 * Before Single Deals Summary Div
 **/
if (!function_exists('wpdeals_show_deals_images')) {
	function wpdeals_show_deals_images() {

		global $post, $wpdeals;

		echo '<div class="images">';

		if (has_post_thumbnail()) :

			$thumb_id = get_post_thumbnail_id();
			$large_thumbnail_size = apply_filters('single_deals_large_thumbnail_size', 'store_single');

			echo '<a itemprop="image" href="'.wp_get_attachment_url($thumb_id).'" class="zoom" rel="thumbnails">' . get_the_post_thumbnail($post->ID, $large_thumbnail_size) . '</a>';

		else :
			echo '<img src="'.$wpdeals->plugin_url().'/wpdeals-assets/images/placeholder.png" alt="Placeholder" />';
		endif;

		do_action('wpdeals_deals_thumbnails');

		echo '</div>';

	}
}
if (!function_exists('wpdeals_show_deals_thumbnails')) {
	function wpdeals_show_deals_thumbnails() {

		global $post;

		echo '<div class="thumbnails">';

		$thumb_id = get_post_thumbnail_id();
		$small_thumbnail_size = apply_filters('single_deals_small_thumbnail_size', 'store_thumbnail');
		$args = array(
			'post_type' 	=> 'attachment',
			'numberposts' 	=> -1,
			'post_status' 	=> null,
			'post_parent' 	=> $post->ID,
			'post__not_in'	=> array($thumb_id),
			'post_mime_type' => 'image',
			'meta_query' 	=> array(
				array(
					'key' 		=> '_wpdeals_exclude_image',
					'value'		=> '1',
					'compare' 	=> '!='
				)
			)
		);
		$attachments = get_posts($args);
		if ($attachments) :
			$loop = 0;
			$columns = apply_filters('wpdeals_deals_thumbnails_columns', 3);
			foreach ( $attachments as $attachment ) :

				$loop++;

				$_post = & get_post( $attachment->ID );
				$url = wp_get_attachment_url($_post->ID);
				$post_title = esc_attr($_post->post_title);
				$image = wp_get_attachment_image($attachment->ID, $small_thumbnail_size);

				echo '<a href="'.$url.'" title="'.$post_title.'" rel="thumbnails" class="zoom ';
				if ($loop==1 || ($loop-1)%$columns==0) echo 'first';
				if ($loop%$columns==0) echo 'last';
				echo '">'.$image.'</a>';

			endforeach;
		endif;
		wp_reset_query();

		echo '</div>';

	}
}

/**
 * Deal summary box
 **/
if (!function_exists('wpdeals_template_single_price')) {
	function wpdeals_template_single_price( $post, $_deals ) {
            ?>
                <div itemprop="price" class="price">
                    <span class="deals-price"><?php _e('Value', 'wpdeals'); ?><br><span class="price-num"><?php echo wpdeals_price($_deals->get_price()); ?></span></span>
                    <span class="deals-price"><?php _e('Discount', 'wpdeals'); ?><br><span class="price-num"><?php echo $_deals->get_discount(); ?></span></span>
                    <span class="deals-price"><?php _e('You Save', 'wpdeals'); ?><br><span class="price-num"><?php echo wpdeals_price($_deals->get_save_price()); ?></span></span>
                </div>
            <?php
	}
}

/**
 * Deal just bought box
 **/
if (!function_exists('wpdeals_template_single_bought')) {
	function wpdeals_template_single_bought( $post, $_deals ) {
            if(!$_deals->is_type('simple')) return;
            ?>
                <div class="has-bought">
                    <span><?php if($_deals->get_purchase() > 1) printf( __('Just Bought %s', 'wpdeals'), $_deals->get_purchase()); else _e('Be the first to buy!', 'wpdeals'); ?></span>
                </div>
            <?php
	}
}

/**
 * Sharing box
 */
if (!function_exists('wpdeals_template_single_sharing')) {
	function wpdeals_template_single_sharing( $post, $_deals ) {
            
                if(get_option('wpdeals_show_share') == 'yes'){

                        echo '<div class="social">
                                <iframe src="https://www.facebook.com/plugins/like.php?href='.urlencode(get_permalink($post->ID)).'&amp;layout=button_count&amp;show_faces=false&amp;width=100&amp;action=like&amp;colorscheme=light&amp;height=21" scrolling="no" frameborder="0" style="border:none; overflow:hidden; width:100px; height:21px;" allowTransparency="true"></iframe>
                                <span class="st_twitter"></span><span class="st_email"></span><span class="st_sharethis"></span><span class="st_plusone_button"></span>
                        </div>';

                        add_action( 'wp_footer', 'wpdeals_sharethis_script' );
                        
                }

	}
}


/**
 * Sharethis
 *
 * Adds social sharing code to the footer
 **/
if (!function_exists('wpdeals_sharethis_script')) {
	function wpdeals_sharethis_script() {
		if (is_single()) :

			if (is_ssl()) :
				$sharethis = 'https://ws.sharethis.com/button/buttons.js';
			else :
				$sharethis = 'http://w.sharethis.com/button/buttons.js';
			endif;

			echo '<script type="text/javascript">var switchTo5x=true;</script><script type="text/javascript" src="'.$sharethis.'"></script><script type="text/javascript">stLight.options({publisher:"'.get_option('wpdeals_sharethis', '').'"});</script>';

		endif;
	}
}


/**
 * Deal Buy now buttons
 **/
if (!function_exists('wpdeals_template_single_add_to_cart')) {
	function wpdeals_template_single_add_to_cart( $post, $_deals ) {
		do_action( 'wpdeals_' . $_deals->deal_type . '_add_to_cart', $post, $_deals );
	}
}
if (!function_exists('wpdeals_simple_add_to_cart')) {
	function wpdeals_simple_add_to_cart( $post, $_deals ) {

		$availability = $_deals->get_availability();

		// No price set - so no button
		if( $_deals->get_price() === '') return;

		// Don't show cart if out of stock
		if (!$_deals->is_in_stock() || $_deals->is_expired()) :
			echo '<link itemprop="availability" href="http://schema.org/OutOfStock">';
			return;
		endif;

		echo '<link itemprop="availability" href="http://schema.org/InStock">';

		do_action('wpdeals_before_add_to_cart_form');

		?>
		<form action="<?php echo esc_url( $_deals->add_to_cart_url() ); ?>" class="cart" method="post" enctype='multipart/form-data'>

		 	<?php do_action('wpdeals_before_add_to_cart_button'); ?>
                    
                        <?php $button_text  = ( !$_deals->is_on_sale() )? sprintf( __('Buy now %s', 'wpdeals'), wpdeals_price($_deals->get_sale())):__('Take It Free!', 'wpdeals'); ?>
                        <button type="submit" class="button-buy-now alt"><?php echo $button_text; ?></button>

		 	<?php do_action('wpdeals_after_add_to_cart_button'); ?>

		</form>
		<?php

		do_action('wpdeals_after_add_to_cart_form');

	}
}
if (!function_exists('wpdeals_variable_add_to_cart')) {
	function wpdeals_variable_add_to_cart( $post, $_deals ) {
		global $wpdeals;

		$attributes = $_deals->get_available_attribute_variations();
		$default_attributes = (array) maybe_unserialize(get_post_meta( $post->ID, '_default_attributes', true ));
		$selected_attributes = apply_filters( 'wpdeals_deals_default_attributes', $default_attributes );

		// Put available variations into an array and put in a Javascript variable (JSON encoded)
        $available_variations = array();

        foreach($_deals->get_children() as $child_id) {

            $variation = $_deals->get_child( $child_id );

            if($variation instanceof wpdeals_deals_variation) {

            	if (get_post_status( $variation->get_variation_id() ) != 'publish') continue; // Disabled

                $variation_attributes = $variation->get_variation_attributes();
                $availability = $variation->get_availability();
                $availability_html = (!empty($availability['availability'])) ? apply_filters( 'wpdeals_stock_html', '<p class="stock '.$availability['class'].'">'. $availability['availability'].'</p>', $availability['availability'] ) : '';

                if (has_post_thumbnail($variation->get_variation_id())) {
                    $attachment_id = get_post_thumbnail_id( $variation->get_variation_id() );
                    $large_thumbnail_size = apply_filters('single_deals_large_thumbnail_size', 'store_single');
                    $image = current(wp_get_attachment_image_src( $attachment_id, $large_thumbnail_size ));
                    $image_link = current(wp_get_attachment_image_src( $attachment_id, 'full' ));
                } else {
                    $image = '';
                    $image_link = '';
                }

                $available_variations[] = array(
                    'variation_id' => $variation->get_variation_id(),
                    'attributes' => $variation_attributes,
                    'image_src' => $image,
                    'image_link' => $image_link,
                    'price_html' => '<span class="price">'.$variation->get_price_html().'</span>',
                    'availability_html' => $availability_html,
                );
            }
        }
		?>
        <script type="text/javascript">
            var deal_variations = <?php echo json_encode($available_variations) ?>;
        </script>

        <?php do_action('wpdeals_before_add_to_cart_form'); ?>

		<form action="<?php echo esc_url( $_deals->add_to_cart_url() ); ?>" class="variations_form cart" method="post" enctype='multipart/form-data'>
			<table class="variations" cellspacing="0">
				<tbody>
				<?php foreach ($attributes as $name => $options) : ?>
					<tr>
						<td><label for="<?php echo sanitize_title($name); ?>"><?php echo $wpdeals->attribute_label($name); ?></label></td>
						<td><select id="<?php echo esc_attr( sanitize_title($name) ); ?>" name="attribute_<?php echo sanitize_title($name); ?>">
							<option value=""><?php echo __('Choose an option', 'wpdeals') ?>&hellip;</option>
							<?php if(is_array($options)) : ?>
								<?php
									$selected_value = (isset($selected_attributes[sanitize_title($name)])) ? $selected_attributes[sanitize_title($name)] : '';
									// Get terms if this is a taxonomy - ordered
									if (taxonomy_exists(sanitize_title($name))) :
										$args = array('menu_order' => 'ASC');
										$terms = get_terms( sanitize_title($name), $args );

										foreach ($terms as $term) :
											if (!in_array($term->slug, $options)) continue;
											echo '<option value="'.$term->slug.'" '.selected($selected_value, $term->slug).'>'.$term->name.'</option>';
										endforeach;
									else :
										foreach ($options as $option) :
											echo '<option value="'.$option.'" '.selected($selected_value, $option).'>'.$option.'</option>';
										endforeach;
									endif;
								?>
							<?php endif;?>
						</td>
					</tr>
                <?php endforeach;?>
				</tbody>
			</table>

			<?php do_action('wpdeals_before_add_to_cart_button'); ?>

			<div class="single_variation_wrap" style="display:none;">
				<div class="single_variation"></div>
				<div class="variations_button">
					<input type="hidden" name="variation_id" value="" />
					<button type="submit" class="button alt"><?php _e('Buy now', 'wpdeals'); ?></button>
				</div>
			</div>
			<div><input type="hidden" name="deal_id" value="<?php echo esc_attr( $post->ID ); ?>" /></div>

			<?php do_action('wpdeals_after_add_to_cart_button'); ?>

		</form>

		<?php do_action('wpdeals_after_add_to_cart_form'); ?>
		<?php
	}
}
if (!function_exists('wpdeals_external_add_to_cart')) {
	function wpdeals_external_add_to_cart( $post, $_deals ) {

		$deal_url = get_post_meta( $_deals->id, 'deal_url', true );
		if (!$deal_url) return;
                if ($_deals->is_expired()) return;

		?>

		<?php do_action('wpdeals_before_add_to_cart_button'); ?>

                <?php $button_text  = ( !$_deals->is_on_sale() )? sprintf( __('Buy now %s', 'wpdeals'), wpdeals_price($_deals->get_sale())):__('Take It Free!', 'wpdeals'); ?>
		<p class="cart"><a href="<?php echo $deal_url; ?>" rel="nofollow" class="button-buy-now alt"><?php echo $button_text; ?></a></p>

		<?php do_action('wpdeals_after_add_to_cart_button'); ?>

		<?php
	}
}
	

/**
 * Deal Add to Cart forms
 **/
if (!function_exists('wpdeals_add_to_cart_form_nonce')) {
	function wpdeals_add_to_cart_form_nonce() {
		global $wpdeals;
		$wpdeals->nonce_field('add_to_cart');
	}
}

/**
 * Pagination
 **/
if (!function_exists('wpdeals_pagination')) {
	function wpdeals_pagination() {

		global $wp_query;

		if (  $wp_query->max_num_pages > 1 ) :
			?>
			<div class="navigation">
				<div class="nav-next"><?php next_posts_link( __( 'Next <span class="meta-nav">&rarr;</span>', 'wpdeals' ) ); ?></div>
				<div class="nav-previous"><?php previous_posts_link( __( '<span class="meta-nav">&larr;</span> Previous', 'wpdeals' ) ); ?></div>
			</div>
			<?php
		endif;

	}
}


/**
 * Sorting deals
 **/
if (!function_exists('wpdeals_catalog_ordering')) {
	function wpdeals_catalog_ordering() {
		if (!isset($_SESSION['orderby'])) $_SESSION['orderby'] = apply_filters('wpdeals_default_catalog_orderby', 'title');
		?>
		<form class="wpdeals_ordering" method="post">
			<select name="catalog_orderby" class="orderby">
				<?php
					$catalog_orderby = apply_filters('wpdeals_catalog_orderby', array(
						'all'       => __('All deals', 'wpdeals'),
						'recent'    => __('Recent deals', 'wpdeals'),
						'past'      => __('Past deals', 'wpdeals')
					));

					foreach ($catalog_orderby as $id => $name) :

						echo '<option value="'.$id.'" '.selected( $_SESSION['orderby'], $id, false ).'>'.$name.'</option>';

					endforeach;
				?>
			</select>
		</form>
		<?php

	}
}


/**
 * Deal single page
 **/
if (!function_exists('wpdeals_deals_description')) {
	function wpdeals_deals_description() {
		echo '<div class="deals-description" itemprop="description">';
		$heading = apply_filters('wpdeals_deals_description_heading', __('Description', 'wpdeals'));
		if ($heading) echo '<h2>' . $heading . '</h2>';
		the_content();
		echo '</div>';
	}
}
if (!function_exists('wpdeals_deals_comments')) {
	function wpdeals_deals_comments() {
		echo '<div class="deals-comments">';
		comments_template();
		echo '</div>';
	}
}




/**
 * bottom of content
 */
if (!function_exists('deals_single_meta_content')) {
        function wpdeals_single_meta_content($post, $_deals){
        ?>                
                <!-- display footer deal -->
                <div id="deal-meta-footer">
                    <div class="deal-categories">
                        <?php echo get_the_term_list($_deals->id, 'deal-categories', __('Categories: ', 'wpdeals'), ', ', ''); ?> 
                    </div>
                    <div class="deal-tags">
                        <?php echo get_the_term_list($_deals->id, 'deal-tags', __('Tags: ', 'wpdeals)'), ', ', ''); ?> 
                    </div>
                </div>
        <?php
        }
}


/**
 * WPDeals Deal Thumbnail
 **/
if (!function_exists('wpdeals_get_deals_thumbnail')) {
	function wpdeals_get_deals_thumbnail( $size = 'store_catalog', $placeholder_width = 0, $placeholder_height = 0 ) {

		global $post, $wpdeals;

		if (!$placeholder_width) $placeholder_width = $wpdeals->get_image_size('store_catalog_image_width');
		if (!$placeholder_height) $placeholder_height = $wpdeals->get_image_size('store_catalog_image_height');

		if ( has_post_thumbnail() ) return get_the_post_thumbnail($post->ID, $size); else return '<img src="'.$wpdeals->plugin_url(). '/wpdeals-assets/images/placeholder.png" alt="Placeholder" width="'.$placeholder_width.'" height="'.$placeholder_height.'" />';

	}
}

/**
 * WPDeals Related Deals
 **/
if (!function_exists('wpdeals_output_related_deals')) {
	function wpdeals_output_related_deals() {
		// 3 Related Deals in 3 columns                
		wpdeals_related_deals( 3, 3 );
	}
}

if (!function_exists('wpdeals_related_deals')) {
	function wpdeals_related_deals( $posts_per_page = 3, $post_columns = 3, $orderby = 'rand' ) {
		global $_deals, $wpdeals_loop;

		// Pass vars to loop
		$wpdeals_loop['columns'] = $post_columns;

		$related = $_deals->get_related();
		if (sizeof($related)>0) :
			echo '<div class="related deals"><h2>'.__('Related Deals', 'wpdeals').'</h2>';
			$args = array(
				'post_type'	=> 'daily-deals',
				'ignore_sticky_posts'	=> 1,
				'posts_per_page' => $posts_per_page,
				'orderby' => $orderby,
				'post__in' => $related,
                                'meta_query' 	=> array(
                                        array(
                                                'key' 		=> '_is_expired',
                                                'value'		=> 'no',
                                                'compare' 	=> '='
                                        )
                                )
			);
			$args = apply_filters('wpdeals_related_deals_args', $args);
			query_posts($args);
			wpdeals_get_template_part( 'loop', 'store' );
			echo '</div>';
		endif;
		wp_reset_query();

	}
}

/**
 * WPDeals Cart totals
 **/
if (!function_exists('wpdeals_cart_totals')) {
	function wpdeals_cart_totals() {
		global $wpdeals;
		?>
		<div class="cart_totals">
			<table cellspacing="0" cellpadding="0">
				<tbody>

					<tr>
						<th><strong><?php _e('Total', 'wpdeals'); ?></strong></th>
						<td><strong><?php echo $wpdeals->cart->get_total(); ?></strong></td>
					</tr>
				</tbody>
			</table>			
		</div>
		<?php
	}
}

/**
 * WPDeals Login Form
 **/
if (!function_exists('wpdeals_login_form')) {
	function wpdeals_login_form( $message = '' ) {
		global $wpdeals;

		if (is_user_logged_in()) return;

		?>
		<form method="post" class="login">
			<?php if ($message) echo wpautop(wptexturize($message)); ?>
			<p class="form-row form-row-first">
				<label for="username"><?php _e('Username', 'wpdeals'); ?> <span class="required">*</span></label>
				<input type="text" class="input-text" name="username" id="username" />
			</p>
			<p class="form-row form-row-last">
				<label for="password"><?php _e('Password', 'wpdeals'); ?> <span class="required">*</span></label>
				<input class="input-text" type="password" name="password" id="password" />
			</p>
			<div class="clear"></div>

			<p class="form-row">
				<?php $wpdeals->nonce_field('login', 'login') ?>
				<input type="submit" class="button" name="login" value="<?php _e('Login', 'wpdeals'); ?>" />
				<a class="lost_password" href="<?php echo esc_url( wp_lostpassword_url( home_url() ) ); ?>"><?php _e('Lost Password?', 'wpdeals'); ?></a>
			</p>
		</form>
		<?php
	}
}

/**
 * WPDeals Checkout Login Form
 **/
if (!function_exists('wpdeals_checkout_login_form')) {
	function wpdeals_checkout_login_form() {

		if (is_user_logged_in()) return;

		if (get_option('wpdeals_enable_signup_and_login_from_checkout')=="no") return;

		$info_message = apply_filters('wpdeals_checkout_login_message', __('Already registered?', 'wpdeals'));

		?><p class="info"><?php echo $info_message; ?> <a href="#" class="showlogin"><?php _e('Click here to login', 'wpdeals'); ?></a></p><?php

		wpdeals_login_form( __('If you have storeped with us before, please enter your username and password in the boxes below.', 'wpdeals') );
	}
}

/**
 * Remove the singular class for wpdeals single deals
 **/
function wpdeals_body_classes ($classes) {

	if( ! is_singular('daily-deals') ) return $classes;

	$key = array_search('singular', $classes);
	if ( $key !== false ) unset($classes[$key]);
	return $classes;

}

/**
 * Order review table for checkout
 **/
function wpdeals_order_review() {
	wpdeals_get_template('checkout/review_order.php', false);
}

/**
 * Demo Banner
 *
 * Adds a demo store banner to the site if enabled
 **/
function wpdeals_demo_store() {
	if (get_option('wpdeals_demo_store')=='yes') :
		echo '<p class="demo_store">'.__('This is a demo store for testing purposes &mdash; no sales shall be fulfilled.', 'wpdeals').'</p>';
	endif;
}

/**
 * Show subcategory thumbnail
 **/
function wpdeals_subcategory_thumbnail( $category ) {
	global $wpdeals;

	$small_thumbnail_size 	= apply_filters('single_deals_small_thumbnail_size', 'store_catalog');
	$image_width 			= $wpdeals->get_image_size('store_catalog_image_width');
	$image_height 			= $wpdeals->get_image_size('store_catalog_image_height');

	$thumbnail_id 	= get_wpdeals_term_meta( $category->term_id, 'thumbnail_id', true );

	if ($thumbnail_id) :
		$image = wp_get_attachment_image_src( $thumbnail_id, $small_thumbnail_size );
		$image = $image[0];
	else :
		$image = $wpdeals->plugin_url().'/wpdeals-assets/images/placeholder.png';
	endif;

	echo '<img src="'.$image.'" alt="'.$category->slug.'" width="'.$image_width.'" height="'.$image_height.'" />';
}

/**
 * Display an sales details in a table
 **/
function wpdeals_order_details_table( $order_id ) {
	global $wpdeals; 
	
	if (!$order_id) return;

	$order = new wpdeals_order( $order_id );
	?>
	<h2><?php _e('Order Details', 'wpdeals'); ?></h2>
	<table class="store_table">
		<thead>
			<tr>
				<th><?php _e('Deal', 'wpdeals'); ?></th>
				<th><?php _e('Qty', 'wpdeals'); ?></th>
				<th><?php _e('Totals', 'wpdeals'); ?></th>
			</tr>
		</thead>
		<tfoot>
			<tr>
				<td scope="row" colspan="2"><?php _e('Order Total:', 'wpdeals'); ?></td>
				<td><?php echo wpdeals_price($order->get_order_total()); ?></td>
			</tr>
			<?php if ($order->customer_note) : ?>
			<tr>
				<td><?php _e('Note:', 'wpdeals'); ?></td>
				<td colspan="2"><?php echo wpautop(wptexturize($order->customer_note)); ?></td>
			</tr>
			<?php endif; ?>
		</tfoot>
		<tbody>
			<?php
			if (sizeof($order->items)>0) :

				foreach($order->items as $item) :

					if (isset($item['variation_id']) && $item['variation_id'] > 0) :
						$_deals = new wpdeals_deals_variation( $item['variation_id'] );
					else :
						$_deals = new wpdeals_deals( $item['id'] );
					endif;

					echo '
						<tr>
							<td class="daily-deals-name">'.$item['name'];
					echo '	</td>
							<td>'.$item['qty'].'</td>
							<td>';
					
					if (!isset($item['base_cost'])) $item['discount_cost'] = $item['cost'];							
                                        echo wpdeals_price( round(($item['discount_cost']*$item['qty']), 2) );

					echo '</td>
						</tr>';
				endforeach;
			endif;
			?>
		</tbody>
	</table>

	<div class="clear"></div>
	<?php
}
