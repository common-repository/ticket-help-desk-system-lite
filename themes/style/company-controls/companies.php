<?php
	$hd_admin_settings_arr = get_option('APF_MyFirstFrom');
		
	$helpdesk_rewriterule_slug = isset($hd_admin_settings_arr['m_helpdesk_form']['helpdesk_rewriterule_slug']) && $hd_admin_settings_arr['m_helpdesk_form']['helpdesk_rewriterule_slug'] ? $hd_admin_settings_arr['m_helpdesk_form']['helpdesk_rewriterule_slug'] : 'company';
?>
<div class="top_hd_menu">
	<?php
		
		if(global_logged_in_user_is_admin()){?>
				<a href="<?php echo site_url().'/add-helpdesk/'?>" class="button button-default btn btn-default"><?php _e('Create New Helpdesk','mhelpdesk');?></a><?php
		}?>
	</div><?php

		//$get_action = isset($_GET['action']) ? $_GET['action'] : false;
		
	?>
            <table id="company_exist">
                <tr>
                    <th class="tick_gen_cols"><p></p><?php _e('Company Title','mhelpdesk');?></th>
                </tr><?php
if(!isset($_GET['action']) ){

 	foreach($comp_DB_objs as $comp_DB_obj){ 
			//$author = $comp_DB_obj->post_author;
			//echo check_user_status($curUserID,$comp_DB_obj->ID);
			?>
					
                    <?php if(check_user_status($curUserID,$comp_DB_obj->ID) == 'admin'){ ?>
						<tr>
						<td class="tick_gen_cols">
                        <a href="<?php echo get_permalink($comp_DB_obj->ID); ?>"><?php echo $comp_DB_obj->post_title; ?></a>
                        &raquo;&raquo;
                        <a href="<?php echo get_permalink($comp_DB_obj->ID); ?>admin"><?php _e('Admin Panel','mhelpdesk');?></a>
                        &nbsp;&nbsp;&nbsp;
                         <a href="?action=delete&id=<?php echo $comp_DB_obj->ID; ?>"><?php _e('Delete This Company','mhelpdesk');?></a> 
                        </td>
						</tr>	
					<?php  
					continue;
					} ?> 
                    
    
    
    
                    <?php if(check_user_status($curUserID,$comp_DB_obj->ID) == 'agent' && check_user_status($curUserID,$comp_DB_obj->ID) != 'false'){?> 
											<tr>
                    
              
                    						<td class="tick_gen_cols">
                                            <a href="<?php echo get_permalink($comp_DB_obj->ID).""; ?>"><?php echo $comp_DB_obj->post_title; ?></a>
                                            &raquo;&raquo;
                                            <a href="<?php echo get_permalink($comp_DB_obj->ID).""; ?>agent"><?php _e('Agent Panel','mhelpdesk');?></a>
											</td>
                    						</tr>
											<?php 
											continue;
										} // if($agentEmail_foundIndex > -1)
								
								?>
                    
                    
						<?php if(check_user_status($curUserID,$comp_DB_obj->ID) == 'customer'){?> 
										<tr>
                    					<td class="tick_gen_cols">
                                        <a href="<?php echo get_permalink($comp_DB_obj->ID) ?>"><?php echo $comp_DB_obj->post_title; ?></a>
                                        &raquo;&raquo;
                                        <a href="<?php echo get_permalink($comp_DB_obj->ID).""; ?>customer"><?php _e('Customer Panel','mhelpdesk');?></a>
										</td>
                						</tr>
										<?php 			
									} // if($customerID_foundIndex > -1)?>
                    
	<?php }// foreach($comp_DB_objs as $comp_DB_obj) ?>
            </table><?php 	
		} //if(!$_GET['action'])





if(isset($_GET['id'])){
	
	///////////////////////////
	// Trashing Tickets Block
	//////////////////////////
	if($_GET['action'] == 'delete'){
		
	
	
		if(isset($authCheck_obj[0]) && $authCheck_obj[0]->post_author == $curUserID){
			echo __('Are you sure to delete this ','mhelpdesk').'"'.get_the_title($company_id).'" '.__('company ','mhelpdesk').'<span 
			class="error">'.__('PERMANENTLY','mhelpdesk').'</span>?<br />';
			echo '<a href="?action=delete&id='.$company_id.'&delete=yes">'.__('Yes','mhelpdesk').'</a>&nbsp;&nbsp;&nbsp;&nbsp;
				<a href="??action=delete&id='.$company_id.'&delete=no"">'.__('No','mhelpdesk').'</a>';
			
			if($_GET['delete'] == 'yes'){
				wp_trash_post($company_id);
				
	$tickDelete_IDs_objs = $wpdb->get_results("SELECT ID FROM $wpdb->posts ticketposts, $wpdb->postmeta companymeta WHERE ticketposts.ID = companymeta.post_id
	AND (companymeta.meta_key = 'ticket-selectedCompany' AND companymeta.meta_value = '$company_id') AND ticketposts.post_type = 'tickets' AND ticketposts.post_status = 'publish'");
	
	if(isset($tickDelete_IDs_objs[0])){
		foreach($tickDelete_IDs_objs as $tickDelete_IDs_obj)
			wp_trash_post($tickDelete_IDs_obj->ID);
	} // if(isset($tickDelete_IDs_objs[0]))
	 echo "<meta http-equiv=refresh content=0;url=".site_url()."/".$helpdesk_rewriterule_slug.">";
			}elseif($_GET['delete'] == 'no') echo "<meta http-equiv=refresh content=0;url=".site_url()."/".$helpdesk_rewriterule_slug.">";
		} // if(isset($tickAuthObj[0]))
		else echo '<h1 class="error">'.__('Invalid Request:','mhelpdesk').'</h1>';
	} // if($_GET['action'] == 'delete')
} // if(isset($_GET['id']))





?>
