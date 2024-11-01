<?php
	$hd_admin_settings_arr = get_option('APF_MyFirstFrom');
		
	$helpdesk_rewriterule_slug = isset($hd_admin_settings_arr['m_helpdesk_form']['helpdesk_rewriterule_slug']) && $hd_admin_settings_arr['m_helpdesk_form']['helpdesk_rewriterule_slug'] ? $hd_admin_settings_arr['m_helpdesk_form']['helpdesk_rewriterule_slug'] : 'company';


?>
<div class="top_hd_menu">
        	 <a href="<?php echo site_url().'/'.$helpdesk_rewriterule_slug?>" class="button btn btn-default"><?php _e('Helpdesk Home','mhelpdesk');?></a> 
             <a href="<?php echo get_permalink($comp_DB_ID)?>" class="button btn btn-default"><?php _e('Create Helpdesk Ticket','mhelpdesk');?></a>  
</div>
	<div role="tabpanel"><?php
        showAdmin_tabmenu();
		?>
          <div class="tab-content">
            <div role="tabpanel" class="tab-pane active" id="customers">
                <div id="customersContent" class="tabed_content"><?php
				
				


//		$compCustomerBlocked_DB_arr = get_metadata('post', $comp_DB_ID, 'company-customers-blocked', true);
		
//		$compCustomerOpened_DB_arr = get_metadata('post', $comp_DB_ID, 'company-customers-opened', true);

			//print_r($compCustomerBlocked_DB_arr); 
			
	if(!isset($_GET['action'])){		
		
		if($compCustomerOpened_DB_arr){?>
			<table>
            	<tr><th class="tick_gen_cols"><?php _e('Customer Name','mhelpdesk');?></th>
                <th class="tick_gen_cols"><?php _e('Account Status','mhelpdesk');?></th>
                <th class="tick_gen_cols"><?php _e('Remarks','mhelpdesk');?></th></tr>
                <?php foreach($compCustomerOpened_DB_arr as $customerID){
						
						$customer_displaynameObj = $wpdb->get_results("SELECT display_name FROM $wpdb->users WHERE ID = '$customerID'");
						if(isset($customer_displaynameObj[0])){?>
                <tr>
                	<td class="tick_gen_cols">
						<?php echo esc_html($customer_displaynameObj[0]->display_name); ?>
                        <br /><br />
                                <p>
                                 <a href="?action=customer_tickets&id=<?php echo esc_html($customerID) ?>&pagenumber=1" class="btn btn-primary btn-sm"><?php _e('View Customer Tickets','mhelpdesk');?></a>&nbsp;&nbsp;&nbsp;
                                 <a href="?action=edit&id=<?php echo esc_html($customerID) ?>" class="btn btn-warning btn-sm"><?php _e('Edit','mhelpdesk');?></a>&nbsp;&nbsp;&nbsp;
                                 <a class="red" href="?action=delete&id=<?php echo esc_html($customerID) ?>" class="btn btn-danger btn-sm" onclick="if(confirm('Are you sure, to delete this record?')) return true; else return false">
									<?php _e('Delete','mhelpdesk');?></a> 
                                </p>
                    </td>
                    <td class="tick_gen_cols">
					<?php echo ( isset($compCustomerBlocked_DB_arr[$customerID]) ? __('Blocked','mhelpdesk') : __('Opened','mhelpdesk') ); ?></td>
                    <td class="tick_gen_cols">
					<?php echo ( isset($compCustomerBlocked_DB_arr[$customerID]) ? ($compCustomerBlocked_DB_arr[$customerID]) : __('OK','mhelpdesk')); ?></td>
                </tr>
                <?php 
						} // if(isset($customer_displaynameObj[0]))
					} //foreach($compCustomerOpened_DB_arr as $customerID) ?>
            </table>
<?php
		} // if($compCustomerOpened_DB_arr)
		else echo '<h1 ><br />'.__('This Company has no Customer yet!','mhelpdesk').'</h1>';
	} // if(!$_GET['action'])
	
	
	
	
	
	
	
	
	
