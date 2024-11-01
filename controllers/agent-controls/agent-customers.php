<?php 
	$hd_admin_settings_arr = get_option('APF_MyFirstFrom');
		
	$helpdesk_rewriterule_slug = isset($hd_admin_settings_arr['m_helpdesk_form']['helpdesk_rewriterule_slug']) && $hd_admin_settings_arr['m_helpdesk_form']['helpdesk_rewriterule_slug'] ? $hd_admin_settings_arr['m_helpdesk_form']['helpdesk_rewriterule_slug'] : 'company';

	if ( !is_user_logged_in() )
	wp_safe_redirect(site_url().'/'.$helpdesk_rewriterule_slug);

	$active_plugins = get_option('active_plugins',array());
	
$urlCompSlug = get_query_var('companyname');
$userRole = get_query_var('userrole');
$curUserID = get_current_user_id();
global $wpdb,$global_customer_block_permission;

$global_customer_block_permission = 'true';


$comp_DB_ID_obj = $wpdb->get_results("SELECT ID FROM $wpdb->posts WHERE post_type = 'companies' AND post_status = 'publish' AND post_name = '$urlCompSlug'");

	if(isset($_POST['company-customerUpdateBtn']) && wp_verify_nonce( $_POST['company-customerUpdateBtn'], 'customerUpdateBtn' ) && isset($_POST['customerUpdateBtn']) ) {
	
	
	$comp_DB_ID = $comp_DB_ID_obj[0]->ID;
	
	$customer_id_GET = preg_replace('/[^0-9]/','',$_GET['id']);
	$compCustomerBlocked_DB_arr = get_metadata('post', $comp_DB_ID, 'company-customers-blocked', true);
if(array_search('hd-wc-extenstion/index_hd_wc_extention.php',$active_plugins)!== false)
	$global_customer_block_permission = apply_filters('customer_block_permission_wc_filter',$comp_DB_ID,$global_customer_block_permission);
		
	if($global_customer_block_permission == 'true'){
		if($_POST['accessStatus'] == 'Blocked'){
				if(isset($compCustomerBlocked_DB_arr[$customer_id_GET]))
					$compCustomerBlocked_DB_arr[$customer_id_GET] = sanitize_text_field($_POST['blockedText']); // update the blocked remarks/ comments
				else $compCustomerBlocked_DB_arr[$customer_id_GET] = sanitize_text_field($_POST['blockedText']); // newly blocked remarks
		update_metadata('post', $comp_DB_ID, 'company-customers-blocked',$compCustomerBlocked_DB_arr);
		
//			if($_POST['deleteAuthorTickets'] == 'yes'){
//				
//				$tickDelete_IDs_objs = $wpdb->get_results("SELECT ID FROM $wpdb->posts ticketposts, $wpdb->postmeta companymeta WHERE ticketposts.ID = companymeta.post_id
//AND ticketposts.post_author = '$customer_id_GET'
//AND (companymeta.meta_key = 'ticket-selectedCompany' AND companymeta.meta_value = '$comp_DB_ID')
//AND ticketposts.post_type = 'tickets'
//AND ticketposts.post_status = 'publish'");
//
//if(isset($tickDelete_IDs_objs[0])){
//	foreach($tickDelete_IDs_objs as $tickDelete_IDs_obj)
//		wp_trash_post($tickDelete_IDs_obj->ID);
//} // if(isset($tickDelete_IDs_objs[0]))
//
//			} // if($_POST['deleteAuthorTickets'] == 'yes')
		
		 
		} // if($_POST['accessStatus'] == 'Blocked')
		else{
			unset($compCustomerBlocked_DB_arr[$customer_id_GET]);
			update_metadata('post', $comp_DB_ID, 'company-customers-blocked', $compCustomerBlocked_DB_arr);
		} // ELSE of   if($_POST['accessStatus'] == 'Blocked')
	}// if($global_customer_block_permission == 'true'){
			
			
	wp_safe_redirect("../customers/ />" );
	} // if(isset($_POST['company-customerUpdateBtn']) && wp_verify_nonce( $_POST['company-customerUpdateBtn'], 'customerUpdateBtn' )


