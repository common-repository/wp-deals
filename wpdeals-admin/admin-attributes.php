<?php
/**
 * Functions used for the attributes section in WordPress Admin
 * 
 * The attributes section lets users add custom attributes to assign to deals - they can also be used in the layered nav widget.
 *
 * @author 		Tokokoo
 * @category 	Admin
 * @package 	WPDeals
 */

/**
 * Attributes admin panel
 * 
 * Shows the created attributes and lets you add new ones.
 * The added attributes are stored in the database and can be used for layered navigation.
 */
function wpdeals_attributes() {
	
	global $wpdb, $wpdeals;
	
	if (isset($_POST['add_new_attribute']) && $_POST['add_new_attribute']) :
		check_admin_referer( 'wpdeals-add-new_attribute' );
		$attribute_name = (string) sanitize_title($_POST['attribute_name']);
		$attribute_type = (string) $_POST['attribute_type'];
		$attribute_label = (string) $_POST['attribute_label'];
		
		if ($attribute_name && strlen($attribute_name)<30 && $attribute_type && !taxonomy_exists( $wpdeals->attribute_taxonomy_name($attribute_name) )) :
		
			$wpdb->insert( $wpdb->prefix . "wpdeals_attribute_taxonomies", array( 'attribute_name' => $attribute_name, 'attribute_label' => $attribute_label, 'attribute_type' => $attribute_type ), array( '%s', '%s' ) );
						
			wp_safe_redirect( get_admin_url() . 'admin.php?page=wpdeals_attributes' );
			exit;
			
		endif;
		
	elseif (isset($_POST['save_attribute']) && $_POST['save_attribute'] && isset($_GET['edit'])) :
		
		$edit = absint($_GET['edit']);
		check_admin_referer( 'wpdeals-save-attribute_' . $edit );
		if ($edit>0) :
		
			$attribute_type = $_POST['attribute_type'];
			$attribute_label = (string) $_POST['attribute_label'];
		
			$wpdb->update( $wpdb->prefix . "wpdeals_attribute_taxonomies", array( 'attribute_type' => $attribute_type, 'attribute_label' => $attribute_label ), array( 'attribute_id' => $_GET['edit'] ), array( '%s', '%s' ) );
		
		endif;
		
		wp_safe_redirect( get_admin_url() . 'admin.php?page=wpdeals_attributes' );
		exit;
			
	elseif (isset($_GET['delete'])) :
		check_admin_referer( 'wpdeals-delete-attribute_' . absint( $_GET['delete'] ) );
		$delete = absint($_GET['delete']);
		
		if ($delete>0) :
		
			$att_name = $wpdb->get_var("SELECT attribute_name FROM " . $wpdb->prefix . "wpdeals_attribute_taxonomies WHERE attribute_id = '$delete'");
			
			if ($att_name && $wpdb->query("DELETE FROM " . $wpdb->prefix . "wpdeals_attribute_taxonomies WHERE attribute_id = '$delete'")) :
				
				$taxonomy = $wpdeals->attribute_taxonomy_name($att_name); 
				
				if (taxonomy_exists($taxonomy)) :
				
					$terms = get_terms($taxonomy, 'orderby=name&hide_empty=0'); 
					foreach ($terms as $term) {
						wp_delete_term( $term->term_id, $taxonomy );
					}
				
				endif;
				
				wp_safe_redirect( get_admin_url() . 'admin.php?page=wpdeals_attributes' );
				exit;
										
			endif;
			
		endif;
		
	endif;
	
	if (isset($_GET['edit']) && $_GET['edit'] > 0) :
		wpdeals_edit_attribute();
	else :	
		wpdeals_add_attribute();
	endif;
	
}

/**
 * Edit Attribute admin panel
 * 
 * Shows the interface for changing an attributes type between select and text
 */
