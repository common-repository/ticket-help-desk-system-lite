<?php 
	$hd_admin_settings_arr = get_option('APF_MyFirstFrom');
		
	$helpdesk_rewriterule_slug = isset($hd_admin_settings_arr['m_helpdesk_form']['helpdesk_rewriterule_slug']) && $hd_admin_settings_arr['m_helpdesk_form']['helpdesk_rewriterule_slug'] ? $hd_admin_settings_arr['m_helpdesk_form']['helpdesk_rewriterule_slug'] : 'company';


get_plugin_header();
echo '<div class="hdwrap">';


if ( !is_user_logged_in() ){?>
<table>
<?php if($helpdesk_theme != 'Core-UI') {?>
	<tr>
    	<th><?php _e('Login','mhelpdesk');?></th><th><?php _e('Registration','mhelpdesk');?></th>
    </tr>
<?php } //if($helpdesk_theme) {?>
    <tr>
    	<td><?php include(MHDESKABSPATH.'/controllers/login.php'); ?></td><td ><?php include(MHDESKABSPATH.'/controllers/registration.php'); ?></td>
    </tr>
</table>
<?php 
} // if ( !is_user_logged_in() )
else{

global $wpdb; 
$curUserID = get_current_user_id();
$comp_DB_objs = $wpdb->get_results("SELECT ID,post_title,post_name FROM $wpdb->posts WHERE post_type = 'companies' AND post_status = 'publish'" ) ;

if(isset($_GET['id']) && $_GET['action'] == 'delete'){

	$company_id = preg_replace('/[^0-9]/','',$_GET['id']);	
	$authCheck_obj = $wpdb->get_results("SELECT post_author FROM $wpdb->posts WHERE ID = $company_id AND post_type = 'companies' AND post_status = 'publish'");

} // if(isset($_GET['id']) && $_GET['action'] == 'delete')


		global $mtheme;
		include($mtheme.'company-controls/companies.php');
		
} // ELSE   if ( !is_user_logged_in() )


echo '</div>';
 get_plugin_footer(); ?>