<?php
	$hd_admin_settings_arr = get_option('APF_MyFirstFrom');
		
	$helpdesk_rewriterule_slug = isset($hd_admin_settings_arr['m_helpdesk_form']['helpdesk_rewriterule_slug']) && $hd_admin_settings_arr['m_helpdesk_form']['helpdesk_rewriterule_slug'] ? $hd_admin_settings_arr['m_helpdesk_form']['helpdesk_rewriterule_slug'] : 'company';
?>
<div class="top_hd_menu">
        	 <a href="<?php echo site_url().'/'.$helpdesk_rewriterule_slug?>" class="button button-default btn btn-default"><?php _e('Helpdesk Home','mhelpdesk');?></a> 
             <a href="<?php echo get_permalink($comp_DB_ID)?>" class="button button-default btn btn-default"><?php _e('Create Helpdesk Ticket','mhelpdesk');?></a>  
</div>
<?php echo esc_html($message) != '' ? $message : ''?>
	<div role="tabpanel"><?php
    showAdmin_tabmenu();?>
          <div class="tab-content">
            <div role="tabpanel" class="tab-pane active" id="tickets">
                <div id="ticketsContent" class="tabed_content">
<?php
		
$compCustomerBlocked_DB_arr = get_metadata('post', $comp_DB_ID, 'company-customers-blocked', true);
$url_action_value = isset($_GET['action']) ? sanitize_text_field($_GET['action']) : ''; 

		
	
///////////////////////////
// Delete & Edit Single Ticket
//////////////////////////
	
