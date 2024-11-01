<?php if (!defined('ABSPATH')) exit; ?>

<?php global $order_id, $order, $wpdeals; ?>

<?php do_action('wpdeals_email_header'); ?>

<?php if ($order->status=='pending') : ?>

	<p><?php echo sprintf( __("An order has been created for you on &ldquo;%s&rdquo;. To pay for this order please use the following link: %s", 'wpdeals'), get_bloginfo('name'), $order->get_checkout_payment_url() ); ?></p>
	
<?php endif; ?>

<?php do_action('wpdeals_email_before_order_table', $order, false); ?>

<h2><?php echo __('Order #', 'wpdeals') . $order->id; ?></h2>

<table cellspacing="0" cellpadding="6" style="width: 100%; border: 1px solid #eee;" border="1" bordercolor="#eee">
	<thead>
		<tr>
			<th scope="col" style="text-align:left; border: 1px solid #eee;"><?php _e('Deal', 'wpdeals'); ?></th>
			<th scope="col" style="text-align:left; border: 1px solid #eee;"><?php _e('Quantity', 'wpdeals'); ?></th>
			<th scope="col" style="text-align:left; border: 1px solid #eee;"><?php _e('Price', 'wpdeals'); ?></th>
		</tr>
	</thead>
	<tfoot>
		<tr>
			<th scope="row" colspan="2" style="text-align:left; border: 1px solid #eee;"><?php _e('Order Total:', 'wpdeals'); ?></th>
			<td style="text-align:left; border: 1px solid #eee;"><?php echo wpdeals_price($order->get_order_total()); ?></td>
		</tr>
	</tfoot>
	<tbody>
		<?php echo $order->email_order_items_table(); ?>
	</tbody>
</table>

<?php do_action('wpdeals_email_after_order_table', $order, false); ?>

<?php do_action('wpdeals_email_footer'); ?>