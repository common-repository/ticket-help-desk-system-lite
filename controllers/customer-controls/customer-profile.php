<?php 
	$hd_admin_settings_arr = get_option('APF_MyFirstFrom');
		
	$helpdesk_rewriterule_slug = isset($hd_admin_settings_arr['m_helpdesk_form']['helpdesk_rewriterule_slug']) && $hd_admin_settings_arr['m_helpdesk_form']['helpdesk_rewriterule_slug'] ? $hd_admin_settings_arr['m_helpdesk_form']['helpdesk_rewriterule_slug'] : 'company';

	if ( !is_user_logged_in() )
	wp_safe_redirect(site_url().'/'.$helpdesk_rewriterule_slug);




$urlCompSlug = get_query_var('companyname');
$userRole = get_query_var('userrole');
$curUserID = get_current_user_id();
global $wpdb;

	$comp_DB_ID_obj = $wpdb->get_results("SELECT ID FROM $wpdb->posts WHERE post_type = 'companies' AND post_status = 'publish' AND post_name = '$urlCompSlug'");

		if(isset($_POST['user-profileBtn']) && wp_verify_nonce( $_POST['user-profileBtn'], 'profileBtn' ) && isset($_POST['profileBtn']) ) {
		
			if($_POST['password'] != '')
				wp_update_user( array( 'ID' => $curUserID, 'user_pass' => sanitize_text_field($_POST['password']) ) );
			
			if($_POST['fullname'] != '')
				wp_update_user( array( 'ID' => $curUserID, 'display_name' => strip_tags(sanitize_text_field($_POST['fullname'])) ) );
			$message.='<div class="alert alert-success">Profile Updated</div>';
		} // if(isset($_POST['user-profileBtn']) && wp_verify_nonce( $_POST['user-profileBtn'], 'profileBtn' ) && isset($_POST['profileBtn']) )





get_plugin_header();
echo '<div class="hdwrap">';

if(isset($comp_DB_ID_obj[0])){
		
		$comp_DB_ID = $comp_DB_ID_obj[0]->ID;
		
		$compCustomerBlocked_DB_arr = get_metadata('post', $comp_DB_ID, 'company-customers-blocked', true);
		
		$compCustomerOpened_DB_arr = get_metadata('post', $comp_DB_ID, 'company-customers-opened', true);

		
		if(!isset($compCustomerBlocked_DB_arr[$curUserID])) {
			$customerID_foundIndex = array_search($curUserID, isset($compCustomerOpened_DB_arr[0]) ? $compCustomerOpened_DB_arr : array());	
					if($customerID_foundIndex > -1) {
						
						
		global $mtheme;	
		$company_customer_tickets_arr = show_company_customer_tickets();
		include($mtheme.'customer-controls/customer-profile.php');



				} // if($customerID_foundIndex > -1)
				else echo '<h1 class="error">'.__('You are not AUTHORIZED to access company','mhelpdesk').'.</h1>';
		} //if(!isset($compCustomerBlocked_DB_arr[$curUserID]))
		else echo '<h1 class="error">'.__('You are currently FREEZED! in this ','mhelpdesk').$urlCompSlug.' '.__($helpdesk_rewriterule_slug,'mhelpdesk').'
		<br />'.__('Please Contact to your admin/agent in this regard','mhelpdesk').'.</h1>';
			
	} // if(isset($comp_DB_ID_obj[0]))
	else echo '<h1 class="error">'.__('This ','mhelpdesk').$urlCompSlug.' '.__('Company is not registered','mhelpdesk').'</h1>';

echo '</div>';
get_plugin_footer(); ?>