$customer_id_GET = isset($_GET['id']) ? sanitize_text_field($_GET['id']) : 0;
	
//////////////////////////////
// View Customers Tickets
//////////////////////////////
if(isset($_GET['action']) && $_GET['action'] == 'customer_tickets'){ 


	
	if(!isset($compCustomerBlocked_DB_arr[$customer_id_GET])){
		if(isset($tickets[0])){?>
		<a href="<?php echo get_permalink($comp_DB_ID)."admin/customers";?>" ><?php _e("Go Back to Customer's Home Link",'mhelpdesk');?></a>
        
		<table>
        
<?php 
if($pages > 1){?>
									<tr><th colspan="4" class="tick_gen_cols">
              
                Pages: <a>&laquo;</a></li><?php
                    for($index = 1; $index <= $pages; $index++){?>
                        
                            <a href="?action=<?php echo $url_action_value.'&id='.$customer_id_GET.'&pagenumber='.$index; ?>" ><?php echo esc_html($index) ?></a>
    <?php			} // for($index = 1; $index <= $pages; $index++) ?>
                <a>&raquo;</a>
              
            
    								<br /></th></tr>
                               <?php } // if($pages > 1) ?>
                               

        	<tr><td colspan="4" class="tick_gen_cols"><h2><?php echo esc_html($customer_displaynameObj[0]->display_name); ?></h2></td></tr>
            <tr>
            	<th class="tick_gen_cols"><?php _e('Ticket Title','mhelpdesk');?></th>
                <th class="tick_gen_cols"><?php _e('Assigned Agent','mhelpdesk');?></th>
                <th class="tick_gen_cols"><?php _e('Ticket Status','mhelpdesk');?></th>
                <th class="tick_gen_cols"><?php _e('Updated Date','mhelpdesk');?></th>
            </tr>
    <?php 
				foreach($tickets as $customerTickets_DB_obj){ 
	
			$selectedAgentID = get_metadata('post', $customerTickets_DB_obj->ID, 'ticket-selectedAgent', true);
			$agent_displaynameObj = $wpdb->get_results("SELECT display_name FROM $wpdb->users WHERE ID = '$selectedAgentID'");
			?>
            <tr>
            	<td class="tick_gen_cols"><!-- company/test/admin/tickets/?action=edit&id=151 -->
					<a href="../tickets/?action=edit&id=<?php echo $customerTickets_DB_obj->ID;?>" ><?php echo $customerTickets_DB_obj->post_title; ?></a>
                    
                    <br />	<?php _e('ID: ','mhelpdesk');  
							echo $customerTickets_DB_obj->ID;?>
                    <br />
 <?php $rating = get_metadata('post',$customerTickets_DB_obj->ID,"ticket-rating",true); 
 if ( $rating ){
	?>
    <div class="comments-content">
		<div class="rating">
		<?php
		for($i = 5; $i >= 1; $i = $i - 1){
		echo '<span '.($i <= $rating ? 'class=fillstar' : '').'>&#9734;</span>';
		} //end for loop
		?>
		</div>
    </div>
    <?php
	} // if ( $rating ) ?>
                                
                </td>
                <td class="tick_gen_cols"><?php echo ( isset($agent_displaynameObj[0]) ? esc_html($agent_displaynameObj[0]->display_name) : __('No Assigned Yet','mhelpdesk')); ?></td>
                <td class="tick_gen_cols">
					<?php echo get_metadata('post', $customerTickets_DB_obj->ID, 'ticket-status', true).' &<br />'.
							   get_metadata('post', $customerTickets_DB_obj->ID, 'ticket-action-status', true);	?>
                </td>
                <td class="tick_gen_cols"><?php echo $customerTickets_DB_obj->post_modified; ?></td>
            </tr>
    <?php 	} //foreach($customerTickets_DB_objs as $customerTickets_DB_obj)
	
	if($pages > 1){?>
									<tr><th colspan="4" class="tick_gen_cols">
              
                Pages: <a>&laquo;</a></li><?php
                    for($index = 1; $index <= $pages; $index++){?>
                        
                            <a href="?action=<?php echo $url_action_value.'&id='.$customer_id_GET.'&pagenumber='.$index; ?>" ><?php echo esc_html($index) ?></a>
    <?php			} // for($index = 1; $index <= $pages; $index++) ?>
                <a>&raquo;</a>
              
            
    								<br /></th></tr>
                               <?php } // if($pages > 1) ?>
	
        </table> <a href="<?php echo get_permalink($comp_DB_ID)."admin/customers";?>" ><?php _e("Go Back to Customer's Home Link",'mhelpdesk');?></a>
<?php  } // if(isset($customerTickets_DB_objs[0]))
		 else echo '<h1 >'.__('The Company has no ticket at the moment!','mhelpdesk').'</h1>'; ?>
        	
<?php	
		} // if(!isset($compCustomerBlocked_DB_arr[$relevantCID_foundIndex]))
		else echo '<h1 ><br /><br />'.__('This Customer ','mhelpdesk').'('.esc_html($customer_displaynameObj[0]->display_name).') '.__('is blocked.','mhelpdesk').'</h1>';
		

	

 ?>



<?php
} // if($_GET['action'] == 'customer_tickets')