get_plugin_header();
echo '<div class="hdwrap">';

	if(isset($comp_DB_ID_obj[0])){
		
		$comp_DB_ID = $comp_DB_ID_obj[0]->ID;

		$DBcomp_agentEmails = get_metadata('post',$comp_DB_ID,'company-agentEmail',true);
				
		$logged_useremailObj = $wpdb->get_results("SELECT user_email FROM $wpdb->users WHERE ID = '$curUserID'");
		
		$agent_authCheck = array_search($logged_useremailObj[0]->user_email, isset($DBcomp_agentEmails[0]) ? $DBcomp_agentEmails : array());

			if($agent_authCheck > -1){
				$ticketsperpage = get_metadata('post', $comp_DB_ID, 'company-tickets-perpage', true);
				$ticketsperpage = $ticketsperpage ? $ticketsperpage : 10;
				$viewedPageNum = isset($_GET['pagenumber']) ? sanitize_text_field($_GET['pagenumber']) : 1;
				$recordShow = ($ticketsperpage * $viewedPageNum) - $ticketsperpage;
		
		
				$compCustomerBlocked_DB_arr = get_metadata('post', $comp_DB_ID, 'company-customers-blocked', true);
		
				$compCustomerOpened_DB_arr = get_metadata('post', $comp_DB_ID, 'company-customers-opened', true);

				$company_agent_tickets_arr = show_company_agent_tickets();
		
if(!isset($_GET['action'])){

// agent's customers: Tickets	
$tickets = $wpdb->get_results("SELECT ID, post_author FROM $wpdb->posts ticketposts, $wpdb->postmeta companymeta, $wpdb->postmeta agentmeta
WHERE ticketposts.ID = companymeta.post_id
AND ticketposts.ID = agentmeta.post_id
AND (companymeta.meta_key = 'ticket-selectedCompany' AND companymeta.meta_value = '$comp_DB_ID')
AND (agentmeta.meta_key = 'ticket-selectedAgent' AND agentmeta.meta_value = '$curUserID')
AND ticketposts.post_type = 'tickets'
AND ticketposts.post_status = 'publish' GROUP BY post_author ORDER BY ticketposts.post_modified DESC");
		

		global $mtheme;
		include($mtheme.'agent-controls/agent-customers.php');
				
}



$customer_id_GET = preg_replace('/[^0-9]/','',isset($_GET['id']) ? $_GET['id'] : 0);
$relevantCID_foundIndex = array_search($customer_id_GET, isset($compCustomerOpened_DB_arr[0]) ? $compCustomerOpened_DB_arr : array());
	
//////////////////////////////
// View Customers Tickets
//////////////////////////////
if(isset($_GET['action']) && $_GET['action'] == 'customer_tickets'){

	$customer_obj = get_user_by('id',sanitize_text_field($_GET['id']));
	
	//print_r($customer_);
	
	if($relevantCID_foundIndex > -1 ){

$tickets = $wpdb->get_results("SELECT ID, post_title, post_modified FROM $wpdb->posts ticketposts, $wpdb->postmeta companymeta, $wpdb->postmeta agentmeta
WHERE ticketposts.ID = companymeta.post_id
AND ticketposts.ID = agentmeta.post_id
AND ticketposts.post_author = '$customer_id_GET'
AND (companymeta.meta_key = 'ticket-selectedCompany' AND companymeta.meta_value = '$comp_DB_ID')
AND (agentmeta.meta_key = 'ticket-selectedAgent' AND agentmeta.meta_value = '$curUserID')
AND ticketposts.post_type = 'tickets'
AND ticketposts.post_status = 'publish' ORDER BY ticketposts.post_modified DESC LIMIT $recordShow, $ticketsperpage");


$pages = $wpdb->get_results("SELECT ID, post_title, post_modified FROM $wpdb->posts ticketposts, $wpdb->postmeta companymeta, $wpdb->postmeta agentmeta
WHERE ticketposts.ID = companymeta.post_id
AND ticketposts.ID = agentmeta.post_id
AND ticketposts.post_author = '$customer_id_GET'
AND (companymeta.meta_key = 'ticket-selectedCompany' AND companymeta.meta_value = '$comp_DB_ID')
AND (agentmeta.meta_key = 'ticket-selectedAgent' AND agentmeta.meta_value = '$curUserID')
AND ticketposts.post_type = 'tickets'
AND ticketposts.post_status = 'publish'");

$pages = count($pages) / (int)$ticketsperpage;
if($pages > 0) if(is_float($pages)) $pages = ((int)$pages +1);


		
		global $mtheme;
		include($mtheme.'agent-controls/agent-customers.php');
				
	} // if($relevantCID_foundIndex > -1 )
	else echo '<h1 >Invalid ID</h1>';

} // if($_GET['action'] == 'customer_tickets'){ 
		
		



//////////////////////////////
// View Customers Tickets
//////////////////////////////
if(isset($_GET['action']) && $_GET['action'] == 'edit'){

	if($relevantCID_foundIndex > -1 ){
		$customer_displaynameObj = $wpdb->get_results("SELECT * FROM $wpdb->users WHERE ID = '$customer_id_GET'");

		
		global $mtheme;
		include($mtheme.'agent-controls/agent-customers.php');
				
	} // if($relevantCID_foundIndex > -1 )
	else echo '<h1 >Invalid ID</h1>';

} // if($_GET['action'] == 'customer_tickets'){ 




	

			} // if($agent_authCheck > -1)
			else echo '<h1 >You are not AUTHORIZED to access this page.</h1>';
			
	} // if(isset($comp_DB_ID_obj[0]))
	else echo '<h1 >This '.$urlCompSlug.' Company is not registered</h1>';


echo '</div>';
get_plugin_footer(); ?>