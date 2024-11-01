<?php
add_filter( 'wp_mail_content_type', 'mhelpdesl_set_html_content_type' );
// Reset content-type to avoid conflicts -- http://core.trac.wordpress.org/ticket/23578
function mhelpdesl_set_html_content_type() {
		return 'text/html';
}
function hd_mail($to, $subject, $message, $headers){
	add_filter( 'wp_mail_content_type', 'mhelpdesl_set_html_content_type' );
	wp_mail( $to, $subject, $message);
	remove_filter( 'wp_mail_content_type', 'mhelpdesl_set_html_content_type' );
}



////////////////////////////////
//// Company Created/ Updated
////////////////////////////////

//////////////////////////////////
/// Notification to System Admin
//////////////////////////////////
add_action('company_created_updated','notifySysAdmin');
function notifySysAdmin($companyArr){
	$curUserID = get_current_user_id();
	if($companyArr['statusFlag'] == 'created' || $companyArr['statusFlag'] == 'updated'){
	
	global $wpdb;
	$compAdmin_DB_Obj = $wpdb->get_results("SELECT display_name, user_email FROM $wpdb->users WHERE ID = '$curUserID'");
	
		//if(isset($compAdmin_DB_Obj[0])){
			$headers  = "From: ".get_bloginfo('name')." <'".get_option('admin_email')."'>\r\n"; //$headers  = 'From: System Admin <'.get_option('admin_email').'>' . "\r\n";
			$to = get_option('admin_email');
			if($companyArr['statusFlag'] == 'created')
				$subject = sprintf(__("New Helpdesk Company is Added = %s",'mhelpdesk'), sanitize_text_field($_POST['companyTitle']));
			else
				$subject = sprintf(__("The Helpdesk Company is Updated = %s",'mhelpdesk'), sanitize_text_field($_POST['companyTitle']));
				
				
			
			$message  = sprintf(__("Company Administrator Name = %s ",'mhelpdesk'), $compAdmin_DB_Obj[0]->display_name).'<br />';
			$message .= sprintf(__("Administrator Email Address = %s ",'mhelpdesk'), $compAdmin_DB_Obj[0]->user_email)."<br />";
			$message .= sprintf(__("Company Name = %s ",'mhelpdesk'), sanitize_text_field($_POST['companyTitle']))."<br />"; 
			//die(print_r($headers).print_r('<br />').print_r($to).print_r('<br />').print_r($subject).print_r('<br />').print_r($message));
			//if($companyArr['statusFlag'] == 'Created') 
			hd_mail( $to, $subject, $message, $headers);
			//die(print_r($message));
		//} // if(isset($compAdmin_DB_Obj[0]))
	} // if($companyArr['statusFlag'] == 'created' || $companyArr['statusFlag'] == 'updated')
} // function notifySysAdmin($companyArr)





