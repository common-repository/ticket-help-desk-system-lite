<?php 
	$hd_admin_settings_arr = get_option('APF_MyFirstFrom');
		
	$helpdesk_rewriterule_slug = isset($hd_admin_settings_arr['m_helpdesk_form']['helpdesk_rewriterule_slug']) && $hd_admin_settings_arr['m_helpdesk_form']['helpdesk_rewriterule_slug'] ? $hd_admin_settings_arr['m_helpdesk_form']['helpdesk_rewriterule_slug'] : 'company';

	if ( !is_user_logged_in() )
	wp_safe_redirect(site_url().'/'.$helpdesk_rewriterule_slug);
	
get_plugin_header();
echo '<div class="hdwrap">';
	
	global $wpdb;
	$urlCompSlug = get_query_var('companyname');
	$userRole = get_query_var('userrole');
	$curUserID = get_current_user_id();
	
	$comp_DB_ID_obj = $wpdb->get_results("SELECT ID FROM $wpdb->posts WHERE post_type = 'companies' AND post_status = 'publish' AND post_name = '$urlCompSlug'");
		
		
		if(isset($comp_DB_ID_obj[0])){
		$comp_DB_ID = $comp_DB_ID_obj[0]->ID;
		
		$compCustomerBlocked_DB_arr = get_metadata('post', $comp_DB_ID, 'company-customers-blocked', true);
		
		$compCustomerOpened_DB_arr = get_metadata('post', $comp_DB_ID, 'company-customers-opened', true);

		
		if(!isset($compCustomerBlocked_DB_arr[$curUserID])) {
			$customerID_foundIndex = array_search($curUserID, isset($compCustomerOpened_DB_arr[0]) ? $compCustomerOpened_DB_arr : array());	
					if($customerID_foundIndex > -1) {
						
$ticketsperpage = get_metadata('post', $comp_DB_ID, 'company-tickets-perpage', true);
$ticketsperpage = $ticketsperpage > 0 ? $ticketsperpage : 10;
$viewedPageNum = isset($_GET['pagenumber']) ? sanitize_text_field($_GET['pagenumber']) : 1 ;
$recordShow = ($ticketsperpage * $viewedPageNum) - $ticketsperpage;
						
$recent_tickets = $wpdb->get_results("SELECT ID, post_title, post_author, post_modified FROM $wpdb->posts ticketposts, $wpdb->postmeta companymeta, $wpdb->postmeta customermeta
WHERE ticketposts.ID = companymeta.post_id AND ticketposts.ID = customermeta.post_id
AND (companymeta.meta_key = 'ticket-selectedCompany' AND companymeta.meta_value = '$comp_DB_ID')
AND (customermeta.meta_key = 'ticket-authorityID' AND customermeta.meta_value = '$curUserID')
AND ticketposts.post_type = 'tickets'
AND ticketposts.post_status = 'publish' ORDER BY ticketposts.post_modified DESC LIMIT $recordShow, $ticketsperpage");

		
		global $mtheme;
		$company_customer_tickets_arr = show_company_customer_tickets();
		include($mtheme.'customer-controls/customer-main.php');

					} // if($customerID_foundIndex > -1)
					else echo '<h1 class="error">You are not AUTHORIZED to access this '.$urlCompSlug.' company <br /> as '.$userRole.'.</h1>';
			} //if(!isset($compCustomerBlocked_DB_arr[$curUserID]))
			else echo '<h1 class="error">'.__('You are currently FREEZED! in this ','mhelpdesk').$urlCompSlug.' '.__($helpdesk_rewriterule_slug,'mhelpdesk').'
			<br />'.__('Please Contact to your admin/agent in this regard','mhelpdesk').'.</h1>';
			
		} // if(isset($comp_DB_ID_obj[0]))
		else echo '<h1 class="error">'.__('The Company is not registered','mhelpdesk').'</h1>';



echo '</div>'; 
get_plugin_footer(); ?>