function wpdeals_edit_attribute() {
	
	global $wpdb;
	
	$edit = absint($_GET['edit']);
		
	$att_type = $wpdb->get_var("SELECT attribute_type FROM " . $wpdb->prefix . "wpdeals_attribute_taxonomies WHERE attribute_id = '$edit'");	
	$att_label = $wpdb->get_var("SELECT attribute_label FROM " . $wpdb->prefix . "wpdeals_attribute_taxonomies WHERE attribute_id = '$edit'");		
	?>
	<div class="wrap wpdeals">
		<div class="icon32 icon32-attributes" id="icon-wpdeals"><br/></div>
	    <h2><?php _e('Attributes', 'wpdeals') ?></h2>
	    <br class="clear" />
	    <div id="col-container">
	    	<div id="col-left">
	    		<div class="col-wrap">
	    			<div class="form-wrap">
	    				<h3><?php _e('Edit Attribute', 'wpdeals') ?></h3>
	    				<p><?php _e('Attribute taxonomy names cannot be changed; you may only change an attributes type.', 'wpdeals') ?></p>
	    				<form action="admin.php?page=wpdeals_attributes&amp;edit=<?php echo absint( $edit ); ?>" method="post">
							
							<div class="form-field">
								<label for="attribute_label"><?php _e('Attribute Label', 'wpdeals'); ?></label>
								<input name="attribute_label" id="attribute_label" type="text" value="<?php echo esc_attr( $att_label ); ?>" />
								<p class="description"><?php _e('Label for the attribute (shown on the front-end).', 'wpdeals'); ?></p>
							</div>
							<div class="form-field">
								<label for="attribute_type"><?php _e('Attribute type', 'wpdeals'); ?></label>
								<select name="attribute_type" id="attribute_type">
									<option value="select" <?php selected($att_type, 'select'); ?>><?php _e('Select', 'wpdeals') ?></option>
									<option value="text" <?php selected($att_type, 'text'); ?>><?php _e('Text', 'wpdeals') ?></option>										
								</select>
							</div>
							
							<p class="submit"><input type="submit" name="save_attribute" id="submit" class="button" value="<?php _e('Save Attribute', 'wpdeals'); ?>"></p>
							<?php wp_nonce_field( 'wpdeals-save-attribute_' . $edit ); ?>
	    				</form>
	    			</div>
	    		</div>
	    	</div>
	    </div>
	</div>
	<?php
	
}

/**
 * Add Attribute admin panel
 * 
 * Shows the interface for adding new attributes
 */
