<?php
	$hd_admin_settings_arr = get_option('APF_MyFirstFrom');
		
	$helpdesk_rewriterule_slug = isset($hd_admin_settings_arr['m_helpdesk_form']['helpdesk_rewriterule_slug']) && $hd_admin_settings_arr['m_helpdesk_form']['helpdesk_rewriterule_slug'] ? $hd_admin_settings_arr['m_helpdesk_form']['helpdesk_rewriterule_slug'] : 'company';
?>
<div class="top_hd_menu">
         <a href="<?php echo site_url().'/'.$helpdesk_rewriterule_slug?>" class="button button-default btn btn-default"><?php _e('Helpdesk Home','mhelpdesk');?></a>  
         <a href="<?php echo get_permalink($comp_DB_ID)?>" class="button button-default btn btn-default"><?php _e('Create Helpdesk Ticket','mhelpdesk');?></a> 
</div>
	
	<div role="tabpanel"><?php
        showCustomer_tabmenu();?>
          <div class="tab-content">
            <div role="tabpanel" class="tab-pane active">
                <div id="ticketsContent" class="tabed_content">
<?php 


	
		
		
		
if($url_action_value != 'edit' ){?> 
      <form method="get">
        <div class="input-group" style="width: 500px;">
        
<?php						if(isset($_GET['action'])){	?>
            <input type="hidden" name="action" value="<?php echo sanitize_text_field($_GET['action']) ?>" />
<?php 						} //if(isset($_GET['action']))	?>
            <select name="group" class="form-control">
        <?php 	foreach($company_departments as $company_department){?>
                <option value="<?php echo $company_department?>"
                <?php echo (isset($_GET['group']) && $_GET['group']==$company_department ? 'selected' : '' )?> ><?php echo $company_department?></option>
        <?php 	} //foreach($company_departments as $company_department){?>
            </select>

            <div class="input-group-btn">
                <button class="btn btn-block btn-info"><?php _e('Find Selected Group Tickets','mhelpdesk')?></button>
            </div>
         
        </div>
       </form>
     
  		<table class="ticketswrap">
        	
            <tr><td colspan="2">
            <form method="get">                    
            		<div class="viewticket_td">
						<span class="viewticket_as">
                        	<?php 
						if(!$url_action_value) echo "All Tickets"; elseif($url_action_value == 'new') echo "New Tickets"; elseif($url_action_value == 'rated') echo "Rated Tickets"; 
						elseif($url_action_value == 'opened') echo "Opened Tickets"; elseif($url_action_value == 'unanswered') echo "Un-Answered Tickets";
						elseif($url_action_value == 'answered') echo "Answered Tickets"; elseif($url_action_value == 'solved') echo "Solved Tickets";
						elseif($url_action_value == 'closed') echo "Closed Tickets"; else echo esc_html("Search Results for :: $search_string");  ?>
                        	
                        </span>
                        

                        <span class="searchblock_right">
                        	
        						<input type="text" name="search_tickets" value="<?php echo (isset($_REQUEST['search_tickets']) && $_REQUEST['search_tickets'] != '' ? $_REQUEST['search_tickets'] : '')?>" />
                                <input type="hidden" name="action" value="search" />
                                <input type="submit" value="Search Ticket Text" />
                            

                        </span>
                    </div>
			</form>
                        
					</td>
				</tr>

            <tr>
            	<td >
                	<?php showCustomer_leftmenu(); ?>
                </td>
            	<td>
					<table class="tickets">
                    <?php if(isset($tickets[0])){ ?>
                    	<tr><td colspan="5" class="tick_gen_cols"><?php if($pages > 1){?>
              
                Pages: <a>&laquo;</a></li><?php
                    for($index = 1; $index <= $pages; $index++){?>
                        
                            <a href="?action=<?php echo $url_action_value.'&id='.$agent_id.'&pagenumber='.$index; ?>" ><?php echo esc_html($index) ?></a>
    <?php			} // for($index = 1; $index <= $pages; $index++) ?>
                <a>&raquo;</a>
              
            
    <?php } // if($pages > 1) ?></td></tr>
                        <tr><th colspan="5" class="tick_gen_cols">*<?php _e('LDAT = Last Date/Time Activity of the Ticket','mhelpdesk');?></th></tr>
                         <tr>
							<th class="tick_gen_cols"><?php _e('Ticket Title','mhelpdesk');?></th>
                            <th class="tick_gen_cols"><?php _e('Agent','mhelpdesk');?></th>
                            <th class="tick_gen_cols"><?php _e('Status','mhelpdesk');?></th>
                            <th class="tick_gen_cols"><?php _e('LDAT','mhelpdesk');?>*</th>
                         </tr>
				<?php 
				 foreach($tickets as $tick_DB_ID_obj){?>
                        <tr>
                        	
                            <td class="tick_gen_cols">
                                <?php echo $tick_DB_ID_obj->post_title; ?><br /><?php _e('ID: ','mhelpdesk');?><?php echo $tick_DB_ID_obj->ID;?><br />
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
                                <br />

                                <p>
                                 <a href="?action=edit&id=<?php echo $tick_DB_ID_obj->ID; ?>"><?php _e('View & Edit','mhelpdesk');?></a> 
                                </p>
                            </td>
							<td class="tick_gen_cols">
																<?php $tickSelectedAgentID = get_metadata('post',$tick_DB_ID_obj->ID, 'ticket-selectedAgent',true);
	$userAgent_DB_Obj = $wpdb->get_results("SELECT display_name FROM $wpdb->users WHERE ID = '$tickSelectedAgentID'");
								echo (isset($userAgent_DB_Obj[0]) ? esc_html($userAgent_DB_Obj[0]->display_name) : 'No Assigned'); 
								?>

                            </td>
                            <td class="tick_gen_cols">
								<?php 
	echo get_post_meta($tick_DB_ID_obj->ID, 'ticket-status',true).' &<br />'.get_post_meta($tick_DB_ID_obj->ID, 'ticket-action-status',true); 
								?>
                            </td>
                            <td class="tick_gen_cols"><?php echo $tick_DB_ID_obj->post_modified; ?></td>
                        </tr>
            <?php  
					
                } // foreach($agent_authCheck_obj as $tick_DB_ID_obj) 
											
				 } // if(isset($customerTick_Objs[0]))
				 else {?>
					
				<tr>
					<td colspan="5" class="tick_gen_cols"><?php echo '<h1 class="error">'.__('The Company has no ticket at the moment!','mhelpdesk').'</h1>'; ?></td>
				</tr>
				<?php } ?>
				<tr>
					<td colspan="5" class="tick_gen_cols"><?php if($pages > 1){?>
              
                Pages: <a>&laquo;</a></li><?php
                    for($index = 1; $index <= $pages; $index++){?>
                        
                            <a href="?action=<?php echo $url_action_value.'&id='.$agent_id.'&pagenumber='.$index; ?>" ><?php echo esc_html($index) ?></a>
    <?php			} // for($index = 1; $index <= $pages; $index++) ?>
                <a>&raquo;</a>
              
            
    <?php } // if($pages > 1) ?></td>
				</tr>
                     </table>                
                </td>                
            </tr>
        </table>
   
<?php	
	} // if(!$_GET['action'])








