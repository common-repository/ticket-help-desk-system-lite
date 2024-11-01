<?php
class create_company_posttype_class{
	function __construct(){
		add_action('init', array(&$this,'custom_company_posttype_func'));
		add_action('add_meta_boxes',array(&$this,'init_custom_company_metaboxCBF'),1,2);
	} // function __construct()
	
		function custom_company_posttype_func(){
			$hd_admin_settings_arr = get_option('APF_MyFirstFrom');
		
			$helpdesk_rewriterule_slug = isset($hd_admin_settings_arr['m_helpdesk_form']['helpdesk_rewriterule_slug']) && $hd_admin_settings_arr['m_helpdesk_form']['helpdesk_rewriterule_slug'] ? $hd_admin_settings_arr['m_helpdesk_form']['helpdesk_rewriterule_slug'] : 'company';
			
				register_post_type( 'companies', array(
				'labels' => array(
				'name_admin_bar' => _x( 'Companies', 'Add New Company','company' ),
				'name'=> __( 'All Companies','company' ),
				'singular_name' => __( 'Company','company' ),
				'add_new' => __( 'Add New Company','company' ),
				'add_new_item' => __( 'Add New Company','company' ),
				'edit' => __( 'Edit Company','company' ),
				'edit_item' => __( 'Edit Company','company' ),
				'new-item' => __( 'New Company','company' ),
				'view' => __( 'View Company','company' ),
				'view_item' => __( 'View Company','company' ),
				),
				'public'  => true,
				'menu_icon' => 'dashicons-share-alt',
				'capability_type' => 'post',
				'map_meta_cap' => true,
				'query_var' => false,
				'delete_with_user' => true,
				'supports' => false, //array('author'),
				'rewrite'  => array( 'slug' => $helpdesk_rewriterule_slug ),
				)
				);
				flush_rewrite_rules();
			
			if(isset($_GET['theme']) && $_GET['theme'] > 0 && $_GET['theme'] < 3)
				setcookie('hd_theme_is', sanitize_text_field($_GET['theme']), time() + (86400 * 1), "/"); // 600 = 5mins   86400 = 1 day
			
		}   ///function custom_company_posttype_func
		

	function init_custom_company_metaboxCBF($this_post_type,$this_post_obj){
	
		 //add_meta_box( $id, $title, $callback, $post_type, $context,$priority, $callback_args );
		 add_meta_box('custom_company_metabox_id', __('Company Name: '.$this_post_obj->post_title,'mhelpdesk'),array(&$this,'add_custom_company_metaboxCBF'),'companies','normal','high');
		 
	} // function init_custom_company_metaboxCBF()
	
	function add_custom_company_metaboxCBF($this_post_obj){
		$faq = (get_metadata('post', $this_post_obj->ID, 'company-faqs', true));
		$company_agentEmail = get_metadata('post', $this_post_obj->ID,'company-agentEmail',true);
		$company_agentEmail = count($company_agentEmail) >= 1 ? $company_agentEmail : 0;
		$company_notificationEmails = get_metadata('post', $this_post_obj->ID,'company-notificationEmails',true);
		
		?>
    <table>
    	<tr>
        	<th style="width:200px;text-align:left;vertical-align: top;"><?php _e('Company Introduction','mhelpdesk');?></th>
            <td style="padding-left:20px;vertical-align: top;"><?php echo $this_post_obj->post_content;?></td>
        </tr>
    	<tr>
        	<th style="text-align:left;vertical-align: top;"><?php _e('Company FAQs','mhelpdesk');?></th>
            <td style="padding-left:20px;vertical-align: top;"><?php echo $faq?></td>
        </tr>
    	<tr>
        	<th style="text-align:left;vertical-align: top;"><?php _e('Company Support Team/ Agents','mhelpdesk');?></th>
            <td style="padding-left:20px;vertical-align: top;"><?php echo $company_agentEmail != 0 ? implode(',',$company_agentEmail) : ''?></td>
        </tr>
    	<!--tr>
        	<th style="text-align:left;vertical-align: top;"><?php _e('The Email Addresses, When New Tickets Created For Notifications purpose','mhelpdesk');?></th>
            <td style="padding-left:20px"><?php echo $company_notificationEmails?></td>
        </tr-->
    </table>
	
<?php    
	} // add_custom_company_metaboxCBF

} //close class
$object = new create_company_posttype_class();
?>