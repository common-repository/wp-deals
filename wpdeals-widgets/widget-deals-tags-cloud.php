<?php
/**
 * Tag Cloud Widget
 * 
 * @package		WPDeals
 * @category	Widgets
 * @author		Tokokoo
 */
 
class WPDeals_Widget_Deal_Tag_Cloud extends WP_Widget {

	/** Variables to setup the widget. */
	var $wpdeals_widget_cssclass;
	var $wpdeals_widget_description;
	var $wpdeals_widget_idbase;
	var $wpdeals_widget_name;
	
	/** constructor */
	function WPDeals_Widget_Deal_Tag_Cloud() {
	
		/* Widget variable settings. */
		$this->wpdeals_widget_cssclass = 'widget_deal_tags_cloud';
		$this->wpdeals_widget_description = __( 'Your most used deals tags in cloud format.', 'wpdeals' );
		$this->wpdeals_widget_idbase = 'wpdeals_deal_tags_cloud';
		$this->wpdeals_widget_name = __('WPDeals Deal Tags', 'wpdeals' );
		
		/* Widget settings. */
		$widget_ops = array( 'classname' => $this->wpdeals_widget_cssclass, 'description' => $this->wpdeals_widget_description );
		
		/* Create the widget. */
		$this->WP_Widget('deal_tags_cloud', $this->wpdeals_widget_name, $widget_ops);
	}
	
	/** @see WP_Widget */
	function widget( $args, $instance ) {
		extract($args);
		$current_taxonomy = $this->_get_current_taxonomy($instance);
		if ( !empty($instance['title']) ) {
			$title = $instance['title'];
		} else {
			if ( 'deal-tags' == $current_taxonomy ) {
				$title = __('Deal Tags', 'wpdeals');
			} else {
				$tax = get_taxonomy($current_taxonomy);
				$title = $tax->labels->name;
			}
		}
		$title = apply_filters('widget_title', $title, $instance, $this->id_base);

		echo $before_widget;
		if ( $title )
			echo $before_title . $title . $after_title;
		echo '<div class="tagcloud">';
		wp_tag_cloud( apply_filters('wpdeals_deal_tags_cloud_widget_args', array('taxonomy' => $current_taxonomy) ) );
		echo "</div>\n";
		echo $after_widget;
	}
	
	/** @see WP_Widget->update */
	function update( $new_instance, $old_instance ) {
		$instance['title'] = strip_tags(stripslashes($new_instance['title']));
		$instance['taxonomy'] = stripslashes($new_instance['taxonomy']);
		return $instance;
	}

	/** @see WP_Widget->form */
	function form( $instance ) {
		$current_taxonomy = $this->_get_current_taxonomy($instance);
?>
	<p><label for="<?php echo $this->get_field_id('title'); ?>"><?php _e('Title:', 'wpdeals') ?></label>
	<input type="text" class="widefat" id="<?php echo esc_attr( $this->get_field_id('title') ); ?>" name="<?php echo esc_attr( $this->get_field_name('title') ); ?>" value="<?php if (isset ( $instance['title'])) {echo esc_attr( $instance['title'] );} ?>" /></p>
	<?php
	}

	function _get_current_taxonomy($instance) {
		return 'deal-tags';
	}
} // class WPDeals_Widget_Deal_Tag_Cloud