/////////////////////////////
//     Edit Customers
/////////////////////////////
if(isset($_GET['action']) && $_GET['action'] == 'edit'){ ?>

<form method="post" action="#">
<a href="<?php echo $_SERVER['HTTP_REFERER'];?>" ><?php _e("Go Back to Customer's Home Link",'mhelpdesk');?></a>
        <table>
            <tr><th class="tick_gen_cols"><?php _e('Customer Name','mhelpdesk');?></th>
            	<th class="tick_gen_cols"><?php _e('Account Status','mhelpdesk');?></th>
                <th class="tick_gen_cols"><?php _e('Delete Tickets','mhelpdesk');?></th></tr>
            <tr>
                <td class="tick_gen_cols"><?php echo esc_html($customer_displaynameObj[0]->display_name); ?></td>
                <td class="tick_gen_cols">
		<?php if($global_customer_block_permission == 'true'){ ?>
                    <select name="accessStatus" id="accessStatus">
                        <option value="Opened"><?php _e('Opened','mhelpdesk');?></option>
                        <option value="Blocked" <?php echo isset($compCustomerBlocked_DB_arr[$customer_id_GET]) ? 'selected' : ''; ?> ><?php _e('Blocked','mhelpdesk');?></option>
                    </select>
		<?php } //if($global_customer_block_permission == 'true'){
				else
					echo '&nbsp;'; ?>
                </td>
                <td class="tick_gen_cols"><input name="deleteAuthorTickets" type="checkbox" value="yes" /></td>
            </tr>
            <tr id="blockedTextRow" class="tick_gen_cols"><td colspan="3" class="tick_gen_cols">
            	<textarea class="tick_textarea" name="blockedText" id="blockedText" required="required" 
                placeholder=<?php _e('Why Blocking? Also mentioned the Ticket Title; the basis you are bocking the customer','mhelpdesk');?>>
				<?php echo isset($compCustomerBlocked_DB_arr[$customer_id_GET]) ? $compCustomerBlocked_DB_arr[$customer_id_GET] : ''; ?></textarea>
            </td></tr>
        </table>
	<?php wp_nonce_field('customerUpdateBtn', 'company-customerUpdateBtn'); ?>
    <input type="submit" name="customerUpdateBtn" value="Save Updation" />
    <br /><br /><a href="<?php echo $_SERVER['HTTP_REFERER'];?>" ><?php _e("Go Back to Customer's Home Link",'mhelpdesk');?></a>
</form>
<script>
jQuery(document).ready(function(){
				
	if(jQuery('#accessStatus :selected').val() == 'Opened'){
		
		jQuery("#blockedTextRow").fadeOut('slow', function(){ 
			jQuery(this).attr('style', 'display:none');
		});
		
		jQuery('#blockedText').prop('disabled', 'disabled');
	}
	else{
			jQuery("#blockedTextRow").fadeIn('slow', function(){ 
			jQuery(this).attr('style', 'display');
		});
		
		jQuery('#blockedText').prop('disabled', false);
	}
					
		jQuery('#accessStatus').change(function(){
			if(jQuery('#accessStatus :selected').val() == 'Opened'){
				
				jQuery("#blockedTextRow").fadeOut('slow', function(){ 
					jQuery(this).attr('style', 'display:none');
				});
				
				jQuery('#blockedText').prop('disabled', 'disabled');
			}
			else{
					jQuery("#blockedTextRow").fadeIn('slow', function(){ 
					jQuery(this).attr('style', 'display');
				});
				
				jQuery('#blockedText').prop('disabled', false);
			}
		}); // END jQuery('#accessStatus').change(function()
}); // END jQuery(document).ready(function()	
</script>
		
<?php		

	


} // if($_GET['action'] == 'edit')


