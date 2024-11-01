<?php 
	$hd_admin_settings_arr = get_option('APF_MyFirstFrom');
		
	$helpdesk_rewriterule_slug = isset($hd_admin_settings_arr['m_helpdesk_form']['helpdesk_rewriterule_slug']) && $hd_admin_settings_arr['m_helpdesk_form']['helpdesk_rewriterule_slug'] ? $hd_admin_settings_arr['m_helpdesk_form']['helpdesk_rewriterule_slug'] : 'company';

	if ( !is_user_logged_in() )
	wp_safe_redirect(site_url().'/'.$helpdesk_rewriterule_slug);


$active_plugins = get_option('active_plugins',array());
$urlCompSlug = get_query_var('companyname');
$userRole = get_query_var('userrole');
$curUserID = get_current_user_id();
global $wpdb,$global_customer_rating_permission,$global_notification_permission;

	$global_notification_permission = 'true';

	$comp_DB_ID_obj = $wpdb->get_results("SELECT ID,post_title FROM $wpdb->posts WHERE post_type = 'companies' AND post_status = 'publish' AND post_name = '$urlCompSlug'");
	$comp_name = $comp_DB_ID_obj[0]->post_title;
	$comp_DB_ID = $comp_DB_ID_obj[0]->ID;
	$ticket_id = preg_replace('/[^0-9]/','',isset($_GET['id']) ? $_GET['id'] : 0);
	$tickTitle = get_the_title($ticket_id);
	$ticketObj = get_post($ticket_id);
	$tickOwnerID = $ticketObj->post_author;
	
	$global_customer_rating_permission = 'true';

if(array_search('hd-wc-extenstion/index_hd_wc_extention.php',$active_plugins)!== false)
	$global_customer_rating_permission = apply_filters('customer_rating_permission_wc_filter',$comp_DB_ID,$global_customer_rating_permission);
	

	
			if(isset($_POST['ticket-adminUpdateActionBtn']) && wp_verify_nonce( $_POST['ticket-adminUpdateActionBtn'], 'adminUpdateActionBtn' ) && 
			isset($_POST['adminUpdateActionBtn']) ) {
				
	if(array_search('hd-wc-extenstion/index_hd_wc_extention.php',$active_plugins)!== false)
		$global_notification_permission = apply_filters('notification_permission_wc_filter',$comp_postID,$global_notification_permission);		
				
do_action( 'ticket_updated', array('companyID'=>$comp_DB_ID,'company_name'=>$comp_name,'ticketTitle'=>$tickTitle,'ticketID'=>$ticket_id,'tickOwnerID'=>$tickOwnerID) );	
		
				if(isset($_POST['ticketActionStatus']))
					update_metadata('post',$ticket_id,"ticket-action-status",'Solved');
				else
					update_metadata('post',$ticket_id,"ticket-action-status",'Un-Answered');
				
				if(isset($_POST['rating']) && $global_customer_rating_permission == 'true')
					update_metadata('post',$ticket_id,"ticket-rating",preg_replace('/[^0-9]/','',sanitize_text_field($_POST['rating'])));
			
				if(isset($_POST['adminResponse']) && $_POST['adminResponse'] != '' ){
					$newComments = array(
									'comment_post_ID' 		=> $ticket_id,
									'comment_author' 		=> get_userdata($curUserID)->display_name,
									'comment_author_email' 	=> get_userdata($curUserID)->user_email,
									'comment_content' 		=> strip_tags(sanitize_text_field($_POST['adminResponse']),'<blockquote><h1><h2><h3><h4><h5><h6><div><p><ul><ol><li><span><br><pre><sup><sub><u><strong><i><em><s>'),
									'comment_type' 			=> 'comment',
									'comment_approved' 		=> 1,
								);
					$newCommentID = wp_insert_comment($newComments);
				}
				
						$supportedDoc_posted = isset($_FILES["supportedDoc"]["name"]) ? sanitize_file_name($_FILES["supportedDoc"]["name"]) : "";
						//print_r($supportedDoc_posted);
						if(!empty($supportedDoc_posted))
						$supportedDoc_id = media_handle_upload("supportedDoc",0 );
						//print_r($supportedDoc_id);
						if(isset($supportedDoc_id) && (is_wp_error($supportedDoc_id) == false))
						update_metadata('comment', $newCommentID, 'ticket-commentAttachment', $supportedDoc_id);

						$updateThisPost = array(
										'ID' => $ticket_id,
										'post_type' => 'tickets',
										'post_status' => 'publish'
										);
						
						wp_update_post($updateThisPost);

				
		if(isset($message))
		$message.='<div class="alert alert-success bg-success">Ticket Updated</div>';
		else
		$message ='<div class="alert alert-success bg-success">Ticket Updated</div>';
		} // if(isset($_POST['ticket-adminUpdateActionBtn']) && wp_verify_nonce( $_POST['ticket-adminUpdateActionBtn'], 'adminUpdateActionBtn' )




