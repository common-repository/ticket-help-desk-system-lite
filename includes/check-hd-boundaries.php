<?php
///////////////////////////////////////////
/// Controling the Company/ Helpdesk Limit
///////////////////////////////////////////

add_action('before_company_created','check_hd_limit');
function check_hd_limit(){
$hd_admin_settings_arr = get_option('APF_MyFirstFrom');
$curUserID = get_current_user_id();
global $wpdb;
global $global_hd_limit_status;

$curUser_hds = $wpdb->get_results("SELECT ID FROM $wpdb->posts WHERE post_author = '$curUserID' AND post_type = 'companies' AND post_status = 'publish'");

	if(isset($curUser_hds[0])){
		
/*
						1		3	true
						2		3	true
						3		3	false
						4		3	false

*/		
		if(count($curUser_hds) < $hd_admin_settings_arr['m_helpdesk_form']['helpdesk_number'] || $hd_admin_settings_arr['m_helpdesk_form']['helpdesk_number'] <= 0)
			$global_hd_limit_status = 'ok';
		else
			$global_hd_limit_status = 'full';
	}
	else $global_hd_limit_status = 'ok';

} // function check_hd_limit()








///////////////////////////////////////////////////////
/// Controling the Limit of the Tickets of the Company
///////////////////////////////////////////////////////

add_action('before_ticket_created','check_hd_tickets_limit');
function check_hd_tickets_limit($paramCID){
$hd_admin_settings_arr = get_option('APF_MyFirstFrom');
$curUserID = get_current_user_id();
global $wpdb;
global $global_ticket_limit_status;

$tickAll_DB_ID_objs = $wpdb->get_results("SELECT ID FROM $wpdb->posts ticketposts, $wpdb->postmeta companymeta
WHERE ticketposts.ID = companymeta.post_id
AND (companymeta.meta_key = 'ticket-selectedCompany' AND companymeta.meta_value = '$paramCID')
AND ticketposts.post_type = 'tickets'
AND ticketposts.post_status = 'publish'");


	if(isset($tickAll_DB_ID_objs[0])){
		
/*
						1		3	true
						2		3	true
						3		3	false
						4		3	false

*/		
		if(count($tickAll_DB_ID_objs) < $hd_admin_settings_arr['m_helpdesk_form']['helpdesk_tickets'] || $hd_admin_settings_arr['m_helpdesk_form']['helpdesk_tickets'] <= 0)
			$global_ticket_limit_status = 'ok';
		else
			$global_ticket_limit_status = 'full';
	}
	else $global_ticket_limit_status = 'ok';

} // function check_hd_tickets_limit($paramCID)








/////////////////////////////////
/// Controling the Agents Limit
////////////////////////////////
add_action('before_agent_add','check_agent_limit');
function check_agent_limit($paramCID){
$hd_admin_settings_arr = get_option('APF_MyFirstFrom');
$DBcomp_agentEmails = get_metadata('post', $paramCID, 'company-agentEmail', true);


global $global_agent_limit_status;
	if(isset($DBcomp_agentEmails[0])){
				
		if(count($DBcomp_agentEmails) < $hd_admin_settings_arr['m_helpdesk_form']['helpdesk_agents'] || $hd_admin_settings_arr['m_helpdesk_form']['helpdesk_agents'] <= 0)
			$global_agent_limit_status = 'ok';
		else
			$global_agent_limit_status = 'full';
	}
	else $global_agent_limit_status = 'ok';
	

} // function check_agent_limit($paramCID)









///////////////////////////////////
/// Controling the Customer Limit
//////////////////////////////////
add_action('before_customer_add','check_customer_limit');
function check_customer_limit($paramCID){

$compCustomerOpened_DB_arr = get_metadata('post', $paramCID, 'company-customers-opened', true);

global $global_customer_limit_status;
$hd_admin_settings_arr = get_option('APF_MyFirstFrom');

if(isset($compCustomerOpened_DB_arr[0])){
		
/*
						1		3	true
						2		3	true
						3		3	false
						4		3	false

*/		
	if(count($compCustomerOpened_DB_arr) < $hd_admin_settings_arr['m_helpdesk_form']['helpdesk_customers'] || $hd_admin_settings_arr['m_helpdesk_form']['helpdesk_customers'] <= 0)
		$global_customer_limit_status = 'ok';
	else
		$global_customer_limit_status = 'full';
	}
else { $global_customer_limit_status = 'ok';}
} // function check_customer_limit($paramCID)

?>