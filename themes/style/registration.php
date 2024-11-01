<?php
	$hd_admin_settings_arr = get_option('APF_MyFirstFrom');
		
	$helpdesk_rewriterule_slug = isset($hd_admin_settings_arr['m_helpdesk_form']['helpdesk_rewriterule_slug']) && $hd_admin_settings_arr['m_helpdesk_form']['helpdesk_rewriterule_slug'] ? $hd_admin_settings_arr['m_helpdesk_form']['helpdesk_rewriterule_slug'] : 'company';
	
	if ( is_user_logged_in() ) {
		echo "<meta http-equiv=refresh content=0;url=".get_bloginfo( 'url' ) . "/wp-admin/profile.php />";
		//wp_safe_redirect( get_bloginfo( 'url' ) . '/wp-admin/profile.php' );
		exit;
	}


	if(isset($_POST['user-registration']) && wp_verify_nonce( $_POST['user-registration'], 'registration' ) && isset($_POST['registrationBtn']) ) {

		if(username_exists($_POST['username']) == true || email_exists($_POST['email']) == true ){
			?>
		   <div id="errorbox">
			<h1 class="error"><?php _e('Already registered with this Username or Email ID!','mhelpdesk');?></h1>
		   </div><br />
			<?php
		}
		else{
			$createdUser_id	= wp_create_user(sanitize_text_field($_POST['username']),sanitize_text_field($_POST['password']),sanitize_text_field($_POST['email']));
			//update_metadata('user', $createdUser_id, 'hd-fullname', $_POST['fullname']);
			
			wp_update_user( array( 'ID' => $createdUser_id, 'display_name' => sanitize_text_field($_POST['fullname']) ) );

			echo "<meta http-equiv=refresh content=0;url=".get_bloginfo( 'url' ) . "/".$helpdesk_rewriterule_slug." />";
			//wp_safe_redirect( get_bloginfo( 'url' ) . '/company' );
		}

		
	} // if(isset($_POST['user-registration']) && wp_verify_nonce( $_POST['user-registration'], 'registration' ) && isset($_POST['registrationBtn']) )


?>
<form name="formregistration" action="#" method="post">
	<table>
		<tr>
			<th><?php _e('Your Full Name','mhelpdesk');?></th>
			<td>
				<input title="<?php _e('Enter your complete name properly; however only these 2 special characters [. -] are allowed','mhelpdesk');?>" 
				type="text" pattern="[a-zA-Z]+(([\.\- ][a-zA-Z ])?[a-zA-Z]*)*"
				name="fullname" onchange="this.setCustomValidity(this.validity.patternMismatch ? '' : '');" required />
			</td>
		</tr>
		<tr>
			<th><?php _e('Login/ Username','mhelpdesk');?></th>
			<td>
				<input name="username" title="<?php _e('Enter username without spaces & special characters','mhelpdesk');?>" type="text" 
				pattern="\w+" onchange="this.setCustomValidity(this.validity.patternMismatch ? '' : '');" required />
			</td>
		</tr>
		<tr>
			<th><?php _e('Password','mhelpdesk');?></th>
			<td>
				<input title="<?php _e('Password must contain at least 6 characters long; including ONE MUST be Uppercase, Lowercase and a Number','mhelpdesk');?>" 
                type="password" pattern="(?=.*\d)(?=.*[a-z])(?=.*[A-Z]).{6,}" name="password"
				onchange="this.setCustomValidity(this.validity.patternMismatch ? '' : '');" required />
			</td>
		</tr>
		<tr>
			<th><?php _e('Email Address','mhelpdesk');?>:</th>
			<td><input type="email" name="email" required /></td>
		</tr>
		<!--tr>
			<th>Register AS</th>
			<td>
				<select name="registeras" required >
					<option value="">Select Registation Type</option>
					<option value="admin">Admin</option>
					<option value="agent">Agent</option>
					<option value="customer">Customer</option>
				</select>
			</td>
		</tr-->
	</table>
	<?php wp_nonce_field('registration', 'user-registration'); ?>
	<input type="submit" value="Register" name="registrationBtn" />
</form>