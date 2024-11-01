<?php
	$hd_admin_settings_arr = get_option('APF_MyFirstFrom');
		
	$helpdesk_rewriterule_slug = isset($hd_admin_settings_arr['m_helpdesk_form']['helpdesk_rewriterule_slug']) && $hd_admin_settings_arr['m_helpdesk_form']['helpdesk_rewriterule_slug'] ? $hd_admin_settings_arr['m_helpdesk_form']['helpdesk_rewriterule_slug'] : 'company';

	if ( !is_user_logged_in() )
	wp_safe_redirect(site_url().'/'.$helpdesk_rewriterule_slug);
	$message = '';

//$domainURL = getenv( 'HTTP_HOST' );
//$urlString = 'http://'.$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'];
//print_r($url).print_r('<br />').print_r($urlString);
	
	

	$active_plugins = get_option('active_plugins',array());
	
	$compExistanceStatus = false;
	global $wpdb; 
	$postname = isset($_POST['slug']) ? sanitize_key($_POST['slug']) : '';
	$result = $wpdb->get_results("SELECT ID FROM $wpdb->posts WHERE post_type = 'companies' AND post_status = 'publish'  AND post_name = '$postname'");
	//print_r($result);

	if(isset($result[0])) {
		$compExistanceStatus = true;
		$message.='<div class="alert alert-danger bg-danger">The company is already registered with the given site identificaton. "'.$postname.'"</div>';	
	}
	else
		$compExistanceStatus = false;	
	
	$hd_admin_settings_arr = get_option('APF_MyFirstFrom');
	
//	print_r($hd_admin_settings_arr);
	
		if(isset($_POST['company-registration']) && wp_verify_nonce( $_POST['company-registration'], 'companyregistration' ) && isset($_POST['companySubmitBtn']) ) {
		
	
	global $global_hd_limit_status,$global_notification_permission;
	
	$global_notification_permission = 'true';
	$global_hd_limit_status = 'ok';	
	
	do_action( 'before_company_created' );
	
	if(array_search('hd-wc-extenstion/index_hd_wc_extention.php',$active_plugins)!== false)
		$global_hd_limit_status = apply_filters('before_company_created_wc_filter',$global_hd_limit_status);
	
	
	if($global_hd_limit_status == 'full')
	$message.='<div class="alert alert-danger bg-danger">The helpdesk creation limit is applied. You can\'t create more helpdesk. Please contact to your (this) feature provider.</div>';
		
			if($global_hd_limit_status == 'ok' && $compExistanceStatus == false && global_logged_in_user_is_admin()){
	
			$compPostArgs = array(
									'post_type' => 'companies',
									'post_title' => sanitize_text_field($_POST['companyTitle']),
									'post_name' => sanitize_key($_POST['slug']),
									'post_content' => strip_tags($_POST['intro'],'<blockquote><h1><h2><h3><h4><h5><h6><div><p><ul><ol><li><span><br><pre><sup><sub><u><strong><i><em><s><img><a>'),
									'post_status' => 'publish'
			 					);
				$comp_postID = wp_insert_post( $compPostArgs );
						
				update_metadata('post', $comp_postID,'company-faqs',strip_tags($_POST['faqs'],'<blockquote><h1><h2><h3><h4><h5><h6><div><p><ul><ol><li><span><br><pre><sup><sub><u><strong><i><em><s><img><a>'));
				update_metadata('post', $comp_postID, 'company-notificationEmails', sanitize_text_field($_POST['notificationEmails']));
				
				if($_POST['departments'] != '')
					update_metadata('post', $comp_postID, 'company-departments', sanitize_text_field($_POST['departments']));
				else
					update_metadata('post', $comp_postID, 'company-departments', 'General');
				
				if($_POST['agentemail'])
				foreach($_POST['agentemail'] as $agentemail)
						$agents_email_saving_onlyone[] = sanitize_email( $agentemail );
				
				$ticketsperpage = isset($_POST['ticketsperpage']) ? preg_replace('/[^0-9]/','',$_POST['ticketsperpage']) : 5;
				update_metadata('post', $comp_DB_ID, 'company-tickets-perpage', $ticketsperpage);
			
				update_metadata('post', $comp_postID,'company-agentEmail',$agents_email_saving_onlyone);
				
			if(array_search('hd-wc-extenstion/index_hd_wc_extention.php',$active_plugins)!== false)
				$global_notification_permission = apply_filters('notification_permission_wc_filter',$comp_postID,$global_notification_permission);
				
				if($global_notification_permission == 'true')
					do_action( 'company_created_updated', array('statusFlag'=>'created','companyID'=>$comp_postID) );
				
				$message.= '<div class="alert alert-success bg-success" role="alert">The Company "'.sanitize_text_field($_POST['companyTitle']).'" has been successfully created.</div>';
				$company_permalink = get_permalink($comp_postID);
				wp_safe_redirect($company_permalink);
			} // if($global_hd_limit_status == 'ok' && $compExistanceStatus == false)
		//echo "<meta http-equiv=refresh content=0;url=".site_url()."/company />";
		} // if(isset($_POST['company-registration']) && wp_verify_nonce( $_POST['company-registration'], 'companyregistration' ) && isset($_POST['companySubmitBtn']) )
		

get_plugin_header();
echo '<div class="hdwrap">';


$curUserID = get_current_user_id();
//if(!isset($_POST['company-registration'])){
		 
		
		 global $mtheme;			
		 include($mtheme.'company-controls/add-helpdesk.php');

//} // if(!isset($_POST['company-registration']))


  
 
echo '</div>';
get_plugin_footer(); ?>