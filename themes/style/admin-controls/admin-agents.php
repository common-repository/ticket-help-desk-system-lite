<div class="top_hd_menu">
         <a href="<?php echo site_url().'/'.$helpdesk_rewriterule_slug?>" class="button button-default btn btn-default"><?php _e('Helpdesk Home','mhelpdesk');?></a>  
         <a href="<?php echo get_permalink($comp_DB_ID)?>" class="button button-default btn btn-default"><?php _e('Create Helpdesk Ticket','mhelpdesk');?></a> 
</div>

	<div role="tabpanel"><?php
        showAdmin_tabmenu();
		?>
          <div class="tab-content">
            <div role="tabpanel" class="tab-pane active" id="agents">
                <div id="agentsContent" class="tabed_content"><?php

		if(!isset($_GET['action'])){?>
<form name="moreAgentForm" action="#" method="post">
     <table>
     	<tr>
        	<th class="tick_gen_cols"><?php _e('Registered Agents','mhelpdesk');?></th>
        	<td>
<?php $DBcomp_agentEmails = get_post_meta($comp_DB_ID,'company-agentEmail',true);
			if(isset($DBcomp_agentEmails[0])){?>
            	<table>
                	<tr>
                    	<th class="tick_gen_cols"><?php _e('Name','mhelpdesk');?></th><th class="tick_gen_cols"><?php _e('Email','mhelpdesk');?></th>
                    </tr>
<?php
				foreach($DBcomp_agentEmails as $DBcomp_agentEmail){ 
					$user_id_emailFound = $wpdb->get_results("SELECT ID, user_email, display_name FROM $wpdb->users WHERE user_email = '$DBcomp_agentEmail'");
					if( isset($user_id_emailFound[0]) && ($user_id_emailFound[0]->user_email != '' )){ ?>         
                    <tr>
                        <td class="tick_gen_cols">
							<?php echo esc_html($user_id_emailFound[0]->display_name); ?><br /><br />
                            <p>
                                 <a href="?action=agent_tickets&id=<?php echo esc_html($user_id_emailFound[0]->ID); ?>&pagenumber=1" class="button button-default btn btn-primary btn-sm"><?php _e('Agent Assigned Tickets(s)','mhelpdesk');?></a> 
                                 
                                 
                                 <a href="?action=delete&id=<?php echo esc_html($user_id_emailFound[0]->ID); ?>" class="btn btn-danger btn-sm"  
                                  		onclick="if(confirm('Are you sure, to delete this record?')) return true; else return false">Delete</a>
                                 
                                 
                                 <!--a href="?action=delete&id=<?php echo esc_html($user_id_emailFound[0]->ID); ?>"><?php _e('Delete','mhelpdesk');?></a--> 
                            </p>
                        </td>
                        <td class="tick_gen_cols"><?php echo esc_html($user_id_emailFound[0]->user_email); ?></td>
                    </tr>
<?php
					} // if(isset($user_id_emailFound[0]))
					
					else $unregEmailsArr[] = $DBcomp_agentEmail;
				} // foreach($DBcomp_agentEmails as $DBcomp_agentEmail)
			?></table><?php
			} // if(isset($DBcomp_agentEmails[0]))
			
			//print_r($unregEmailsArr);
 ?>                
            </td>
        </tr>
<?php if(isset($unregEmailsArr[0])) { ?>
        <tr>
        	<th class="tick_gen_cols"><?php _e('Un-Registered Agents','mhelpdesk');?></th>
            <td>
            	<table>
        		<?php foreach($unregEmailsArr as $unregisteredEmail){ ?>
                	<tr>
                    	<th class="tick_gen_cols"><?php _e('Email','mhelpdesk');?></th>
                        <td class="tick_gen_cols">
	<?php echo esc_html($unregisteredEmail) ?>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;  
    <a href="?action=delete&agent_email=<?php echo esc_html($unregisteredEmail) ?>" class="btn btn-danger btn-sm" 
                                  		onclick="if(confirm('Are you sure, to delete this record?')) return true; else return false">Delete</a> 
                        </td>
                    </tr>
                <?php } //foreach($unregEmailsArr as $unregisteredEmail) ?>
                </table>
            </td>
        </tr>
<?php } //if(isset($unregEmailsArr[0])) ?>
        <tr>
        	<th class="tick_gen_cols"><?php _e('Add More Agents','mhelpdesk');?></th>
            <td class="tick_gen_cols">
                <div class="agentBlock">
                    <div align="left">
                        <a href="javascript:void(0);"><span class="dashicons dashicons-plus-alt"></span></a><?php _e('Add More Agents','mhelpdesk');?>
                     </div>
                    <table class="firstAgent">
                            <th class="tick_gen_cols"><?php _e('Agent Email Address','mhelpdesk');?></th>
                            <td class="tick_gen_cols"><input name="agentemail[]" type="email" /></td>
                        </tr>
                        <tr>
                            <td colspan="2"><a href="#" class="dashicons dashicons-dismiss" ><span class="dashicons dashicons-dismiss"></span></a></td>
                        </tr>
                    </table>
                </div>
            </td>
        </tr>
        <tr>
        	<td colspan="2">
            	<?php wp_nonce_field('adminAgentBtn', 'company-adminAgentBtn'); ?>
			    <input type="submit" name="adminAgentBtn" value="Save Agent(s)" />
            </td>
        </tr>
     </table>
</form>

<script>
jQuery(document).ready(function() {
		
    var wrapper         = jQuery(".agentBlock"); 
    var add_button      = jQuery(".dashicons-plus-alt"); 
    
    var x = 1;
		jQuery(add_button).click(function(e){ //on add input button click
			e.preventDefault();
	
				x++; 
				var origTable = jQuery('table.firstAgent').html();
	
				jQuery(wrapper).append('<table class="remove_TRs'+x+'">'+origTable+'</table>');
				//update_slidenum();
		  
		});
    
		jQuery('.dashicons-dismiss').live("click",function(e){ //user click on remove text
			e.preventDefault(); 
			tableclass = jQuery(this).closest('table').attr('class');
			if(tableclass != 'firstAgent'){
			jQuery(this).closest('table').remove(); 
			x--;
		
			}
			else
			alert('<?php _e('You cannot remove first agent!','mhelpdesk');?>');
			
		
		});
});
</script>
<?php
	} // if(!$_GET['action'])
	
	
	
	
	
	
	
	