if(isset($_GET['id'])){
	$ticket_id = sanitize_text_field($_GET['id']);
	$tickAuthObj = $wpdb->get_results("SELECT post_author FROM $wpdb->posts ticketposts, $wpdb->postmeta companymeta
	WHERE ticketposts.ID = companymeta.post_id
	AND ticketposts.ID = $ticket_id
	AND (companymeta.meta_key = 'ticket-selectedCompany' AND companymeta.meta_value = '$comp_DB_ID')
	AND ticketposts.post_type = 'tickets'
	AND ticketposts.post_status = 'publish'");
	
	
	///////////////////////////
	// Trashing Tickets Block
	//////////////////////////
	if($_GET['action'] == 'delete'){
		
		if(isset($tickAuthObj[0])){
			echo __('Are you sure to delete this "','mhelpdesk').get_the_title($ticket_id).__('" ticket?','mhelpdesk').'<br />';
			echo '<a href="?action=delete&id='.$ticket_id.'&delete=yes">'.__('Yes','mhelpdesk').'</a>&nbsp;&nbsp;&nbsp;&nbsp;
				<a href="?action=delete&id='.$ticket_id.'&delete=no">'.__('No','mhelpdesk').'</a>';
			
			if($_GET['delete'] == 'yes'){
				wp_trash_post($ticket_id);
					echo "<meta http-equiv=refresh content=0;url=".get_permalink($comp_DB_ID)."admin/tickets/?pagenumber=1 />";
			}elseif($_GET['delete'] == 'no') echo "<meta http-equiv=refresh content=0;url=".get_permalink($comp_DB_ID)."admin/tickets/?pagenumber=1 />";
		} // if(isset($tickAuthObj[0]))
		else echo '<h1 >'.__('Invalid Ticket: ','mhelpdesk').$ticket_id.'</h1>';
	} // if($_GET['action'] == 'delete')
	





	
	
	
	
	///////////////////////////
	// View & Edit Single Ticket
	//////////////////////////
	if($_GET['action'] == 'edit'){
	
	//echo '<pre>';
	//	print_r($tickAuthObj);
	//echo '</pre>';
		
		if(isset($tickAuthObj[0])){
	
		$post_author_id = $tickAuthObj[0]->post_author;
		//print_r($post_auth_id);
		$ticketAuthorDisplayname_DB_Obj = $wpdb->get_results("SELECT display_name, user_email FROM $wpdb->users WHERE ID = '$post_author_id'");?>
	<form name="ticketEditForm" method="post" action="#" enctype="multipart/form-data">	
		<table class="ticketswrap">
			<tr><td colspan="2"><h2><?php echo get_the_title($comp_DB_ID); ?></h2></td></tr>
			<tr>
				<td >
						<?php showAdmin_leftmenu(); $ticketStatus = get_post_meta($ticket_id,'ticket-status',true);?>
				</td>
				<td>
					<table class="tickets">
						<tr>
							<th class="tick_gen_cols"><?php _e('Ticket Title','mhelpdesk');?></th>
							<td class="tick_gen_cols"><?php echo get_the_title($ticket_id); $actionStatus = get_metadata('post',$ticket_id,'ticket-action-status',true); ?>
								
							</td>
						</tr>
						<tr>
							<th class="tick_gen_cols"><?php _e('Ticket Group','mhelpdesk');?></th>
							<td class="tick_gen_cols"><?php echo get_metadata('post',$ticket_id,'ticket-selected_department',true);?></td>
						</tr>
						<tr>
							<th class="tick_gen_cols"><?php _e('Status','mhelpdesk');?></th>
							<td class="tick_gen_cols"><?php echo esc_html($ticketStatus).'/ '.$actionStatus; ?></td>
						</tr>
                        
                        <tr>
							<th class="tick_gen_cols"><?php _e('Customer Name','mhelpdesk');?></th>
							<td class="tick_gen_cols"><?php echo esc_html($ticketAuthorDisplayname_DB_Obj[0]->display_name); ?></td>
						</tr>
						<tr>
							<th class="tick_gen_cols"><?php _e('Email Address','mhelpdesk');?></th>
							<td class="tick_gen_cols"><?php echo esc_html($ticketAuthorDisplayname_DB_Obj[0]->user_email); ?></td>
						</tr>
						
	
						<tr>
							<td colspan="2">
								<?php echo showConversation($ticket_id); ?>
							</td>
						</tr>
			 <?php if($ticketStatus != 'Closed'){ ?>
						<tr>
							<th colspan="2" class="tick_gen_cols"><h3><?php _e('Update the Ticket','mhelpdesk');?></h3></th>
						</tr>            
	
						<tr>
							<th class="tick_gen_cols"><?php _e('Select Agent','mhelpdesk');?></th>
							<td> 
								<?php 
									$DBcomp_agentEmails = get_post_meta($comp_DB_ID,'company-agentEmail',true);
									
										if(isset($DBcomp_agentEmails[0])){?>
										&nbsp;&nbsp;
										<select name="selectedAgent" required >
										<option value=""><?php _e('Select Registered Agent','mhelpdesk');?></option>
											<?php $DBselectedAgentID = get_post_meta(sanitize_text_field($_GET['id']),"ticket-selectedAgent",true);
												foreach($DBcomp_agentEmails as $DBcomp_agentEmail){
										  $user_id_emailFound = $wpdb->get_results("SELECT ID, display_name, user_email FROM $wpdb->users WHERE user_email = '$DBcomp_agentEmail'");
													if(isset($user_id_emailFound[0]) && $user_id_emailFound[0]->user_email != ''){?>
															<option value="<?php echo esc_html($user_id_emailFound[0]->ID); ?>" 
															<?php echo ( ($DBselectedAgentID == $user_id_emailFound[0]->ID) ? 'selected' : '' ); ?> >
															<?php echo esc_html($user_id_emailFound[0]->display_name); ?></option>
										   <?php
													} // if(isset($user_id_emailFound[0]))
												} // foreach($DBcomp_agentEmails as $DBcomp_agentEmail) ?>
										</select>	
								<?php   } // if(isset($DBcomp_agentEmails[0]))?>
	
							<input type="submit" name="adminAssignAgent" value="Assign Agent">
							</td>
							
						</tr>
						<tr>
							<th class="tick_gen_cols"><?php _e('Admin Response','mhelpdesk');?></th>
							<td class="tick_gen_cols"> 
								<?php if($actionStatus != 'Solved'){ ?> <textarea class="tick_textarea" name="adminResponse" 
                                											rows="10"></textarea><?php } // if($actionStatus != 'Solved') ?>
								<p><br /><strong><?php _e('Submit Ticket As: ','mhelpdesk');?></strong> 
										<select name="ticketActionStatus" >
											<option value="Un-Answered"><?php _e('Un-Answered','mhelpdesk');?></option>
											<option value="Answered"  <?php echo $actionStatus == 'Answered' ? 'selected' : ''; ?> ><?php _e('Answered','mhelpdesk');?></option>
											<option value="Solved" <?php echo $actionStatus == 'Solved' ? 'selected' : ''; ?> ><?php _e('Solved','mhelpdesk');?></option>
											<option value="Closed"><?php _e('Closed','mhelpdesk');?></option>
										</select>
								</p>
							</td>
						</tr>
						<?php if($actionStatus != 'Solved'){ ?>
						<tr>
							<th class="tick_gen_cols"><?php _e('Supported Attachment (if any)','mhelpdesk');?></th>
							<td class="tick_gen_cols">
     <input type="file" name="supportedDoc" id="supportedDoc" accept=".zip, .rar" title="<?php _e('Upload Supported Attachment if any as zip or rar format','mhelpdesk');?>" />
							<p id="displaysize"></p>
							</td>
						</tr>
						<?php } // if($actionStatus != 'Solved') ?>
				<?php } //if($ticketStatus != 'Closed') ?>
	
	
	<?php if( $ticketStatus  == 'Closed' ){ ?>
						<tr>
							<th colspan="2" class="tick_gen_cols"><h3><?php _e('Update the Ticket','mhelpdesk');?></h3></th>
						</tr>            
						<tr>
						   
							<td colspan="2" class="tick_gen_cols">
								<p><br /><strong><?php _e('Submit Ticket As: ','mhelpdesk');?></strong> 
                                    <select name="ticketActionStatus" >
                                        <option value="Opened"><?php _e('Opened','mhelpdesk');?></option>
                                        <option value="Closed" <?php echo $ticketStatus == 'Closed' ? 'selected' : ''; ?> ><?php _e('Closed','mhelpdesk');?></option>
                                    </select>
								</p>
							</td>
						</tr>
	<?php } //if($ticketStatus == 'Closed') ?>
	
				
					</table>
				</td>                
			</tr>
	
			<tr>
				<td colspan="2" class="tick_gen_cols">
					<?php wp_nonce_field('adminUpdateActionBtn', 'ticket-adminUpdateActionBtn'); ?>
					<input type="submit" name="adminUpdateActionBtn" value="Submit Comment" />
				</td>
			</tr>
		</table>
	</form>
	<script>
		jQuery(document).ready(function() {
	
			jQuery("#supportedDoc").change(function (e){ 
				var file = jQuery('input:file[name="supportedDoc"]').val();
				var exts = ['zip','rar'];
				var get_ext = file.split('.');
				get_ext = get_ext.reverse();
				var size_part = Math.round((jQuery("#supportedDoc")[0].files[0].size / 1024));
				if(!(jQuery.inArray ( get_ext[0].toLowerCase(), exts ) > -1 )){
				alert( <?php _e('Only zip or rar format allowed!','mhelpdesk');?> );
				jQuery("#supportedDoc").replaceWith( jQuery("#supportedDoc").clone( true ) );
				jQuery("#displaysize").html(" ");
				}
				else if(size_part > 2048 ){
					alert(<?php _e('Your Attachment should be less than 2-MB','mhelpdesk');?>);
					jQuery("#displaysize").html(" ");
					jQuery("#supportedDoc").replaceWith( jQuery("#supportedDoc").clone( true ) );
				}					
				else{
				size_part = (Math.round((size_part * 100) / 100));
				jQuery("#displaysize").html( "( " + size_part + "Kbs )");
				e.preventDefault();
				}
			});		
		}); // END jQuery(document).ready(function()
	</script>
	
	<?php	
		} // if(isset($tickAuthObj[0]))
		else echo '<h1 >'.__('Invalid Ticket: ','mhelpdesk').$ticket_id.'</h1>';
	} // if($_GET['action'] == 'edit')
}// if(isset($_GET['id']))








////////////////////////
// View Multiple Tickets
///////////////////////

if((isset($_GET['action']) && $_GET['action'] != 'edit' && $_GET['action'] != 'delete') || !isset($_GET['action']) ){?>
                          <form method="get">
                            <div class="input-group" style="width: 500px;">
                            
<?php						if(isset($_GET['action'])){	?>
                            	<input type="hidden" name="action" value="<?php echo sanitize_text_field($_GET['action']) ?>" />
<?php 						} //if(isset($_GET['action']))	?>
                                <select name="group" class="form-control">
                            <?php 	foreach($company_departments as $company_department){?>
                                    <option value="<?php echo $company_department?>"
                                    <?php echo (isset($_GET['group']) && $_GET['group']==$company_department ? 'selected' : '' )?> ><?php echo esc_html($company_department)?></option>
                            <?php 	} //foreach($company_departments as $company_department){?>
                                </select>

                              	<div class="input-group-btn">
                                	<button class="btn btn-block btn-info"><?php _e('Find Selected Group Tickets','mhelpdesk')?></button>
                             	</div>
                             
                            </div>
                           </form>


		<form name="answeredForm" method="POST" action="#">          
			<table class="ticketswrap">
				<tr>
					<td colspan="2">
                    <div class="viewticket_td">
						<span class="viewticket_as">
                        	<?php 
						if(!$url_action_value) echo "All Tickets"; elseif($url_action_value == 'new') echo "New Tickets"; elseif($url_action_value == 'rated') echo "Rated Tickets"; 
						elseif($url_action_value == 'opened') echo "Opened Tickets"; elseif($url_action_value == 'unanswered') echo "Un-Answered Tickets";
						elseif($url_action_value == 'answered') echo "Answered Tickets"; elseif($url_action_value == 'solved') echo "Solved Tickets";
						elseif($url_action_value == 'closed') echo "Closed Tickets"; else echo "Search Results for :: " . esc_html($search_string);  ?>
                        	
                        </span>
                        

                        <span class="searchblock_right">
                                <!--select name="group" class="form-control">
                            <?php 	foreach($company_departments as $company_department){?>
                                    <option value="<?php echo esc_html($company_department)?>"
                                    <?php echo (isset($_REQUEST['group']) && $_REQUEST['group']==$company_department ? 'selected' : '' )?> ><?php echo $company_department?></option>
                            <?php 	} //foreach($company_departments as $company_department){?>
                                </select-->

        						<input type="text" name="search_tickets" placeholder="Search Ticket Text" value="<?php echo (isset($_REQUEST['search_tickets']) && $_REQUEST['search_tickets'] != '' ? $_REQUEST['search_tickets'] : '')?>" />
                                <input type="hidden" name="action" value="search" />
                                <input type="submit" value="Search..." />
                        </span>
                    </div>

                        
					</td>
				</tr>
				<tr>
					<td>
						<?php showAdmin_leftmenu(); ?>
					</td>
					<td>
						<table>
						<?php if(isset($tickets[0])){
								if($pages > 1){?>
									<tr><th colspan="6" class="tick_gen_cols">
              
                Pages: <a>&laquo;</a></li><?php
                    for($index = 1; $index <= $pages; $index++){?>
                        
                            <a href="?action=<?php echo $url_action_value.'&id='.$agent_id.'&pagenumber='.$index; ?>" ><?php echo esc_html($index); ?></a>
    <?php			} // for($index = 1; $index <= $pages; $index++) ?>
                <a>&raquo;</a>
              
            
    								<br /></th></tr>
                               <?php } // if($pages > 1) ?>
									
<tr><th colspan="6">
                        	<input type="submit" name="checkbox_del_btn" value="Delete" />
 							<?php wp_nonce_field('newTicketForm', 'ticket-newTicketForm'); ?>
                        </th></tr>
									 <tr>
										<th>&nbsp;</th>
										<th class="tick_gen_cols"><?php _e('Ticket Title','mhelpdesk');?></th>
										<th class="tick_gen_cols"><?php _e('Agent','mhelpdesk');?></th>
										<th class="tick_gen_cols"><?php _e('Customer','mhelpdesk');?></th>
										<th class="tick_gen_cols"><?php _e('Status','mhelpdesk');?></th>
										<th class="tick_gen_cols"><?php _e('Updated','mhelpdesk');?>*</th>
									 </tr>                    
						<?php foreach($tickets as $tick_DB_ID_obj){ 
				$ticketAuthorID = $tick_DB_ID_obj->post_author;
				
				$ticketAuthorDisplayname_DB_Obj = $wpdb->get_results("SELECT display_name FROM $wpdb->users WHERE ID = '$ticketAuthorID'");?>            
				
									<tr>
                                    <td class="tick_del_CBs_col"><input type="checkbox" name="ticketCBs[]" value="<?php echo $tick_DB_ID_obj->ID;?>" /></td>
										<td class="tick_gen_cols">
											<a href="?action=edit&id=<?php echo $tick_DB_ID_obj->ID; ?>"><?php echo $tick_DB_ID_obj->post_title; ?></a><br /><?php _e('ID: ','mhelpdesk'); echo $tick_DB_ID_obj->ID;?><br />
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
											<p>
											 <a href="?action=edit&id=<?php echo $tick_DB_ID_obj->ID; ?>"><?php _e('Edit','mhelpdesk');?></a> 
											 
											</p>
										</td>
										<td class="tick_gen_cols">
											<?php $tickSelectedAgentID = get_metadata('post',$tick_DB_ID_obj->ID, 'ticket-selectedAgent',true);
				$userAgent_DB_Obj = $wpdb->get_results("SELECT display_name FROM $wpdb->users WHERE ID = '$tickSelectedAgentID'");
											echo (isset($userAgent_DB_Obj[0]) ? $userAgent_DB_Obj[0]->display_name : 'No Assigned'); 
											?>
										</td>
										<td class="tick_gen_cols"><?php echo esc_html($ticketAuthorDisplayname_DB_Obj[0]->display_name); ?></td>
										<td class="tick_gen_cols">
											<?php 
				echo get_post_meta($tick_DB_ID_obj->ID, 'ticket-status',true).' &<br />'.get_post_meta($tick_DB_ID_obj->ID, 'ticket-action-status',true); 
											?>
										</td>
										<td class="tick_gen_cols"><?php echo $tick_DB_ID_obj->post_modified;?></td>
									</tr>
						<?php 
								
							} // foreach($tick_DB_ID_objs as $tick_DB_ID_obj) 
								} // if(isset($tick_DB_ID_objs[0]))
								 else {?>
									
											<tr>
											<td colspan="6">
												<?php echo '<h1 >'.__('No '.strtoupper($url_action_value).' ticket at the moment!','mhelpdesk').'</h1>'; ?></td>
											</tr>
								<?php } ?>
									<tr>
										<td colspan="6"><?php if($pages > 1){?>
              
                Pages: <a>&laquo;</a></li><?php
                    for($index = 1; $index <= $pages; $index++){?>
                        
                            <a href="?action=<?php echo esc_html($url_action_value).'&id='.$agent_id.'&pagenumber='.$index; ?>" ><?php echo esc_html($index); ?></a>
    <?php			} // for($index = 1; $index <= $pages; $index++) ?>
                <a>&raquo;</a>
              
            
    <?php } // if($pages > 1) ?> </td>
									</tr>
							</table>                
					</td>                
				</tr>
			</table>   
		</form>   
	<?php 
} // if(isset($_GET['action'])) ?>

				</div>
            </div>
        </div>
    </div>