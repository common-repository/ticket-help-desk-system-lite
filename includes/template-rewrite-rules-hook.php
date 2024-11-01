<?php 

//add_action('init','rulesss');
//function rulesss(){
//global $wp_rewrite;
//print_r($wp_rewrite);	
//}

add_action( 'post_type_link', 'single_company_url',10,2);
function single_company_url($link,$post){
	if ( 'companies' == get_post_type( $post ) ) {
		$hd_admin_settings_arr = get_option('APF_MyFirstFrom');
		$subdomains = $hd_admin_settings_arr['m_helpdesk_form']['helpdesk_subdomains'];
		
		if($subdomains)
		$link = preg_replace('/(?<=http\:\/\/)([a-z0-9_\-\.]+)\/company(.*)\/([a-z0-9\-\_]+)/','$3.$1', $link);
		return $link;
		        
    }
	return $link;
}








add_action( 'generate_rewrite_rules', 'helpdeskPluhgin_rewrite_rule');
function helpdeskPluhgin_rewrite_rule($wp_rewrite){
global $wpdb;
	$url = getenv( 'HTTP_HOST' );
	 // print_r($url);
	$domain = explode( ".", $url );
        $compSlug = $domain[0];
	$hd_admin_settings_arr = get_option('APF_MyFirstFrom');
		
	$helpdesk_rewriterule_slug = isset($hd_admin_settings_arr['m_helpdesk_form']['helpdesk_rewriterule_slug']) && $hd_admin_settings_arr['m_helpdesk_form']['helpdesk_rewriterule_slug'] ? $hd_admin_settings_arr['m_helpdesk_form']['helpdesk_rewriterule_slug'] : 'company';
		
		
	$comp_DB_ID_obj = $wpdb->get_results("SELECT ID FROM $wpdb->posts WHERE post_type = 'companies' AND post_status = 'publish' AND post_name = '$compSlug'");

	$rules[$helpdesk_rewriterule_slug.'/([^/]+)/customer/profile'] = 'index.php?companyname=$matches[1]&userrole=customer&tabaction=profile';

	$rules[$helpdesk_rewriterule_slug.'/([^/]+)/customer/tickets'] = 'index.php?companyname=$matches[1]&userrole=customer&tabaction=tickets';

	$rules[$helpdesk_rewriterule_slug.'/([^/]+)/customer'] = 'index.php?companyname=$matches[1]&userrole=customer';




	$rules[$helpdesk_rewriterule_slug.'/([^/]+)/agent/profile'] = 'index.php?companyname=$matches[1]&userrole=agent&tabaction=profile';

	$rules[$helpdesk_rewriterule_slug.'/([^/]+)/agent/customers'] = 'index.php?companyname=$matches[1]&userrole=agent&tabaction=customers';

	$rules[$helpdesk_rewriterule_slug.'/([^/]+)/agent/tickets'] = 'index.php?companyname=$matches[1]&userrole=agent&tabaction=tickets';

	$rules[$helpdesk_rewriterule_slug.'/([^/]+)/agent'] = 'index.php?companyname=$matches[1]&userrole=agent';




	$rules[$helpdesk_rewriterule_slug.'/([^/]+)/admin/profile'] = 'index.php?companyname=$matches[1]&userrole=admin&tabaction=profile';

	$rules[$helpdesk_rewriterule_slug.'/([^/]+)/admin/settings'] = 'index.php?companyname=$matches[1]&userrole=admin&tabaction=settings';

	$rules[$helpdesk_rewriterule_slug.'/([^/]+)/admin/customers'] = 'index.php?companyname=$matches[1]&userrole=admin&tabaction=customers';

	$rules[$helpdesk_rewriterule_slug.'/([^/]+)/admin/agents'] = 'index.php?companyname=$matches[1]&userrole=admin&tabaction=agents';
	
	$rules[$helpdesk_rewriterule_slug.'/([^/]+)/admin/tickets'] = 'index.php?companyname=$matches[1]&userrole=admin&tabaction=tickets';
	
	$rules[$helpdesk_rewriterule_slug.'/([^/]+)/admin'] = 'index.php?companyname=$matches[1]&userrole=admin';
	
	$rules[$helpdesk_rewriterule_slug.'/([^/]+)'] = 'index.php?helpdesk='.$helpdesk_rewriterule_slug.'&companyname=$matches[1]';


	if(isset($comp_DB_ID_obj[0])){
		$rules['customer/profile'] = "index.php?companyname=$compSlug&userrole=customer&tabaction=profile";
		$rules['customer/tickets'] = "index.php?companyname=$compSlug&userrole=customer&tabaction=tickets";
		$rules['customer'] = "index.php?companyname=$compSlug&userrole=customer";
	



	$rules['agent/profile'] = "index.php?companyname=$compSlug&userrole=agent&tabaction=profile";

	$rules['agent/customers'] = "index.php?companyname=$compSlug&userrole=agent&tabaction=customers";

	$rules['agent/tickets'] = "index.php?companyname=$compSlug&userrole=agent&tabaction=tickets";

	$rules['agent'] = "index.php?companyname=$compSlug&userrole=agent";



	
		$rules['admin/profile'] = "index.php?companyname=$compSlug&userrole=admin&tabaction=profile";
	
		$rules['admin/settings'] = "index.php?companyname=$compSlug&userrole=admin&tabaction=settings";
	
		$rules['admin/customers'] = "index.php?companyname=$compSlug&userrole=admin&tabaction=customers";
	
		$rules['admin/agents'] = "index.php?companyname=$compSlug&userrole=admin&tabaction=agents";
		
		$rules['admin/tickets'] = "index.php?companyname=$compSlug&userrole=admin&tabaction=tickets";
		
		$rules['admin'] = "index.php?companyname=$compSlug&userrole=admin";
		
		$rules['$'] = "index.php?helpdesk=$helpdesk_rewriterule_slug&companyname=$compSlug";
	}






//	$rules['login'] = 'index.php?helpdesk=login';
//	$rules['registration'] = 'index.php?helpdesk=registration';
	$rules['add-helpdesk'] = 'index.php?helpdesk=add-helpdesk';
	$rules[$helpdesk_rewriterule_slug] = 'index.php?helpdesk='.$helpdesk_rewriterule_slug;

	
	
	
	
	$wp_rewrite->rules = $rules + $wp_rewrite->rules;
}


