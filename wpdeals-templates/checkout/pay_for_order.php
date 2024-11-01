<?php global $order, $wpdeals; ?>
<form id="order_review" method="post">
	
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
				<td colspan="2"><?php _e('Subtotal', 'wpdeals'); ?></td>
				<td><?php echo $order->get_subtotal_to_display(); ?></td>
			</tr>
			<tr>
				<td colspan="2"><strong><?php _e('Grand Total', 'wpdeals'); ?></strong></td>
				<td><strong><?php echo wpdeals_price($order->order_total); ?></strong></td>
			</tr>
		</tfoot>
		<tbody>
			<?php
			if (sizeof($order->items)>0) : 
				foreach ($order->items as $item) :
					echo '
						<tr>
							<td>'.$item['name'].'</td>
							<td>'.$item['qty'].'</td>
							<td>'.wpdeals_price( $item['cost']*$item['qty'] ).'</td>
						</tr>';
				endforeach; 
			endif;
			?>
		</tbody>
	</table>
	
	<div id="payment">
		<?php if ($order->order_total > 0) : ?>
		<ul class="payment_methods methods">
			<?php 
				$available_gateways = $wpdeals->payment_gateways->get_available_payment_gateways();
				if ($available_gateways) : 
					// Chosen Method
					if (sizeof($available_gateways)) current($available_gateways)->set_current();
					foreach ($available_gateways as $gateway ) :
						?>
						<li>
							<input type="radio" id="payment_method_<?php echo $gateway->id; ?>" class="input-radio" name="payment_method" value="<?php echo esc_attr( $gateway->id ); ?>" <?php if ($gateway->chosen) echo 'checked="checked"'; ?> />
							<label for="payment_method_<?php echo $gateway->id; ?>"><?php echo $gateway->title; ?> <?php echo $gateway->icon(); ?></label> 
							<?php
								if ($gateway->has_fields || $gateway->description) : 
									echo '<div class="payment_box payment_method_'.$gateway->id.'" style="display:none;">';
									$gateway->payment_fields();
									echo '</div>';
								endif;
							?>
						</li>
						<?php
					endforeach;
				else :
				
					echo '<p>'.__('Sorry, it seems that there are no available payment methods for your location. Please contact us if you require assistance or wish to make alternate arrangements.', 'wpdeals').'</p>';
					
				endif;
			?>
		</ul>
		<?php endif; ?>

		<div class="form-row">
			<?php $wpdeals->nonce_field('pay')?>
			<input type="submit" class="button-alt" name="pay" id="place_order" value="<?php _e('Pay for order', 'wpdeals'); ?>" />

		</div>

	</div>
	
</form>