////////////////////////////////////
/// Notification to Company Owner
////////////////////////////////////
add_action('company_created_updated','notifyCompOwner');
function notifyCompOwner($companyArr){
	$hd_admin_settings_arr = get_option('APF_MyFirstFrom');
		
	$helpdesk_rewriterule_slug = isset($hd_admin_settings_arr['m_helpdesk_form']['helpdesk_rewriterule_slug']) && $hd_admin_settings_arr['m_helpdesk_form']['helpdesk_rewriterule_slug'] ? $hd_admin_settings_arr['m_helpdesk_form']['helpdesk_rewriterule_slug'] : 'company';

	if ( !is_user_logged_in() )
	wp_safe_redirect(site_url().'/'.$helpdesk_rewriterule_slug);

	$curUserID = get_current_user_id();
	if($companyArr['statusFlag'] == 'created' || $companyArr['statusFlag'] == 'updated'){

global $wpdb;
$compAdmin_DB_Obj = $wpdb->get_results("SELECT display_name, user_email FROM $wpdb->users WHERE ID = '$curUserID'");
	
		//if(isset($compAdmin_DB_Obj[0])){
			$headers  = "From: ".get_bloginfo('name')." <'".get_option('admin_email')."'>\r\n";
			$to = $compAdmin_DB_Obj[0]->user_email;
			if($companyArr['statusFlag'] == 'created')
			$subject = sprintf(__("Helpdesk Created Successfully = %s",'mhelpdesk'), sanitize_text_field($_POST['companyTitle']));
			else
			$subject = sprintf(__("Helpdesk Updated = %s",'mhelpdesk'), sanitize_text_field($_POST['companyTitle']));
			
			$message  = sprintf(__("Helpdesk Administrator (Your) Name = %s",'mhelpdesk'), $compAdmin_DB_Obj[0]->display_name)."<br />";
			$message .= sprintf(__("Email Address = %s",'mhelpdesk'), $compAdmin_DB_Obj[0]->user_email)."<br />";
			$message .= sprintf(__("Helpdesk Name = %s",'mhelpdesk'), sanitize_text_field($_POST['companyTitle']))."<br />";
			$message .= __("Login URL = ",'mhelpdesk').site_url()."/".$helpdesk_rewriterule_slug."<br />";
			//die(print_r($headers).print_r('<br />').print_r($to).print_r('<br />').print_r($subject).print_r('<br />').print_r($message));
			//if($companyArr['statusFlag'] == 'Created') 
			hd_mail( $to, $subject, $message, $headers);
			//die(print_r($message));
		//} // if(isset($compAdmin_DB_Obj[0]))
	}// if($companyArr['statusFlag'] == 'created' || $companyArr['statusFlag'] == 'updated')
} // function notifyCompOwner($companyArr)
//
//
//
//
//
//
////////////////////////////////////
/// Notification to Company Agent(s)
////////////////////////////////////
add_action('company_created_updated','notifyCompAgents');
function notifyCompAgents($companyArr){
	$hd_admin_settings_arr = get_option('APF_MyFirstFrom');
		
	$helpdesk_rewriterule_slug = isset($hd_admin_settings_arr['m_helpdesk_form']['helpdesk_rewriterule_slug']) && $hd_admin_settings_arr['m_helpdesk_form']['helpdesk_rewriterule_slug'] ? $hd_admin_settings_arr['m_helpdesk_form']['helpdesk_rewriterule_slug'] : 'company';
	
	$curUserID = get_current_user_id();
	if($companyArr['statusFlag'] == 'newAgents'){
	$companyTitle = isset($_POST['companyTitle']) ? sanitize_text_field($_POST['companyTitle']) : $companyArr['company_name'];
	global $wpdb;
	$compAdmin_DB_Obj = $wpdb->get_results("SELECT display_name, user_email FROM $wpdb->users WHERE ID = '$curUserID'");
	
		//if(isset($compAdmin_DB_Obj[0])){
			$headers  = "From: ".get_bloginfo('name')." <'".get_option('admin_email')."'>\r\n";
			$to = sanitize_text_field($_POST['agentemail']);
			$subject = sprintf(__("Helpdesk = ( %s ) added to you as an Agent",'mhelpdesk'), $companyTitle);
			
			$message  = sprintf(__("Helpdesk = ( %s ) added to you as an Agent. So, Login to the following URL",'mhelpdesk'),$companyTitle).'<br />';
			$message .= sprintf(__("Helpdesk Administrator = %s",'mhelpdesk'),$compAdmin_DB_Obj[0]->display_name)."<br />";
			$message .= sprintf(__("The Helpdesk Administrator's Email Address = %s",'mhelpdesk'),$compAdmin_DB_Obj[0]->user_email)."<br />";
			$message .= __("Login URL = ",'mhelpdesk').site_url()."/".$helpdesk_rewriterule_slug."<br />";
			//die(print_r($headers).print_r('<br />').print_r($to).print_r('<br />').print_r($subject).print_r('<br />').print_r($message));
			hd_mail( $to, $subject, $message, $headers);
			//die(print_r($to).print_r($message));
		//} // if(isset($compAdmin_DB_Obj[0]))
	} // if($companyArr['statusFlag'] == 'Created' || $companyArr['statusFlag'] == 'newAgent' ||  $companyArr['statusFlag'] == 'updated')
} // function notifyCompAgents($companyArr)

//
//
//
//
//
//
//
//
//
//
//
///////////////////////
//// Ticket Created
//////////////////////

