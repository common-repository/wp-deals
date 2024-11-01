<?php

global $wpdeals_loop;

$wpdeals_loop['loop'] = 0;
$wpdeals_loop['show_deals'] = true;

if (!isset($wpdeals_loop['columns']) || !$wpdeals_loop['columns']) $wpdeals_loop['columns'] = apply_filters('loop_store_columns', 3);

?>

<?php do_action('wpdeals_before_store_loop'); ?>

<ul class="daily-deals">

	<?php 
	
	do_action('wpdeals_before_store_loop_deals');
	
	if ($wpdeals_loop['show_deals'] && have_posts()) : while (have_posts()) : the_post(); 
	
		$_deals = new wpdeals_deals( $post->ID );
                
                if ($_deals->is_visible()) continue; 
		
		$wpdeals_loop['loop']++;
		
		?>
		<li class="daily-deal <?php if ($wpdeals_loop['loop']%$wpdeals_loop['columns']==0) echo 'last'; if (($wpdeals_loop['loop']-1)%$wpdeals_loop['columns']==0) echo 'first'; ?>">
			
			<?php do_action('wpdeals_before_store_loop_item'); ?>
			
			<a href="<?php the_permalink(); ?>">
				
				<?php do_action('wpdeals_before_store_loop_item_title', $post, $_deals); ?>
				
				<h3><?php the_title(); ?></h3>
				
				<?php do_action('wpdeals_after_store_loop_item_title', $post, $_deals); ?>
			
			</a>
	
			<?php do_action('wpdeals_after_store_loop_item', $post, $_deals); ?>
			
		</li><?php 
		
	endwhile; endif;
	
	if ($wpdeals_loop['loop']==0) echo '<li class="info">'.__('No deals found which match your selection.', 'wpdeals').'</li>'; 

	?>

</ul>

<div class="clear"></div>

<?php do_action('wpdeals_after_store_loop'); ?>