/////////////////
/// Rating Code
////////////////
if(isset($_POST['ratingBtn']) && $global_customer_rating_permission == 'true')
update_metadata('post',$ticket_id,"ticket-rating",preg_replace('/[^0-9]/','',sanitize_text_field($_POST['rating'])));





get_plugin_header();
echo '<div class="hdwrap">';
//global $wpdb;
//$test = array(
//				'ID' => 320,
//				'post_type' => 'tickets',
//				'post_status' => 'publish'
//				);
//var_dump($test);
//
//wp_update_post($test);

function showCustomer_leftmenu(){ 
global $wpdb;
$curUserID = get_current_user_id();

  
$urlCompSlug = get_query_var('companyname'); 

$comp_DB_ID_obj = $wpdb->get_results("SELECT ID FROM $wpdb->posts WHERE post_type = 'companies' AND post_status = 'publish' AND post_name = '$urlCompSlug'");

$comp_DB_ID = $comp_DB_ID_obj[0]->ID;


$tickAll_DB_ID_objs = $wpdb->get_results("SELECT ID FROM $wpdb->posts ticketposts, $wpdb->postmeta companymeta, $wpdb->postmeta customermeta
WHERE ticketposts.ID = companymeta.post_id
AND ticketposts.ID = customermeta.post_id
AND (companymeta.meta_key = 'ticket-selectedCompany' AND companymeta.meta_value = '$comp_DB_ID')
AND (customermeta.meta_key = 'ticket-authorityID' AND customermeta.meta_value = '$curUserID')
AND ticketposts.post_type = 'tickets'
AND ticketposts.post_status = 'publish' ORDER BY ticketposts.post_modified DESC");


$tickOpened_DB_ID_objs = $wpdb->get_results("SELECT ID FROM $wpdb->posts ticketposts, $wpdb->postmeta companymeta, $wpdb->postmeta customermeta, $wpdb->postmeta ticketStatusmeta
WHERE ticketposts.ID = companymeta.post_id
AND ticketposts.ID = ticketStatusmeta.post_id
AND ticketposts.ID = customermeta.post_id
AND (companymeta.meta_key = 'ticket-selectedCompany' AND companymeta.meta_value = '$comp_DB_ID')
AND (customermeta.meta_key = 'ticket-authorityID' AND customermeta.meta_value = '$curUserID')
AND (ticketStatusmeta.meta_key = 'ticket-status' AND ticketStatusmeta.meta_value = 'Opened')
AND ticketposts.post_type = 'tickets'
AND ticketposts.post_status = 'publish' ORDER BY ticketposts.post_modified DESC");


$tickClosed_DB_ID_objs = $wpdb->get_results("SELECT ID FROM $wpdb->posts ticketposts, $wpdb->postmeta companymeta, $wpdb->postmeta customermeta, $wpdb->postmeta ticketStatusmeta
WHERE ticketposts.ID = companymeta.post_id
AND ticketposts.ID = ticketStatusmeta.post_id
AND ticketposts.ID = customermeta.post_id
AND (companymeta.meta_key = 'ticket-selectedCompany' AND companymeta.meta_value = '$comp_DB_ID')
AND (customermeta.meta_key = 'ticket-authorityID' AND customermeta.meta_value = '$curUserID')
AND (ticketStatusmeta.meta_key = 'ticket-status' AND ticketStatusmeta.meta_value = 'Closed')
AND ticketposts.post_type = 'tickets'
AND ticketposts.post_status = 'publish' ORDER BY ticketposts.post_modified DESC");


$tickUnanswered_DB_ID_objs = $wpdb->get_results("SELECT ID FROM $wpdb->posts ticketposts, $wpdb->postmeta companymeta, $wpdb->postmeta customermeta, $wpdb->postmeta ticketStatusmeta
WHERE ticketposts.ID = companymeta.post_id
AND ticketposts.ID = ticketStatusmeta.post_id
AND ticketposts.ID = customermeta.post_id
AND (companymeta.meta_key = 'ticket-selectedCompany' AND companymeta.meta_value = '$comp_DB_ID')
AND (customermeta.meta_key = 'ticket-authorityID' AND customermeta.meta_value = '$curUserID')
AND (ticketStatusmeta.meta_key = 'ticket-action-status' AND ticketStatusmeta.meta_value = 'Un-Answered')
AND ticketposts.post_type = 'tickets'
AND ticketposts.post_status = 'publish' ORDER BY ticketposts.post_modified DESC");


$tickAanswered_DB_ID_objs = $wpdb->get_results("SELECT ID FROM $wpdb->posts ticketposts, $wpdb->postmeta companymeta, $wpdb->postmeta customermeta, $wpdb->postmeta ticketStatusmeta
WHERE ticketposts.ID = companymeta.post_id
AND ticketposts.ID = ticketStatusmeta.post_id
AND ticketposts.ID = customermeta.post_id
AND (companymeta.meta_key = 'ticket-selectedCompany' AND companymeta.meta_value = '$comp_DB_ID')
AND (customermeta.meta_key = 'ticket-authorityID' AND customermeta.meta_value = '$curUserID')
AND (ticketStatusmeta.meta_key = 'ticket-action-status' AND ticketStatusmeta.meta_value = 'Answered')
AND ticketposts.post_type = 'tickets'
AND ticketposts.post_status = 'publish' ORDER BY ticketposts.post_modified DESC");

$tickSolved_DB_ID_objs = $wpdb->get_results("SELECT ID FROM $wpdb->posts ticketposts, $wpdb->postmeta companymeta, $wpdb->postmeta customermeta, $wpdb->postmeta ticketStatusmeta
WHERE ticketposts.ID = companymeta.post_id
AND ticketposts.ID = ticketStatusmeta.post_id
AND ticketposts.ID = customermeta.post_id
AND (companymeta.meta_key = 'ticket-selectedCompany' AND companymeta.meta_value = '$comp_DB_ID')
AND (customermeta.meta_key = 'ticket-authorityID' AND customermeta.meta_value = '$curUserID')
AND (ticketStatusmeta.meta_key = 'ticket-action-status' AND ticketStatusmeta.meta_value = 'Solved')
AND ticketposts.post_type = 'tickets'
AND ticketposts.post_status = 'publish' ORDER BY ticketposts.post_modified DESC");

?>
<div class="msidemenu">
<p><a href="<?php echo get_permalink($comp_DB_ID)."customer/tickets/?pagenumber=1/"; ?>">
	<?php _e('All Tickets','mhelpdesk');?> <span class="ticketcounter_right">(<?php echo count($tickAll_DB_ID_objs); ?>)</span></a></p>
<p><a href="?action=opened&pagenumber=1"><?php _e('Opened','mhelpdesk');?><span class="ticketcounter_right">(<?php echo count($tickOpened_DB_ID_objs); ?>)</span></a></p>
<p><a href="?action=closed&pagenumber=1"><?php _e('Closed','mhelpdesk');?><span class="ticketcounter_right">(<?php echo count($tickClosed_DB_ID_objs); ?>)</span></a></p>
<p><a href="?action=unanswered&pagenumber=1"><?php _e('Un-Answered','mhelpdesk');?>
	<span class="ticketcounter_right">(<?php echo count($tickUnanswered_DB_ID_objs); ?>)</span></a></p>
<p><a href="?action=answered&pagenumber=1"><?php _e('Answered','mhelpdesk');?><span class="ticketcounter_right">(<?php echo count($tickAanswered_DB_ID_objs); ?>)</span></a></p>
<p><a href="?action=solved&pagenumber=1"><?php _e('Solved','mhelpdesk');?><span class="ticketcounter_right">(<?php echo count($tickSolved_DB_ID_objs); ?>)</span></a></p>
</div>
<?php
} // function showAdminAgent_leftmenu()




	$comp_DB_ID_obj = $wpdb->get_results("SELECT ID FROM $wpdb->posts WHERE post_type = 'companies' AND post_status = 'publish' AND post_name = '$urlCompSlug'");
	
	if(isset($comp_DB_ID_obj[0])){
		
	$comp_DB_ID = $comp_DB_ID_obj[0]->ID;
	


		$compCustomerBlocked_DB_arr = get_metadata('post', $comp_DB_ID, 'company-customers-blocked', true);
		
		$compCustomerOpened_DB_arr = get_metadata('post', $comp_DB_ID, 'company-customers-opened', true);

		
		if(!isset($compCustomerBlocked_DB_arr[$curUserID])) {
			$customerID_foundIndex = array_search($curUserID, isset($compCustomerOpened_DB_arr[0]) ? $compCustomerOpened_DB_arr : array());	
					if($customerID_foundIndex > -1) {
				$ticketsperpage = get_metadata('post', $comp_DB_ID, 'company-tickets-perpage', true);
				$ticketsperpage = $ticketsperpage ? $ticketsperpage : 10;
				$viewedPageNum = isset($_GET['pagenumber']) ? (int)sanitize_text_field($_GET['pagenumber']) : 1;
				$recordShow = ($ticketsperpage * $viewedPageNum) - $ticketsperpage;
				
				$company_customer_tickets_arr = show_company_customer_tickets();
		//$selected_group = isset($_GET['group']) && $_GET['group']!='' ? $_GET['group'] : 'General';

if(!isset($_GET['action'])){
	if(isset($_GET['group']) && $_GET['group']!=''){
		$selected_group = sanitize_text_field($_GET['group']);
		$tickets = $wpdb->get_results("SELECT ID, post_title, post_author, post_modified FROM $wpdb->posts ticketposts, $wpdb->postmeta companymeta, $wpdb->postmeta customermeta,
		$wpdb->postmeta s_dept WHERE ticketposts.ID = companymeta.post_id AND ticketposts.ID = customermeta.post_id AND ticketposts.ID = s_dept.post_id
		AND (companymeta.meta_key = 'ticket-selectedCompany' AND companymeta.meta_value = '$comp_DB_ID')
		AND (customermeta.meta_key = 'ticket-authorityID' AND customermeta.meta_value = '$curUserID')
		AND (s_dept.meta_key = 'ticket-selected_department' AND s_dept.meta_value = '$selected_group')
		AND ticketposts.post_type = 'tickets'
		AND ticketposts.post_status = 'publish' ORDER BY ticketposts.post_modified DESC LIMIT $recordShow, $ticketsperpage");
	}
	else{
		$tickets = $wpdb->get_results("SELECT ID, post_title, post_author, post_modified FROM $wpdb->posts ticketposts, $wpdb->postmeta companymeta, $wpdb->postmeta customermeta
		WHERE ticketposts.ID = companymeta.post_id AND ticketposts.ID = customermeta.post_id
		AND (companymeta.meta_key = 'ticket-selectedCompany' AND companymeta.meta_value = '$comp_DB_ID')
		AND (customermeta.meta_key = 'ticket-authorityID' AND customermeta.meta_value = '$curUserID')
		AND ticketposts.post_type = 'tickets'
		AND ticketposts.post_status = 'publish' ORDER BY ticketposts.post_modified DESC LIMIT $recordShow, $ticketsperpage");
	}
$pages = $company_customer_tickets_arr['all_tickets'];
}


if(isset($_GET['action']) && $_GET['action'] == 'rated'){
	if(isset($_GET['group']) && $_GET['group']!=''){
		$selected_group = sanitize_text_field($_GET['group']);
		$tickets = $wpdb->get_results("SELECT ID, post_title, post_author, post_modified FROM $wpdb->posts ticketposts, $wpdb->postmeta companymeta, $wpdb->postmeta customermeta,
		$wpdb->postmeta s_dept WHERE ticketposts.ID = companymeta.post_id AND ticketposts.ID = customermeta.post_id AND ticketposts.ID = s_dept.post_id
		AND (companymeta.meta_key = 'ticket-selectedCompany' AND companymeta.meta_value = '$comp_DB_ID')
		AND (customermeta.meta_key = 'ticket-authorityID' AND customermeta.meta_value = '$curUserID')
		AND (s_dept.meta_key = 'ticket-selected_department' AND s_dept.meta_value = '$selected_group')
				AND EXISTS (
							  SELECT post_id FROM $wpdb->postmeta
							  WHERE $wpdb->postmeta.meta_key = 'ticket-rating' AND $wpdb->postmeta.meta_value > 0
							  AND ticketposts.ID = $wpdb->postmeta.post_id
							)
		AND ticketposts.post_type = 'tickets'
		AND ticketposts.post_status = 'publish' ORDER BY ticketposts.post_modified DESC LIMIT $recordShow, $ticketsperpage");
	}
	else{
		$tickets = $wpdb->get_results("SELECT ID, post_title, post_author, post_modified FROM $wpdb->posts ticketposts, $wpdb->postmeta companymeta, $wpdb->postmeta customermeta
		WHERE ticketposts.ID = companymeta.post_id AND ticketposts.ID = customermeta.post_id
		AND (companymeta.meta_key = 'ticket-selectedCompany' AND companymeta.meta_value = '$comp_DB_ID')
		AND (customermeta.meta_key = 'ticket-authorityID' AND customermeta.meta_value = '$curUserID')
				AND EXISTS (
							  SELECT post_id FROM $wpdb->postmeta
							  WHERE $wpdb->postmeta.meta_key = 'ticket-rating' AND $wpdb->postmeta.meta_value > 0
							  AND ticketposts.ID = $wpdb->postmeta.post_id
							)
		AND ticketposts.post_type = 'tickets'
		AND ticketposts.post_status = 'publish' ORDER BY ticketposts.post_modified DESC LIMIT $recordShow, $ticketsperpage");
	}
$pages = $company_customer_tickets_arr['rated_tickets'];
}


if(isset($_GET['action']) && $_GET['action'] == 'opened'){
	if(isset($_GET['group']) && $_GET['group']!=''){
		$selected_group = sanitize_text_field($_GET['group']);
		$tickets = $wpdb->get_results("SELECT ID, post_title, post_author, post_modified FROM $wpdb->posts ticketposts, $wpdb->postmeta companymeta, 
		$wpdb->postmeta ticketStatusmeta, $wpdb->postmeta customermeta, $wpdb->postmeta s_dept
		WHERE ticketposts.ID = companymeta.post_id AND ticketposts.ID = ticketStatusmeta.post_id AND ticketposts.ID = customermeta.post_id AND ticketposts.ID = s_dept.post_id
		AND (customermeta.meta_key = 'ticket-authorityID' AND customermeta.meta_value = '$curUserID')
		AND (companymeta.meta_key = 'ticket-selectedCompany' AND companymeta.meta_value = '$comp_DB_ID')
		AND (ticketStatusmeta.meta_key = 'ticket-status' AND ticketStatusmeta.meta_value = 'Opened')
		AND (s_dept.meta_key = 'ticket-selected_department' AND s_dept.meta_value = '$selected_group')
		AND ticketposts.post_type = 'tickets'
		AND ticketposts.post_status = 'publish' ORDER BY ticketposts.post_modified DESC LIMIT $recordShow, $ticketsperpage");
	}
	else{
		$tickets = $wpdb->get_results("SELECT ID, post_title, post_author, post_modified FROM $wpdb->posts ticketposts, $wpdb->postmeta companymeta, 
		$wpdb->postmeta ticketStatusmeta, $wpdb->postmeta customermeta
		WHERE ticketposts.ID = companymeta.post_id AND ticketposts.ID = ticketStatusmeta.post_id AND ticketposts.ID = customermeta.post_id
		AND (customermeta.meta_key = 'ticket-authorityID' AND customermeta.meta_value = '$curUserID')
		AND (companymeta.meta_key = 'ticket-selectedCompany' AND companymeta.meta_value = '$comp_DB_ID')
		AND (ticketStatusmeta.meta_key = 'ticket-status' AND ticketStatusmeta.meta_value = 'Opened')
		AND ticketposts.post_type = 'tickets'
		AND ticketposts.post_status = 'publish' ORDER BY ticketposts.post_modified DESC LIMIT $recordShow, $ticketsperpage");
	}

$pages = $company_customer_tickets_arr['opened_tickets'];
}



if(isset($_GET['action']) && $_GET['action'] == 'closed'){
	if(isset($_GET['group']) && $_GET['group']!=''){
		$selected_group = sanitize_text_field($_GET['group']);
		$tickets = $wpdb->get_results("SELECT ID, post_title, post_author, post_modified FROM $wpdb->posts ticketposts, $wpdb->postmeta companymeta, 
		$wpdb->postmeta ticketStatusmeta, $wpdb->postmeta customermeta, $wpdb->postmeta s_dept
		WHERE ticketposts.ID = companymeta.post_id AND ticketposts.ID = ticketStatusmeta.post_id AND ticketposts.ID = customermeta.post_id AND ticketposts.ID = s_dept.post_id
		AND (customermeta.meta_key = 'ticket-authorityID' AND customermeta.meta_value = '$curUserID')
		AND (companymeta.meta_key = 'ticket-selectedCompany' AND companymeta.meta_value = '$comp_DB_ID')
		AND (ticketStatusmeta.meta_key = 'ticket-status' AND ticketStatusmeta.meta_value = 'Closed')
		AND (s_dept.meta_key = 'ticket-selected_department' AND s_dept.meta_value = '$selected_group')
		AND ticketposts.post_type = 'tickets'
		AND ticketposts.post_status = 'publish' ORDER BY ticketposts.post_modified DESC LIMIT $recordShow, $ticketsperpage");
	}
	else{
		$tickets = $wpdb->get_results("SELECT ID, post_title, post_author, post_modified FROM $wpdb->posts ticketposts, $wpdb->postmeta companymeta, 
		$wpdb->postmeta ticketStatusmeta, $wpdb->postmeta customermeta
		WHERE ticketposts.ID = companymeta.post_id AND ticketposts.ID = ticketStatusmeta.post_id AND ticketposts.ID = customermeta.post_id
		AND (customermeta.meta_key = 'ticket-authorityID' AND customermeta.meta_value = '$curUserID')
		AND (companymeta.meta_key = 'ticket-selectedCompany' AND companymeta.meta_value = '$comp_DB_ID')
		AND (ticketStatusmeta.meta_key = 'ticket-status' AND ticketStatusmeta.meta_value = 'Closed')
		AND ticketposts.post_type = 'tickets'
		AND ticketposts.post_status = 'publish' ORDER BY ticketposts.post_modified DESC LIMIT $recordShow, $ticketsperpage");
	}
$pages = $company_customer_tickets_arr['closed_tickets'];
}



if(isset($_GET['action']) && $_GET['action'] == 'unanswered'){
	if(isset($_GET['group']) && $_GET['group']!=''){
		$selected_group = sanitize_text_field($_GET['group']);
		$tickets = $wpdb->get_results("SELECT ID, post_title, post_author, post_modified FROM $wpdb->posts ticketposts, $wpdb->postmeta companymeta, 
		$wpdb->postmeta ticketStatusmeta, $wpdb->postmeta customermeta, $wpdb->postmeta s_dept
		WHERE ticketposts.ID = companymeta.post_id AND ticketposts.ID = ticketStatusmeta.post_id AND ticketposts.ID = customermeta.post_id AND ticketposts.ID = s_dept.post_id
		AND (customermeta.meta_key = 'ticket-authorityID' AND customermeta.meta_value = '$curUserID')
		AND (companymeta.meta_key = 'ticket-selectedCompany' AND companymeta.meta_value = '$comp_DB_ID')
		AND (ticketStatusmeta.meta_key = 'ticket-action-status' AND ticketStatusmeta.meta_value = 'Un-Answered')
		AND (s_dept.meta_key = 'ticket-selected_department' AND s_dept.meta_value = '$selected_group')
		AND ticketposts.post_type = 'tickets'
		AND ticketposts.post_status = 'publish' ORDER BY ticketposts.post_modified DESC LIMIT $recordShow, $ticketsperpage");
	}
	else{
		$tickets = $wpdb->get_results("SELECT ID, post_title, post_author, post_modified FROM $wpdb->posts ticketposts, $wpdb->postmeta companymeta, 
		$wpdb->postmeta ticketStatusmeta, $wpdb->postmeta customermeta
		WHERE ticketposts.ID = companymeta.post_id AND ticketposts.ID = ticketStatusmeta.post_id AND ticketposts.ID = customermeta.post_id
		AND (customermeta.meta_key = 'ticket-authorityID' AND customermeta.meta_value = '$curUserID')
		AND (companymeta.meta_key = 'ticket-selectedCompany' AND companymeta.meta_value = '$comp_DB_ID')
		AND (ticketStatusmeta.meta_key = 'ticket-action-status' AND ticketStatusmeta.meta_value = 'Un-Answered')
		
		AND ticketposts.post_type = 'tickets'
		AND ticketposts.post_status = 'publish' ORDER BY ticketposts.post_modified DESC LIMIT $recordShow, $ticketsperpage");
	}
$pages = $company_customer_tickets_arr['unanswered_tickets'];
}


if(isset($_GET['action']) && $_GET['action'] == 'answered'){
	if(isset($_GET['group']) && $_GET['group']!=''){
		$selected_group = sanitize_text_field($_GET['group']);
		$tickets = $wpdb->get_results("SELECT ID, post_title, post_author, post_modified FROM $wpdb->posts ticketposts, $wpdb->postmeta companymeta, 
		$wpdb->postmeta ticketStatusmeta, $wpdb->postmeta customermeta, $wpdb->postmeta s_dept
		WHERE ticketposts.ID = companymeta.post_id AND ticketposts.ID = ticketStatusmeta.post_id AND ticketposts.ID = customermeta.post_id AND ticketposts.ID = s_dept.post_id
		AND (customermeta.meta_key = 'ticket-authorityID' AND customermeta.meta_value = '$curUserID')
		AND (companymeta.meta_key = 'ticket-selectedCompany' AND companymeta.meta_value = '$comp_DB_ID')
		AND (ticketStatusmeta.meta_key = 'ticket-action-status' AND ticketStatusmeta.meta_value = 'Answered')
		AND (s_dept.meta_key = 'ticket-selected_department' AND s_dept.meta_value = '$selected_group')
		AND ticketposts.post_type = 'tickets'
		AND ticketposts.post_status = 'publish' ORDER BY ticketposts.post_modified DESC LIMIT $recordShow, $ticketsperpage");
	}
	else{
		$tickets = $wpdb->get_results("SELECT ID, post_title, post_author, post_modified FROM $wpdb->posts ticketposts, $wpdb->postmeta companymeta, 
		$wpdb->postmeta ticketStatusmeta, $wpdb->postmeta customermeta
		WHERE ticketposts.ID = companymeta.post_id AND ticketposts.ID = ticketStatusmeta.post_id AND ticketposts.ID = customermeta.post_id
		AND (customermeta.meta_key = 'ticket-authorityID' AND customermeta.meta_value = '$curUserID')
		AND (companymeta.meta_key = 'ticket-selectedCompany' AND companymeta.meta_value = '$comp_DB_ID')
		AND (ticketStatusmeta.meta_key = 'ticket-action-status' AND ticketStatusmeta.meta_value = 'Answered')
		AND ticketposts.post_type = 'tickets'
		AND ticketposts.post_status = 'publish' ORDER BY ticketposts.post_modified DESC LIMIT $recordShow, $ticketsperpage");
	}
$pages = $company_customer_tickets_arr['answered_tickets'];
}


if(isset($_GET['action']) && $_GET['action'] == 'solved'){
	if(isset($_GET['group']) && $_GET['group']!=''){
		$selected_group = sanitize_text_field($_GET['group']);
		$tickets = $wpdb->get_results("SELECT ID, post_title, post_author, post_modified FROM $wpdb->posts ticketposts, $wpdb->postmeta companymeta, 
		$wpdb->postmeta ticketStatusmeta, $wpdb->postmeta customermeta, $wpdb->postmeta s_dept
		WHERE ticketposts.ID = companymeta.post_id AND ticketposts.ID = ticketStatusmeta.post_id AND ticketposts.ID = customermeta.post_id AND ticketposts.ID = s_dept.post_id
		AND (customermeta.meta_key = 'ticket-authorityID' AND customermeta.meta_value = '$curUserID')
		AND (companymeta.meta_key = 'ticket-selectedCompany' AND companymeta.meta_value = '$comp_DB_ID')
		AND (ticketStatusmeta.meta_key = 'ticket-action-status' AND ticketStatusmeta.meta_value = 'Solved')
		AND (s_dept.meta_key = 'ticket-selected_department' AND s_dept.meta_value = '$selected_group')
		AND ticketposts.post_type = 'tickets'
		AND ticketposts.post_status = 'publish' ORDER BY ticketposts.post_modified DESC LIMIT $recordShow, $ticketsperpage");
	}
	else{
		$tickets = $wpdb->get_results("SELECT ID, post_title, post_author, post_modified FROM $wpdb->posts ticketposts, $wpdb->postmeta companymeta, 
		$wpdb->postmeta ticketStatusmeta, $wpdb->postmeta customermeta
		WHERE ticketposts.ID = companymeta.post_id AND ticketposts.ID = ticketStatusmeta.post_id AND ticketposts.ID = customermeta.post_id
		AND (customermeta.meta_key = 'ticket-authorityID' AND customermeta.meta_value = '$curUserID')
		AND (companymeta.meta_key = 'ticket-selectedCompany' AND companymeta.meta_value = '$comp_DB_ID')
		AND (ticketStatusmeta.meta_key = 'ticket-action-status' AND ticketStatusmeta.meta_value = 'Solved')
		AND ticketposts.post_type = 'tickets'
		AND ticketposts.post_status = 'publish' ORDER BY ticketposts.post_modified DESC LIMIT $recordShow, $ticketsperpage");
	}
$pages = $company_customer_tickets_arr['solved_tickets'];
}




if(isset($_GET['action']) && $_GET['action'] == 'search'){

$search_string = isset($_REQUEST['search_tickets']) ? $_REQUEST['search_tickets'] : $_REQUEST['group'];
$selected_group = isset($_REQUEST['group']) ? $_REQUEST['group'] : '';
$tickets = $wpdb->get_results("SELECT ID, post_title, post_author, post_modified FROM $wpdb->posts ticketposts, $wpdb->comments commentposts, $wpdb->postmeta companymeta,$wpdb->postmeta customermeta, $wpdb->postmeta s_dept 
WHERE ticketposts.ID = commentposts.comment_post_ID AND ticketposts.ID = companymeta.post_id AND ticketposts.ID = customermeta.post_id 
AND ticketposts.ID = s_dept.post_id 
AND (customermeta.meta_key = 'ticket-authorityID' AND customermeta.meta_value = '$curUserID')
AND (companymeta.meta_key = 'ticket-selectedCompany' AND companymeta.meta_value = '$comp_DB_ID')
AND (commentposts.comment_content LIKE \"%$search_string%\" OR ticketposts.post_title LIKE \"%$search_string%\" OR ticketposts.ID LIKE \"%$search_string%\") 
AND (s_dept.meta_key = 'ticket-selected_department' AND s_dept.meta_value = \"$selected_group\") 
AND ticketposts.post_type = 'tickets' 
AND ticketposts.post_status = 'publish' GROUP BY ticketposts.ID 
ORDER BY ticketposts.post_modified DESC LIMIT $recordShow, $ticketsperpage");
	
	

$pages = $wpdb->get_results("SELECT ID, post_title, post_author, post_modified FROM $wpdb->posts ticketposts, $wpdb->comments commentposts, $wpdb->postmeta companymeta,$wpdb->postmeta customermeta, $wpdb->postmeta s_dept 
WHERE ticketposts.ID = commentposts.comment_post_ID AND ticketposts.ID = companymeta.post_id AND ticketposts.ID = customermeta.post_id 
AND ticketposts.ID = s_dept.post_id 
AND (customermeta.meta_key = 'ticket-authorityID' AND customermeta.meta_value = '$curUserID')
AND (companymeta.meta_key = 'ticket-selectedCompany' AND companymeta.meta_value = '$comp_DB_ID')
AND (commentposts.comment_content LIKE \"%$search_string%\" OR ticketposts.post_title LIKE \"%$search_string%\" OR ticketposts.ID LIKE \"%$search_string%\") 
AND (s_dept.meta_key = 'ticket-selected_department' AND s_dept.meta_value = \"$selected_group\") 
AND ticketposts.post_type = 'tickets' 
AND ticketposts.post_status = 'publish' GROUP BY ticketposts.ID 
ORDER BY ticketposts.post_modified DESC");
}


$pages = array();
$pages = count($pages) / (int)$ticketsperpage;
if($pages > 0) if(is_float($pages)) $pages = ((int)$pages +1);



$URL_ticket_id = preg_replace('/[^0-9]/','',isset($_GET['id']) ? $_GET['id'] : 0);

$valid_ticket = $wpdb->get_results("SELECT ID FROM $wpdb->posts ticketposts, $wpdb->postmeta companymeta, $wpdb->postmeta customermeta
WHERE ticketposts.ID = companymeta.post_id
AND ticketposts.ID = customermeta.post_id
AND ticketposts.ID = $URL_ticket_id
AND (companymeta.meta_key = 'ticket-selectedCompany' AND companymeta.meta_value = '$comp_DB_ID')
AND (customermeta.meta_key = 'ticket-authorityID' AND customermeta.meta_value = '$curUserID')
AND ticketposts.post_type = 'tickets'
AND ticketposts.post_status = 'publish'");


$company_departments = get_metadata('post', $comp_DB_ID, 'company-departments', true);
$company_departments = $company_departments != '' ? explode(',',$company_departments) : array('Default Category');


if(isset($_GET['action']) && $_GET['action'] == 'edit'){
	if(isset($valid_ticket[0])){
		// $error = 'Not Authorized';
		$ticket = get_post($URL_ticket_id);
		$ticket_id = $ticket->ID;
		$ticket_author = $ticket->post_author;
		$ticket_author_object = get_user_by('id',$ticket_author);
		$actionStatus = get_metadata('post',$ticket_id,'ticket-action-status',true);
		$selected_department = get_metadata('post',$ticket_id,'ticket-selected_department',true);
		$ticketStatus = get_metadata('post',$ticket_id,"ticket-status",true);
		$ticketAgent = get_metadata('post',$ticket_id,'ticket-selectedAgent',true);
		if($ticketAgent)
			$ticketAgent_object = get_user_by('id',$ticketAgent);
		
		$discussions = get_conversation_object($ticket->ID);
	} // if(isset($valid_ticket[0])){
		else {
		$error = 'Invalid Ticket ID';
	}
}

$url_action_value = isset($_GET['action']) ? sanitize_text_field($_GET['action']) : false;


		if($error == ''){
			global $mtheme;
			$company_customer_tickets_arr = show_company_customer_tickets();
			include($mtheme.'customer-controls/customer-tickets.php');
		}
		else echo '<h1 class="error">'.$error.'</h1>';


					} // if($customerID_foundIndex > -1)
					else echo '<h1 class="error">'.__('You are not AUTHORIZED to access this company','mhelpdesk').'.</h1>';
			} //if(!isset($compCustomerBlocked_DB_arr[$curUserID]))
			else echo '<h1 class="error">'.__('You are currently FREEZED! in this ','mhelpdesk').$urlCompSlug.' '.__($helpdesk_rewriterule_slug,'mhelpdesk').'
			<br />'.__('Please Contact to your admin/agent in this regard.','mhelpdesk').'</h1>';
			
	} // if(isset($comp_DB_ID_obj[0]))
	else echo '<h1 class="error">'.__('This ','mhelpdesk').$urlCompSlug.' '.__('Company is not registered','mhelpdesk').'</h1>';
echo '</div>';
get_plugin_footer();


