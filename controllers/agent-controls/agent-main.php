<?php 
	$hd_admin_settings_arr = get_option('APF_MyFirstFrom');
		
	$helpdesk_rewriterule_slug = isset($hd_admin_settings_arr['m_helpdesk_form']['helpdesk_rewriterule_slug']) && $hd_admin_settings_arr['m_helpdesk_form']['helpdesk_rewriterule_slug'] ? $hd_admin_settings_arr['m_helpdesk_form']['helpdesk_rewriterule_slug'] : 'company';

	if ( !is_user_logged_in() )
	wp_safe_redirect(site_url().'/'.$helpdesk_rewriterule_slug);
	
get_plugin_header();
echo '<div class="hdwrap">';
	
	global $wpdb, $current_user;
	$urlCompSlug = get_query_var('companyname');
	$userRole = get_query_var('userrole');
	$curUserID = get_current_user_id();
	$comp_DB_ID_obj = $wpdb->get_results("SELECT ID FROM $wpdb->posts WHERE post_type = 'companies' AND post_status = 'publish' AND post_name = '$urlCompSlug'");
		
	if(isset($comp_DB_ID_obj[0])){
		$comp_DB_ID = $comp_DB_ID_obj[0]->ID;
		
		$DBcomp_agentEmails = get_metadata('post',$comp_DB_ID,'company-agentEmail',true);
				
		$logged_useremailObj = $wpdb->get_results("SELECT user_email FROM $wpdb->users WHERE ID = '$curUserID'");
		
		$agent_authCheck = array_search($logged_useremailObj[0]->user_email, isset($DBcomp_agentEmails[0]) ? $DBcomp_agentEmails : array());
		
//$agent_authCheck_obj = $wpdb->get_results("SELECT ID FROM $wpdb->posts ticketposts, $wpdb->postmeta companymeta, $wpdb->postmeta agentmeta
//WHERE ticketposts.ID = companymeta.post_id
//AND ticketposts.ID = agentmeta.post_id
//AND (companymeta.meta_key = 'ticket-selectedCompany' AND companymeta.meta_value = '$comp_DB_ID')
//AND (agentmeta.meta_key = 'ticket-selectedAgent' AND agentmeta.meta_value = '$curUserID')
//AND ticketposts.post_type = 'tickets'
//AND ticketposts.post_status = 'publish' ORDER BY ticketposts.post_modified DESC");
//print_r($agent_authCheck_obj);
		
			if($agent_authCheck > -1){
				
				
				
		global $mtheme;	
		$company_agent_tickets_arr = show_company_agent_tickets();
		
		$agent_id = $current_user->ID;
		
		$tickets_perpage = get_metadata('post', $comp_DB_ID, 'company-tickets-perpage', true);

$recent_tickets = $wpdb->get_results("SELECT ID, post_title, post_author, post_modified FROM $wpdb->posts ticketposts, $wpdb->postmeta companymeta, $wpdb->postmeta agentmeta
WHERE ticketposts.ID = companymeta.post_id
AND ticketposts.ID = agentmeta.post_id
AND (companymeta.meta_key = 'ticket-selectedCompany' AND companymeta.meta_value = '$comp_DB_ID')
AND (agentmeta.meta_key = 'ticket-selectedAgent' AND agentmeta.meta_value = '$agent_id')
AND ticketposts.post_type = 'tickets'
AND ticketposts.post_status = 'publish' ORDER BY ticketposts.post_modified DESC LIMIT $tickets_perpage");

			
		include($mtheme.'agent-controls/agent-main.php');
				
			} //if(isset($comp_authCheck_obj[0]))
			else echo '<h1 class="error">'.__('You are not AUTHORIZED to access this company','mhelpdesk').'.</h1>';
			
		} // if(isset($comp_DB_ID_obj[0]))
		else echo '<h1 class="error">'.__('The accessed Company is not registered','mhelpdesk').'</h1>';


echo '</div>';
get_plugin_footer(); ?>