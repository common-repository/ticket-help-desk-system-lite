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
				wp_update_user( array( 'ID' => $curUserID, 'user_pass' => sanitize_text_field($_POST['password'] )) );
			
			if($_POST['fullname'] != '')
				wp_update_user( array( 'ID' => $curUserID, 'display_name' => strip_tags(sanitize_text_field($_POST['fullname'])) ) );
			
		} // if(isset($_POST['user-profileBtn']) && wp_verify_nonce( $_POST['user-profileBtn'], 'profileBtn' ) && isset($_POST['profileBtn']) )





get_plugin_header();
echo '<div class="hdwrap">';



	if(isset($comp_DB_ID_obj[0])){
		
		$comp_DB_ID = $comp_DB_ID_obj[0]->ID;
		

		$DBcomp_agentEmails = get_metadata('post',$comp_DB_ID,'company-agentEmail',true);
				
		$logged_useremailObj = $wpdb->get_results("SELECT user_email FROM $wpdb->users WHERE ID = '$curUserID'");
		
		$agent_authCheck = array_search($logged_useremailObj[0]->user_email, isset($DBcomp_agentEmails[0]) ? $DBcomp_agentEmails : array());

	if($agent_authCheck > -1){
		
		$company_agent_tickets_arr = show_company_agent_tickets();
		global $mtheme;			
		include($mtheme.'agent-controls/agent-profile.php');



	} //if(agent_authCheck)
		else echo '<h1 class="error">'.__('You are not AUTHORIZED to access this company','mhelpdesk').'.</h1>';
			
	} // if(isset($comp_DB_ID_obj[0]))
	else echo '<h1 class="error">'.__('The accessed Company is not registered','mhelpdesk').'</h1>';

echo '</div>';
get_plugin_footer(); ?>