<?php
	$hd_admin_settings_arr = get_option('APF_MyFirstFrom');
		
	$helpdesk_rewriterule_slug = isset($hd_admin_settings_arr['m_helpdesk_form']['helpdesk_rewriterule_slug']) && $hd_admin_settings_arr['m_helpdesk_form']['helpdesk_rewriterule_slug'] ? $hd_admin_settings_arr['m_helpdesk_form']['helpdesk_rewriterule_slug'] : 'company';
?>
<div class="top_hd_menu">
        	 <a href="<?php echo site_url().'/'.$helpdesk_rewriterule_slug?>" class="button button-default btn btn-default"><?php _e('Helpdesk Home','mhelpdesk');?></a> 
             <a href="<?php echo get_permalink($comp_DB_ID)?>" class="button button-default btn btn-default"><?php _e('Create Helpdesk Ticket','mhelpdesk');?></a>  
</div>

	<div role="tabpanel"><?php
        showCustomer_tabmenu();
		?>
          <div class="tab-content">
            <div role="tabpanel" class="tab-pane active" id="profile">
                <div id="profileContent" class="tabed_content"><?php
			
			$curUserInfoObj = $wpdb->get_results("SELECT display_name, user_email, user_login FROM $wpdb->users WHERE ID = '$curUserID' "); ?>
<form name="formProfileUpdation" action="#" method="post">
	<table>
		<tr>
			<th class="tick_gen_cols"><?php _e('My Name','mhelpdesk');?></th>
			<td>
				<input title="<?php _e('Enter your complete name properly; however only these 2 special characters [. -] are allowed','mhelpdesk');?>" 
				type="text" pattern="[a-zA-Z]+(([\.\- ][a-zA-Z ])?[a-zA-Z]*)*" class="tick_textarea"
				name="fullname" onchange="this.setCustomValidity(this.validity.patternMismatch ? '' : '');" value="<?php echo esc_html($curUserInfoObj[0]->display_name); ?>" required />
			</td>
		</tr>
		<tr>
			<th class="tick_gen_cols"><?php _e('Username','mhelpdesk');?></th>
			<td class="tick_gen_cols"><?php echo esc_html($curUserInfoObj[0]->user_login); ?></td>
		</tr>
        <tr>
			<td colspan="2" class="tick_gen_cols"><input type="checkbox" id="changePwdCB" /> <?php _e('Change Password','mhelpdesk');?></td>
		</tr>

		<tr id="blockingRow">
			<th class="tick_gen_cols"><?php _e('Change Password','mhelpdesk');?></th>
			<td>
				<input title="<?php _e('Password must contain at least 6 characters long; including ONE MUST be Uppercase, Lowercase and a Number','mhelpdesk');?>" 
                type="password" class="tick_textarea"
				pattern="(?=.*\d)(?=.*[a-z])(?=.*[A-Z]).{6,}" name="password" id="changePwd" onchange="this.setCustomValidity(this.validity.patternMismatch ? '' : '');" />
			</td>
		</tr>
		<tr>
			<th class="tick_gen_cols"><?php _e('Email Address:','mhelpdesk');?></th>
			<td class="tick_gen_cols"><?php echo esc_html($curUserInfoObj[0]->user_email); ?></td>
		</tr>
	</table>
	<?php wp_nonce_field('profileBtn', 'user-profileBtn'); ?>
	<input type="submit" value="Update My Profile" name="profileBtn" /><p></p>
</form>







<script>
jQuery(document).ready(function(){
				
		if(jQuery('#changePwdCB').is(":checked")){
			
			jQuery("#blockingRow").fadeIn('slow', function(){ 
				jQuery(this).attr('style', 'display');
			});
			
			jQuery('#changePwd').prop('disabled', false);
		}
		else{
				jQuery("#blockingRow").fadeOut('slow', function(){ 
				jQuery(this).attr('style', 'display:none');
			});
			
			jQuery('#changePwd').prop('disabled', 'disabled');
		}
	jQuery('#changePwdCB').click(function(){
		if(jQuery(this).is(":checked")){
			jQuery("#blockingRow").fadeIn('slow', function(){ 
				jQuery(this).attr('style', 'display');
			});
			
			jQuery('#changePwd').prop('disabled', false);
		}
		else{
				jQuery("#blockingRow").fadeOut('slow', function(){ 
				jQuery(this).attr('style', 'display:none');
			});
			
			jQuery('#changePwd').prop('disabled', 'disabled');
		}
	}); // END jQuery('#fulldayCB').click(function()
}); // END jQuery(document).ready(function()	
</script>

				</div>
            </div>
        </div>
    </div>