function wpdeals_add_attribute() {
	global $wpdeals;
	?>
	<div class="wrap wpdeals">
		<div class="icon32 icon32-attributes" id="icon-wpdeals"><br/></div>
	    <h2><?php _e('Attributes', 'wpdeals') ?></h2>
	    <br class="clear" />
	    <div id="col-container">
	    	<div id="col-right">
	    		<div class="col-wrap">
		    		<table class="widefat fixed" style="width:100%">
				        <thead>
				            <tr>
				                <th scope="col"><?php _e('Name', 'wpdeals') ?></th>
				                <th scope="col"><?php _e('Label', 'wpdeals') ?></th>
				                <th scope="col"><?php _e('Type', 'wpdeals') ?></th>
				                <th scope="col" colspan="2"><?php _e('Terms', 'wpdeals') ?></th>
				            </tr>
				        </thead>
				        <tbody>
				        	<?php
				        		$attribute_taxonomies = $wpdeals->get_attribute_taxonomies();
				        		if ( $attribute_taxonomies ) :
				        			foreach ($attribute_taxonomies as $tax) :
				        				$att_title = $tax->attribute_name;
				        				if ( isset( $tax->attribute_label ) ) { $att_title = $tax->attribute_label; }
				        				?><tr>

				        					<td><a href="edit-tags.php?taxonomy=<?php echo $wpdeals->attribute_taxonomy_name($tax->attribute_name); ?>&amp;post_type=daily-deals"><?php echo $tax->attribute_name; ?></a>
				        					
				        					<div class="row-actions"><span class="edit"><a href="<?php echo esc_url( add_query_arg('edit', $tax->attribute_id, 'admin.php?page=wpdeals_attributes') ); ?>"><?php _e('Edit', 'wpdeals'); ?></a> | </span><span class="delete"><a class="delete" href="<?php echo esc_url( wp_nonce_url( add_query_arg('delete', $tax->attribute_id, 'admin.php?page=wpdeals_attributes'), 'wpdeals-delete-attribute_' . $tax->attribute_id ) ); ?>"><?php _e('Delete', 'wpdeals'); ?></a></span></div>				        					
				        					</td>
				        					<td><?php echo esc_html( ucwords( $att_title ) ); ?></td>
				        					<td><?php echo esc_html( ucwords( $tax->attribute_type ) ); ?></td>
				        					<td><?php 
				        						if (taxonomy_exists($wpdeals->attribute_taxonomy_name($tax->attribute_name))) :
					        						$terms_array = array();
					        						$terms = get_terms( $wpdeals->attribute_taxonomy_name($tax->attribute_name), 'orderby=name&hide_empty=0' );
					        						if ($terms) :
						        						foreach ($terms as $term) :
															$terms_array[] = $term->name;
														endforeach;
														echo implode(', ', $terms_array);
													else :
														echo '<span class="na">&ndash;</span>';
													endif;
												else :
													echo '<span class="na">&ndash;</span>';
												endif;
				        					?></td>
				        					<td><a href="edit-tags.php?taxonomy=<?php echo $wpdeals->attribute_taxonomy_name($tax->attribute_name); ?>&amp;post_type=daily-deals" class="button alignright"><?php _e('Configure&nbsp;terms', 'wpdeals'); ?></a></td>
				        				</tr><?php
				        			endforeach;
				        		else :
				        			?><tr><td colspan="5"><?php _e('No attributes currently exist.', 'wpdeals') ?></td></tr><?php
				        		endif;
				        	?>
				        </tbody>
				    </table>
	    		</div>
	    	</div>
	    	<div id="col-left">
	    		<div class="col-wrap">
	    			<div class="form-wrap">
	    				<h3><?php _e('Add New Attribute', 'wpdeals') ?></h3>
	    				<p><?php _e('Attributes let you define extra deals data, such as size or colour. You can use these attributes in the store sidebar using the "layered nav" widgets. Please note: you cannot rename an attribute later on.', 'wpdeals') ?></p>
	    				<form action="admin.php?page=wpdeals_attributes" method="post">
							<div class="form-field">
								<label for="attribute_name"><?php _e('Attribute Name', 'wpdeals'); ?></label>
								<input name="attribute_name" id="attribute_name" type="text" value="" maxlength="29" />
								<p class="description"><?php _e('Unique name/reference for the attribute; must be shorter than 28 characters.', 'wpdeals'); ?></p>
							</div>
							<div class="form-field">
								<label for="attribute_label"><?php _e('Attribute Label', 'wpdeals'); ?></label>
								<input name="attribute_label" id="attribute_label" type="text" value="" />
								<p class="description"><?php _e('Label for the attribute (shown on the front-end).', 'wpdeals'); ?></p>
							</div>
							<div class="form-field">
								<label for="attribute_type"><?php _e('Attribute type', 'wpdeals'); ?></label>
								<select name="attribute_type" id="attribute_type">
									<option value="select"><?php _e('Select', 'wpdeals') ?></option>
									<option value="text"><?php _e('Text', 'wpdeals') ?></option>										
								</select>
								<p class="description"><?php _e('Determines how you select attributes for deals. <strong>Text</strong> allows manual entry via the deals page, whereas <strong>select</strong> attribute terms can be defined from this section. If you plan on using an attribute for variations use <strong>select</strong>.', 'wpdeals'); ?></p>
							</div>
							
							<p class="submit"><input type="submit" name="add_new_attribute" id="submit" class="button" value="<?php _e('Add Attribute', 'wpdeals'); ?>"></p>
							<?php wp_nonce_field( 'wpdeals-add-new_attribute' ); ?>
	    				</form>
	    			</div>
	    		</div>
	    	</div>
	    </div>
	    <script type="text/javascript">
		/* <![CDATA[ */
		
			jQuery('a.delete').click(function(){
	    		var answer = confirm ("<?php _e('Are you sure you want to delete this attribute?', 'wpdeals'); ?>");
				if (answer) return true;
				return false;
	    	});
		    	
		/* ]]> */
		</script>
	</div>
	<?php
}