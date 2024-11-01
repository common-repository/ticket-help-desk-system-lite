<?php 
	$hd_admin_settings_arr = get_option('APF_MyFirstFrom');
		
	$helpdesk_rewriterule_slug = isset($hd_admin_settings_arr['m_helpdesk_form']['helpdesk_rewriterule_slug']) && $hd_admin_settings_arr['m_helpdesk_form']['helpdesk_rewriterule_slug'] ? $hd_admin_settings_arr['m_helpdesk_form']['helpdesk_rewriterule_slug'] : 'company';

	if ( !is_user_logged_in() )
	wp_safe_redirect(site_url().'/'.$helpdesk_rewriterule_slug);

	
	$message = '';

$active_plugins = get_option('active_plugins',array());

$urlCompSlug = get_query_var('companyname');

$urlAAction = get_query_var('tabaction');

global $wpdb; 

//die(print_r($_POST));

	$comp_DB_ID_obj = $wpdb->get_results("SELECT ID, post_author, post_title FROM $wpdb->posts WHERE post_type = 'companies' 
	AND post_status = 'publish' AND post_name = '$urlCompSlug'");
	

	$curUserID = get_current_user_id();
	if(isset($comp_DB_ID_obj[0])){

	$comp_DB_ID = $comp_DB_ID_obj[0]->ID;
	$comp_name = $comp_DB_ID_obj[0]->post_title;
	
		global $global_customer_limit_status, $global_ticket_limit_status,$global_notification_permission; 
		$global_customer_limit_status = 'ok'; $global_ticket_limit_status = 'ok'; $global_notification_permission = 'true';
		
			//$global_notification_permission = apply_filters('notification_permission_wc_filter',$comp_postID,$global_notification_permission);
	
	do_action( 'before_ticket_created', $comp_DB_ID );
	do_action( 'before_customer_add', $comp_DB_ID );
		
	if(array_search('hd-wc-extenstion/index_hd_wc_extention.php',$active_plugins)!== false){
		$global_notification_permission = apply_filters('notification_permission_wc_filter',$comp_postID,$global_notification_permission);
		$global_ticket_limit_status = apply_filters('before_ticket_created_wc_filter',$comp_DB_ID,$global_ticket_limit_status);
		$global_customer_limit_status = apply_filters('before_customer_add_wc_filter',$comp_DB_ID,$global_customer_limit_status);
	}

	
	$hd_admin_settings_arr = get_option('APF_MyFirstFrom');
		
	//echo get_permalink($comp_DB_ID);
	
		$compCustomerBlocked_DB_arr = get_metadata('post', $comp_DB_ID, 'company-customers-blocked', true);
		
		$customerBlockStatus = 'false';
		
		if(isset($compCustomerBlocked_DB_arr[$curUserID]))
			$customerBlockStatus = 'true';
		
		

		if($customerBlockStatus == 'false'){
			
			$compCustomerOpened_DB_arr = get_metadata('post', $comp_DB_ID, 'company-customers-opened', true);
			if($compCustomerOpened_DB_arr){
				
				$customerID_foundIndex = array_search($curUserID, isset($compCustomerOpened_DB_arr[0]) ? $compCustomerOpened_DB_arr : array());
				
					if($customerID_foundIndex > -1)
					; // do nothing
					else{
						
						//do_action( 'before_customer_add', $comp_DB_ID );
						
						//global $global_customer_limit_status; 
						//die(var_dump($comp_DB_ID));
						if($global_ticket_limit_status == 'full'){
							$message.='<div class="alert alert-danger bg-danger">The Ticket Creation limit is applied, therefore NO new request can be made!</div>';
							$global_customer_limit_status = 'full';							
						}
						
						if($global_customer_limit_status == 'full'){
							$message.='<div class="alert alert-danger bg-danger">The Cutomer Creation limit is applied, therefore NO new request can be made!</div>';
							$global_ticket_limit_status = 'full';							
						}
						
						
						if($global_customer_limit_status == 'ok'){
							$compCustomerOpened_DB_arr[] = $curUserID;
							rsort($compCustomerOpened_DB_arr);
							update_metadata('post', $comp_DB_ID, 'company-customers-opened', $compCustomerOpened_DB_arr);
							
						} // if($global_customer_limit_status == 'ok')
						
					} // ELSE of    if($customerID_foundIndex > -1)

			} // if($compCustomerOpened_DB_arr)
			else{
					$compCustomerOpened_DB_arr = array();
					//array_push($compCustomerOpened_DB_arr, $curUserID);
					$compCustomerOpened_DB_arr[] = $curUserID;
					update_metadata('post', $comp_DB_ID, 'company-customers-opened', $compCustomerOpened_DB_arr);
			} // // ELSE of    if(compCustomerOpened_DB_arr)
				
	

	
	$viewPostObj = get_post($comp_DB_ID); // registered company object
	if(isset($_POST['ticket-registration']) && wp_verify_nonce( $_POST['ticket-registration'], 'ticketregistration' ) && isset($_POST['ticketSubmitBtn']) ) {
	
	
	//do_action( 'before_customer_add', $comp_DB_ID );
	//global $global_customer_limit_status;
	
	//do_action( 'before_ticket_created', $comp_DB_ID );
	//global $global_ticket_limit_status;

		
		if($global_ticket_limit_status == 'ok'){
	
		$current_userInfo = get_userdata( $curUserID );
		$ticketPostArgs = array(
								'post_type' => 'tickets',
								'post_title' => sanitize_text_field($_POST['title']),
								'post_status' => 'publish'
							);
					$ticket_postID = wp_insert_post( $ticketPostArgs );
	
	do_action( 'ticket_created', array('companyID'=>$comp_DB_ID,'company_name'=>$comp_name,'ticketID'=>$ticket_postID) );
				
					update_metadata('post',$ticket_postID,'ticket-selected_department',sanitize_text_field($_POST['selected_department']));
					update_metadata('post',$ticket_postID,'ticket-selectedCompany',$comp_DB_ID);
					update_metadata('post',$ticket_postID, 'ticket-authorityID', $curUserID);

					$newComments = array(
						'comment_post_ID' 		=> $ticket_postID,
						'comment_author' 		=> $current_userInfo->display_name,
						'comment_author_email' 	=> $current_userInfo->user_email,
						'comment_content' 		=> strip_tags(sanitize_text_field($_POST['customerComment']),'<blockquote><h1><h2><h3><h4><h5><h6><div><p><ul><ol><li><span><br><pre><sup><sub><u><strong><i><em><s>'),
						'comment_type' 			=> 'comment',
						'comment_approved' 		=> 1,
					);
					$newCommentID = wp_insert_comment($newComments);
					
					
					$supportedDoc_posted = isset($_FILES["supportedDoc"]["name"]) ? sanitize_file_name($_FILES["supportedDoc"]["name"]) : "";
					
					if(!empty($supportedDoc_posted))
					$supportedDoc_id = media_handle_upload("supportedDoc", $curUserID );
					
					if(isset($supportedDoc_id) && (is_wp_error($supportedDoc_id) == false))
					update_metadata('comment', $newCommentID, 'ticket-commentAttachment', $supportedDoc_id);
					
					update_metadata('post',$ticket_postID,"ticket-status",'Opened');
					
					update_metadata('post',$ticket_postID,"ticket-action-status",'Un-Answered');
					
					
					//echo "<meta http-equiv=refresh content=0;url=".get_permalink($comp_DB_ID)."/customer/tickets/?action=edit&id=$ticket_postID />";
					wp_safe_redirect("customer/tickets/?action=edit&id=$ticket_postID");
		} // if($global_ticket_limit_status == 'ok')
		else $message.='<div class="alert alert-danger bg-danger">The Ticket Creation limit is full, therefore NO new request can be made!</div>';	
		
		
		
	} // if(isset($_POST['ticket-registration']) && wp_verify_nonce( $_POST['ticket-registration'], 'ticketregistration' ) && isset($_POST['ticketSubmitBtn']) )
		} // if($customerBlockStatus == 'false')
	} // if(isset($comp_DB_ID_obj[0]))
	
		
get_plugin_header();
echo '<div class="hdwrap">';
if ( !is_user_logged_in() ){?>
<table>
	<tr>
    	<th><?php _e('Login','mhelpdesk');?></th><th><?php _e('Registration','mhelpdesk');?></th>
    </tr>
    <tr>
    	<td><?php include(MHDESKABSPATH.'/controllers/login.php'); ?></td><td ><?php include(MHDESKABSPATH.'/controllers/registration.php'); ?></td>
    </tr>
</table>
<?php 
} // if ( !is_user_logged_in() )
else{	
	//if ( is_user_logged_in() && isset($comp_DB_ID_obj[0])) {
			//if($customerBlockStatus == 'false'){
         
		global $mtheme;
		
		$company_departments = get_metadata('post', $comp_DB_ID, 'company-departments', true);
		$company_departments = $company_departments != '' ? explode(',',$company_departments) : array('Default Category');
		$company_admin_tickets_arr = show_company_admin_tickets();
		
		include($mtheme.'customer-controls/customer-company-ticket.php');


		//	} // if($customerBlockStatus == 'false')
			//else echo "<h1 class='error'><br /><br />'.__('You are currently Freezed! Please Contact to your admin/agent in this regard','mhelpdesk').'.</h1>";
	//} // if(isset($comp_DB_ID_obj[0]))
	//else echo '<h1 class="error"><br /><br />'.__('The Company/ Helpdesk is not registered yet','mhelpdesk').'.</h1>';?> 

		
<?php 
		} // ELSE    if (!is_user_logged_in())

 echo '</div><!-- .hdwrap -->'; 
get_plugin_footer(); ?>