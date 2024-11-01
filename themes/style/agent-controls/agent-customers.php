<?php
	$hd_admin_settings_arr = get_option('APF_MyFirstFrom');
		
	$helpdesk_rewriterule_slug = isset($hd_admin_settings_arr['m_helpdesk_form']['helpdesk_rewriterule_slug']) && $hd_admin_settings_arr['m_helpdesk_form']['helpdesk_rewriterule_slug'] ? $hd_admin_settings_arr['m_helpdesk_form']['helpdesk_rewriterule_slug'] : 'company';
?>

<div class="top_hd_menu">
    	 <a href="<?php echo site_url().'/'.$helpdesk_rewriterule_slug?>">Helpdesk Home</a>  <a href="<?php echo get_permalink($comp_DB_ID)?>">Create Helpdesk Ticket</a> 
    </div>
	<div role="tabpanel"><?php
        showAgent_tabmenu();
		?>
          <div class="tab-content">
            <div role="tabpanel" class="tab-pane active" id="customers">
                <div id="customersContent" class="tabed_content"><?php
				
				

		
		
		$compCustomerBlocked_DB_arr = get_metadata('post', $comp_DB_ID, 'company-customers-blocked', true);
		
		$compCustomerOpened_DB_arr = get_metadata('post', $comp_DB_ID, 'company-customers-opened', true);

			//print_r($compCustomerBlocked_DB_arr); 
			
	if(!isset($_GET['action'])){
		
$agent_customersTick_objs = $wpdb->get_results("SELECT ID, post_author FROM $wpdb->posts ticketposts, $wpdb->postmeta companymeta, $wpdb->postmeta agentmeta
WHERE ticketposts.ID = companymeta.post_id
AND ticketposts.ID = agentmeta.post_id
AND (companymeta.meta_key = 'ticket-selectedCompany' AND companymeta.meta_value = '$comp_DB_ID')
AND (agentmeta.meta_key = 'ticket-selectedAgent' AND agentmeta.meta_value = '$curUserID')
AND ticketposts.post_type = 'tickets'
AND ticketposts.post_status = 'publish' ORDER BY ticketposts.post_modified DESC");
		
		if(isset($agent_customersTick_objs[0])){
		
		foreach($agent_customersTick_objs as $tick_DB_ID_obj)
			$customers_of_AgentArr[$tick_DB_ID_obj->post_author] = $tick_DB_ID_obj->post_author;?>
			<table>
            	<tr><th  >Customer Name</th><th >Account Status</th><th>Remarks</th></tr>
                <?php foreach($customers_of_AgentArr as $customerID){
							$customer_displaynameObj = $wpdb->get_results("SELECT display_name FROM $wpdb->users WHERE ID = '$customerID'");?>
                <tr>
                	<td>
						<?php echo esc_html($customer_displaynameObj[0]->display_name); ?>
                        <br /><br />
                                <p>
                                 <a href="?action=customer_tickets&id=<?php echo esc_html($customerID) ?>">View Tickets</a>
                                 <a href="?action=edit&id=<?php echo esc_html($customerID) ?>">Edit</a> 
                                </p>
                    </td>
                    	
                    <td><?php echo ( isset($compCustomerBlocked_DB_arr[$customerID]) ? 'Blocked' : 'Opened' ); ?></td>
                    <td ><?php echo ( isset($compCustomerBlocked_DB_arr[$customerID]) ? ($compCustomerBlocked_DB_arr[$customerID]) : 'OK'); ?></td>
                </tr>
                <?php } //foreach($customers_of_AgentArr as $customerID) ?>
            </table>
<?php
		} // if($agent_customersTick_objs)
		else echo '<h1 >This '.$urlCompSlug.' Company has no CUSTOMER<br />at the moment!</h1>';
	} // if(!$_GET['action'])
	
	
	
	
	
	
	
	
	
$customer_id_GET = isset($_GET['id']) ? sanitize_text_field($_GET['id']) : false;
	
//////////////////////////////
// View Customers Tickets
//////////////////////////////
if(isset($_GET['action']) && $_GET['action'] == 'customer_tickets'){ 


$relevantCID_foundIndex = array_search($customer_id_GET, isset($compCustomerOpened_DB_arr[0]) ? $compCustomerOpened_DB_arr : array());

	if($relevantCID_foundIndex > -1 ){
		
$recordShow = ($ticketsperpage * $viewedPageNum) - $ticketsperpage;

$queryString_allTick = "SELECT ID FROM $wpdb->posts ticketposts, $wpdb->postmeta companymeta, $wpdb->postmeta agentmeta WHERE ticketposts.ID = companymeta.post_id
AND ticketposts.ID = agentmeta.post_id AND ticketposts.post_author = '$customer_id_GET' AND (companymeta.meta_key = 'ticket-selectedCompany' AND companymeta.meta_value = '$comp_DB_ID') AND (agentmeta.meta_key = 'ticket-selectedAgent' AND agentmeta.meta_value = '$curUserID')  AND ticketposts.post_type = 'tickets'  AND ticketposts.post_status = 'publish'";


$customerTickets_DB_objs = $wpdb->get_results("SELECT ID, post_title, post_modified FROM $wpdb->posts ticketposts, $wpdb->postmeta companymeta, $wpdb->postmeta agentmeta
WHERE ticketposts.ID = companymeta.post_id
AND ticketposts.ID = agentmeta.post_id
AND ticketposts.post_author = '$customer_id_GET'
AND (companymeta.meta_key = 'ticket-selectedCompany' AND companymeta.meta_value = '$comp_DB_ID')
AND (agentmeta.meta_key = 'ticket-selectedAgent' AND agentmeta.meta_value = '$curUserID')
AND ticketposts.post_type = 'tickets'
AND ticketposts.post_status = 'publish' ORDER BY ticketposts.post_modified DESC LIMIT $recordShow, $ticketsperpage");

//print_r($compCustomerBlocked_DB_arr);  
	$customer_displaynameObj = $wpdb->get_results("SELECT display_name FROM $wpdb->users WHERE ID = '$customer_id_GET'");
	
	if(!isset($compCustomerBlocked_DB_arr[$customer_id_GET])){
			if(isset($customerTickets_DB_objs[0])){?> <a href="<?php echo get_permalink($comp_DB_ID)."agent/customers";?>" >Go Back to Customer's Home Link</a>
		<br /><br /><?php echo insert_pagination($queryString_allTick,$ticketsperpage);?>
		<table>
        	<tr><td colspan="3" class="tick_gen_cols"><h2><?php echo esc_html($customer_displaynameObj[0]->display_name); ?></h2></td></tr>
            <tr>
            	<th class="tick_gen_cols">Ticket Title</th>
                <th class="tick_gen_cols">Ticket Status</th>
                <th class="tick_gen_cols">Updated Date</th>
            </tr>
    	<?php 	foreach($customerTickets_DB_objs as $customerTickets_DB_obj){?>
            <tr>
            	<td class="tick_gen_cols"><?php echo $customerTickets_DB_obj->post_title; ?>
<br />ID: <?php echo $customerTickets_DB_obj->ID;?>
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
                <td class="tick_gen_cols">
					<?php echo get_metadata('post', $customerTickets_DB_obj->ID, 'ticket-status', true).' &<br />'.
							   get_metadata('post', $customerTickets_DB_obj->ID, 'ticket-action-status', true);	?>
                </td>
                <td class="tick_gen_cols"><?php echo $customerTickets_DB_obj->post_modified; ?></td>
            </tr>
    <?php 		} //foreach($customerTickets_DB_objs as $customerTickets_DB_obj)?>
		    <tr><td colspan="3"><br /><?php echo insert_pagination($queryString_allTick,$ticketsperpage);?></td></tr>
        </table> <a href="<?php echo get_permalink($comp_DB_ID)."agent/customers";?>" >Go Back to Customer's Home Link<br /><br /></a>
<?php  } // if(isset($customerTickets_DB_objs[0]))
		 else echo '<h1 >This '.$urlCompSlug.' Company has no ticket at the moment for ('.$customer_displaynameObj[0]->display_name.') customer!</h1>'; 
		 	
	} // if(!isset($compCustomerBlocked_DB_arr[$customer_id_GET]))
		else echo '<h1 ><br /><br />This Customer ('.esc_html($customer_displaynameObj[0]->display_name).') is BLOCKED.</h1>';
		
	} //if($relevantCID_foundIndex > -1 )
	else echo '<h1 >Irrelevant Request/ Index ('.$customer_id_GET.') passed.</h1>';

 
} // if($_GET['action'] == 'customer_tickets')









/////////////////////////////
//     Edit Customers
/////////////////////////////
if(isset($_GET['action']) && $_GET['action'] == 'edit'){ 

$relevantCID_foundIndex = array_search($customer_id_GET, isset($compCustomerOpened_DB_arr[0]) ? $compCustomerOpened_DB_arr : array());

	if($relevantCID_foundIndex > -1 ){
		$customer_displaynameObj = $wpdb->get_results("SELECT display_name FROM $wpdb->users WHERE ID = '$customer_id_GET'");?>	
<form method="post" action="#">
<a href="<?php echo $_SERVER['HTTP_REFERER'];?>" >Go Back to Customer's Home Link</a>
        <table>
            <tr><th>Customer Name</th><th>Account Status</th><!--th>Delete Tickets</th--></tr>
            <tr>
                <td><?php echo esc_html($customer_displaynameObj[0]->display_name); ?></td>
                <td>
		<?php if($global_customer_block_permission == 'true'){ ?>
                    <select name="accessStatus" id="accessStatus">
                        <option value="Opened">Opened</option>
                        <option value="Blocked" <?php echo isset($compCustomerBlocked_DB_arr[$customer_id_GET]) ? 'selected' : ''; ?> >Blocked</option>
                    </select>
		<?php } //if($global_customer_block_permission == 'true'){ 
				else
					echo '&nbsp;';?>
                </td>
                <!--td><input name="deleteAuthorTickets" type="checkbox" value="yes" /></td-->
            </tr>
            <tr id="blockedTextRow" ><td colspan="3">
            	<textarea name="blockedText" id="blockedText" required="required" 
                placeholder="Why Blocking? Also mentioned the Ticket Title; the basis you are bocking the customer"><?php 
					echo isset($compCustomerBlocked_DB_arr[$customer_id_GET]) ? $compCustomerBlocked_DB_arr[$customer_id_GET] : ''; ?></textarea>
            </td></tr>
        </table>
        
	<?php wp_nonce_field('customerUpdateBtn', 'company-customerUpdateBtn'); ?>
    <input type="submit" name="customerUpdateBtn" value="Save Updation" />
    <br /><br /><a href="<?php echo $_SERVER['HTTP_REFERER'];?>" >Go Back to Customer's Home Link</a>
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
	} //if($relevantCID_foundIndex > -1 )
	else echo '<h1 >Irrelevant Request/ Index ('.$customer_id_GET.') passed.</h1>';


} // if($_GET['action'] == 'edit')

	
 ?>                
				</div>
            </div>
        </div>
    </div>