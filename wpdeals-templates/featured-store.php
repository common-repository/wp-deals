<?php get_header('store'); ?>
	  
<?php do_action('wpdeals_before_main_content'); ?>

    <?php if ( have_posts() ) while ( have_posts() ) : the_post(); global $_deals, $post; $_deals = new wpdeals_deals( $post->ID ); ?>

            <?php do_action('wpdeals_before_single_deals', $post, $_deals); ?>

            <div itemscope itemtype="http://schema.org/Product" id="daily-deals-<?php the_ID(); ?>" <?php post_class(); ?>>

                    <h1 itemprop="name" class="deal_title"><?php the_title(); ?></h1>

                    <?php do_action('wpdeals_before_single_deals_summary', $post, $_deals); ?>

                    <div class="summary">

                            <?php do_action( 'wpdeals_single_deals_summary', $post, $_deals ); ?>

                    </div>

                    <?php do_action('wpdeals_after_single_deals_summary', $post, $_deals); ?>

            </div>

            <?php do_action('wpdeals_after_single_deals', $post, $_deals); ?>

    <?php endwhile; wp_reset_query(); ?>

<?php do_action('wpdeals_after_main_content'); // </div></div> ?>

<?php do_action('wpdeals_sidebar'); ?>

<?php get_footer('store'); ?>