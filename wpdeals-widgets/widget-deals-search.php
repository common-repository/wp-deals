<?php
/**
 * Deal Search Widget
 * 
 * @package		WPDeals
 * @category	Widgets
 * @author		Tokokoo
 */

class WPDeals_Widget_Deal_Search extends WP_Widget {

	/** Variables to setup the widget. */
	var $wpdeals_widget_cssclass;
	var $wpdeals_widget_description;
	var $wpdeals_widget_idbase;
	var $wpdeals_widget_name;
	
	/** constructor */
	function WPDeals_Widget_Deal_Search() {
	
		/* Widget variable settings. */
		$this->wpdeals_widget_cssclass = 'widget_deals_search';
		$this->wpdeals_widget_description = __( 'A Search box for deals only.', 'wpdeals' );
		$this->wpdeals_widget_idbase = 'wpdeals_deals_search';
		$this->wpdeals_widget_name = __('WPDeals Deal Search', 'wpdeals' );
		
		/* Widget settings. */
		$widget_ops = array( 'classname' => $this->wpdeals_widget_cssclass, 'description' => $this->wpdeals_widget_description );
		
		/* Create the widget. */
		$this->WP_Widget('deal_search', $this->wpdeals_widget_name, $widget_ops);
	}

	/** @see WP_Widget */
	function widget( $args, $instance ) {
		extract($args);

		$title = $instance['title'];
		$title = apply_filters('widget_title', $title, $instance, $this->id_base);
		
		echo $before_widget;
		
		if ($title) echo $before_title . $title . $after_title;
		
		?>
		<form role="search" method="get" id="searchform" action="<?php echo esc_url( home_url() ); ?>">
			<div>
				<label class="screen-reader-text" for="s"><?php _e('Search for:', 'wpdeals'); ?></label>
				<input type="text" value="<?php the_search_query(); ?>" name="s" id="s" placeholder="<?php _e('Search for deals', 'wpdeals'); ?>" />
				<input type="submit" id="searchsubmit" value="<?php _e('Search', 'wpdeals'); ?>" />
				<input type="hidden" name="post_type" value="daily-deals" />
			</div>
		</form>
		<?php
		
		echo $after_widget;
	}

	/** @see WP_Widget->update */
	function update( $new_instance, $old_instance ) {
		$instance['title'] = strip_tags(stripslashes($new_instance['title']));
		return $instance;
	}

	/** @see WP_Widget->form */
	function form( $instance ) {
		global $wpdb;
		?>
			<p><label for="<?php echo $this->get_field_id('title'); ?>"><?php _e('Title:', 'wpdeals') ?></label>
			<input type="text" class="widefat" id="<?php echo esc_attr( $this->get_field_id('title') ); ?>" name="<?php echo esc_attr( $this->get_field_name('title') ); ?>" value="<?php if (isset ( $instance['title'])) {echo esc_attr( $instance['title'] );} ?>" /></p>
		<?php
	}
} // WPDeals_Widget_Deal_Search