add_action( 'query_vars', 'ticket_query_vars');
function ticket_query_vars($vars){
	$vars[] = 'helpdesk';
	$vars[] = 'companyname';
	$vars[] = 'userrole';
	$vars[] = 'tabaction';
	return $vars;
}


add_action( 'template_include', 'helpdesk_template_include',1 );
function helpdesk_template_include( $template ) {


//	if(get_query_var( 'helpdesk' ) == 'login' ) {
//		$new_template = MHDESKABSPATH . '/login.php';
//		$template   = $new_template;
//	}
//	elseif(get_query_var( 'helpdesk' ) == 'registration' ) {
//		$new_template = MHDESKABSPATH . '/registration.php';
//		$template   = $new_template;
//	}

	$hd_admin_settings_arr = get_option('APF_MyFirstFrom');
		
	$helpdesk_rewriterule_slug = isset($hd_admin_settings_arr['m_helpdesk_form']['helpdesk_rewriterule_slug']) && $hd_admin_settings_arr['m_helpdesk_form']['helpdesk_rewriterule_slug'] ? $hd_admin_settings_arr['m_helpdesk_form']['helpdesk_rewriterule_slug'] : 'company';
	
	
	if(get_query_var( 'helpdesk' ) == $helpdesk_rewriterule_slug && !get_query_var( 'companyname' )) {
		$new_template = MHDESKABSPATH . '/controllers/company-controls/companies.php';
		$template   = $new_template;
	}


	elseif(get_query_var( 'helpdesk' ) == 'add-helpdesk') {
		$new_template = MHDESKABSPATH . '/controllers/company-controls/add-helpdesk.php';
		$template   = $new_template;
	}
	

	
	
	elseif(get_query_var( 'helpdesk' ) == $helpdesk_rewriterule_slug && get_query_var( 'companyname' )){
		$new_template = MHDESKABSPATH . '/controllers/customer-controls/customer-company-ticket.php';
		$template   = $new_template;
		}
	
	
	
	
	elseif( (get_query_var( 'userrole' ) == 'admin') && get_query_var( 'companyname' ) && !get_query_var( 'tabaction' )){
		$new_template = MHDESKABSPATH . '/controllers/admin-controls/admin-main.php';
		$template   = $new_template;
		}

	elseif( get_query_var( 'companyname' ) && (get_query_var( 'userrole' ) == 'admin') && (get_query_var( 'tabaction' ) == 'tickets') ){
		$new_template = MHDESKABSPATH . '/controllers/admin-controls/admin-tickets.php';
		$template   = $new_template;
		}

	elseif( get_query_var( 'companyname' ) && (get_query_var( 'userrole' ) == 'admin') && (get_query_var( 'tabaction' ) == 'agents') ){
		$new_template = MHDESKABSPATH . '/controllers/admin-controls/admin-agents.php';
		$template   = $new_template;
		}

	elseif( get_query_var( 'companyname' ) && (get_query_var( 'userrole' ) == 'admin') && (get_query_var( 'tabaction' ) == 'customers') ){
		$new_template = MHDESKABSPATH . '/controllers/admin-controls/admin-customers.php';
		$template   = $new_template;
		}

	elseif( get_query_var( 'companyname' ) && (get_query_var( 'userrole' ) == 'admin') && (get_query_var( 'tabaction' ) == 'settings') ){
		$new_template = MHDESKABSPATH . '/controllers/admin-controls/admin-settings.php';
		$template   = $new_template;
		}

	elseif( get_query_var( 'companyname' ) && (get_query_var( 'userrole' ) == 'admin') && (get_query_var( 'tabaction' ) == 'profile') ){
		$new_template = MHDESKABSPATH . '/controllers/admin-controls/admin-profile.php';
		$template   = $new_template;
		}






	elseif( (get_query_var( 'userrole' ) == 'agent') && get_query_var( 'companyname' ) && !get_query_var( 'tabaction' )){
		$new_template = MHDESKABSPATH . '/controllers/agent-controls/agent-main.php';
		$template   = $new_template;
		}

	elseif( get_query_var( 'companyname' ) && (get_query_var( 'userrole' ) == 'agent') && (get_query_var( 'tabaction' ) == 'tickets') ){
		$new_template = MHDESKABSPATH . '/controllers/agent-controls/agent-tickets.php';
		$template   = $new_template;
		}

	elseif( get_query_var( 'companyname' ) && (get_query_var( 'userrole' ) == 'agent') && (get_query_var( 'tabaction' ) == 'customers') ){
		$new_template = MHDESKABSPATH . '/controllers/agent-controls/agent-customers.php';
		$template   = $new_template;
		}

	elseif( get_query_var( 'companyname' ) && (get_query_var( 'userrole' ) == 'agent') && (get_query_var( 'tabaction' ) == 'profile') ){
		$new_template = MHDESKABSPATH . '/controllers/agent-controls/agent-profile.php';
		$template   = $new_template;
		}






	elseif( (get_query_var( 'userrole' ) == 'customer') && get_query_var( 'companyname' ) && !get_query_var( 'tabaction' )){
		$new_template = MHDESKABSPATH . '/controllers/customer-controls/customer-main.php';
		$template   = $new_template;
		}

	elseif( get_query_var( 'companyname' ) && (get_query_var( 'userrole' ) == 'customer') && (get_query_var( 'tabaction' ) == 'tickets') ){
		$new_template = MHDESKABSPATH . '/controllers/customer-controls/customer-tickets.php';
		$template   = $new_template;
		}

	elseif( get_query_var( 'companyname' ) && (get_query_var( 'userrole' ) == 'customer') && (get_query_var( 'tabaction' ) == 'profile') ){
		$new_template = MHDESKABSPATH . '/controllers/customer-controls/customer-profile.php';
		$template   = $new_template;
		}



		return $template;
} // END function helpdesk_template_include( $template )

?>