/*/////////////////////////////
//     Delete Customers
/////////////////////////////
if($_GET['action'] == 'delete'){ 

$relevantCID_foundIndex = array_search($customer_id_GET, isset($compCustomerOpened_DB_arr[0]) ? $compCustomerOpened_DB_arr : array());

	if($relevantCID_foundIndex > -1 ){
		$customer_displaynameObj = $wpdb->get_results("SELECT display_name FROM $wpdb->users WHERE ID = '$customer_id_GET'");
		
		echo __('Are you sure to delete this ','mhelpdesk').'"'.esc_html($customer_displaynameObj[0]->display_name).'" '.__('Customer?','mhelpdesk').'<br />';
		echo '<a href="?action=delete&id='.$customer_id_GET.'&delete=yes">'.__('Yes','mhelpdesk').'</a>&nbsp;&nbsp;&nbsp;&nbsp
				<a href="??action=delete&id='.$customer_id_GET.'&delete=no">'.__('No','mhelpdesk').'</a>';
				
			if($_GET['delete'] == 'yes'){ // 
				
				unset($compCustomerOpened_DB_arr[$relevantCID_foundIndex]);
				rsort($compCustomerOpened_DB_arr);
				update_metadata('post', $comp_DB_ID, 'company-customers-opened',$compCustomerOpened_DB_arr); 

$tickDelete_IDs_objs = $wpdb->get_results("SELECT ID FROM $wpdb->posts ticketposts, $wpdb->postmeta companymeta WHERE ticketposts.ID = companymeta.post_id
AND ticketposts.post_author = '$customer_id_GET'
AND (companymeta.meta_key = 'ticket-selectedCompany' AND companymeta.meta_value = '$comp_DB_ID')
AND ticketposts.post_type = 'tickets'
AND ticketposts.post_status = 'publish'");

if(isset($tickDelete_IDs_objs[0])){
	foreach($tickDelete_IDs_objs as $tickDelete_IDs_obj)
		wp_trash_post($tickDelete_IDs_obj->ID);
} // if(isset($tickDelete_IDs_objs[0]))
				
				echo "<meta http-equiv=refresh content=0;url=".get_permalink($comp_DB_ID)."admin/customers/ />";
			}elseif($_GET['delete'] == 'no') echo "<meta http-equiv=refresh content=0;url=".get_permalink($comp_DB_ID)."admin/customers/ />";
		
	} //if($relevantCID_foundIndex > -1 )
	else echo '<h1 >'.__('Irrelevant Request','mhelpdesk').'('.$customer_id_GET.') '.__('passed.','mhelpdesk').'</h1>';


} // if($_GET['action'] == 'delete')
*/

?>


				</div>
            </div>
        </div>
    </div>