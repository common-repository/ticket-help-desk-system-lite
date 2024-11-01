<?php 
	$hd_admin_settings_arr = get_option('APF_MyFirstFrom');
		
	$helpdesk_rewriterule_slug = isset($hd_admin_settings_arr['m_helpdesk_form']['helpdesk_rewriterule_slug']) && $hd_admin_settings_arr['m_helpdesk_form']['helpdesk_rewriterule_slug'] ? $hd_admin_settings_arr['m_helpdesk_form']['helpdesk_rewriterule_slug'] : 'company';
	if ( !is_user_logged_in() )
	wp_safe_redirect(site_url().'/'.$helpdesk_rewriterule_slug);




//print_r($_GET);
global $wpdb;
$message = '';
	$curUserID = get_current_user_id();
	$urlCompSlug = get_query_var('companyname');
$comp_DB_ID_obj = $wpdb->get_results("SELECT ID, post_title FROM $wpdb->posts WHERE post_type = 'companies' AND post_status = 'publish' AND post_name = '$urlCompSlug'");


		$comp_name = $comp_DB_ID_obj[0]->post_title;
		$comp_DB_ID = $comp_DB_ID_obj[0]->ID;

if(isset($_GET['id'])){
	$ticket_id = preg_replace('/[^0-9]/','',$_GET['id']);
	
	
	//if(isset($comp_DB_ID_obj[0])){

	
		$tickTitleObj = $wpdb->get_results("SELECT post_title,post_author FROM $wpdb->posts WHERE ID = $ticket_id AND post_type = 'tickets' AND post_status = 'publish'");
		{$tickTitle = $tickTitleObj[0]->post_title; $tickOwnerID = $tickTitleObj[0]->post_author;}
	//}
	
		
				if(isset($_POST['ticket-adminUpdateActionBtn']) && wp_verify_nonce( $_POST['ticket-adminUpdateActionBtn'], 'adminUpdateActionBtn' ) 
					&& isset($_POST['adminUpdateActionBtn']) ) {
				
				// he is replying and becoming its agent too AND  Note: if earlier the agent was set, then that will be unset and current user become this ticket agent
				update_metadata('post',$ticket_id,"ticket-selectedAgent",$curUserID);
				
		do_action( 'ticket_updated', array('companyID'=>$comp_DB_ID,'company_name'=>$comp_name,'ticketTitle'=>$tickTitle,'ticketID'=>$ticket_id,'tickOwnerID'=>$tickOwnerID) );
				
				
				if($_POST['ticketActionStatus'] == 'Un-Answered' || $_POST['ticketActionStatus'] == 'Answered' || $_POST['ticketActionStatus'] == 'Solved')
					update_metadata('post',$ticket_id,"ticket-action-status",sanitize_text_field($_POST['ticketActionStatus']));
				
					if($_POST['ticketActionStatus'] == 'Closed')
							update_metadata('post',$ticket_id,"ticket-status",'Closed');
	
					if($_POST['ticketActionStatus'] == 'Opened'){
							update_metadata('post',$ticket_id,"ticket-status",'Opened');
							update_metadata('post',$ticket_id,"ticket-action-status",'Un-Answered');
					}
				
					if(isset($_POST['adminResponse']) && $_POST['adminResponse'] != ''){
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
							$supportedDoc_id = media_handle_upload("supportedDoc",$ticket_id );
							//print_r($supportedDoc_id);
							if(isset($supportedDoc_id) && (is_wp_error($supportedDoc_id) == false))
							update_metadata('comment', $newCommentID, 'ticket-commentAttachment', $supportedDoc_id);
	
	$updateThisPost = array(
					'ID' => $ticket_id,
					'post_type' => 'tickets',
					'post_status' => 'publish'
					);
	
	wp_update_post($updateThisPost);
	
					
			$message.='<div class="alert alert-success bg-success">Ticket Updated</div>';
			//echo "<meta http-equiv=refresh content=0;url=".site_url()."/company/$urlCompSlug/admin/$urlAAction/?action=edit&ticket_id=$ticket_id />";
			} // if(isset($_POST['ticket-adminUpdateActionBtn']) && wp_verify_nonce( $_POST['ticket-adminUpdateActionBtn'], 'adminUpdateActionBtn' )
			
		
		
		// if realy assigning agent then this peace of code executed
		if(isset($_POST['ticket-adminUpdateActionBtn']) && wp_verify_nonce( $_POST['ticket-adminUpdateActionBtn'], 'adminUpdateActionBtn' ) 
			&& isset($_POST['adminAssignAgent'])) {
				update_metadata('post',$ticket_id,"ticket-selectedAgent",preg_replace('/[^0-9]/','',sanitize_text_field($_POST['selectedAgent'])));
				do_action( 'ticket_updated',array('companyID'=>$comp_DB_ID,'company_name'=>$comp_name,'ticketTitle'=>$tickTitle,'ticketID'=>$ticket_id,'tickOwnerID'=>$tickOwnerID));
		}

			
} // if(isset($_GET['id']))







////////////////////////////
//// Start Bulk Action Code
///////////////////////////

if(isset($_GET['bulkAction']) ){
	

$bulkAction = sanitize_text_field($_GET['bulkAction']);

//// Delete

if(isset($_GET['ticketCBs']) && $bulkAction == 'delete'){
$ticketCBs = ($_GET['ticketCBs']);

	foreach($ticketCBs as $ticketCB){
		sanitize_text_field($ticketCB);
		$tickAuthObj = $wpdb->get_results("SELECT ID FROM $wpdb->posts ticketposts, $wpdb->postmeta companymeta
		WHERE ticketposts.ID = companymeta.post_id
		AND ticketposts.ID = $ticketCB
		AND (companymeta.meta_key = 'ticket-selectedCompany' AND companymeta.meta_value = '$comp_DB_ID')
		AND ticketposts.post_type = 'tickets'
		AND ticketposts.post_status = 'publish'");
			if(isset($tickAuthObj[0])){
				wp_trash_post($ticketCB);
				
			} // if(isset($tickAuthObj[0]))
	} // foreach($ticketCBs as $ticketCB)
	$message.='<div class="alert alert-danger bg-danger">The Ticket(s) Successfully deleted</div>';
} // if(isset($_GET['ticketCBs']) && $bulkAction == 'delete')






} // if(isset($_GET['bulkAction']) && isset($_GET['ticket-newTicketForm']) && wp_verify_nonce( $_GET['ticket-newTicketForm'], 'newTicketForm' ))

//////////////////////////
//// END Bulk Action Code
//////////////////////////






if(isset($_POST['ticketCBs']) && isset($_POST['checkbox_del_btn'])){
$ticketCBs = $_POST['ticketCBs'];
//$agentID = $_GET['id'];

	foreach($ticketCBs as $ticketCB){
		sanitize_text_field($ticketCB);
		$tickAuthObj = $wpdb->get_results("SELECT post_title FROM $wpdb->posts ticketposts, $wpdb->postmeta companymeta
		WHERE ticketposts.ID = companymeta.post_id
		AND ticketposts.ID = $ticketCB
		AND (companymeta.meta_key = 'ticket-selectedCompany' AND companymeta.meta_value = '$comp_DB_ID')
		AND ticketposts.post_type = 'tickets'
		AND ticketposts.post_status = 'publish'");
			if(isset($tickAuthObj[0])){
				wp_trash_post($ticketCB);
			} // if(isset($tickAuthObj[0]))
	} // foreach($ticketCBs as $ticketCB)
	$message.='<div class="alert alert-danger bg-danger">The selected Ticket(s) Successfully deleted</div>';
	//echo "<meta http-equiv=refresh content=0;url=../agents//?action=agent_tickets&id=$agentID />";
} // if(isset($_POST['ticketCBs']))







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

function showAdmin_leftmenu(){ 
global $wpdb;  // UPDATE wp_posts SET post_author = 'new-author-id' WHERE post_author = 'old-author-id';
$urlCompSlug = get_query_var('companyname'); 

$comp_DB_ID_obj = $wpdb->get_results("SELECT ID FROM $wpdb->posts WHERE post_type = 'companies' AND post_status = 'publish' AND post_name = '$urlCompSlug'");

$comp_DB_ID = $comp_DB_ID_obj[0]->ID;

$compCustomerBlocked_DB_arr = get_metadata('post', $comp_DB_ID, 'company-customers-blocked', true);

$tickAll_DB_ID_objs = $wpdb->get_results("SELECT ID FROM $wpdb->posts ticketposts, $wpdb->postmeta companymeta
WHERE ticketposts.ID = companymeta.post_id
AND (companymeta.meta_key = 'ticket-selectedCompany' AND companymeta.meta_value = '$comp_DB_ID')
AND ticketposts.post_type = 'tickets'
AND ticketposts.post_status = 'publish'");

/////////////////////////////////////////////////////////////////////////
/////////////////////////////////////////////////////////////////////////
/////////////////////////////////////////////////////////////////////////

$unassingedTickCounter = 0;
foreach($tickAll_DB_ID_objs as $tickAll_DB_ID_obj){
	
	$tickSelectedAgentID = get_metadata('post',$tickAll_DB_ID_obj->ID, 'ticket-selectedAgent',true);
	$userAgent_DB_Obj = $wpdb->get_results("SELECT display_name FROM $wpdb->users WHERE ID = '$tickSelectedAgentID'");
		if(!isset($userAgent_DB_Obj[0]))
			$unassingedTickCounter++;
} // foreach($tickAll_DB_ID_objs as $tickAll_DB_ID_obj)

/////////////////////////////////////////////////////////////////////////
/////////////////////////////////////////////////////////////////////////
/////////////////////////////////////////////////////////////////////////


$tickAll_DB_ID_objs = $wpdb->get_results("SELECT ID FROM $wpdb->posts ticketposts, $wpdb->postmeta companymeta
WHERE ticketposts.ID = companymeta.post_id
AND (companymeta.meta_key = 'ticket-selectedCompany' AND companymeta.meta_value = '$comp_DB_ID')
AND ticketposts.post_type = 'tickets'
AND ticketposts.post_status = 'publish'");






$tickOpened_DB_ID_objs = $wpdb->get_results("SELECT ID FROM $wpdb->posts ticketposts, $wpdb->postmeta companymeta, $wpdb->postmeta ticketStatusmeta
WHERE ticketposts.ID = companymeta.post_id
AND ticketposts.ID = ticketStatusmeta.post_id
AND (companymeta.meta_key = 'ticket-selectedCompany' AND companymeta.meta_value = '$comp_DB_ID')
AND (ticketStatusmeta.meta_key = 'ticket-status' AND ticketStatusmeta.meta_value = 'Opened')
AND ticketposts.post_type = 'tickets'
AND ticketposts.post_status = 'publish'");

$tickClosed_DB_ID_objs = $wpdb->get_results("SELECT ID FROM $wpdb->posts ticketposts, $wpdb->postmeta companymeta, $wpdb->postmeta ticketStatusmeta
WHERE ticketposts.ID = companymeta.post_id
AND ticketposts.ID = ticketStatusmeta.post_id
AND (companymeta.meta_key = 'ticket-selectedCompany' AND companymeta.meta_value = '$comp_DB_ID')
AND (ticketStatusmeta.meta_key = 'ticket-status' AND ticketStatusmeta.meta_value = 'Closed')
AND ticketposts.post_type = 'tickets'
AND ticketposts.post_status = 'publish'");


$tickUnanswered_DB_ID_objs = $wpdb->get_results("SELECT ID FROM $wpdb->posts ticketposts, $wpdb->postmeta companymeta, $wpdb->postmeta ticketStatusmeta
WHERE ticketposts.ID = companymeta.post_id
AND ticketposts.ID = ticketStatusmeta.post_id
AND (companymeta.meta_key = 'ticket-selectedCompany' AND companymeta.meta_value = '$comp_DB_ID')
AND (ticketStatusmeta.meta_key = 'ticket-action-status' AND ticketStatusmeta.meta_value = 'Un-Answered')
AND ticketposts.post_type = 'tickets'
AND ticketposts.post_status = 'publish'");


$tickAanswered_DB_ID_objs = $wpdb->get_results("SELECT ID FROM $wpdb->posts ticketposts, $wpdb->postmeta companymeta, $wpdb->postmeta ticketStatusmeta
WHERE ticketposts.ID = companymeta.post_id
AND ticketposts.ID = ticketStatusmeta.post_id
AND (companymeta.meta_key = 'ticket-selectedCompany' AND companymeta.meta_value = '$comp_DB_ID')
AND (ticketStatusmeta.meta_key = 'ticket-action-status' AND ticketStatusmeta.meta_value = 'Answered')
AND ticketposts.post_type = 'tickets'
AND ticketposts.post_status = 'publish'");

$tickSolved_DB_ID_objs = $wpdb->get_results("SELECT ID FROM $wpdb->posts ticketposts, $wpdb->postmeta companymeta, $wpdb->postmeta ticketStatusmeta
WHERE ticketposts.ID = companymeta.post_id
AND ticketposts.ID = ticketStatusmeta.post_id
AND (companymeta.meta_key = 'ticket-selectedCompany' AND companymeta.meta_value = '$comp_DB_ID')
AND (ticketStatusmeta.meta_key = 'ticket-action-status' AND ticketStatusmeta.meta_value = 'Solved')
AND ticketposts.post_type = 'tickets'
AND ticketposts.post_status = 'publish'");
?>
<div class="msidemenu">
<p><a href="<?php echo get_permalink($comp_DB_ID)."admin/tickets/?pagenumber=1" ?>"><?php _e('All Tickets','mhelpdesk');?> 
	<span class="ticketcounter_right">(<?php echo count($tickAll_DB_ID_objs) ?>)</span></a></p>
<p><a href="?action=new&pagenumber=1"><?php _e('New Tickets','mhelpdesk');?> <span class="ticketcounter_right">(<?php echo $unassingedTickCounter; ?>)</span></a></p>
<p><a href="?action=opened&pagenumber=1"><?php _e('Opened','mhelpdesk');?> <span class="ticketcounter_right">(<?php echo count($tickOpened_DB_ID_objs); ?>)</span></a></p>
<p><a href="?action=closed&pagenumber=1"><?php _e('Closed','mhelpdesk');?> <span class="ticketcounter_right">(<?php echo count($tickClosed_DB_ID_objs) ?>)</span></a></p>
<p><a href="?action=unanswered&pagenumber=1"><?php _e('Un-Answered','mhelpdesk');?><span class="ticketcounter_right">(<?php echo count($tickUnanswered_DB_ID_objs) ?>)</span></a></p>
<p><a href="?action=answered&pagenumber=1"><?php _e('Answered','mhelpdesk');?><span class="ticketcounter_right">(<?php echo count($tickAanswered_DB_ID_objs); ?>)</span></a></p>
<p><a href="?action=solved&pagenumber=1"><?php _e('Solved','mhelpdesk');?><span class="ticketcounter_right">(<?php echo count($tickSolved_DB_ID_objs); ?>)</span></a></p>
</div>
<?php
} // function showAdmin_leftmenu()


/** QUERY ALL TICKETS **/

global $wpdb;  // UPDATE wp_posts SET post_author = 'new-author-id' WHERE post_author = 'old-author-id';
$urlCompSlug = get_query_var('companyname'); 

$comp_DB_ID_obj = $wpdb->get_results("SELECT * FROM $wpdb->posts WHERE post_type = 'companies' AND post_status = 'publish' AND post_name = '$urlCompSlug'");

$comp_DB_ID = $comp_DB_ID_obj[0]->ID;
$ticketsperpage = get_metadata('post', $comp_DB_ID, 'company-tickets-perpage', true);
$ticketsperpage = $ticketsperpage > 0 ? $ticketsperpage : 10;
$viewedPageNum = isset($_GET['pagenumber']) ? sanitize_text_field($_GET['pagenumber']) : 1 ;
$recordShow = ($ticketsperpage * $viewedPageNum) - $ticketsperpage;

$company_admin_tickets_arr = show_company_admin_tickets();

//print_r($company_admin_tickets_arr);

//$compCustomerBlocked_DB_arr = get_metadata('post', $comp_DB_ID, 'company-customers-blocked', true);

/*$tickAll_DB_ID_objs = $wpdb->get_results("SELECT * FROM $wpdb->posts ticketposts, $wpdb->postmeta companymeta
WHERE ticketposts.ID = companymeta.post_id
AND (companymeta.meta_key = 'ticket-selectedCompany' AND companymeta.meta_value = '$comp_DB_ID')
AND ticketposts.post_type = 'tickets'
AND ticketposts.post_status = 'publish'");*/


/*$all_tickets = $wpdb->get_results("SELECT * FROM $wpdb->posts ticketposts, $wpdb->postmeta companymeta
WHERE ticketposts.ID = companymeta.post_id
AND (companymeta.meta_key = 'ticket-selectedCompany' AND companymeta.meta_value = '$comp_DB_ID')
AND ticketposts.post_type = 'tickets'
AND ticketposts.post_status = 'publish'" );*/

if(!isset($_GET['action'])){

	if(isset($_GET['group']) && $_GET['group']!=''){
		$selected_group = sanitize_text_field($_GET['group']);
		$tickets = $wpdb->get_results("SELECT * FROM $wpdb->posts ticketposts, $wpdb->postmeta companymeta, $wpdb->postmeta s_dept 
		WHERE ticketposts.ID = companymeta.post_id AND ticketposts.ID = s_dept.post_id
		AND (s_dept.meta_key = 'ticket-selected_department' AND s_dept.meta_value = '$selected_group')
		AND (companymeta.meta_key = 'ticket-selectedCompany' AND companymeta.meta_value = '$comp_DB_ID')
		AND ticketposts.post_type = 'tickets'
		AND ticketposts.post_status = 'publish' ORDER BY ticketposts.post_modified DESC LIMIT $recordShow, $ticketsperpage" );
	}
	else{
		$tickets = $wpdb->get_results("SELECT * FROM $wpdb->posts ticketposts, $wpdb->postmeta companymeta
		WHERE ticketposts.ID = companymeta.post_id
		AND (companymeta.meta_key = 'ticket-selectedCompany' AND companymeta.meta_value = '$comp_DB_ID')
		AND ticketposts.post_type = 'tickets'
		AND ticketposts.post_status = 'publish' ORDER BY ticketposts.post_modified DESC LIMIT $recordShow, $ticketsperpage" );	
	}


$pages = $company_admin_tickets_arr['all_tickets'];
}


/*$new_tickets = $wpdb->get_results("SELECT ID FROM $wpdb->posts ticketposts, $wpdb->postmeta companymeta
WHERE ticketposts.ID = companymeta.post_id
AND (companymeta.meta_key = 'ticket-selectedCompany' AND companymeta.meta_value = '$comp_DB_ID')
AND NOT EXISTS (
              SELECT post_id FROM $wpdb->postmeta
               WHERE $wpdb->postmeta.post_id=ticketposts.ID
                AND $wpdb->postmeta.meta_key = 'ticket-selectedAgent'
            ) 
AND ticketposts.post_type = 'tickets'
AND ticketposts.post_status = 'publish'");*/
if(isset($_GET['action']) && $_GET['action'] == 'new'){

	if(isset($_GET['group']) && $_GET['group']!=''){
		$selected_group = sanitize_text_field($_GET['group']);
		$tickets = $wpdb->get_results("SELECT ID, post_title, post_author, post_modified FROM $wpdb->posts ticketposts, $wpdb->postmeta companymeta,$wpdb->postmeta s_dept
		WHERE ticketposts.ID = companymeta.post_id AND ticketposts.ID = s_dept.post_id
		AND (companymeta.meta_key = 'ticket-selectedCompany' AND companymeta.meta_value = '$comp_DB_ID')
		AND (s_dept.meta_key = 'ticket-selected_department' AND s_dept.meta_value = '$selected_group')
		AND NOT EXISTS (
					  SELECT post_id FROM $wpdb->postmeta
					   WHERE $wpdb->postmeta.post_id=ticketposts.ID
						AND $wpdb->postmeta.meta_key = 'ticket-selectedAgent'
					) 
		AND ticketposts.post_type = 'tickets'
		AND ticketposts.post_status = 'publish' ORDER BY ticketposts.post_modified DESC LIMIT $recordShow, $ticketsperpage");
	}
	else{
		$tickets = $wpdb->get_results("SELECT ID, post_title, post_author, post_modified FROM $wpdb->posts ticketposts, $wpdb->postmeta companymeta
		WHERE ticketposts.ID = companymeta.post_id
		AND (companymeta.meta_key = 'ticket-selectedCompany' AND companymeta.meta_value = '$comp_DB_ID')
		AND NOT EXISTS (
					  SELECT post_id FROM $wpdb->postmeta
					   WHERE $wpdb->postmeta.post_id=ticketposts.ID
						AND $wpdb->postmeta.meta_key = 'ticket-selectedAgent'
					) 
		AND ticketposts.post_type = 'tickets'
		AND ticketposts.post_status = 'publish' ORDER BY ticketposts.post_modified DESC LIMIT $recordShow, $ticketsperpage");
	}
/*
$pages = $wpdb->get_results("SELECT ID FROM $wpdb->posts ticketposts, $wpdb->postmeta companymeta
WHERE ticketposts.ID = companymeta.post_id
AND (companymeta.meta_key = 'ticket-selectedCompany' AND companymeta.meta_value = '$comp_DB_ID')
AND NOT EXISTS (
              SELECT post_id FROM $wpdb->postmeta
               WHERE $wpdb->postmeta.post_id=ticketposts.ID
                AND $wpdb->postmeta.meta_key = 'ticket-selectedAgent'
            ) 
AND ticketposts.post_type = 'tickets'
AND ticketposts.post_status = 'publish'");*/

$pages = $company_admin_tickets_arr['new_tickets'];
}



/*$rated_tickets = $wpdb->get_results("SELECT ID FROM $wpdb->posts ticketposts, $wpdb->postmeta companymeta
		WHERE ticketposts.ID = companymeta.post_id
		AND (companymeta.meta_key = 'ticket-selectedCompany' AND companymeta.meta_value = '$comp_DB_ID')
		AND EXISTS (
					  SELECT post_id FROM $wpdb->postmeta
					  WHERE $wpdb->postmeta.meta_key = 'ticket-rating' AND $wpdb->postmeta.meta_value > 0
					  AND ticketposts.ID = $wpdb->postmeta.post_id
					)
AND ticketposts.post_type = 'tickets'
AND ticketposts.post_status = 'publish'");*/
if(isset($_GET['action']) && $_GET['action'] == 'rated'){
	if(isset($_GET['group']) && $_GET['group']!=''){
		$selected_group = sanitize_text_field($_GET['group']);
		$tickets = $wpdb->get_results("SELECT ID, post_title, post_author, post_modified FROM $wpdb->posts ticketposts, $wpdb->postmeta companymeta,$wpdb->postmeta s_dept
				WHERE ticketposts.ID = companymeta.post_id AND ticketposts.ID = s_dept.post_id
				AND (companymeta.meta_key = 'ticket-selectedCompany' AND companymeta.meta_value = '$comp_DB_ID')
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
		$tickets = $wpdb->get_results("SELECT ID, post_title, post_author, post_modified FROM $wpdb->posts ticketposts, $wpdb->postmeta companymeta
				WHERE ticketposts.ID = companymeta.post_id
				AND (companymeta.meta_key = 'ticket-selectedCompany' AND companymeta.meta_value = '$comp_DB_ID')
				AND EXISTS (
							  SELECT post_id FROM $wpdb->postmeta
							  WHERE $wpdb->postmeta.meta_key = 'ticket-rating' AND $wpdb->postmeta.meta_value > 0
							  AND ticketposts.ID = $wpdb->postmeta.post_id
							)
		AND ticketposts.post_type = 'tickets'
		AND ticketposts.post_status = 'publish' ORDER BY ticketposts.post_modified DESC LIMIT $recordShow, $ticketsperpage");
	}
/*$pages = $wpdb->get_results("SELECT ID FROM $wpdb->posts ticketposts, $wpdb->postmeta companymeta
		WHERE ticketposts.ID = companymeta.post_id
		AND (companymeta.meta_key = 'ticket-selectedCompany' AND companymeta.meta_value = '$comp_DB_ID')
		AND EXISTS (
					  SELECT post_id FROM $wpdb->postmeta
					  WHERE $wpdb->postmeta.meta_key = 'ticket-rating' AND $wpdb->postmeta.meta_value > 0
					  AND ticketposts.ID = $wpdb->postmeta.post_id
					)
AND ticketposts.post_type = 'tickets'
AND ticketposts.post_status = 'publish'");*/

$pages = $company_admin_tickets_arr['rated_tickets'];
}



/*
$opened_tickets = $wpdb->get_results("SELECT * FROM $wpdb->posts ticketposts, $wpdb->postmeta companymeta, $wpdb->postmeta ticketStatusmeta
WHERE ticketposts.ID = companymeta.post_id
AND ticketposts.ID = ticketStatusmeta.post_id
AND (companymeta.meta_key = 'ticket-selectedCompany' AND companymeta.meta_value = '$comp_DB_ID')
AND (ticketStatusmeta.meta_key = 'ticket-status' AND ticketStatusmeta.meta_value = 'Opened')
AND ticketposts.post_type = 'tickets'
AND ticketposts.post_status = 'publish'");*/
if(isset($_GET['action']) && $_GET['action'] == 'opened'){
	if(isset($_GET['group']) && $_GET['group']!=''){
		$selected_group = sanitize_text_field($_GET['group']);
		$tickets = $wpdb->get_results("SELECT * FROM $wpdb->posts ticketposts, $wpdb->postmeta companymeta, $wpdb->postmeta ticketStatusmeta,$wpdb->postmeta s_dept
		WHERE ticketposts.ID = companymeta.post_id AND ticketposts.ID = s_dept.post_id
		AND ticketposts.ID = ticketStatusmeta.post_id
		AND (companymeta.meta_key = 'ticket-selectedCompany' AND companymeta.meta_value = '$comp_DB_ID')
		AND (s_dept.meta_key = 'ticket-selected_department' AND s_dept.meta_value = '$selected_group')
		AND (ticketStatusmeta.meta_key = 'ticket-status' AND ticketStatusmeta.meta_value = 'Opened')
		AND ticketposts.post_type = 'tickets'
		AND ticketposts.post_status = 'publish' ORDER BY ticketposts.post_modified DESC LIMIT $recordShow, $ticketsperpage");
	}
	else{
		$tickets = $wpdb->get_results("SELECT * FROM $wpdb->posts ticketposts, $wpdb->postmeta companymeta, $wpdb->postmeta ticketStatusmeta
		WHERE ticketposts.ID = companymeta.post_id
		AND ticketposts.ID = ticketStatusmeta.post_id
		AND (companymeta.meta_key = 'ticket-selectedCompany' AND companymeta.meta_value = '$comp_DB_ID')
		AND (ticketStatusmeta.meta_key = 'ticket-status' AND ticketStatusmeta.meta_value = 'Opened')
		AND ticketposts.post_type = 'tickets'
		AND ticketposts.post_status = 'publish' ORDER BY ticketposts.post_modified DESC LIMIT $recordShow, $ticketsperpage");
	}
/*$pages = $wpdb->get_results("SELECT * FROM $wpdb->posts ticketposts, $wpdb->postmeta companymeta, $wpdb->postmeta ticketStatusmeta
WHERE ticketposts.ID = companymeta.post_id
AND ticketposts.ID = ticketStatusmeta.post_id
AND (companymeta.meta_key = 'ticket-selectedCompany' AND companymeta.meta_value = '$comp_DB_ID')
AND (ticketStatusmeta.meta_key = 'ticket-status' AND ticketStatusmeta.meta_value = 'Opened')
AND ticketposts.post_type = 'tickets'
AND ticketposts.post_status = 'publish'");*/

$pages = $company_admin_tickets_arr['opened_tickets'];
}





/*$closed_tickets = $wpdb->get_results("SELECT * FROM $wpdb->posts ticketposts, $wpdb->postmeta companymeta, $wpdb->postmeta ticketStatusmeta
WHERE ticketposts.ID = companymeta.post_id
AND ticketposts.ID = ticketStatusmeta.post_id
AND (companymeta.meta_key = 'ticket-selectedCompany' AND companymeta.meta_value = '$comp_DB_ID')
AND (ticketStatusmeta.meta_key = 'ticket-status' AND ticketStatusmeta.meta_value = 'Closed')
AND ticketposts.post_type = 'tickets'
AND ticketposts.post_status = 'publish'");*/
if(isset($_GET['action']) && $_GET['action'] == 'closed'){
	if(isset($_GET['group']) && $_GET['group']!=''){
		$selected_group = sanitize_text_field($_GET['group']);
		$tickets = $wpdb->get_results("SELECT * FROM $wpdb->posts ticketposts, $wpdb->postmeta companymeta, $wpdb->postmeta ticketStatusmeta,$wpdb->postmeta s_dept
		WHERE ticketposts.ID = companymeta.post_id AND ticketposts.ID = ticketStatusmeta.post_id AND ticketposts.ID = s_dept.post_id
		AND (companymeta.meta_key = 'ticket-selectedCompany' AND companymeta.meta_value = '$comp_DB_ID')
		AND (s_dept.meta_key = 'ticket-selected_department' AND s_dept.meta_value = '$selected_group')
		AND (ticketStatusmeta.meta_key = 'ticket-status' AND ticketStatusmeta.meta_value = 'Closed')
		AND ticketposts.post_type = 'tickets'
		AND ticketposts.post_status = 'publish' ORDER BY ticketposts.post_modified DESC LIMIT $recordShow, $ticketsperpage");
	}
	else{
		$tickets = $wpdb->get_results("SELECT * FROM $wpdb->posts ticketposts, $wpdb->postmeta companymeta, $wpdb->postmeta ticketStatusmeta
		WHERE ticketposts.ID = companymeta.post_id
		AND ticketposts.ID = ticketStatusmeta.post_id
		AND (companymeta.meta_key = 'ticket-selectedCompany' AND companymeta.meta_value = '$comp_DB_ID')
		AND (ticketStatusmeta.meta_key = 'ticket-status' AND ticketStatusmeta.meta_value = 'Closed')
		AND ticketposts.post_type = 'tickets'
		AND ticketposts.post_status = 'publish' ORDER BY ticketposts.post_modified DESC LIMIT $recordShow, $ticketsperpage");
	}
	
/*$pages = $wpdb->get_results("SELECT * FROM $wpdb->posts ticketposts, $wpdb->postmeta companymeta, $wpdb->postmeta ticketStatusmeta
WHERE ticketposts.ID = companymeta.post_id
AND ticketposts.ID = ticketStatusmeta.post_id
AND (companymeta.meta_key = 'ticket-selectedCompany' AND companymeta.meta_value = '$comp_DB_ID')
AND (ticketStatusmeta.meta_key = 'ticket-status' AND ticketStatusmeta.meta_value = 'Closed')
AND ticketposts.post_type = 'tickets'
AND ticketposts.post_status = 'publish'");*/

$pages = $company_admin_tickets_arr['closed_tickets'];
}





/*$unanswered_tickets = $wpdb->get_results("SELECT * FROM $wpdb->posts ticketposts, $wpdb->postmeta companymeta, $wpdb->postmeta ticketStatusmeta
WHERE ticketposts.ID = companymeta.post_id
AND ticketposts.ID = ticketStatusmeta.post_id
AND (companymeta.meta_key = 'ticket-selectedCompany' AND companymeta.meta_value = '$comp_DB_ID')
AND (ticketStatusmeta.meta_key = 'ticket-action-status' AND ticketStatusmeta.meta_value = 'Un-Answered')
AND ticketposts.post_type = 'tickets'
AND ticketposts.post_status = 'publish'");*/
if(isset($_GET['action']) && $_GET['action'] == 'unanswered'){
	if(isset($_GET['group']) && $_GET['group']!=''){
		$selected_group = sanitize_text_field($_GET['group']);
		$tickets = $wpdb->get_results("SELECT * FROM $wpdb->posts ticketposts, $wpdb->postmeta companymeta, $wpdb->postmeta ticketStatusmeta,$wpdb->postmeta s_dept
		WHERE ticketposts.ID = companymeta.post_id AND ticketposts.ID = ticketStatusmeta.post_id AND ticketposts.ID = s_dept.post_id
		AND (companymeta.meta_key = 'ticket-selectedCompany' AND companymeta.meta_value = '$comp_DB_ID')
		AND (s_dept.meta_key = 'ticket-selected_department' AND s_dept.meta_value = '$selected_group')
		AND (ticketStatusmeta.meta_key = 'ticket-action-status' AND ticketStatusmeta.meta_value = 'Un-Answered')
		AND ticketposts.post_type = 'tickets'
		AND ticketposts.post_status = 'publish' ORDER BY ticketposts.post_modified DESC LIMIT $recordShow, $ticketsperpage");
	}
	else{
		$tickets = $wpdb->get_results("SELECT * FROM $wpdb->posts ticketposts, $wpdb->postmeta companymeta, $wpdb->postmeta ticketStatusmeta
		WHERE ticketposts.ID = companymeta.post_id
		AND ticketposts.ID = ticketStatusmeta.post_id
		AND (companymeta.meta_key = 'ticket-selectedCompany' AND companymeta.meta_value = '$comp_DB_ID')
		AND (ticketStatusmeta.meta_key = 'ticket-action-status' AND ticketStatusmeta.meta_value = 'Un-Answered')
		AND ticketposts.post_type = 'tickets'
		AND ticketposts.post_status = 'publish' ORDER BY ticketposts.post_modified DESC LIMIT $recordShow, $ticketsperpage");
	}
/*$pages = $wpdb->get_results("SELECT * FROM $wpdb->posts ticketposts, $wpdb->postmeta companymeta, $wpdb->postmeta ticketStatusmeta
WHERE ticketposts.ID = companymeta.post_id
AND ticketposts.ID = ticketStatusmeta.post_id
AND (companymeta.meta_key = 'ticket-selectedCompany' AND companymeta.meta_value = '$comp_DB_ID')
AND (ticketStatusmeta.meta_key = 'ticket-action-status' AND ticketStatusmeta.meta_value = 'Un-Answered')
AND ticketposts.post_type = 'tickets'
AND ticketposts.post_status = 'publish'");*/
$pages = $company_admin_tickets_arr['unanswered_tickets'];
}






/*$answered_tickets = $wpdb->get_results("SELECT * FROM $wpdb->posts ticketposts, $wpdb->postmeta companymeta, $wpdb->postmeta ticketStatusmeta
WHERE ticketposts.ID = companymeta.post_id
AND ticketposts.ID = ticketStatusmeta.post_id
AND (companymeta.meta_key = 'ticket-selectedCompany' AND companymeta.meta_value = '$comp_DB_ID')
AND (ticketStatusmeta.meta_key = 'ticket-action-status' AND ticketStatusmeta.meta_value = 'Answered')
AND ticketposts.post_type = 'tickets'
AND ticketposts.post_status = 'publish'");*/
if(isset($_GET['action']) && $_GET['action'] == 'answered'){
	if(isset($_GET['group']) && $_GET['group']!=''){
		$selected_group = sanitize_text_field($_GET['group']);
		$tickets = $wpdb->get_results("SELECT * FROM $wpdb->posts ticketposts, $wpdb->postmeta companymeta, $wpdb->postmeta ticketStatusmeta,$wpdb->postmeta s_dept
		WHERE ticketposts.ID = companymeta.post_id AND ticketposts.ID = ticketStatusmeta.post_id AND ticketposts.ID = s_dept.post_id
		AND (companymeta.meta_key = 'ticket-selectedCompany' AND companymeta.meta_value = '$comp_DB_ID')
		AND (s_dept.meta_key = 'ticket-selected_department' AND s_dept.meta_value = '$selected_group')
		AND (ticketStatusmeta.meta_key = 'ticket-action-status' AND ticketStatusmeta.meta_value = 'Answered')
		AND ticketposts.post_type = 'tickets'
		AND ticketposts.post_status = 'publish' ORDER BY ticketposts.post_modified DESC LIMIT $recordShow, $ticketsperpage");
	}
	else{
		$tickets = $wpdb->get_results("SELECT * FROM $wpdb->posts ticketposts, $wpdb->postmeta companymeta, $wpdb->postmeta ticketStatusmeta
		WHERE ticketposts.ID = companymeta.post_id
		AND ticketposts.ID = ticketStatusmeta.post_id
		AND (companymeta.meta_key = 'ticket-selectedCompany' AND companymeta.meta_value = '$comp_DB_ID')
		AND (ticketStatusmeta.meta_key = 'ticket-action-status' AND ticketStatusmeta.meta_value = 'Answered')
		AND ticketposts.post_type = 'tickets'
		AND ticketposts.post_status = 'publish' ORDER BY ticketposts.post_modified DESC LIMIT $recordShow, $ticketsperpage");
	}
/*$pages = $wpdb->get_results("SELECT * FROM $wpdb->posts ticketposts, $wpdb->postmeta companymeta, $wpdb->postmeta ticketStatusmeta
WHERE ticketposts.ID = companymeta.post_id
AND ticketposts.ID = ticketStatusmeta.post_id
AND (companymeta.meta_key = 'ticket-selectedCompany' AND companymeta.meta_value = '$comp_DB_ID')
AND (ticketStatusmeta.meta_key = 'ticket-action-status' AND ticketStatusmeta.meta_value = 'Answered')
AND ticketposts.post_type = 'tickets'
AND ticketposts.post_status = 'publish'");*/
$pages = $company_admin_tickets_arr['answered_tickets'];
}





/*$solved_tickets = $wpdb->get_results("SELECT * FROM $wpdb->posts ticketposts, $wpdb->postmeta companymeta, $wpdb->postmeta ticketStatusmeta
WHERE ticketposts.ID = companymeta.post_id
AND ticketposts.ID = ticketStatusmeta.post_id
AND (companymeta.meta_key = 'ticket-selectedCompany' AND companymeta.meta_value = '$comp_DB_ID')
AND (ticketStatusmeta.meta_key = 'ticket-action-status' AND ticketStatusmeta.meta_value = 'Solved')
AND ticketposts.post_type = 'tickets'
AND ticketposts.post_status = 'publish'");*/
if(isset($_GET['action']) && $_GET['action'] == 'solved'){
	if(isset($_GET['group']) && $_GET['group']!=''){
		$selected_group = sanitize_text_field($_GET['group']);
		$tickets = $wpdb->get_results("SELECT * FROM $wpdb->posts ticketposts, $wpdb->postmeta companymeta, $wpdb->postmeta ticketStatusmeta,$wpdb->postmeta s_dept
		WHERE ticketposts.ID = companymeta.post_id AND ticketposts.ID = ticketStatusmeta.post_id AND ticketposts.ID = s_dept.post_id
		AND (companymeta.meta_key = 'ticket-selectedCompany' AND companymeta.meta_value = '$comp_DB_ID')
		AND (s_dept.meta_key = 'ticket-selected_department' AND s_dept.meta_value = '$selected_group')
		AND (ticketStatusmeta.meta_key = 'ticket-action-status' AND ticketStatusmeta.meta_value = 'Solved')
		AND ticketposts.post_type = 'tickets'
		AND ticketposts.post_status = 'publish' ORDER BY ticketposts.post_modified DESC LIMIT $recordShow, $ticketsperpage");
	}
	else{
		$tickets = $wpdb->get_results("SELECT * FROM $wpdb->posts ticketposts, $wpdb->postmeta companymeta, $wpdb->postmeta ticketStatusmeta
		WHERE ticketposts.ID = companymeta.post_id
		AND ticketposts.ID = ticketStatusmeta.post_id
		AND (companymeta.meta_key = 'ticket-selectedCompany' AND companymeta.meta_value = '$comp_DB_ID')
		AND (ticketStatusmeta.meta_key = 'ticket-action-status' AND ticketStatusmeta.meta_value = 'Solved')
		AND ticketposts.post_type = 'tickets'
		AND ticketposts.post_status = 'publish' ORDER BY ticketposts.post_modified DESC LIMIT $recordShow, $ticketsperpage");
	}
/*$pages = $wpdb->get_results("SELECT * FROM $wpdb->posts ticketposts, $wpdb->postmeta companymeta, $wpdb->postmeta ticketStatusmeta
WHERE ticketposts.ID = companymeta.post_id
AND ticketposts.ID = ticketStatusmeta.post_id
AND (companymeta.meta_key = 'ticket-selectedCompany' AND companymeta.meta_value = '$comp_DB_ID')
AND (ticketStatusmeta.meta_key = 'ticket-action-status' AND ticketStatusmeta.meta_value = 'Solved')
AND ticketposts.post_type = 'tickets'
AND ticketposts.post_status = 'publish'");*/
$pages = $company_admin_tickets_arr['solved_tickets'];
}




///////////////////////
//// Searched Tickets
///////////////////////

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

//////////////////////////
//// END Searched Tickets
//////////////////////////







$pages = array();
$pages = count($pages) / (int)$ticketsperpage;
if($pages > 0) if(is_float($pages)) $pages = ((int)$pages +1);



$URL_ticket_id = preg_replace('/[^0-9]/','',(isset($_GET['id']) ? $_GET['id'] : 0));

$valid_ticket = $wpdb->get_results("SELECT ID FROM $wpdb->posts ticketposts, $wpdb->postmeta companymeta
WHERE ticketposts.ID = companymeta.post_id
AND ticketposts.ID = $URL_ticket_id
AND (companymeta.meta_key = 'ticket-selectedCompany' AND companymeta.meta_value = '$comp_DB_ID')
AND ticketposts.post_type = 'tickets'
AND ticketposts.post_status = 'publish'");

$error = '';

if(isset($_GET['action']) && $_GET['action'] == 'edit'){
	if(isset($valid_ticket[0])){
		// $error = 'Not Authorized';
		$ticket = get_post($URL_ticket_id);
		$ticket_id = $ticket->ID;
		$ticket_author = $ticket->post_author;
		$ticket_author_object = get_user_by('id',$ticket_author);
		$actionStatus = get_metadata('post',$ticket_id,'ticket-action-status',true);
		$ticketStatus = get_metadata('post',$ticket_id,"ticket-status",true);
		$ticketAgent = get_metadata('post',$ticket_id,'ticket-selectedAgent',true);
		//var_dump($ticketAgent);
		if($ticketAgent)
			$ticketAgent_object = get_user_by('id',$ticketAgent);
		$discussions = get_conversation_object($ticket->ID);
	} // if(isset($valid_ticket[0])){
	else $error = 'Invalid Ticket ID';
	
}

$company = get_post($comp_DB_ID);
$company_metas = get_metadata('post',$comp_DB_ID);



$userRole = get_query_var( 'userrole' );


	
	if(isset($comp_DB_ID_obj[0])){


$comp_authCheck_obj = $wpdb->get_results("SELECT post_author FROM $wpdb->posts companyposts, $wpdb->postmeta companymeta
WHERE companyposts.ID = companymeta.post_id  AND companyposts.post_name = '$urlCompSlug'
AND companyposts.post_type = 'companies'
AND companyposts.post_status = 'publish'");



		if(isset($comp_authCheck_obj[0]))
			if($comp_authCheck_obj[0]->post_author == $curUserID){

		$comp_DB_ID = $comp_DB_ID_obj[0]->ID;
		
		$company_departments = get_metadata('post', $comp_DB_ID, 'company-departments', true);
		$company_departments = $company_departments != '' ? explode(',',$company_departments) : array('Default Category');
		
		$ticketsperpage = get_metadata('post', $comp_DB_ID, 'company-tickets-perpage', true);
        $ticketsperpage = $ticketsperpage ? $ticketsperpage : 5;
		$viewedPageNum = isset($_GET['pagenumber']) ? sanitize_text_field($_GET['pagenumber']) : 1 ;
		
		
		if($error == ''){
			global $mtheme;
			include($mtheme.'admin-controls/admin-tickets.php');	
		}
		else echo '<h1 class="error">'.$error.'</h1>';	
				


		} //if(isset($comp_authCheck_obj[0]))
			else echo '<h1 >'.__('You are not AUTHORIZED to access this page','mhelpdesk').'</h1>';
			
	} // if(isset($comp_DB_ID_obj[0]))
	else echo '<h1 >'.__('The accessed Company is not registered yet!','mhelpdesk').'</h1>';
 
echo '</div>';
get_plugin_footer();?>