////////////////////////////////////
/// Notification to Ticket Owner
////////////////////////////////////
add_action('ticket_created','notifyTickOwner');
function notifyTickOwner($newTickArr){
//	add_filter( 'wp_mail_content_type', 'mhelpdesl_set_html_content_type' );
	$hd_admin_settings_arr = get_option('APF_MyFirstFrom');
		
	$helpdesk_rewriterule_slug = isset($hd_admin_settings_arr['m_helpdesk_form']['helpdesk_rewriterule_slug']) && $hd_admin_settings_arr['m_helpdesk_form']['helpdesk_rewriterule_slug'] ? $hd_admin_settings_arr['m_helpdesk_form']['helpdesk_rewriterule_slug'] : 'company';
	$curUserID = get_current_user_id();
	$urlCompSlug = get_query_var('companyname');
	//if($companyArr['statusFlag'] == 'created' || $companyArr['statusFlag'] == 'updated'){

global $wpdb;
//$compAdmin_DB_Obj = $wpdb->get_results("SELECT display_name, user_email FROM $wpdb->users WHERE ID = $newTickArr[companyLID]");
	
$tickOwner_DB_Obj = $wpdb->get_results("SELECT user_email FROM $wpdb->users WHERE ID = '$curUserID'");
	
		//if(isset($tickOwner_DB_Obj[0]) && isset($compAdmin_DB_Obj[0])){
			$headers  = "From: ".get_bloginfo('name')." <'".get_option('admin_email')."'>\r\n";
			$to = $tickOwner_DB_Obj[0]->user_email;
			$subject = sprintf(__("Your Ticket Query %s submitted successfully",'mhelpdesk'),sanitize_text_field($_POST['title']));
			$message  = sprintf(__("Your Ticket Query %s has been successfully submitted to %s company. Please, Login to the following 
						URL to get updation of your Ticket Query",'mhelpdesk'),sanitize_text_field($_POST['title']),$newTickArr['company_name']).'<br />';
			$message .= __("Login URL = ",'mhelpdesk').site_url()."/".$helpdesk_rewriterule_slug."<br />";
			$message .= __("Ticket Access URL = ",'mhelpdesk').get_permalink($newTickArr['companyID'])."customer/tickets/?action=edit&id=$newTickArr[ticketID]<br />";
		//	die(print_r($headers).print_r('<br />').print_r($to).print_r('<br />').print_r($subject).print_r('<br />').print_r($message));
			 hd_mail( $to, $subject, $message, $headers);
		
		//die(print_r($headers).print_r('<br />').print_r($to).print_r('<br />').print_r($subject).print_r('<br />').print_r($message));
		//} // if(isset($tickOwner_DB_Obj[0]) && isset($compAdmin_DB_Obj[0]))
	//}// if($companyArr['statusFlag'] == 'created' || $companyArr['statusFlag'] == 'updated')
//remove_filter( 'wp_mail_content_type', 'set_html_content_type' );
} // function notifyTickOwner($newTickArr)
//
//
//
//
//
//
//////////////////////////////////////////////////
/// Notification to Company Admin of the Ticket
/////////////////////////////////////////////////
add_action('ticket_created','notifyTickComp');
/*
add_action('init','test');
function test(){
$notificationEmails = get_metadata('post',13, 'company-notificationEmails',true);
$notificationEmails = explode(',',$notificationEmails);
print_r($notificationEmails);
}*/

function notifyTickComp($newTickArr){
//add_filter( 'wp_mail_content_type', 'set_html_content_type' );
	$curUserID = get_current_user_id();
	$urlCompSlug = get_query_var('companyname');
	$notificationEmails = get_metadata('post',$newTickArr['companyID'], 'company-notificationEmails',true);
	$notificationEmails = explode(',',$notificationEmails);
	//if($companyArr['statusFlag'] == 'created' || $companyArr['statusFlag'] == 'updated'){
	$hd_admin_settings_arr = get_option('APF_MyFirstFrom');
		
	$helpdesk_rewriterule_slug = isset($hd_admin_settings_arr['m_helpdesk_form']['helpdesk_rewriterule_slug']) && $hd_admin_settings_arr['m_helpdesk_form']['helpdesk_rewriterule_slug'] ? $hd_admin_settings_arr['m_helpdesk_form']['helpdesk_rewriterule_slug'] : 'company';
	$curUserID = get_current_user_id();
	$urlCompSlug = get_query_var('companyname');

global $wpdb;
//$compAdmin_DB_Obj = $wpdb->get_results("SELECT display_name, user_email FROM $wpdb->users WHERE ID = $newTickArr[companyLID]");

$tickOwner_DB_Obj = $wpdb->get_results("SELECT display_name, user_email FROM $wpdb->users WHERE ID = '$curUserID'");

//die(print_r($compAdmin_DB_Obj));
		//if(isset($compAdmin_DB_Obj[0])){
			$headers  = "From: ".get_bloginfo('name')." <'".get_option('admin_email')."'>\r\n";
			//$to = $notificationEmail;
			$subject  = sprintf(__("The New Ticket Query %s is created",'mhelpdesk'),sanitize_text_field($_POST['title']));
			$message  = sprintf(__("The new Ticket Query %s has been created to your ( %s ) company.",'mhelpdesk'),sanitize_text_field($_POST['title']),$newTickArr['company_name']).'<br />';
			$message .= sprintf(__("Name of the Customer ",'mhelpdesk'),$tickOwner_DB_Obj[0]->display_name).'<br />';
			$message .= sprintf(__("Email Address ",'mhelpdesk'),$tickOwner_DB_Obj[0]->user_email)."<br />";
			$message .= __("Login URL = ",'mhelpdesk').site_url()."/".$helpdesk_rewriterule_slug."<br />";
			$message .= __("Ticket Access URL for Admin ",'mhelpdesk').get_permalink($newTickArr['companyID'])."admin/tickets/?action=edit&id=$newTickArr[ticketID]<br />";
			$message .= __("<br />Ticket Access URL for Agent ",'mhelpdesk').get_permalink($newTickArr['companyID'])."agent/tickets/?action=edit&id=$newTickArr[ticketID]<br />";
			//die(print_r($headers).print_r('<br />').print_r($to).print_r('<br />').print_r($subject).print_r('<br />').print_r($message));
			// hd_mail( $to, $subject, $message, $headers);


		foreach($notificationEmails as $notificationEmail){
			 hd_mail( $notificationEmail, $subject, $message, $headers);
		} // foreach($notificationEmails as $notificationEmail)
		//die(print_r($tickID));
		
		//} // if(isset($tickOwner_DB_Obj[0]) && isset($compAdmin_DB_Obj[0]))
	//}// if($companyArr['statusFlag'] == 'created' || $companyArr['statusFlag'] == 'updated')
	//remove_filter( 'wp_mail_content_type', 'set_html_content_type' );
} // function notifyTickOwner($newTickArr)






///////////////////////
//// Ticket Updated
//////////////////////

//////////////////////////////////
// Notification of Updation to Ticket Owner
//////////////////////////////////
add_action('ticket_updated','updateNotifyTickOwner');
function updateNotifyTickOwner($newTickArr){
	$curUserID = get_current_user_id();
	$urlCompSlug = get_query_var('companyname');
	
	$hd_admin_settings_arr = get_option('APF_MyFirstFrom');
		
	$helpdesk_rewriterule_slug = isset($hd_admin_settings_arr['m_helpdesk_form']['helpdesk_rewriterule_slug']) && $hd_admin_settings_arr['m_helpdesk_form']['helpdesk_rewriterule_slug'] ? $hd_admin_settings_arr['m_helpdesk_form']['helpdesk_rewriterule_slug'] : 'company';

	//if($companyArr['statusFlag'] == 'created' || $companyArr['statusFlag'] == 'updated'){

global $wpdb;
//$compAdmin_DB_Obj = $wpdb->get_results("SELECT display_name, user_email FROM $wpdb->users WHERE ID = '$curUserID'");
	
$tickOwner_DB_Obj = $wpdb->get_results("SELECT user_email FROM $wpdb->users WHERE ID = $newTickArr[tickOwnerID]");
	
		//if(isset($tickOwner_DB_Obj[0]) && isset($compAdmin_DB_Obj[0])){
			$headers  = "From: ".get_bloginfo('name')." <'".get_option('admin_email')."'>\r\n";
			$to = $tickOwner_DB_Obj[0]->user_email;
			if(isset($_POST['ticketActionStatus']) && $_POST['ticketActionStatus'] == 'Solved'){
				$subject = sprintf(__("Your Ticket Query ( %s ) is Solved. Now Rate the Ticket",'mhelpdesk'),$newTickArr['ticketTitle']);
				$message = sprintf(__("Your Ticket Query ( %s ) in Company ( %s ) is solved. Please, Login and Rate Overall the Ticket / Conversation",'mhelpdesk'),
				$newTickArr['ticketTitle'],$newTickArr['company_name'])."<br />";
			}
			elseif(isset($_POST['ticketActionStatus']) && $_POST['ticketActionStatus'] == 'Closed'){
				$subject  = sprintf(__("Your Ticket / Query ( %s ) is Closed.",'mhelpdesk'),$newTickArr['ticketTitle']);
				$message  = sprintf(__("Your Ticket / Query ( %s ) in Company ( %s ) is Closed",'mhelpdesk'),$newTickArr['ticketTitle'],$newTickArr['company_name']).'<br />';
			}
			elseif(isset($_POST['adminAssignAgent'])){
				$subject  = sprintf(__("Your Ticket / Query = ( %s ) has been assigned an Agent",'mhelpdesk'),$newTickArr['ticketTitle']);
				$message  = sprintf(__("The agent has assigned to your Ticket/ Query = ( %s ) in Company = ( %s ). So, Logon to the following 
				URL to get updated info of your Ticket/ Query",'mhelpdesk'),$newTickArr['ticketTitle'],$newTickArr['company_name']).'<br />';
				$message .= __("Login URL = ",'mhelpdesk').site_url()."/".$helpdesk_rewriterule_slug."<br />";
				$message .= __("Ticket Access URL = ",'mhelpdesk').get_permalink($newTickArr['companyID'])."customer/tickets/?action=edit&id=$newTickArr[ticketID]<br />";
			}
			else{
			$subject  = sprintf(__("Your Ticket/ Query = ( %s ) is updated (i.e. Status)",'mhelpdesk'),$newTickArr['ticketTitle']);
			$message  = sprintf(__("Your Ticket/ Query = ( %s ) is updated. Company = ( %s ). So, Logon to the following  URL to get updated info of your 
						Ticket/ Query",'mhelpdesk'),$newTickArr['ticketTitle'],$newTickArr['company_name']).'<br />';
			$message .= __("Login URL = ",'mhelpdesk').site_url()."/".$helpdesk_rewriterule_slug."<br />";
			$message .= __("Ticket Access URL = ",'mhelpdesk').get_permalink($newTickArr['companyID'])."<br />";
			}
			//die(print_r($headers).print_r('<br />').print_r($to).print_r('<br />').print_r($subject).print_r('<br />').print_r($message));
			hd_mail( $to, $subject, $message, $headers);
		
		
		//} // if(isset($tickOwner_DB_Obj[0]) && isset($compAdmin_DB_Obj[0]))
	//}// if($companyArr['statusFlag'] == 'created' || $companyArr['statusFlag'] == 'updated')
} // function notifyTickOwner($newTickArr)










/////////////////////////////////////////
// Notification of Updation to an Agent
/////////////////////////////////////////
add_action('ticket_updated','updateNotifyTickAgent');
function updateNotifyTickAgent($newTickArr){
	$curUserID = get_current_user_id();
	$urlCompSlug = get_query_var('companyname');
	$hd_admin_settings_arr = get_option('APF_MyFirstFrom');
		
	$helpdesk_rewriterule_slug = isset($hd_admin_settings_arr['m_helpdesk_form']['helpdesk_rewriterule_slug']) && $hd_admin_settings_arr['m_helpdesk_form']['helpdesk_rewriterule_slug'] ? $hd_admin_settings_arr['m_helpdesk_form']['helpdesk_rewriterule_slug'] : 'company';
	//if($companyArr['statusFlag'] == 'created' || $companyArr['statusFlag'] == 'updated'){

global $wpdb;
//$compCustomer_DB_Obj = $wpdb->get_results("SELECT display_name, user_email FROM $wpdb->users WHERE ID = '$curUserID'");

$tickSelectedAgentID = get_metadata('post',$newTickArr['ticketID'], 'ticket-selectedAgent',true);
$userAgent_DB_Obj = $wpdb->get_results("SELECT user_email FROM $wpdb->users WHERE ID = '$tickSelectedAgentID'");
	
//$tickOwner_DB_Obj = $wpdb->get_results("SELECT user_email FROM $wpdb->users WHERE ID = $newTickArr[tickOwnerID]");
	
		//if( isset($userAgent_DB_Obj[0]) ){
			$headers  = "From: ".get_bloginfo('name')." <'".get_option('admin_email')."'>\r\n";
			$to = $userAgent_DB_Obj[0]->user_email;
		if(isset($_POST['rating']) && $_POST['rating'] != '')
			$subject = sprintf(__("The Ticket Query %s is now Rated",'mhelpdesk'),$newTickArr['ticketTitle']);
		else
			$subject = sprintf(__("The Ticket Query %s is assiged to you",'mhelpdesk'),$newTickArr['ticketTitle']);
			$message  = sprintf(__("The Ticket Query %s is updated in the Company %s. So, Logon to the following URL to get updated info of the Ticket Query"
						,'mhelpdesk'),$newTickArr['ticketTitle'],$newTickArr['company_name']).'<br />';
			$message .= __("Login URL = ",'mhelpdesk').site_url()."/".$helpdesk_rewriterule_slug."<br />";
			$message .= __("Ticket Access URL = ",'mhelpdesk').get_permalink($newTickArr['companyID']);
			
			//die(print_r($headers).print_r('<br />').print_r($to).print_r('<br />').print_r($subject).print_r('<br />').print_r($message));
			hd_mail( $to, $subject, $message, $headers);
		
		
		//} // if(isset($tickOwner_DB_Obj[0]) && isset($compAdmin_DB_Obj[0]))
	//}// if($companyArr['statusFlag'] == 'created' || $companyArr['statusFlag'] == 'updated')
} // function notifyTickOwner($newTickArr)







remove_filter( 'wp_mail_content_type', 'mhelpdesl_set_html_content_type' );

?>