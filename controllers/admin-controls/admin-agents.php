<?php 
	$hd_admin_settings_arr = get_option('APF_MyFirstFrom');
		
	$helpdesk_rewriterule_slug = isset($hd_admin_settings_arr['m_helpdesk_form']['helpdesk_rewriterule_slug']) && $hd_admin_settings_arr['m_helpdesk_form']['helpdesk_rewriterule_slug'] ? $hd_admin_settings_arr['m_helpdesk_form']['helpdesk_rewriterule_slug'] : 'company';
	if ( !is_user_logged_in() )
	wp_safe_redirect(site_url().'/'.$helpdesk_rewriterule_slug);

$active_plugins = get_option('active_plugins',array());
get_plugin_header();
echo '<div class="hdwrap">';

$urlCompSlug = get_query_var('companyname');
$userRole = get_query_var('userrole');
$curUserID = get_current_user_id();

global $wpdb,$global_agent_limit_status;
	$comp_DB_ID_obj = $wpdb->get_results("SELECT ID, post_title FROM $wpdb->posts WHERE post_type = 'companies' AND post_status = 'publish' AND post_name = '$urlCompSlug'");

	$comp_DB_ID = $comp_DB_ID_obj[0]->ID;
	$comp_DB_title = $comp_DB_ID_obj[0]->post_title;

	$message = '';

