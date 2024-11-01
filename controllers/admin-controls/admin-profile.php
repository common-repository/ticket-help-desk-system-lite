<?php 
	$hd_admin_settings_arr = get_option('APF_MyFirstFrom');
		
	$helpdesk_rewriterule_slug = isset($hd_admin_settings_arr['m_helpdesk_form']['helpdesk_rewriterule_slug']) && $hd_admin_settings_arr['m_helpdesk_form']['helpdesk_rewriterule_slug'] ? $hd_admin_settings_arr['m_helpdesk_form']['helpdesk_rewriterule_slug'] : 'company';
	if ( !is_user_logged_in() )
	wp_safe_redirect(site_url().'/'.$helpdesk_rewriterule_slug);



$urlCompSlug = get_query_var('companyname');
$userRole = get_query_var('userrole');
$curUserID = get_current_user_id();
global $wpdb;

	$comp_DB_ID_obj = $wpdb->get_results("SELECT ID FROM $wpdb->posts WHERE post_type = 'companies' AND post_status = 'publish' AND post_name = '$urlCompSlug'");

		if(isset($_POST['user-profileBtn']) && wp_verify_nonce( $_POST['user-profileBtn'], 'profileBtn' ) && isset($_POST['profileBtn']) ) {
		$message = '';
			if($_POST['password'] != '')
				wp_update_user( array( 'ID' => $curUserID, 'user_pass' => sanitize_text_field($_POST['password']) ) );
			
			if($_POST['fullname'] != '')
				wp_update_user( array( 'ID' => $curUserID, 'display_name' => strip_tags(sanitize_text_field($_POST['fullname'])) ) );
		$message.='<div class="alert alert-success bg-success">Profile Updated</div>';
			
		} // if(isset($_POST['user-profileBtn']) && wp_verify_nonce( $_POST['user-profileBtn'], 'profileBtn' ) && isset($_POST['profileBtn']) )





get_plugin_header();
echo '<div class="hdwrap">';

if(isset($comp_DB_ID_obj[0])){
		
		$comp_DB_ID = $comp_DB_ID_obj[0]->ID;
		
		//print_r($comp_DB_ID_obj[0]->ID);
		
$comp_authCheck_obj = $wpdb->get_results("SELECT post_author FROM $wpdb->posts companyposts, $wpdb->postmeta companymeta
WHERE companyposts.ID = companymeta.post_id  AND companyposts.post_name = '$urlCompSlug'
AND companyposts.post_type = 'companies'
AND companyposts.post_status = 'publish'");
		
	
		
			if(isset($comp_authCheck_obj[0]))
				if($comp_authCheck_obj[0]->post_author == $curUserID){
				global $mtheme;
				$company_admin_tickets_arr = show_company_admin_tickets();
				include($mtheme.'admin-controls/admin-profile.php');
		} //if(isset($comp_authCheck_obj[0]))
		else echo '<h1 >'.__('You are not AUTHORIZED to access this company','mhelpdesk').'.</h1>';
			
	} // if(isset($comp_DB_ID_obj[0]))
	else echo '<h1 >'.__('The accessed Company is not registered','mhelpdesk').'</h1>';
echo '</div>';
get_plugin_footer(); ?>