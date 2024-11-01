<?php 
	$hd_admin_settings_arr = get_option('APF_MyFirstFrom');
		
	$helpdesk_rewriterule_slug = isset($hd_admin_settings_arr['m_helpdesk_form']['helpdesk_rewriterule_slug']) && $hd_admin_settings_arr['m_helpdesk_form']['helpdesk_rewriterule_slug'] ? $hd_admin_settings_arr['m_helpdesk_form']['helpdesk_rewriterule_slug'] : 'company';

	$title = "Admin Dashboard";
		global $wpdb;
		$urlCompSlug = get_query_var('companyname');
		$userRole = get_query_var( 'userrole' );
		$comp_DB_ID_obj = $wpdb->get_results("SELECT ID FROM $wpdb->posts WHERE post_type = 'companies' AND post_status = 'publish' AND post_name = '$urlCompSlug'");
		
		$comp_DB_ID = $comp_DB_ID_obj[0]->ID;

get_plugin_header();
echo '<div class="hdwrap">';
	if ( is_user_logged_in() ) {
		global $wpdb;
		
		$curUserID = get_current_user_id();
	
$comp_authCheck_obj = $wpdb->get_results("SELECT post_author FROM $wpdb->posts companyposts, $wpdb->postmeta companymeta
WHERE companyposts.ID = companymeta.post_id  AND companyposts.post_name = '$urlCompSlug'
AND companyposts.post_type = 'companies'
AND companyposts.post_status = 'publish'");
//print_r($comp_authCheck_obj);		
		
		if(isset($comp_DB_ID_obj[0])){
		$comp_DB_ID = $comp_DB_ID_obj[0]->ID;
		
			if(isset($comp_authCheck_obj[0]))
					if($comp_authCheck_obj[0]->post_author == $curUserID){
						
$all_tickets = $wpdb->get_results("SELECT ID FROM $wpdb->posts ticketposts, $wpdb->postmeta companymeta
WHERE ticketposts.ID = companymeta.post_id
AND (companymeta.meta_key = 'ticket-selectedCompany' AND companymeta.meta_value = '$comp_DB_ID')
AND ticketposts.post_type = 'tickets'
AND ticketposts.post_status = 'publish'");


$tickets_perpage = get_metadata('post', $comp_DB_ID, 'company-tickets-perpage', true);

$recent_tickets = $wpdb->get_results("SELECT ID, post_title, post_modified FROM $wpdb->posts ticketposts, $wpdb->postmeta companymeta
WHERE ticketposts.ID = companymeta.post_id
AND (companymeta.meta_key = 'ticket-selectedCompany' AND companymeta.meta_value = '$comp_DB_ID')
AND ticketposts.post_type = 'tickets'
AND ticketposts.post_status = 'publish' ORDER BY ticketposts.post_modified DESC LIMIT $tickets_perpage");



/////////////////////////////
/// All New / Un-Assinged Ticketsc
/////////////////////////////
$total_unassinged_tickets = $wpdb->get_results("SELECT ID FROM $wpdb->posts ticketposts, $wpdb->postmeta companymeta
WHERE ticketposts.ID = companymeta.post_id
AND (companymeta.meta_key = 'ticket-selectedCompany' AND companymeta.meta_value = '$comp_DB_ID')
 AND NOT EXISTS (
              SELECT post_id FROM $wpdb->postmeta
               WHERE $wpdb->postmeta.post_id=ticketposts.ID
                AND $wpdb->postmeta.meta_key = 'ticket-selectedAgent'
            ) 
AND ticketposts.post_type = 'tickets'
AND ticketposts.post_status = 'publish'");









/*$unassingedTickCounter = 0;
foreach($all_tickets as $ticket){
	
	$tickSelectedAgentID = get_metadata('post',$ticket->ID, 'ticket-selectedAgent',true);
	$userAgent_DB_Obj = $wpdb->get_results("SELECT display_name FROM $wpdb->users WHERE ID = '$tickSelectedAgentID'");
		if(!isset($userAgent_DB_Obj[0]))
			$unassinged_TickCounter++;
} // foreach($all_tickets as $ticket)

$total_unassinged_tickets = $unassinged_TickCounter;*/







////////////////////////
/// All Rated Tickets
////////////////////////
/*$ratedTickCounter = 0;
foreach($all_tickets as $ticket){
	
	$rating = get_metadata('post',$ticket->ID,"ticket-rating",true);

		if ( $rating )
			$ratedTickCounter++;
} // foreach($all_tickets as $ticket)*/


$total_rated_tickets = $wpdb->get_results("SELECT ID FROM $wpdb->posts ticketposts, $wpdb->postmeta companymeta
WHERE ticketposts.ID = companymeta.post_id
AND (companymeta.meta_key = 'ticket-selectedCompany' AND companymeta.meta_value = '$comp_DB_ID')
 AND EXISTS (
              SELECT post_id FROM $wpdb->postmeta
               WHERE $wpdb->postmeta.meta_key = 'ticket-rating' AND $wpdb->postmeta.meta_value > 0
                AND $wpdb->postmeta.post_id=ticketposts.ID
            )           
AND ticketposts.post_type = 'tickets'
AND ticketposts.post_status = 'publish'");







$all_open_tickets = $wpdb->get_results("SELECT ID FROM $wpdb->posts ticketposts, $wpdb->postmeta companymeta, $wpdb->postmeta ticketStatusmeta
WHERE ticketposts.ID = companymeta.post_id
AND ticketposts.ID = ticketStatusmeta.post_id
AND (companymeta.meta_key = 'ticket-selectedCompany' AND companymeta.meta_value = '$comp_DB_ID')
AND (ticketStatusmeta.meta_key = 'ticket-status' AND ticketStatusmeta.meta_value = 'Opened')
AND ticketposts.post_type = 'tickets'
AND ticketposts.post_status = 'publish'");

$all_closed_tickets = $wpdb->get_results("SELECT ID FROM $wpdb->posts ticketposts, $wpdb->postmeta companymeta, $wpdb->postmeta ticketStatusmeta
WHERE ticketposts.ID = companymeta.post_id
AND ticketposts.ID = ticketStatusmeta.post_id
AND (companymeta.meta_key = 'ticket-selectedCompany' AND companymeta.meta_value = '$comp_DB_ID')
AND (ticketStatusmeta.meta_key = 'ticket-status' AND ticketStatusmeta.meta_value = 'Closed')
AND ticketposts.post_type = 'tickets'
AND ticketposts.post_status = 'publish'");


$all_unaswered_tickets = $wpdb->get_results("SELECT ID FROM $wpdb->posts ticketposts, $wpdb->postmeta companymeta, $wpdb->postmeta ticketStatusmeta
WHERE ticketposts.ID = companymeta.post_id
AND ticketposts.ID = ticketStatusmeta.post_id
AND (companymeta.meta_key = 'ticket-selectedCompany' AND companymeta.meta_value = '$comp_DB_ID')
AND (ticketStatusmeta.meta_key = 'ticket-action-status' AND ticketStatusmeta.meta_value = 'Un-Answered')
AND ticketposts.post_type = 'tickets'
AND ticketposts.post_status = 'publish'");


$all_answered_tickets = $wpdb->get_results("SELECT ID FROM $wpdb->posts ticketposts, $wpdb->postmeta companymeta, $wpdb->postmeta ticketStatusmeta
WHERE ticketposts.ID = companymeta.post_id
AND ticketposts.ID = ticketStatusmeta.post_id
AND (companymeta.meta_key = 'ticket-selectedCompany' AND companymeta.meta_value = '$comp_DB_ID')
AND (ticketStatusmeta.meta_key = 'ticket-action-status' AND ticketStatusmeta.meta_value = 'Answered')
AND ticketposts.post_type = 'tickets'
AND ticketposts.post_status = 'publish'");

$all_solved_tickets = $wpdb->get_results("SELECT ID FROM $wpdb->posts ticketposts, $wpdb->postmeta companymeta, $wpdb->postmeta ticketStatusmeta
WHERE ticketposts.ID = companymeta.post_id
AND ticketposts.ID = ticketStatusmeta.post_id
AND (companymeta.meta_key = 'ticket-selectedCompany' AND companymeta.meta_value = '$comp_DB_ID')
AND (ticketStatusmeta.meta_key = 'ticket-action-status' AND ticketStatusmeta.meta_value = 'Solved')
AND ticketposts.post_type = 'tickets'
AND ticketposts.post_status = 'publish'");

$total_agents = get_metadata('post',$comp_DB_ID,'company-agentEmail',true);  // array 
		
$total_customers = get_metadata('post', $comp_DB_ID, 'company-customers-opened', true);
$total_customers = $total_customers && is_array($total_customers) ? count($total_customers) : 0;
//print_r($total_customers);
						
$total_customers_blocked = get_metadata('post', $comp_DB_ID, 'company-customers-blocked', true);
$total_customers_blocked = $total_customers_blocked && is_array($total_customers_blocked) ? count($total_customers_blocked) : 0;
//var_dump($total_customers_blocked);
if(is_array($total_customers_blocked) && count($total_customers_blocked) > 0)
	$total_customers = $total_customers - $total_customers_blocked;

						

$hd_admin_settings_arr = get_option('APF_MyFirstFrom');

	
			//if($userRole == 'admin'){
				global $mtheme;
				$company_admin_tickets_arr = show_company_admin_tickets();
				include($mtheme.'admin-controls/admin-main.php');				
			} //if(isset($comp_authCheck_obj[0]))
			else  echo '<h1 >'.__('You are not AUTHORIZED to access this page','mhelpdesk').'</h1>';
			
		} // if(isset($comp_DB_ID_obj[0]))
		else echo '<h1 >'.__('The accessed Company is not registered yet!','mhelpdesk').'</h1>';
	} // if ( is_user_logged_in() )
else {
?>        
<div id="errorbox">
	<h1 ><?php _e('Only registered users are allowed to access this page','mhelpdesk');?></h1><br />
    <a href="<?php echo site_url().'/'.$helpdesk_rewriterule_slug; ?>"><?php _e('Click Here to Login or Register','mhelpdesk');?></a>
</div>
<?php } // ELSE  of if ( is_user_logged_in() )

echo '</div>';

get_plugin_footer(); ?>