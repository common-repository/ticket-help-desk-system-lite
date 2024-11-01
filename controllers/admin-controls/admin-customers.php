<?php 
	$hd_admin_settings_arr = get_option('APF_MyFirstFrom');
		
	$helpdesk_rewriterule_slug = isset($hd_admin_settings_arr['m_helpdesk_form']['helpdesk_rewriterule_slug']) && $hd_admin_settings_arr['m_helpdesk_form']['helpdesk_rewriterule_slug'] ? $hd_admin_settings_arr['m_helpdesk_form']['helpdesk_rewriterule_slug'] : 'company';
	if ( !is_user_logged_in() )
	wp_safe_redirect(site_url().'/'.$helpdesk_rewriterule_slug);

$urlCompSlug = get_query_var('companyname');
$userRole = get_query_var('userrole');
$curUserID = get_current_user_id();
$active_plugins = get_option('active_plugins',array());



global $wpdb,$global_customer_block_permission;

$global_customer_block_permission = 'true';


	if(isset($_POST['company-customerUpdateBtn']) && wp_verify_nonce( $_POST['company-customerUpdateBtn'], 'customerUpdateBtn' ) && isset($_POST['customerUpdateBtn']) ) {
	
	$comp_DB_ID_obj = $wpdb->get_results("SELECT ID FROM $wpdb->posts WHERE post_type = 'companies' AND post_status = 'publish' AND post_name = '$urlCompSlug'");
	$comp_DB_ID = $comp_DB_ID_obj[0]->ID;
	
	$customer_id_GET = preg_replace('/[^0-9]/','',isset($_GET['id']) ? $_GET['id'] : 0);
	$compCustomerBlocked_DB_arr = get_metadata('post', $comp_DB_ID, 'company-customers-blocked', true);
if(array_search('hd-wc-extenstion/index_hd_wc_extention.php',$active_plugins)!== false)	
	$global_customer_block_permission = apply_filters('customer_block_permission_wc_filter',$comp_DB_ID,$global_customer_block_permission);
	
	if($global_customer_block_permission == 'true'){
		if($_POST['accessStatus'] == 'Blocked'){
				if(isset($compCustomerBlocked_DB_arr[$customer_id_GET]))
					$compCustomerBlocked_DB_arr[$customer_id_GET] = sanitize_text_field($_POST['blockedText']); // update the blocked remarks/ comments
				else $compCustomerBlocked_DB_arr[$customer_id_GET] = sanitize_text_field($_POST['blockedText']); // newly blocked remarks
		update_metadata('post', $comp_DB_ID, 'company-customers-blocked',$compCustomerBlocked_DB_arr);
		$message.='<div class="alert alert-success">Customer Blocked</div>';
			if($_POST['deleteAuthorTickets'] == 'yes'){
				
$tickDelete_IDs_objs = $wpdb->get_results("SELECT ID FROM $wpdb->posts ticketposts, $wpdb->postmeta companymeta WHERE ticketposts.ID = companymeta.post_id
AND ticketposts.post_author = '$customer_id_GET'
AND (companymeta.meta_key = 'ticket-selectedCompany' AND companymeta.meta_value = '$comp_DB_ID')
AND ticketposts.post_type = 'tickets'
AND ticketposts.post_status = 'publish'");

if(isset($tickDelete_IDs_objs[0])){
	foreach($tickDelete_IDs_objs as $tickDelete_IDs_obj)
		wp_trash_post($tickDelete_IDs_obj->ID);
} // if(isset($tickDelete_IDs_objs[0]))

			} // if($_POST['deleteAuthorTickets'] == 'yes')
		
		 
		} // if($_POST['accessStatus'] == 'Blocked')
		else{
			unset($compCustomerBlocked_DB_arr[$customer_id_GET]);
			update_metadata('post', $comp_DB_ID, 'company-customers-blocked', $compCustomerBlocked_DB_arr);
		} // ELSE of   if($_POST['accessStatus'] == 'Blocked')
	} // if($global_customer_block_permission == 'true')
			
			
	wp_safe_redirect('');
	} // if(isset($_POST['company-customerUpdateBtn']) && wp_verify_nonce( $_POST['company-customerUpdateBtn'], 'customerUpdateBtn' )




get_plugin_header();
echo '<div class="hdwrap">';

$comp_DB_ID_obj = $wpdb->get_results("SELECT ID FROM $wpdb->posts WHERE post_type = 'companies' AND post_status = 'publish' AND post_name = '$urlCompSlug'");

	if(isset($comp_DB_ID_obj[0])){
		
		$comp_DB_ID = $comp_DB_ID_obj[0]->ID;
		
$comp_authCheck_obj = $wpdb->get_results("SELECT post_author FROM $wpdb->posts companyposts, $wpdb->postmeta companymeta
WHERE companyposts.ID = companymeta.post_id  AND companyposts.post_name = '$urlCompSlug'
AND companyposts.post_type = 'companies'
AND companyposts.post_status = 'publish'");

//print_r($comp_authCheck_obj);
		
			if(isset($comp_authCheck_obj[0]))
				if($comp_authCheck_obj[0]->post_author == $curUserID){

			$ticketsperpage = get_metadata('post', $comp_DB_ID, 'company-tickets-perpage', true);
$ticketsperpage = $ticketsperpage ? $ticketsperpage : 10;
			$viewedPageNum = (isset($_GET['pagenumber']) ? sanitize_text_field($_GET['pagenumber']) : 1 );
			
			$compCustomerBlocked_DB_arr = get_metadata('post', $comp_DB_ID, 'company-customers-blocked', true);
		
			$compCustomerOpened_DB_arr = get_metadata('post', $comp_DB_ID, 'company-customers-opened', true);
			




/////////////////////////////
//     Edit Customers
/////////////////////////////
if(isset($_GET['action']) && $_GET['action'] == 'edit'){ 
$customer_id_GET = preg_replace('/[^0-9]/','',$_GET['id']);
$relevantCID_foundIndex = array_search($customer_id_GET, isset($compCustomerOpened_DB_arr[0]) ? $compCustomerOpened_DB_arr : array());
	if($relevantCID_foundIndex > -1 ){
	$customer_displaynameObj = $wpdb->get_results("SELECT * FROM $wpdb->users WHERE ID = '$customer_id_GET'");
	}
	else echo '<h1 >'.__('Irrelevant Request','mhelpdesk').'</h1>';


} // if($_GET['action'] == 'edit')


//////////////////////////////
// View Customers Tickets
//////////////////////////////
if(isset($_GET['action']) && $_GET['action'] == 'customer_tickets'){ 
$customer_id_GET = preg_replace('/[^0-9]/','',$_GET['id']);

$url_action_value = sanitize_text_field($_GET['action']);

$relevantCID_foundIndex = array_search($customer_id_GET, isset($compCustomerOpened_DB_arr[0]) ? $compCustomerOpened_DB_arr : array());
//print_r($relevantCID_foundIndex);

	if($relevantCID_foundIndex > -1 ){

$ticketsperpage = get_metadata('post', $comp_DB_ID, 'company-tickets-perpage', true);
$ticketsperpage = $ticketsperpage > 0 ? $ticketsperpage : 10;
$viewedPageNum = isset($_GET['pagenumber']) ? sanitize_text_field($_GET['pagenumber']) : 1 ;
$recordShow = ($ticketsperpage * $viewedPageNum) - $ticketsperpage;

	

$tickets = $wpdb->get_results("SELECT ID, post_title, post_modified FROM $wpdb->posts ticketposts, $wpdb->postmeta companymeta
WHERE ticketposts.ID = companymeta.post_id
AND ticketposts.post_author = '$customer_id_GET'
AND (companymeta.meta_key = 'ticket-selectedCompany' AND companymeta.meta_value = '$comp_DB_ID')
AND ticketposts.post_type = 'tickets'
AND ticketposts.post_status = 'publish' ORDER BY ticketposts.post_modified DESC LIMIT $recordShow, $ticketsperpage");

$pages = $wpdb->get_results("SELECT ID FROM $wpdb->posts ticketposts, $wpdb->postmeta companymeta
WHERE ticketposts.ID = companymeta.post_id
AND ticketposts.post_author = '$customer_id_GET'
AND (companymeta.meta_key = 'ticket-selectedCompany' AND companymeta.meta_value = '$comp_DB_ID')
AND ticketposts.post_type = 'tickets'
AND ticketposts.post_status = 'publish'");

$pages = count($pages) / (int)$ticketsperpage;
if($pages > 0) if(is_float($pages)) $pages = ((int)$pages +1);

	$customer_displaynameObj = $wpdb->get_results("SELECT display_name FROM $wpdb->users WHERE ID = '$customer_id_GET'");
	
	} // if($relevantCID_foundIndex > -1 )
	else echo '<h1 >'.__('Irrelevant Request','mhelpdesk').'</h1>';
} // if($_GET['action'] == 'customer_tickets')








/////////////////////////////
//     Delete Customers
/////////////////////////////
if(isset($_GET['action']) && $_GET['action'] == 'delete'){ 
$customer_id_GET = preg_replace('/[^0-9]/','',$_GET['id']);
$relevantCID_foundIndex = array_search($customer_id_GET, isset($compCustomerOpened_DB_arr[0]) ? $compCustomerOpened_DB_arr : array());

	if($relevantCID_foundIndex > -1 ){				
		unset($compCustomerOpened_DB_arr[$relevantCID_foundIndex]);
		rsort($compCustomerOpened_DB_arr);
		update_metadata('post', $comp_DB_ID, 'company-customers-opened',$compCustomerOpened_DB_arr); 

$tickDelete_IDs_objs = $wpdb->get_results("SELECT ID FROM $wpdb->posts ticketposts, $wpdb->postmeta companymeta WHERE ticketposts.ID = companymeta.post_id
AND ticketposts.post_author = '$customer_id_GET'
AND (companymeta.meta_key = 'ticket-selectedCompany' AND companymeta.meta_value = '$comp_DB_ID')
AND ticketposts.post_type = 'tickets'
AND ticketposts.post_status = 'publish'");

				if(isset($tickDelete_IDs_objs[0])){
					foreach($tickDelete_IDs_objs as $tickDelete_IDs_obj)
						wp_trash_post($tickDelete_IDs_obj->ID);
				} // if(isset($tickDelete_IDs_objs[0]))
				echo "<meta http-equiv=refresh content=0;url='../customers' />";

	} //if($relevantCID_foundIndex > -1 )
} // if($_GET['action'] == 'delete')





			
				global $mtheme;
				$company_admin_tickets_arr = show_company_admin_tickets();
				include($mtheme.'admin-controls/admin-customers.php');	






			} //if(isset($comp_authCheck_obj[0]))
			else echo '<h1 >'.__('You are not AUTHORIZED to access this page','mhelpdesk').'</h1>';
			
	} // if(isset($comp_DB_ID_obj[0]))
	else echo '<h1 >'.__('The accessed Company is not registered yet!','mhelpdesk').'</h1>';
echo '</div>';
get_plugin_footer(); ?>