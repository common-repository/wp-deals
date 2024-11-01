<?php if (!defined('ABSPATH')) exit; ?>

<?php global $order_id, $wpdeals, $customer_note; $order = new wpdeals_order( $order_id ); ?>

<?php do_action('wpdeals_email_header'); ?>

<p><?php _e("Hello, a note has just been added to your order:", 'wpdeals'); ?></p>

<blockquote><?php echo wpautop(wptexturize( $customer_note )) ?></blockquote>

<p><?php _e("For your reference, your order details are shown below.", 'wpdeals'); ?></p>

<?php do_action('wpdeals_email_before_order_table', $order, false); ?>

<h2><?php echo __('Order #:', 'wpdeals') . ' ' . $order->id; ?></h2>

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
		<?php if ($order->status=='completed') echo $order->email_order_items_table( true, true ); else echo $order->email_order_items_table( false, true ); ?>
	</tbody>
</table>

<?php do_action('wpdeals_email_after_order_table', $order, false); ?>

<div style="clear:both;"></div>

<?php do_action('wpdeals_email_footer'); ?>