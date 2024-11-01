<?php if (!defined('ABSPATH')) exit; ?>

<?php global $wpdeals, $voucher, $current_user; ?>

<?php get_currentuserinfo(); ?>

<?php do_action('wpdeals_voucher_header'); ?>

<?php do_action('wpdeals_voucher_before_detail', $voucher); ?>

<h4><?php echo get_the_title($voucher['id']); ?></h4>
<img src="http://api.qrserver.com/v1/create-qr-code/?size=100x100&data=<?php printf( __('%s - %s', 'wpdeals'), get_the_title($voucher['id']), $voucher['code']); ?>" style="float: right;"/>
<p><?php printf( __('Voucher Code: <br/>%s', 'wpdeals'), $voucher['code'] ); ?></p>
<p><?php printf( __('Purchased By: <br/>%s', 'wpdeals'), $current_user->display_name ); ?></p>

<hr/>

<h4><?php _e('Instructions:', 'wpdeals'); ?></h4>

<p><?php echo stripslashes(get_post_meta($voucher['id'], 'how_to_use', true)); ?></p>

<?php do_action('wpdeals_voucher_after_detail', $voucher); ?>

<div style="clear:both;"></div>

[<a href="#" onclick="window.print();return false;"><?php _e('Print here', 'wpdeals'); ?></a>]

<?php do_action('wpdeals_voucher_footer'); ?>
