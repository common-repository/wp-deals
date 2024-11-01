<?php get_header('store'); ?>
	  
<?php do_action('wpdeals_before_main_content'); // <div id="container"><div id="content" role="main"> ?>

	<?php 
		$store_page_id = get_option('wpdeals_store_page_id');
		$store_page = get_post($store_page_id);
		$store_page_title = (get_option('wpdeals_store_page_title')) ? get_option('wpdeals_store_page_title') : $store_page->post_title;
	?>
	
	<?php if (is_search()) : ?>		
		<h1 class="page-title"><?php _e('Search Results:', 'wpdeals'); ?> &ldquo;<?php the_search_query(); ?>&rdquo; <?php if (get_query_var('paged')) echo ' &mdash; Page '.get_query_var('paged'); ?></h1>
	<?php else : ?>
		<h1 class="page-title"><?php echo apply_filters('the_title', $store_page_title); ?></h1>
	<?php endif; ?>
	
	<?php echo apply_filters('the_content', $store_page->post_content); ?>

	<?php wpdeals_get_template_part( 'loop', 'store' ); ?>
	
	<?php do_action('wpdeals_pagination'); ?>

<?php do_action('wpdeals_after_main_content'); // </div></div> ?>

<?php do_action('wpdeals_sidebar'); ?>

<?php get_footer('store'); ?>