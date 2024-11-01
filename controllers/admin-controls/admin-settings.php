<?php 
	$hd_admin_settings_arr = get_option('APF_MyFirstFrom');
		
	$helpdesk_rewriterule_slug = isset($hd_admin_settings_arr['m_helpdesk_form']['helpdesk_rewriterule_slug']) && $hd_admin_settings_arr['m_helpdesk_form']['helpdesk_rewriterule_slug'] ? $hd_admin_settings_arr['m_helpdesk_form']['helpdesk_rewriterule_slug'] : 'company';
	if ( !is_user_logged_in() )
	wp_safe_redirect(site_url().'/'.$helpdesk_rewriterule_slug);


$message ='';

$active_plugins = get_option('active_plugins',array());
$urlCompSlug = get_query_var('companyname');
$userRole = get_query_var('userrole');
$curUserID = get_current_user_id();
global $wpdb;

$comp_DB_ID_obj = $wpdb->get_results("SELECT ID FROM $wpdb->posts WHERE post_type = 'companies' AND post_status = 'publish' AND post_name = '$urlCompSlug'");

		if(isset($_POST['company-companyUpdateBtn']) && wp_verify_nonce( $_POST['company-companyUpdateBtn'], 'companyUpdateBtn' ) && isset($_POST['companyUpdateBtn']) ) {

		
		$comp_DB_ID = $comp_DB_ID_obj[0]->ID;
		
		$global_helpdesk_skin_permission = 'true';
	
		$global_notification_permission = 'true';
		
	if(array_search('hd-wc-extenstion/index_hd_wc_extention.php',$active_plugins)!== false)
		$global_helpdesk_skin_permission = apply_filters('helpdesk_skin_permission_wc_filter',$comp_DB_ID,$global_helpdesk_skin_permission);

		
		$ticketsperpage = isset($_POST['ticketsperpage']) ? preg_replace('/[^0-9]/','',sanitize_text_field($_POST['ticketsperpage'])) : 5;
			$updateCompanyArgs = array(
									'ID' => $comp_DB_ID,
									'post_type' => 'companies',
									'post_title' => sanitize_text_field($_POST['companyTitle']),
									'post_content' => strip_tags(sanitize_textarea_field($_POST['intro']),'<blockquote><h1><h2><h3><h4><h5><h6><div><p><ul><ol><li><span><br><pre><sup><sub><u><strong><i><em><s><img><a><div>'),
									'post_status' => 'publish'
			 					);

			update_metadata('post', $comp_DB_ID,'company-faqs',strip_tags($_POST['faqs'],'<blockquote><h1><h2><h3><h4><h5><h6><div><p><ul><ol><li><span><br><pre><sup><sub><u><strong><i><em><s><img><a>'));
			
			update_metadata('post', $comp_DB_ID, 'company-notificationEmails', sanitize_text_field($_POST['notificationEmails']));
			
			update_metadata('post', $comp_DB_ID, 'company-tickets-perpage', $ticketsperpage);
			
				if($_POST['departments'] != '')
					update_metadata('post', $comp_DB_ID, 'company-departments', sanitize_text_field($_POST['departments']));
				else
					update_metadata('post', $comp_DB_ID, 'company-departments', 'Default Category');
			//die(print_r($_POST));
			update_metadata('post',$comp_DB_ID,'company_custom_script',(sanitize_textarea_field($_POST['company_custom_script'])));
			update_metadata('post',$comp_DB_ID,'company_custom_css',(sanitize_textarea_field($_POST['company_custom_css'])));


			wp_update_post($updateCompanyArgs);
		if(array_search('hd-wc-extenstion/index_hd_wc_extention.php',$active_plugins)!== false)
			$global_notification_permission = apply_filters('notification_permission_wc_filter',$comp_postID,$global_notification_permission);
				
			do_action( 'company_created_updated', array('statusFlag'=>'updated','companyID'=>$comp_DB_ID) );

			
			$message.='<div class="alert alert-success bg-success">Settings Updated</div>';				
			
			/////////////////////////////////wp_safe_redirect("admin/settings" );
 	
		} // if(isset($_POST['company-companyUpdateBtn']) && wp_verify_nonce( $_POST['company-companyUpdateBtn'], 'companyUpdateBtn' ) && isset($_POST['companyUpdateBtn']) )

	


get_plugin_header();
echo '<div class="hdwrap">';

	if(isset($comp_DB_ID_obj[0])){
		
		$comp_DB_ID = $comp_DB_ID_obj[0]->ID;
		
		//print_r($comp_DB_ID_obj[0]->ID);
		
$comp_authCheck_obj = $wpdb->get_results("SELECT companyposts.post_author, companyposts.post_title, companyposts.post_name, companyposts.post_content 
FROM $wpdb->posts companyposts, $wpdb->postmeta companymeta WHERE companyposts.ID = companymeta.post_id AND companyposts.post_name = '$urlCompSlug' 
AND companyposts.post_author='$curUserID' AND companyposts.post_type = 'companies' AND companyposts.post_status = 'publish' GROUP BY companyposts.post_author");
		
		if(isset($comp_authCheck_obj[0]))
			//if($comp_authCheck_obj[0]->post_author == $curUserID){

				global $mtheme;
				$ticketsperpage = get_metadata('post', $comp_DB_ID, 'company-tickets-perpage', true);
				$departments = get_metadata('post', $comp_DB_ID, 'company-departments', true);
				$company_admin_tickets_arr = show_company_admin_tickets();
				include($mtheme.'admin-controls/admin-settings.php');




		//} //if(isset($comp_authCheck_obj[0]))
		//else echo '<h1 >'.__('You are not AUTHORIZED to access this company','mhelpdesk').'.</h1>';
			
	} // if(isset($comp_DB_ID_obj[0]))
	else echo '<h1 >'.__('The Company is not registered OR invalid Access','mhelpdesk').'</h1>';

	

echo '</div><!-- div class="hdwrap" -->';
get_plugin_footer(); ?>