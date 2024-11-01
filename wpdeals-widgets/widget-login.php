<?php
/**
 * Login Widget
 *
 * @package		WPDeals
 * @category	Widgets
 * @author		Tokokoo
 */
class WPDeals_Widget_Login extends WP_Widget {
	
	/** Variables to setup the widget. */
	var $wpdeals_widget_cssclass;
	var $wpdeals_widget_description;
	var $wpdeals_widget_idbase;
	var $wpdeals_widget_name;

	/** constructor */
	function WPDeals_Widget_Login() {
	
		/* Widget variable settings. */
		$this->wpdeals_widget_cssclass = 'widget_login';
		$this->wpdeals_widget_description = __( 'Display a login area and "My Account" links in the sidebar.', 'wpdeals' );
		$this->wpdeals_widget_idbase = 'wpdeals_login';
		$this->wpdeals_widget_name = __('WPDeals Login', 'wpdeals' );
		
		/* Widget settings. */
		$widget_ops = array( 'classname' => $this->wpdeals_widget_cssclass, 'description' => $this->wpdeals_widget_description );
		
		/* Create the widget. */
		$this->WP_Widget('wpdeals_login', $this->wpdeals_widget_name, $widget_ops);
	}

	/** @see WP_Widget */
	function widget( $args, $instance ) {
		global $wpdeals;
		
		extract($args);
		
		$logged_out_title = (!empty($instance['logged_out_title'])) ? $instance['logged_out_title'] : __('Customer Login', 'wpdeals');
		$logged_in_title = (!empty($instance['logged_in_title'])) ? $instance['logged_in_title'] : __('Welcome %s', 'wpdeals');

		echo $before_widget;
		
		if (is_user_logged_in()) {
			
			$user = get_user_by('id', get_current_user_id());
			
			if ( $logged_in_title ) echo $before_title . sprintf( $logged_in_title, ucwords($user->display_name) ) . $after_title;
			
			do_action('wpdeals_login_widget_logged_in_before_links');
						
			$links = apply_filters( 'wpdeals_login_widget_logged_in_links', array(
				__('My account', 'wpdeals') 	=> get_permalink(get_option('wpdeals_myaccount_page_id')), 
				__('Change my password', 'wpdeals') => get_permalink(get_option('wpdeals_change_password_page_id')),
				__('Logout', 'wpdeals')		=> wp_logout_url(home_url())
			));
			
			if (sizeof($links>0)) :
				
				echo '<ul class="pagenav">';
				
				foreach ($links as $name => $link) :
					echo '<li><a href="'.$link.'">'.$name.'</a></li>';
				endforeach;
				
				echo '</ul>';

			endif;	
			
			do_action('wpdeals_login_widget_logged_in_after_links');
			
		} else {
		
			if ( $logged_out_title ) echo $before_title . $logged_out_title . $after_title;
			
			do_action('wpdeals_login_widget_logged_out_before_form');
			
			global $login_errors;
	
			if ( is_wp_error($login_errors) && $login_errors->get_error_code() ) foreach ($login_errors->get_error_messages() as $error) :
				echo '<div class="wpdeals_error">' . $error . "</div>\n";
				break;
			endforeach;					
			
			// Get redirect URL
			$redirect_to = apply_filters( 'wpdeals_login_widget_redirect', get_permalink(get_option('wpdeals_myaccount_page_id')) );
			?>
			<form method="post">
			
				<p><label for="user_login"><?php _e('Username', 'wpdeals'); ?></label> <input name="log" value="<?php if (isset($_POST['log'])) echo esc_attr(stripslashes($_POST['log'])); ?>" class="input-text" id="user_login" type="text" /></p>
				
				<p><label for="user_pass"><?php _e('Password', 'wpdeals'); ?></label> <input name="pwd" class="input-text" id="user_pass" type="password" /></p>		
				
				<p><input type="submit" class="submitbutton" name="wp-submit" id="wp-submit" value="<?php _e('Login &raquo;', 'wpdeals'); ?>" /> <a href="<?php echo wp_lostpassword_url(); ?>"><?php echo __('Lost password?', 'wpdeals'); ?></a></p>
				
				<div>
					<input type="hidden" name="redirect_to" class="redirect_to" value="<?php echo $redirect_to; ?>" />
					<input type="hidden" name="testcookie" value="1" />
					<input type="hidden" name="wpdeals_login" value="1" />
					<input type="hidden" name="rememberme" value="forever" />
				</div>
				
				<?php do_action('wpdeals_login_widget_logged_out_form'); ?>
			
			</form>
			<?php 
			do_action('wpdeals_login_widget_logged_out_after_form');	
			
			$wpdeals->add_inline_js("
				// Ajax Login
				jQuery('.widget_login form').submit(function(){
					
					var thisform = this;
					
					jQuery(thisform).block({ message: null, overlayCSS: { 
				        backgroundColor: '#fff', 
				        opacity:         0.6 
				    } });
				    
				    var data = {
						action: 		'wpdeals_sidebar_login_process',
						security: 		'".wp_create_nonce("wpdeals-sidebar-login-action")."',
						user_login: 	jQuery('input[name=\"log\"]', thisform).val(),
						user_password: 	jQuery('input[name=\"pwd\"]', thisform).val(),
						redirect_to:	jQuery('.redirect_to:eq(0)', thisform).val()
					};
				
					// Ajax action
					jQuery.post( '".admin_url('admin-ajax.php')."', data, function(response) {
						jQuery('.wpdeals_error').remove();
						
						result = jQuery.parseJSON( response );
						
						if (result.success==1) {
							window.location = result.redirect;
						} else {
							jQuery(thisform).prepend('<div class=\"wpdeals_error\">' + result.error + '</div>');
							jQuery(thisform).unblock();
						}
					});
					
					return false;
				});
			");
					
			$links = apply_filters( 'wpdeals_login_widget_logged_out_links', array());
			
			if (sizeof($links>0)) :
				
				echo '<ul class="pagenav">';
				
				foreach ($links as $name => $link) :
					echo '<li><a href="'.$link.'">'.$name.'</a></li>';
				endforeach;
				
				echo '</ul>';

			endif;	
	
		}		
			
		echo $after_widget;
	}

	/** @see WP_Widget->update */
	function update( $new_instance, $old_instance ) {
		$instance['logged_out_title'] = strip_tags(stripslashes($new_instance['logged_out_title']));
		$instance['logged_in_title'] = strip_tags(stripslashes($new_instance['logged_in_title']));
		return $instance;
	}

	/** @see WP_Widget->form */
	function form( $instance ) {
		?>
		
		<p><label for="<?php echo $this->get_field_id('logged_out_title'); ?>"><?php _e('Logged out title:', 'wpdeals') ?></label>
		<input type="text" class="widefat" id="<?php echo esc_attr( $this->get_field_id('logged_out_title') ); ?>" name="<?php echo esc_attr( $this->get_field_name('logged_out_title') ); ?>" value="<?php if (isset ( $instance['logged_out_title'])) echo esc_attr( $instance['logged_out_title'] ); else echo __('Customer Login', 'wpdeals'); ?>" /></p>
		
		<p><label for="<?php echo $this->get_field_id('logged_in_title'); ?>"><?php _e('Logged in title:', 'wpdeals') ?></label>
		<input type="text" class="widefat" id="<?php echo esc_attr( $this->get_field_id('logged_in_title') ); ?>" name="<?php echo esc_attr( $this->get_field_name('logged_in_title') ); ?>" value="<?php if (isset ( $instance['logged_in_title'])) echo esc_attr( $instance['logged_in_title'] ); else echo __('Welcome %s', 'wpdeals'); ?>" /></p>
		
		<?php
	}

} // class WPDeals_Widget_Login

/**
 * Process ajax login
 */
add_action('wp_ajax_nopriv_wpdeals_sidebar_login_process', 'wpdeals_sidebar_login_ajax_process');

function wpdeals_sidebar_login_ajax_process() {

	check_ajax_referer( 'wpdeals-sidebar-login-action', 'security' );
	
	// Get post data
	$creds = array();
	$creds['user_login'] 	= esc_attr($_POST['user_login']);
	$creds['user_password'] = esc_attr($_POST['user_password']);
	$creds['remember'] 		= 'forever';
	$redirect_to 			= esc_attr($_POST['redirect_to']);
	
	// Check for Secure Cookie
	$secure_cookie = '';
	
	// If the user wants ssl but the session is not ssl, force a secure cookie.
	if ( !empty($_POST['log']) && !force_ssl_admin() ) {
		$user_name = sanitize_user($_POST['log']);
		if ( $user = get_user_by('login', $user_name) ) {
			if ( get_user_option('use_ssl', $user->ID) ) {
				$secure_cookie = true;
				force_ssl_admin(true);
			}
		}
	}
	
	if ( !$secure_cookie && is_ssl() && force_ssl_login() && !force_ssl_admin() && ( 0 !== strpos($redirect_to, 'https') ) && ( 0 === strpos($redirect_to, 'http') ) )
	$secure_cookie = false;

	// Login
	$user = wp_signon($creds, $secure_cookie);
	
	// Redirect filter
	if ( $secure_cookie && false !== strpos($redirect_to, 'wp-admin') ) $redirect_to = preg_replace('|^http://|', 'https://', $redirect_to);

	// Result
	$result = array();
	
	if ( !is_wp_error($user) ) :
		$result['success'] = 1;
		$result['redirect'] = $redirect_to;
	else :
		$result['success'] = 0;
		foreach ($user->errors as $error) {
			$result['error'] = $error[0];
			break;
		}
	endif;
	
	echo json_encode($result);

	die();
}

add_action('init', 'wpdeals_sidebar_login_process', 0);

function wpdeals_sidebar_login_process() {

	if (isset($_POST['wpdeals_login'])) {
		
		global $login_errors;
		
		// Get redirect URL
		$redirect_to = apply_filters( 'wpdeals_login_widget_redirect', get_permalink(get_option('wpdeals_myaccount_page_id')) );

		// Check for Secure Cookie
		$secure_cookie = '';
		
		// If the user wants ssl but the session is not ssl, force a secure cookie.
		if ( !empty($_POST['log']) && !force_ssl_admin() ) {
			$user_name = sanitize_user($_POST['log']);
			if ( $user = get_user_by('login', $user_name) ) {
				if ( get_user_option('use_ssl', $user->ID) ) {
					$secure_cookie = true;
					force_ssl_admin(true);
				}
			}
		}
		
		if ( !$secure_cookie && is_ssl() && force_ssl_login() && !force_ssl_admin() && ( 0 !== strpos($redirect_to, 'https') ) && ( 0 === strpos($redirect_to, 'http') ) )
		$secure_cookie = false;

		// Login
		$user = wp_signon('', $secure_cookie);

		// Redirect filter
		if ( $secure_cookie && false !== strpos($redirect_to, 'wp-admin') ) $redirect_to = preg_replace('|^http://|', 'https://', $redirect_to);
		
		// Check the username
		if ( !$_POST['log'] ) :
			$user = new WP_Error();
			$user->add('empty_username', __('<strong>ERROR</strong>: Please enter a username.', 'wpdeals'));
		elseif ( !$_POST['pwd'] ) :
			$user = new WP_Error();
			$user->add('empty_username', __('<strong>ERROR</strong>: Please enter your password.', 'wpdeals'));
		endif;
		
		// Redirect if successful
		if ( !is_wp_error($user) ) :
			wp_safe_redirect( $redirect_to );
			exit;
		endif;
		
		$login_errors = $user;
			
	}
}