function showAgent_leftmenu(){
global $wpdb;
$agent_id = preg_replace('/[^0-9]/','',$_GET['id']);
$urlCompSlug = get_query_var('companyname'); 

$comp_DB_ID_obj = $wpdb->get_results("SELECT ID FROM $wpdb->posts WHERE post_type = 'companies' AND post_status = 'publish' AND post_name = '$urlCompSlug'");

$comp_DB_ID = $comp_DB_ID_obj[0]->ID;



$tickAll_DB_ID_objs = $wpdb->get_results("SELECT ID FROM $wpdb->posts ticketposts, $wpdb->postmeta companymeta, $wpdb->postmeta agentmeta
WHERE ticketposts.ID = companymeta.post_id
AND ticketposts.ID = agentmeta.post_id
AND (companymeta.meta_key = 'ticket-selectedCompany' AND companymeta.meta_value = '$comp_DB_ID')
AND (agentmeta.meta_key = 'ticket-selectedAgent' AND agentmeta.meta_value = '$agent_id')
AND ticketposts.post_type = 'tickets'
AND ticketposts.post_status = 'publish' ORDER BY ticketposts.post_modified DESC");
//print_r($tickAll_DB_ID_objs);

$tickOpened_DB_ID_objs = $wpdb->get_results("SELECT ID FROM $wpdb->posts ticketposts, $wpdb->postmeta companymeta, $wpdb->postmeta agentmeta, $wpdb->postmeta ticketStatusmeta
WHERE ticketposts.ID = companymeta.post_id
AND ticketposts.ID = ticketStatusmeta.post_id
AND ticketposts.ID = agentmeta.post_id
AND (companymeta.meta_key = 'ticket-selectedCompany' AND companymeta.meta_value = '$comp_DB_ID')
AND (agentmeta.meta_key = 'ticket-selectedAgent' AND agentmeta.meta_value = '$agent_id')
AND (ticketStatusmeta.meta_key = 'ticket-status' AND ticketStatusmeta.meta_value = 'Opened')
AND ticketposts.post_type = 'tickets'
AND ticketposts.post_status = 'publish' ORDER BY ticketposts.post_modified DESC");


$tickClosed_DB_ID_objs = $wpdb->get_results("SELECT ID FROM $wpdb->posts ticketposts, $wpdb->postmeta companymeta, $wpdb->postmeta agentmeta, $wpdb->postmeta ticketStatusmeta
WHERE ticketposts.ID = companymeta.post_id
AND ticketposts.ID = ticketStatusmeta.post_id
AND ticketposts.ID = agentmeta.post_id
AND (companymeta.meta_key = 'ticket-selectedCompany' AND companymeta.meta_value = '$comp_DB_ID')
AND (agentmeta.meta_key = 'ticket-selectedAgent' AND agentmeta.meta_value = '$agent_id')
AND (ticketStatusmeta.meta_key = 'ticket-status' AND ticketStatusmeta.meta_value = 'Closed')
AND ticketposts.post_type = 'tickets'
AND ticketposts.post_status = 'publish' ORDER BY ticketposts.post_modified DESC"); //print_r($tickClosed_DB_ID_objs);


$tickUnanswered_DB_ID_objs = $wpdb->get_results("SELECT ID FROM $wpdb->posts ticketposts, $wpdb->postmeta companymeta, $wpdb->postmeta agentmeta, $wpdb->postmeta ticketStatusmeta
WHERE ticketposts.ID = companymeta.post_id
AND ticketposts.ID = ticketStatusmeta.post_id
AND ticketposts.ID = agentmeta.post_id
AND (companymeta.meta_key = 'ticket-selectedCompany' AND companymeta.meta_value = '$comp_DB_ID')
AND (agentmeta.meta_key = 'ticket-selectedAgent' AND agentmeta.meta_value = '$agent_id')
AND (ticketStatusmeta.meta_key = 'ticket-action-status' AND ticketStatusmeta.meta_value = 'Un-Answered')
AND ticketposts.post_type = 'tickets'
AND ticketposts.post_status = 'publish' ORDER BY ticketposts.post_modified DESC");


$tickAanswered_DB_ID_objs = $wpdb->get_results("SELECT ID FROM $wpdb->posts ticketposts, $wpdb->postmeta companymeta, $wpdb->postmeta agentmeta, $wpdb->postmeta ticketStatusmeta
WHERE ticketposts.ID = companymeta.post_id
AND ticketposts.ID = ticketStatusmeta.post_id
AND ticketposts.ID = agentmeta.post_id
AND (companymeta.meta_key = 'ticket-selectedCompany' AND companymeta.meta_value = '$comp_DB_ID')
AND (agentmeta.meta_key = 'ticket-selectedAgent' AND agentmeta.meta_value = '$agent_id')
AND (ticketStatusmeta.meta_key = 'ticket-action-status' AND ticketStatusmeta.meta_value = 'Answered')
AND ticketposts.post_type = 'tickets'
AND ticketposts.post_status = 'publish' ORDER BY ticketposts.post_modified DESC");

$tickSolved_DB_ID_objs = $wpdb->get_results("SELECT ID FROM $wpdb->posts ticketposts, $wpdb->postmeta companymeta, $wpdb->postmeta agentmeta, $wpdb->postmeta ticketStatusmeta
WHERE ticketposts.ID = companymeta.post_id
AND ticketposts.ID = ticketStatusmeta.post_id
AND ticketposts.ID = agentmeta.post_id
AND (companymeta.meta_key = 'ticket-selectedCompany' AND companymeta.meta_value = '$comp_DB_ID')
AND (agentmeta.meta_key = 'ticket-selectedAgent' AND agentmeta.meta_value = '$agent_id')
AND (ticketStatusmeta.meta_key = 'ticket-action-status' AND ticketStatusmeta.meta_value = 'Solved')
AND ticketposts.post_type = 'tickets'
AND ticketposts.post_status = 'publish' ORDER BY ticketposts.post_modified DESC");

?>
<div class="msidemenu">
    <p><a href="<?php echo get_permalink($comp_DB_ID)."admin/agents/?pagenumber=1" ?>"><?php _e('Agents Home','mhelpdesk');?></a></p>
    <p><a href="<?php echo get_permalink($comp_DB_ID)."admin/agents/?action=agent_tickets&id=".$agent_id."&pagenumber=1"; ?>">
        <?php _e("All Assigned Tickets",'mhelpdesk');?> <span class="ticketcounter_right">(<?php echo count($tickAll_DB_ID_objs); ?>)</span></a></p>
    <p><a href="?action=opened&id=<?php echo $agent_id; ?>&pagenumber=1"><?php _e('Opened','mhelpdesk');?> 
    	<span class="ticketcounter_right">(<?php echo count($tickOpened_DB_ID_objs); ?>)</span></a></p>
    <p><a href="?action=closed&id=<?php echo $agent_id; ?>&pagenumber=1"><?php _e('Closed','mhelpdesk');?>
    	<span class="ticketcounter_right">(<?php echo count($tickClosed_DB_ID_objs); ?>)</span></a></p>
    <p><a href="?action=unanswered&id=<?php echo $agent_id; ?>&pagenumber=1"><?php _e('Un-Answered','mhelpdesk');?>
    	<span class="ticketcounter_right">(<?php echo count($tickUnanswered_DB_ID_objs); ?>)</span></a></p>
    <p><a href="?action=answered&id=<?php echo $agent_id; ?>&pagenumber=1"><?php _e('Answered','mhelpdesk');?>
    	<span class="ticketcounter_right">(<?php echo count($tickAanswered_DB_ID_objs); ?>)</span></a></p>
    <p><a href="?action=solved&id=<?php echo $agent_id; ?>&pagenumber=1"><?php _e('Solved','mhelpdesk');?>
    	<span class="ticketcounter_right">(<?php echo count($tickSolved_DB_ID_objs); ?>)</span></a></p>
</div>
<?php
} // function showAgent_leftmenu()






//print_r($comp_DB_title);
$DBcomp_agentEmails = get_post_meta($comp_DB_ID,'company-agentEmail',true) ? get_post_meta($comp_DB_ID,'company-agentEmail',true) : array();
		if(isset($_POST['company-adminAgentBtn']) && wp_verify_nonce( $_POST['company-adminAgentBtn'], 'adminAgentBtn' ) && isset($_POST['adminAgentBtn']) ) {
			
			if($_POST['agentemail'][0] != ''){
				$hd_admin_settings_arr = get_option('APF_MyFirstFrom');
		
				global $global_agent_limit_status,$global_notification_permission;
	
				$global_notification_permission = 'true';
				
				$global_agent_limit_status = 'ok';

				do_action( 'before_agent_add', $comp_DB_ID );
		
			if(array_search('hd-wc-extenstion/index_hd_wc_extention.php',$active_plugins)!== false)
				$global_agent_limit_status = apply_filters('before_agent_add_wc_filter',$comp_DB_ID,$global_agent_limit_status);
		
				if($global_agent_limit_status == 'ok'){
					do_action( 'company_created_updated', array('statusFlag'=>'newAgents','companyID'=>$comp_DB_ID,'company_name'=>$comp_DB_title) );
					
					foreach($_POST['agentemail'] as $agentemail)
						$agents_email_arr[] = sanitize_email( $agentemail );
					
					update_metadata('post', $comp_DB_ID, 'company-agentEmail', array_merge($DBcomp_agentEmails, $agents_email_arr));
					$message.='<div class="alert alert-success bg-success">New Agent(s) Added Successfully.</div>';
				}
			}// if($_POST['agentemail'][0] != '')
			
			

		//if(isset($_POST['adminAgentBtn']))
		//echo "<meta http-equiv=refresh content=0;url=".get_permalink($comp_DB_ID)."admin/agents/ />";
		} // if(isset($_POST['ticket-adminAgentBtn']) && wp_verify_nonce( $_POST['ticket-adminAgentBtn'], 'adminAgentBtn' )





$comp_DB_ID_obj = $wpdb->get_results("SELECT ID FROM $wpdb->posts WHERE post_type = 'companies' AND post_status = 'publish' AND post_name = '$urlCompSlug'");
	if(isset($comp_DB_ID_obj[0])){
		
		$comp_DB_ID = $comp_DB_ID_obj[0]->ID;
		
$comp_authCheck_obj = $wpdb->get_results("SELECT post_author FROM $wpdb->posts companyposts, $wpdb->postmeta companymeta
WHERE companyposts.ID = companymeta.post_id  AND companyposts.post_name = '$urlCompSlug'
AND companyposts.post_type = 'companies'
AND companyposts.post_status = 'publish'");
		
		if(isset($comp_authCheck_obj[0]))
			if($comp_authCheck_obj[0]->post_author == $curUserID){
				
	$DBcomp_agentEmails = get_metadata('post', $comp_DB_ID,'company-agentEmail',true);
				
	$agent_id = preg_replace('/[^0-9]/','',isset($_GET['id']) ? $_GET['id'] : '');
	$ticketsperpage = get_metadata('post', $comp_DB_ID, 'company-tickets-perpage', true);
	$ticketsperpage = $ticketsperpage ? $ticketsperpage : 10;
	$viewedPageNum = (isset($_GET['pagenumber']) ? $_GET['pagenumber'] : 1 );
	$recordShow = ($ticketsperpage * $viewedPageNum) - $ticketsperpage;
		
	//////////////////////////////
	// View Aagent's Tickets ( ALL )
	//////////////////////////////
		$agent_assigned_tickets = $wpdb->get_results("SELECT ID FROM $wpdb->posts ticketposts, $wpdb->postmeta companymeta, $wpdb->postmeta agentmeta
		WHERE ticketposts.ID = companymeta.post_id
		AND ticketposts.ID = agentmeta.post_id
		AND (companymeta.meta_key = 'ticket-selectedCompany' AND companymeta.meta_value = '$comp_DB_ID')
		AND (agentmeta.meta_key = 'ticket-selectedAgent' AND agentmeta.meta_value = '$agent_id')
		AND ticketposts.post_type = 'tickets'
		AND ticketposts.post_status = 'publish'");

	
	if(isset($_GET['action']) && $_GET['action'] == 'agent_tickets'){ // ( ALL Agent's Tickets)
		$tickets = $wpdb->get_results("SELECT ID, post_title, post_author, post_modified FROM $wpdb->posts ticketposts, $wpdb->postmeta companymeta, $wpdb->postmeta agentmeta
		WHERE ticketposts.ID = companymeta.post_id
		AND ticketposts.ID = agentmeta.post_id
		AND (companymeta.meta_key = 'ticket-selectedCompany' AND companymeta.meta_value = '$comp_DB_ID')
		AND (agentmeta.meta_key = 'ticket-selectedAgent' AND agentmeta.meta_value = '$agent_id')
		AND ticketposts.post_type = 'tickets'
		AND ticketposts.post_status = 'publish' ORDER BY ticketposts.post_modified DESC LIMIT $recordShow, $ticketsperpage");
		
		$pages = $wpdb->get_results("SELECT ID FROM $wpdb->posts ticketposts, $wpdb->postmeta companymeta, $wpdb->postmeta agentmeta
		WHERE ticketposts.ID = companymeta.post_id
		AND ticketposts.ID = agentmeta.post_id
		AND (companymeta.meta_key = 'ticket-selectedCompany' AND companymeta.meta_value = '$comp_DB_ID')
		AND (agentmeta.meta_key = 'ticket-selectedAgent' AND agentmeta.meta_value = '$agent_id')
		AND ticketposts.post_type = 'tickets'
		AND ticketposts.post_status = 'publish'");
		
	} // if($_GET['action'] == 'agent_tickets')
	
	
	
	
	////////////////////////////////////
	// View Agent's Rated Tickets
	///////////////////////////////////
		$agent_rated_tickets = $wpdb->get_results("SELECT ID FROM $wpdb->posts ticketposts, $wpdb->postmeta companymeta, $wpdb->postmeta agentmeta
		WHERE ticketposts.ID = companymeta.post_id
		AND ticketposts.ID = agentmeta.post_id
		AND (companymeta.meta_key = 'ticket-selectedCompany' AND companymeta.meta_value = '$comp_DB_ID')
		AND (agentmeta.meta_key = 'ticket-selectedAgent' AND agentmeta.meta_value = '$agent_id')
		AND EXISTS (
					  SELECT post_id FROM $wpdb->postmeta
					  WHERE $wpdb->postmeta.meta_key = 'ticket-rating' AND $wpdb->postmeta.meta_value > 0
					  AND ticketposts.ID = $wpdb->postmeta.post_id
					)           
		AND ticketposts.post_type = 'tickets'
		AND ticketposts.post_status = 'publish'");

	
	if(isset($_GET['action']) && $_GET['action'] == 'rated'){	
	//$recordShow = ($ticketsperpage * $viewedPageNum) - $ticketsperpage;
	
		$tickets = $wpdb->get_results("SELECT ID, post_title, post_author, post_modified FROM $wpdb->posts ticketposts, $wpdb->postmeta companymeta, $wpdb->postmeta agentmeta
		WHERE ticketposts.ID = companymeta.post_id
		AND ticketposts.ID = agentmeta.post_id
		AND (companymeta.meta_key = 'ticket-selectedCompany' AND companymeta.meta_value = '$comp_DB_ID')
		AND (agentmeta.meta_key = 'ticket-selectedAgent' AND agentmeta.meta_value = '$agent_id')
		 AND EXISTS (
					  SELECT post_id FROM $wpdb->postmeta
					   WHERE $wpdb->postmeta.meta_key = 'ticket-rating' AND $wpdb->postmeta.meta_value > 0
						AND $wpdb->postmeta.post_id=ticketposts.ID
					)           
		AND ticketposts.post_type = 'tickets'
		AND ticketposts.post_status = 'publish' ORDER BY ticketposts.post_modified DESC LIMIT $recordShow, $ticketsperpage");

	$pages = $wpdb->get_results("SELECT ID FROM $wpdb->posts ticketposts, $wpdb->postmeta companymeta, $wpdb->postmeta agentmeta
	WHERE ticketposts.ID = companymeta.post_id
	AND ticketposts.ID = agentmeta.post_id
	AND (companymeta.meta_key = 'ticket-selectedCompany' AND companymeta.meta_value = '$comp_DB_ID')
	AND (agentmeta.meta_key = 'ticket-selectedAgent' AND agentmeta.meta_value = '$agent_id')
	AND EXISTS (
				  SELECT post_id FROM $wpdb->postmeta
				  WHERE $wpdb->postmeta.meta_key = 'ticket-rating' AND $wpdb->postmeta.meta_value > 0
				  AND $wpdb->postmeta.post_id=ticketposts.ID
				)           
	AND ticketposts.post_type = 'tickets'
	AND ticketposts.post_status = 'publish'");
		
	} // if($_GET['action'] == 'rated')
	
	


	////////////////////////////////////
	// View Agent's Opened Tickets
	///////////////////////////////////
		$agent_opened_tickets = $wpdb->get_results("SELECT ID FROM $wpdb->posts ticketposts, $wpdb->postmeta companymeta, 
		$wpdb->postmeta agentmeta, $wpdb->postmeta ticketStatusmeta
		WHERE ticketposts.ID = companymeta.post_id
		AND ticketposts.ID = ticketStatusmeta.post_id
		AND ticketposts.ID = agentmeta.post_id
		AND (companymeta.meta_key = 'ticket-selectedCompany' AND companymeta.meta_value = '$comp_DB_ID')
		AND (agentmeta.meta_key = 'ticket-selectedAgent' AND agentmeta.meta_value = '$agent_id')
		AND (ticketStatusmeta.meta_key = 'ticket-status' AND ticketStatusmeta.meta_value = 'Opened')
		AND ticketposts.post_type = 'tickets'
		AND ticketposts.post_status = 'publish'");
	if(isset($_GET['action']) && $_GET['action'] == 'opened'){	
	//$recordShow = ($ticketsperpage * $viewedPageNum) - $ticketsperpage;
	
	$tickets = $wpdb->get_results("SELECT ID, post_title, post_author, post_modified FROM $wpdb->posts ticketposts, $wpdb->postmeta companymeta, $wpdb->postmeta agentmeta, 
	$wpdb->postmeta ticketStatusmeta
	WHERE ticketposts.ID = companymeta.post_id
	AND ticketposts.ID = ticketStatusmeta.post_id
	AND ticketposts.ID = agentmeta.post_id
	AND (companymeta.meta_key = 'ticket-selectedCompany' AND companymeta.meta_value = '$comp_DB_ID')
	AND (agentmeta.meta_key = 'ticket-selectedAgent' AND agentmeta.meta_value = '$agent_id')
	AND (ticketStatusmeta.meta_key = 'ticket-status' AND ticketStatusmeta.meta_value = 'Opened')
	AND ticketposts.post_type = 'tickets'
	AND ticketposts.post_status = 'publish' ORDER BY ticketposts.post_modified DESC LIMIT $recordShow, $ticketsperpage");

		$pages = $wpdb->get_results("SELECT ID FROM $wpdb->posts ticketposts, $wpdb->postmeta companymeta, 
		$wpdb->postmeta agentmeta, $wpdb->postmeta ticketStatusmeta
		WHERE ticketposts.ID = companymeta.post_id
		AND ticketposts.ID = ticketStatusmeta.post_id
		AND ticketposts.ID = agentmeta.post_id
		AND (companymeta.meta_key = 'ticket-selectedCompany' AND companymeta.meta_value = '$comp_DB_ID')
		AND (agentmeta.meta_key = 'ticket-selectedAgent' AND agentmeta.meta_value = '$agent_id')
		AND (ticketStatusmeta.meta_key = 'ticket-status' AND ticketStatusmeta.meta_value = 'Opened')
		AND ticketposts.post_type = 'tickets'
		AND ticketposts.post_status = 'publish'");
		
	} // if($_GET['action'] == 'opened')
	
	
	
	
	
	$agent_unanswered_tickets = $wpdb->get_results("SELECT ID FROM $wpdb->posts ticketposts, 
	$wpdb->postmeta companymeta, $wpdb->postmeta agentmeta, $wpdb->postmeta ticketStatusmeta
	WHERE ticketposts.ID = companymeta.post_id
	AND ticketposts.ID = ticketStatusmeta.post_id
	AND ticketposts.ID = agentmeta.post_id
	AND (companymeta.meta_key = 'ticket-selectedCompany' AND companymeta.meta_value = '$comp_DB_ID')
	AND (agentmeta.meta_key = 'ticket-selectedAgent' AND agentmeta.meta_value = '$agent_id')
	AND (ticketStatusmeta.meta_key = 'ticket-action-status' AND ticketStatusmeta.meta_value = 'Un-Answered')
	AND ticketposts.post_type = 'tickets'
	AND ticketposts.post_status = 'publish'");
	////////////////////////////////////
	// View Agent's Unanswered Tickets
	///////////////////////////////////
	if(isset($_GET['action']) && $_GET['action'] == 'unanswered'){
	
	//$recordShow = ($ticketsperpage * $viewedPageNum) - $ticketsperpage;
	
	$tickets = $wpdb->get_results("SELECT ID, post_title, post_author, post_modified FROM $wpdb->posts ticketposts, $wpdb->postmeta companymeta, $wpdb->postmeta agentmeta, 
	$wpdb->postmeta ticketStatusmeta
	WHERE ticketposts.ID = companymeta.post_id
	AND ticketposts.ID = ticketStatusmeta.post_id
	AND ticketposts.ID = agentmeta.post_id
	AND (companymeta.meta_key = 'ticket-selectedCompany' AND companymeta.meta_value = '$comp_DB_ID')
	AND (agentmeta.meta_key = 'ticket-selectedAgent' AND agentmeta.meta_value = '$agent_id')
	AND (ticketStatusmeta.meta_key = 'ticket-action-status' AND ticketStatusmeta.meta_value = 'Un-Answered')
	AND ticketposts.post_type = 'tickets'
	AND ticketposts.post_status = 'publish' ORDER BY ticketposts.post_modified DESC LIMIT $recordShow, $ticketsperpage");
	
	$pages = $wpdb->get_results("SELECT ID FROM $wpdb->posts ticketposts, 
	$wpdb->postmeta companymeta, $wpdb->postmeta agentmeta, $wpdb->postmeta ticketStatusmeta
	WHERE ticketposts.ID = companymeta.post_id
	AND ticketposts.ID = ticketStatusmeta.post_id
	AND ticketposts.ID = agentmeta.post_id
	AND (companymeta.meta_key = 'ticket-selectedCompany' AND companymeta.meta_value = '$comp_DB_ID')
	AND (agentmeta.meta_key = 'ticket-selectedAgent' AND agentmeta.meta_value = '$agent_id')
	AND (ticketStatusmeta.meta_key = 'ticket-action-status' AND ticketStatusmeta.meta_value = 'Un-Answered')
	AND ticketposts.post_type = 'tickets'
	AND ticketposts.post_status = 'publish'");
	}
	
	
	
	
	
	
	
	$agent_answered_tickets = $wpdb->get_results("SELECT ID FROM $wpdb->posts ticketposts, 
	$wpdb->postmeta companymeta, $wpdb->postmeta agentmeta, $wpdb->postmeta ticketStatusmeta
	WHERE ticketposts.ID = companymeta.post_id
	AND ticketposts.ID = ticketStatusmeta.post_id
	AND ticketposts.ID = agentmeta.post_id
	AND (companymeta.meta_key = 'ticket-selectedCompany' AND companymeta.meta_value = '$comp_DB_ID')
	AND (agentmeta.meta_key = 'ticket-selectedAgent' AND agentmeta.meta_value = '$agent_id')
	AND (ticketStatusmeta.meta_key = 'ticket-action-status' AND ticketStatusmeta.meta_value = 'Answered')
	AND ticketposts.post_type = 'tickets'
	AND ticketposts.post_status = 'publish'");	
	//////////////////////////////
	// View Agent's Answered Tickets
	//////////////////////////////
	if(isset($_GET['action']) && $_GET['action'] == 'answered'){
	
	//$recordShow = ($ticketsperpage * $viewedPageNum) - $ticketsperpage;
	
	$tickets = $wpdb->get_results("SELECT ID, post_title, post_author, post_modified FROM $wpdb->posts ticketposts, $wpdb->postmeta companymeta, $wpdb->postmeta agentmeta, 
	$wpdb->postmeta ticketStatusmeta
	WHERE ticketposts.ID = companymeta.post_id
	AND ticketposts.ID = ticketStatusmeta.post_id
	AND ticketposts.ID = agentmeta.post_id
	AND (companymeta.meta_key = 'ticket-selectedCompany' AND companymeta.meta_value = '$comp_DB_ID')
	AND (agentmeta.meta_key = 'ticket-selectedAgent' AND agentmeta.meta_value = '$agent_id')
	AND (ticketStatusmeta.meta_key = 'ticket-action-status' AND ticketStatusmeta.meta_value = 'Answered')
	AND ticketposts.post_type = 'tickets'
	AND ticketposts.post_status = 'publish' ORDER BY ticketposts.post_modified DESC LIMIT $recordShow, $ticketsperpage"); 
	
	$pages = $wpdb->get_results("SELECT ID FROM $wpdb->posts ticketposts, 
	$wpdb->postmeta companymeta, $wpdb->postmeta agentmeta, $wpdb->postmeta ticketStatusmeta
	WHERE ticketposts.ID = companymeta.post_id
	AND ticketposts.ID = ticketStatusmeta.post_id
	AND ticketposts.ID = agentmeta.post_id
	AND (companymeta.meta_key = 'ticket-selectedCompany' AND companymeta.meta_value = '$comp_DB_ID')
	AND (agentmeta.meta_key = 'ticket-selectedAgent' AND agentmeta.meta_value = '$agent_id')
	AND (ticketStatusmeta.meta_key = 'ticket-action-status' AND ticketStatusmeta.meta_value = 'Answered')
	AND ticketposts.post_type = 'tickets'
	AND ticketposts.post_status = 'publish'");
	}
	
	
	
	
	
	$agent_solved_tickets = $wpdb->get_results("SELECT ID FROM $wpdb->posts ticketposts, $wpdb->postmeta companymeta, $wpdb->postmeta agentmeta, 
	$wpdb->postmeta ticketStatusmeta
	WHERE ticketposts.ID = companymeta.post_id
	AND ticketposts.ID = ticketStatusmeta.post_id
	AND ticketposts.ID = agentmeta.post_id
	AND (companymeta.meta_key = 'ticket-selectedCompany' AND companymeta.meta_value = '$comp_DB_ID')
	AND (agentmeta.meta_key = 'ticket-selectedAgent' AND agentmeta.meta_value = '$agent_id')
	AND (ticketStatusmeta.meta_key = 'ticket-action-status' AND ticketStatusmeta.meta_value = 'Solved')
	AND ticketposts.post_type = 'tickets'
	AND ticketposts.post_status = 'publish'");	
	//////////////////////////////
	// View Agent's Solved Tickets
	//////////////////////////////
	if(isset($_GET['action']) && $_GET['action'] == 'solved'){
	
	//$recordShow = ($ticketsperpage * $viewedPageNum) - $ticketsperpage;
	
	$tickets = $wpdb->get_results("SELECT ID, post_title, post_author, post_modified FROM $wpdb->posts ticketposts, $wpdb->postmeta companymeta, $wpdb->postmeta agentmeta, 
	$wpdb->postmeta ticketStatusmeta
	WHERE ticketposts.ID = companymeta.post_id
	AND ticketposts.ID = ticketStatusmeta.post_id
	AND ticketposts.ID = agentmeta.post_id
	AND (companymeta.meta_key = 'ticket-selectedCompany' AND companymeta.meta_value = '$comp_DB_ID')
	AND (agentmeta.meta_key = 'ticket-selectedAgent' AND agentmeta.meta_value = '$agent_id')
	AND (ticketStatusmeta.meta_key = 'ticket-action-status' AND ticketStatusmeta.meta_value = 'Solved')
	AND ticketposts.post_type = 'tickets'
	AND ticketposts.post_status = 'publish' ORDER BY ticketposts.post_modified DESC LIMIT $recordShow, $ticketsperpage"); 
	
	$pages = $wpdb->get_results("SELECT ID FROM $wpdb->posts ticketposts, 
	$wpdb->postmeta companymeta, $wpdb->postmeta agentmeta, $wpdb->postmeta ticketStatusmeta
	WHERE ticketposts.ID = companymeta.post_id
	AND ticketposts.ID = ticketStatusmeta.post_id
	AND ticketposts.ID = agentmeta.post_id
	AND (companymeta.meta_key = 'ticket-selectedCompany' AND companymeta.meta_value = '$comp_DB_ID')
	AND (agentmeta.meta_key = 'ticket-selectedAgent' AND agentmeta.meta_value = '$agent_id')
	AND (ticketStatusmeta.meta_key = 'ticket-action-status' AND ticketStatusmeta.meta_value = 'Solved')
	AND ticketposts.post_type = 'tickets'
	AND ticketposts.post_status = 'publish'");
	} 







	////////////////////////////////////
	// View Agent's Opened Tickets
	///////////////////////////////////
		$agent_closed_tickets = $wpdb->get_results("SELECT ID FROM $wpdb->posts ticketposts, $wpdb->postmeta companymeta, 
		$wpdb->postmeta agentmeta, $wpdb->postmeta ticketStatusmeta
		WHERE ticketposts.ID = companymeta.post_id
		AND ticketposts.ID = ticketStatusmeta.post_id
		AND ticketposts.ID = agentmeta.post_id
		AND (companymeta.meta_key = 'ticket-selectedCompany' AND companymeta.meta_value = '$comp_DB_ID')
		AND (agentmeta.meta_key = 'ticket-selectedAgent' AND agentmeta.meta_value = '$agent_id')
		AND (ticketStatusmeta.meta_key = 'ticket-status' AND ticketStatusmeta.meta_value = 'Closed')
		AND ticketposts.post_type = 'tickets'
		AND ticketposts.post_status = 'publish'");
	if(isset($_GET['action']) && $_GET['action'] == 'closed'){	
	//$recordShow = ($ticketsperpage * $viewedPageNum) - $ticketsperpage;
	
	$tickets = $wpdb->get_results("SELECT ID, post_title, post_author, post_modified FROM $wpdb->posts ticketposts, $wpdb->postmeta companymeta, $wpdb->postmeta agentmeta, 
	$wpdb->postmeta ticketStatusmeta
	WHERE ticketposts.ID = companymeta.post_id
	AND ticketposts.ID = ticketStatusmeta.post_id
	AND ticketposts.ID = agentmeta.post_id
	AND (companymeta.meta_key = 'ticket-selectedCompany' AND companymeta.meta_value = '$comp_DB_ID')
	AND (agentmeta.meta_key = 'ticket-selectedAgent' AND agentmeta.meta_value = '$agent_id')
	AND (ticketStatusmeta.meta_key = 'ticket-status' AND ticketStatusmeta.meta_value = 'Closed')
	AND ticketposts.post_type = 'tickets'
	AND ticketposts.post_status = 'publish' ORDER BY ticketposts.post_modified DESC LIMIT $recordShow, $ticketsperpage");

		$pages = $wpdb->get_results("SELECT ID FROM $wpdb->posts ticketposts, $wpdb->postmeta companymeta, 
		$wpdb->postmeta agentmeta, $wpdb->postmeta ticketStatusmeta
		WHERE ticketposts.ID = companymeta.post_id
		AND ticketposts.ID = ticketStatusmeta.post_id
		AND ticketposts.ID = agentmeta.post_id
		AND (companymeta.meta_key = 'ticket-selectedCompany' AND companymeta.meta_value = '$comp_DB_ID')
		AND (agentmeta.meta_key = 'ticket-selectedAgent' AND agentmeta.meta_value = '$agent_id')
		AND (ticketStatusmeta.meta_key = 'ticket-status' AND ticketStatusmeta.meta_value = 'Closed')
		AND ticketposts.post_type = 'tickets'
		AND ticketposts.post_status = 'publish'");
		
	} // if($_GET['action'] == 'closed')

if(isset($pages)){	
$pages = count($pages) / (int)$ticketsperpage;
if($pages > 0) if(is_float($pages)) $pages = ((int)$pages +1);
}
	









	
	
	/////////////////////////////
	// Delete Agent by ID Block
	/////////////////////////////
	if(isset($_GET['action']) && $_GET['action'] == 'delete' && isset($_GET['id'])){
		
		$userTableAgentEmail_DB_Obj = $wpdb->get_results("SELECT display_name, user_email FROM $wpdb->users WHERE ID = '$agent_id'"); 
	 
		$userTableAgentEmail_DB_arr = (array) $userTableAgentEmail_DB_Obj[0];
	 
		$emailFound_index = array_search($userTableAgentEmail_DB_arr['user_email'], isset($DBcomp_agentEmails[0]) ? $DBcomp_agentEmails : array()); 
	
		if($emailFound_index > -1 ){
			unset($DBcomp_agentEmails[$emailFound_index]);
			sort($DBcomp_agentEmails);		
			update_post_meta($comp_DB_ID, 'company-agentEmail', $DBcomp_agentEmails);
			echo "<meta http-equiv=refresh content=0;url='../agents/' />";
		}
		else echo "".__('This is Irrelevant ID ','mhelpdesk')."($agent_id)!";

		
	} // if($_GET['action'] == 'delete' && isset($_GET['id']))
		






///////////////////////////////
// Delete Agent by Email
///////////////////////////////
if(isset($_GET['action']) && $_GET['action'] == 'delete' && isset($_GET['agent_email'])){
 
 	$emailFound_index = array_search($_GET['agent_email'], isset($DBcomp_agentEmails[0]) ? $DBcomp_agentEmails : array()); 
	
		if($emailFound_index > -1 ){
			unset($DBcomp_agentEmails[$emailFound_index]);
			sort($DBcomp_agentEmails);		
			update_post_meta($comp_DB_ID, 'company-agentEmail', $DBcomp_agentEmails);
			echo "<meta http-equiv=refresh content=0;url='../agents/' />";
		}
		else echo "".__('This is Irrelevant ID ','mhelpdesk')."($agent_id)!";
	
	
	
} // if($_GET['action'] == 'delete')






//print_r($_POST['ticketCBs']);




if(isset($_POST['ticketCBs'])){
$ticketCBs = ($_POST['ticketCBs']);
$agentID = preg_replace('/[^0-9]/','',$_GET['id']);

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
	echo "<meta http-equiv=refresh content=0;url=../agents//?action=agent_tickets&id=$agentID />";
} // if(isset($_POST['ticketCBs']))
	
	
	
	
	
				global $mtheme;
				$company_admin_tickets_arr = show_company_admin_tickets();
				$url_action_value = isset($_GET['action']) ? sanitize_text_field($_GET['action']) : false;
				include($mtheme.'admin-controls/admin-agents.php');

	

			} //if(isset($comp_authCheck_obj[0]))
			else echo '<h1 >You are not AUTHORIZED to access this page</h1>';
			
	} // if(isset($comp_DB_ID_obj[0]))
	else echo '<h1 >'.__('The accessed Company is not registered yet!','mhelpdesk').'</h1>';

	 
echo '</div>';
get_plugin_footer(); ?>