if(isset($_GET['id'])){
	$agent_id = sanitize_text_field($_GET['id']);
	
	
	
	//////////////////////////////
	// View Agent's Tickets
	//////////////////////////////
	if(isset($_GET['action'])){
	
	//print_r($tick_DB_ID_objs);?>
			<table class="ticketswrap">
				<tr>
					<td colspan="2">
                    <div class="viewticket_td">
						<span class="viewticket_as">
							 <?php _e('Agent : ','mhelpdesk');?>
						(<?php $userAgent_DB_Obj = $wpdb->get_results("SELECT display_name FROM $wpdb->users WHERE ID = '$agent_id'"); echo esc_html($userAgent_DB_Obj[0]->display_name); ?>) 
						<?php if($url_action_value == 'agent_tickets') echo "All Assigned Tickets"; elseif($url_action_value == 'rated') echo "Rated Tickets"; 
						elseif($url_action_value == 'opened') echo "Opened Tickets"; elseif($url_action_value == 'unanswered') echo "Un-Answered Tickets";
						elseif($url_action_value == 'answered') echo "Answered Tickets"; elseif($url_action_value == 'solved') echo "Solved Tickets";
						elseif($url_action_value == 'closed') echo "Closed Tickets"; else echo "Search Results for :: $search_string";  ?>
                        </span>
                   </div>

				</td>
			</tr>
				<tr>
					<td class="style_leftmenu">
						<?php showAgent_leftmenu(); ?>
					</td>
					<td >
						<table>
						<?php if(isset($tickets[0])){
								if($pages > 1){?>
									<tr><th colspan="4" class="tick_gen_cols">
              
                Pages: <a>&laquo;</a></li><?php
                    for($index = 1; $index <= $pages; $index++){?>
                        
                            <a href="?action=<?php echo $url_action_value.'&id='.$agent_id.'&pagenumber='.$index; ?>" ><?php echo esc_html($index) ?></a>
    <?php			} // for($index = 1; $index <= $pages; $index++) ?>
                <a>&raquo;</a>
              
            
    								<br /></th></tr>
                               <?php } // if($pages > 1) ?>

									<tr><th colspan="4" class="tick_gen_cols">*<?php _e('LDAT = Last Date/Time of Activity of the Ticket','mhelpdesk');?></th></tr>
									 <tr>
										<th class="tick_gen_cols"><?php _e('Ticket Title','mhelpdesk');?></th>
										<th class="tick_gen_cols"><?php _e('Customer','mhelpdesk');?></th>
										<th class="tick_gen_cols"><?php _e('Status','mhelpdesk');?></th>
										<th><?php _e('LDAT','mhelpdesk');?>*</th>
									 </tr>                    
						<?php foreach($tickets as $tick_DB_ID_obj){ 
				$ticketAuthorID = $tick_DB_ID_obj->post_author;
				
				$ticketAuthorDisplayname_DB_Obj = $wpdb->get_results("SELECT display_name FROM $wpdb->users WHERE ID = '$ticketAuthorID'");?>            
									<tr>
										<td class="tick_gen_cols"><!-- company/test/admin/tickets/?action=edit&id=169 -->
                                        	<a href="../tickets/?action=edit&id=<?php echo $tick_DB_ID_obj->ID;?>" > <?php echo ($tick_DB_ID_obj->post_title); ?></a>
                                            <br /><?php _e('ID: ','mhelpdesk');?><?php echo $tick_DB_ID_obj->ID;?><br />
	 <?php $rating = get_metadata('post',$tick_DB_ID_obj->ID,"ticket-rating",true);
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
										<td class="tick_gen_cols"><?php echo esc_html($ticketAuthorDisplayname_DB_Obj[0]->display_name); ?></td>
										<td class="tick_gen_cols">
											<?php 
				echo get_post_meta($tick_DB_ID_obj->ID, 'ticket-status',true).' &<br />'.get_post_meta($tick_DB_ID_obj->ID, 'ticket-action-status',true); 
											?>
										</td>
										<td class="tick_gen_cols"><?php echo get_post_modified_time('Y-m-d h:i:s','false',$tick_DB_ID_obj->ID,'false');?></td>
									</tr>
						<?php 	
							} // foreach($tick_DB_ID_objs as $tick_DB_ID_obj) 
								} // if(isset($tick_DB_ID_objs[0]))
								 else {?>
											<tr>
												<td colspan="4" class="tick_gen_cols">
													<?php echo '<h1 >'.__('No '.strtoupper($url_action_value).' ticket at the moment','mhelpdesk').'</h1>'; ?></th>
											</tr>
								<?php } 								
								if($pages > 1){?>
									<tr><th colspan="6" class="tick_gen_cols">
              
                Pages: <a>&laquo;</a></li><?php
                    for($index = 1; $index <= $pages; $index++){?>
                        
                            <a href="?action=<?php echo $url_action_value.'&id='.$agent_id.'&pagenumber='.$index; ?>" ><?php echo esc_html($index) ?></a>
    <?php			} // for($index = 1; $index <= $pages; $index++) ?>
                <a>&raquo;</a>
              
            
    								<br /></th></tr>
                               <?php } // if($pages > 1) ?>

							</table>                
					</td>                
				</tr>
			</table>   
		   
	<?php
	
	} // if($_GET['action'])

		
}






?>
				</div>
            </div>
        </div>
    </div>