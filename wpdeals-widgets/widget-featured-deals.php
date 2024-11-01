<?php
/**
 * Featured Deals Widget
 *
 * Gets and displays featured deals in an unordered list
 * 
 * @package		WPDeals
 * @category	Widgets
 * @author		Tokokoo
 */
class WPDeals_Widget_Featured_Deals extends WP_Widget {
	
	/** Variables to setup the widget. */
	var $wpdeals_widget_cssclass;
	var $wpdeals_widget_description;
	var $wpdeals_widget_idbase;
	var $wpdeals_widget_name;
	
	/** constructor */
	function WPDeals_Widget_Featured_Deals() {
	
		/* Widget variable settings. */
		$this->wpdeals_widget_cssclass = 'widget_featured_deals';
		$this->wpdeals_widget_description = __( 'Display a list of featured deals on your site.', 'wpdeals' );
		$this->wpdeals_widget_idbase = 'wpdeals_featured_deals';
		$this->wpdeals_widget_name = __('WPDeals Featured Deals', 'wpdeals' );
		
		/* Widget settings. */
		$widget_ops = array( 'classname' => $this->wpdeals_widget_cssclass, 'description' => $this->wpdeals_widget_description );
		
		/* Create the widget. */
		$this->WP_Widget('featured-daily-deals', $this->wpdeals_widget_name, $widget_ops);

		add_action( 'save_post', array(&$this, 'flush_widget_cache') );
		add_action( 'deleted_post', array(&$this, 'flush_widget_cache') );
		add_action( 'switch_theme', array(&$this, 'flush_widget_cache') );
	}

	/** @see WP_Widget */
	function widget($args, $instance) {
		global $wpdeals;
		
		$cache = wp_cache_get('widget_featured_deals', 'widget');

		if ( !is_array($cache) ) $cache = array();

		if ( isset($cache[$args['widget_id']]) ) {
			echo $cache[$args['widget_id']];
			return;
		}

		ob_start();
		extract($args);

		$title = apply_filters('widget_title', empty($instance['title']) ? __('Featured Deals', 'wpdeals') : $instance['title'], $instance, $this->id_base);
		if ( !$number = (int) $instance['number'] )
			$number = 10;
		else if ( $number < 1 )
			$number = 1;
		else if ( $number > 15 )
			$number = 15;

		$featured_posts = get_posts(array('numberposts' => $number, 'post_status' => 'publish', 'post_type' => 'daily-deals', 'meta_query' => array( array('key' => '_is_expired', 'value' => 'no'), array('key' => 'featured', 'value' => 'yes')) ));
		if ($featured_posts) :
?>
		<?php echo $before_widget; ?>
		<?php if ( $title ) echo $before_title . $title . $after_title; ?>
		<ul class="deal_list_widget">
		<?php foreach ($featured_posts as $r) : $_deals = new wpdeals_deals( $r->ID ); ?>
		
		<li>
                <a href="<?php echo esc_url( get_permalink( $r->ID ) ); ?>" title="<?php echo esc_attr($r->post_title ? $r->post_title : $r->ID); ?>">
			<?php echo $_deals->get_image('store_catalog'); ?>
			<?php if ( $r->post_title ) echo get_the_title( $r->ID ); else echo $r->ID; ?>			
		</a> <?php echo $_deals->get_price_html(); ?>
                <?php wpdeals_template_loop_countdown($r, $_deals); ?>
                </li>
		
		<?php endforeach; ?>
		</ul>
		<?php echo $after_widget; ?>
<?php

		endif;

		$cache[$args['widget_id']] = ob_get_flush();
		wp_cache_set('widget_featured_deals', $cache, 'widget');
	}

	/** @see WP_Widget->update */
	function update( $new_instance, $old_instance ) {
		$instance = $old_instance;
		$instance['title'] = strip_tags($new_instance['title']);
		$instance['number'] = (int) $new_instance['number'];
		$this->flush_widget_cache();

		$alloptions = wp_cache_get( 'alloptions', 'options' );
		if ( isset($alloptions['widget_featured_deals']) ) delete_option('widget_featured_deals');

		return $instance;
	}

	function flush_widget_cache() {
		wp_cache_delete('widget_featured_deals', 'widget');
	}

	/** @see WP_Widget->form */
	function form( $instance ) {
		$title = isset($instance['title']) ? esc_attr($instance['title']) : '';
		if ( !isset($instance['number']) || !$number = (int) $instance['number'] )
			$number = 2;
?>
		<p><label for="<?php echo $this->get_field_id('title'); ?>"><?php _e('Title:', 'wpdeals'); ?></label>
		<input class="widefat" id="<?php echo esc_attr( $this->get_field_id('title') ); ?>" name="<?php echo esc_attr( $this->get_field_name('title') ); ?>" type="text" value="<?php echo esc_attr( $title ); ?>" /></p>

		<p><label for="<?php echo $this->get_field_id('number'); ?>"><?php _e('Number of deals to show:', 'wpdeals'); ?></label>
		<input id="<?php echo esc_attr( $this->get_field_id('number') ); ?>" name="<?php echo esc_attr( $this->get_field_name('number') ); ?>" type="text" value="<?php echo esc_attr( $number ); ?>" size="3" /></p>
<?php
	}
} // class WPDeals_Widget_Featured_Deals