$tickAuthObj = $wpdb->get_results("SELECT post_author FROM $wpdb->posts ticketposts, $wpdb->postmeta companymeta, $wpdb->postmeta customermeta
WHERE ticketposts.ID = companymeta.post_id
AND ticketposts.ID = customermeta.post_id
AND ticketposts.ID = $ticket_id
AND (companymeta.meta_key = 'ticket-selectedCompany' AND companymeta.meta_value = '$comp_DB_ID')
AND (customermeta.meta_key = 'ticket-authorityID' AND customermeta.meta_value = '$curUserID')
AND ticketposts.post_status = 'publish'");


///////////////////////////
// View & Edit Block
//////////////////////////
if($url_action_value == 'edit'){
	if(isset($tickAuthObj[0])){

	$post_author_id = esc_html($tickAuthObj[0]->post_author);
	//print_r($post_auth_id);
	$ticketAuthorDisplayname_DB_Obj = $wpdb->get_results("SELECT display_name, user_email FROM $wpdb->users WHERE ID = '$post_author_id'");?>
<style>
span.fillstar {
    position: relative;
}
</style>
<form name="ticketEditForm" method="post" action="#" enctype="multipart/form-data">
    <table class="ticketswrap">
        <tr><td colspan="2"><h2><?php echo get_the_title($comp_DB_ID); ?></h2></td></tr>
        <tr>
            <td >
                    <?php showCustomer_leftmenu(); $ticketStatus = get_metadata('post',$ticket_id,'ticket-status',true);?>
            </td>
            <td >
                <table>
                    <tr>
                        <th class="tick_gen_cols"><?php _e('Ticket Title','mhelpdesk');?></th>
                        <td class="tick_gen_cols"><?php echo get_the_title($ticket_id); $actionStatus = get_metadata('post',$ticket_id,'ticket-action-status',true); ?>
                        	<div align="right"><strong><?php _e('Status:','mhelpdesk');?></strong> <?php echo $ticketStatus.'/ '.$actionStatus; ?></div>
                        </td>
                    </tr>
                    <tr>
                        <th class="tick_gen_cols"><?php _e('Ticket Group','mhelpdesk');?></th>
                        <td class="tick_gen_cols"><?php echo get_metadata('post',$ticket_id,'ticket-selected_department',true);?></td>
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
                        <th colspan="2"><h3 class="tick_gen_cols"><?php _e('Customer/ Agent Conversation','mhelpdesk');?></h3></th>
                    </tr>

                    <tr>
                        <td colspan="2">
                        	<?php echo showConversation($ticket_id); ?>
                        </td>
                    </tr>
         <?php if($ticketStatus != 'Closed'){ ?>
                    <tr>
                        <th colspan="2"><h3 class="tick_gen_cols"><?php _e('Update the Ticket','mhelpdesk');?></h3></th>
                    </tr>            
					<tr>
                        <th><?php _e('Customer Query','mhelpdesk');?></th>
                        <td><textarea name="adminResponse" class="tick_textarea" rows="10"></textarea>
                        	<p><label>
                                  <input type="checkbox" name="ticketActionStatus" class="minimal">
                                </label>
                                <label>
                                  Mark as Solved
                                </label>    
                            </p>
                        </td>
                    </tr>
                   
                    <tr>
                        <th class="tick_gen_cols"><?php _e('Supported Attachment (if any)','mhelpdesk');?></th>
                        <td><input type="file" name="supportedDoc" id="supportedDoc" accept=".zip, .rar" 
                        title="<?php _e('Upload Supported Attachment (if any) as zip or rar format','mhelpdesk');?>" />
                        <p id="displaysize"></p>
                        </td>
                    </tr>
        <?php } //if($ticketStatus != 'Closed') ?>
		<?php if($actionStatus == 'Solved' || $ticketStatus == 'Closed'){ 
					$rating = get_metadata('post',$ticket_id,"ticket-rating",true); ?>
                            <tr>
                                <td colspan="2" class="tick_gen_cols"><strong><?php _e('Rating','mhelpdesk');?></strong><br />
                                <input type="radio" <?php echo ($rating == '1' ? 'checked' : ''); ?> name="rating" value="1" required /> 
                                <span class="fillstar">☆</span><br />
                                
                                <input type="radio"<?php echo ($rating == '2' ? 'checked' : ''); ?> name="rating" value="2" required /> 
                                <span class="fillstar">☆</span> <span class="fillstar">☆</span><br />
                                
                                <input type="radio" <?php echo ($rating == '3' ? 'checked' : ''); ?> name="rating" value="3" required /> 
                                <span class="fillstar">☆</span> <span class="fillstar">☆</span> <span class="fillstar">☆</span><br />
                                
                                <input type="radio" <?php echo ($rating == '4' ? 'checked' : ''); ?> name="rating" value="4" required /> 
                                <span class="fillstar">☆</span> <span class="fillstar">☆</span> <span class="fillstar">☆</span> <span class="fillstar">☆</span><br />
                                
                                <input type="radio" <?php echo ($rating == '5' ? 'checked' : ''); ?> name="rating" value="5" required /> 
                                <span class="fillstar">☆</span> <span class="fillstar">☆</span> <span class="fillstar">☆</span> <span class="fillstar">☆</span> 
                                <span class="fillstar">☆</span>
                                </td>
                            </tr>
        <?php } //if($actionStatus == 'Solved'  $ticketStatus == 'Closed') ?>
            
                </table>
            </td>                
        </tr>

        <tr>
        	<td colspan="2" >
            	<?php wp_nonce_field('adminUpdateActionBtn', 'ticket-adminUpdateActionBtn'); ?>
			    <input type="submit" name="adminUpdateActionBtn" value="Save Updation" />
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
			alert( 'Only zip or rar format allowed!' );
			jQuery("#supportedDoc").replaceWith( jQuery("#supportedDoc").clone( true ) );
			jQuery("#displaysize").html(" ");
			}
			else if(size_part > 2048 ){
				alert('Your Attachment should be less than 2-MB');
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
	else echo '<h1 class="error">'.__('Invalid Ticket Accessing','mhelpdesk').'?</h1>';
} // if($_GET['action'] == 'edit')










?>                
				</div>
            </div>
        </div>
    </div>