<?php
add_action( 'wp_ajax_mam_open_company_ticket_ajax', 'mam_open_company_ticket_ajaxCBF' );
add_action( 'wp_ajax_nopriv_mam_open_company_ticket_ajax', 'mam_open_company_ticket_ajaxCBF' );
function mam_open_company_ticket_ajaxCBF(){
	global $wpdb;
	if(isset($_GET['customer_company_slug']) && !empty($_GET['customer_company_slug']) && isset($_POST['customer_email']) && !empty($_POST['customer_email'])
	  && isset($_POST['open_company_ticket-registration']) && wp_verify_nonce( $_POST['open_company_ticket-registration'], 'open_company_ticket_ticketregistration' ) 
	  && isset($_POST['open_company_ticketSubmitBtn'])){
		$urlCompSlug = sanitize_text_field($_GET['customer_company_slug']);
		$customer_email = sanitize_email($_POST['customer_email']);
		
		$comp_DB_ID_obj = $wpdb->get_results("SELECT ID FROM $wpdb->posts WHERE post_type='companies' AND post_status='publish' AND post_name='$urlCompSlug'");

		
		if($this_user_id = email_exists($customer_email) ){

			$curUserID = $this_user_id;
			
			if(isset($comp_DB_ID_obj[0])){ // company exists/ alive till or not
		
				$comp_DB_ID = $comp_DB_ID_obj[0]->ID;
				
				do_action( 'before_ticket_created', $comp_DB_ID );
				
				global $global_ticket_limit_status; 
					
				$compCustomerBlocked_DB_arr = get_metadata('post', $comp_DB_ID, 'company-customers-blocked', true);
					
				$customerBlockStatus = 'false';
					
					if(isset($compCustomerBlocked_DB_arr[$curUserID]))
						$customerBlockStatus = 'true';
						
					if($customerBlockStatus == 'false'){ // Its mean this customer is not blocked i.e. OK

							if($global_ticket_limit_status == 'ok'){

								$current_userInfo = get_userdata( $curUserID );
								
								$ticketPostArgs = array(
														'post_type' => 'tickets',
														'post_author' => $curUserID,
														'post_title' => sanitize_text_field($_POST['mam_open_comp_ticket_title']),
														'post_status' => 'publish'
													);
								$ticket_postID = wp_insert_post( $ticketPostArgs );
								
								do_action( 'ticket_created', array('companyID'=>$comp_DB_ID,'company_name'=>$comp_name,'ticketID'=>$ticket_postID) );
				
								update_metadata('post',$ticket_postID,'ticket-selected_department',sanitize_text_field($_POST['mam_open_comp_ticket_selected_department']));
										
								update_metadata('post',$ticket_postID,'ticket-selectedCompany',$comp_DB_ID);
								update_metadata('post',$ticket_postID, 'ticket-authorityID', $curUserID);
								
								update_metadata('post',$ticket_postID,"ticket-status",'Opened');
								update_metadata('post',$ticket_postID,"ticket-action-status",'Un-Answered');
						
								$newComments = array(
									'comment_post_ID' 		=> $ticket_postID,
									'comment_author' 		=> $current_userInfo->display_name,
									'comment_author_email' 	=> $current_userInfo->user_email,
									'comment_content' 		=> sanitize_text_field($_POST['mam_open_comp_ticket_description']),
									'comment_type' 			=> 'comment',
									'comment_approved' 		=> 1,
								);
								
								$newCommentID = wp_insert_comment($newComments);
							
							} // if($global_ticket_limit_status == 'ok')
						
					} // if($customerBlockStatus == 'false')
					
			} // if(isset($comp_DB_ID_obj[0]))
			
		} else{ // if($this_user_id = email_exists($customer_email) )
			
			$new_created_user_id = register_new_user( $customer_email, $customer_email );

			$curUserID = $new_created_user_id;
			
			$current_userInfo = get_userdata( $curUserID );
				
				if(isset($comp_DB_ID_obj[0])){ // company exists/ alive till or not
			
					$comp_DB_ID = $comp_DB_ID_obj[0]->ID;
					
					do_action( 'before_ticket_created', $comp_DB_ID );
					do_action( 'before_customer_add', $comp_DB_ID );
					
					global $global_customer_limit_status, $global_ticket_limit_status; 
						
					$compCustomerBlocked_DB_arr = get_metadata('post', $comp_DB_ID, 'company-customers-blocked', true);
						
					$customerBlockStatus = 'false';
						
						if(isset($compCustomerBlocked_DB_arr[$curUserID]))
							$customerBlockStatus = 'true';
							
						if($customerBlockStatus == 'false'){ // Its mean this customer is not blocked i.e. OK
	
							$compCustomerOpened_DB_arr = get_metadata('post', $comp_DB_ID, 'company-customers-opened', true);
							if($compCustomerOpened_DB_arr){
								
								$customerID_foundIndex = array_search($curUserID, isset($compCustomerOpened_DB_arr[0]) ? $compCustomerOpened_DB_arr : array());
								
									if($customerID_foundIndex > -1)
									; // do nothing
									else{									
										
										if($global_customer_limit_status == 'ok'){
											$compCustomerOpened_DB_arr[] = $curUserID;
											rsort($compCustomerOpened_DB_arr);
											update_metadata('post', $comp_DB_ID, 'company-customers-opened', $compCustomerOpened_DB_arr);
											
										} // if($global_customer_limit_status == 'ok')
										
									} // ELSE of    if($customerID_foundIndex > -1)
				
							} // if($compCustomerOpened_DB_arr)
							else{
									$compCustomerOpened_DB_arr = array();
									$compCustomerOpened_DB_arr[] = $curUserID;
									update_metadata('post', $comp_DB_ID, 'company-customers-opened', $compCustomerOpened_DB_arr);
							} // // ELSE of    if(compCustomerOpened_DB_arr)
	
	
	
								if($global_ticket_limit_status == 'ok'){
									
									$ticketPostArgs = array(
															'post_type' => 'tickets',
															'post_author' => $curUserID,
															'post_title' => sanitize_text_field($_POST['mam_open_comp_ticket_title']),
															'post_status' => 'publish'
														);
									$ticket_postID = wp_insert_post( $ticketPostArgs );

									do_action( 'ticket_created', array('companyID'=>$comp_DB_ID,'company_name'=>$comp_name,'ticketID'=>$ticket_postID) );
					
									update_metadata('post',$ticket_postID,'ticket-selected_department',sanitize_text_field($_POST['mam_open_comp_ticket_selected_department']));

									update_metadata('post',$ticket_postID,'ticket-selectedCompany',$comp_DB_ID);
									update_metadata('post',$ticket_postID, 'ticket-authorityID', $curUserID);
									update_metadata('post',$ticket_postID,"ticket-status",'Opened');
									update_metadata('post',$ticket_postID,"ticket-action-status",'Un-Answered');

									$newComments = array(
										'comment_post_ID' 		=> $ticket_postID,
										'comment_author' 		=> $current_userInfo->display_name,
										'comment_author_email' 	=> $current_userInfo->user_email,
										'comment_content' 		=> sanitize_text_field($_POST['mam_open_comp_ticket_description']),
										'comment_type' 			=> 'comment',
										'comment_approved' 		=> 1,
									);
									
									$newCommentID = wp_insert_comment($newComments);
																	
								} // if($global_ticket_limit_status == 'ok')
							
						} // if($customerBlockStatus == 'false')
						
				} // if(isset($comp_DB_ID_obj[0]))			
		
		} // ELSE  of   if(email_exists($sender_email) && username_exists( $sender_login_name ))
	} // if(isset($_GET['customer_company_slug']) && !empty($_GET['customer_company_slug']) && isset($_POST['customer_email']) && !empty($_POST['customer_email']))
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	if(isset($_GET['customer_company_slug']) && !empty($_GET['customer_company_slug']) ){
		$urlCompSlug = sanitize_text_field($_GET['customer_company_slug']);
		
		
		$comp_DB_ID_obj = $wpdb->get_results("SELECT ID FROM $wpdb->posts WHERE post_type='companies' AND post_status='publish' AND post_name='$urlCompSlug'");
		if(isset($comp_DB_ID_obj[0])){ // company exists/ alive till or not
			$comp_DB_ID = $comp_DB_ID_obj[0]->ID;
			$company_departments = get_metadata('post', $comp_DB_ID, 'company-departments', true);
			$company_departments = $company_departments != '' ? explode(',',$company_departments) : array('Default Category');?>
	  
		<form name="ticketRegistration" method="post" action="<?php echo admin_url('admin-ajax.php').'?action=mam_open_company_ticket_ajax&customer_company_slug='.$urlCompSlug?>" enctype="multipart/form-data">
			<table>
				<tr>
					<th style="text-align:left;width:130px"><?php _e('Select Group','mhelpdesk');?></th>
					<td>    <select name="mam_open_comp_ticket_selected_department">
					<?php 		foreach($company_departments as $company_department){?>
                                <option value="<?php echo $company_department?>"><?php echo $company_department?></option>
                    <?php 		} //foreach($company_departments as $company_department){?>
                            </select>
					</td>
				</tr>
				<tr>
					<th style="text-align:left"><?php _e('Ticket Title','mhelpdesk');?></th>
					<td><input name="mam_open_comp_ticket_title" type="text" required="required" /></td>
				</tr>
                <tr>
					<th style="text-align:left;"><?php _e('Cutomer Email','mhelpdesk');?></th>
					<td><input name="customer_email" type="text" required="required" /></td>
				</tr>
				<tr>
					<th style="text-align:left; vertical-align:top"><?php _e('Enter Query','mhelpdesk');?></th>
					<td><textarea name="mam_open_comp_ticket_description" rows="10" cols="50" required="required" ></textarea></td>
				</tr>
                <tr><td colspan="2">
					<?php wp_nonce_field('open_company_ticket_ticketregistration', 'open_company_ticket-registration'); ?> <input type="submit" name="open_company_ticketSubmitBtn" value="Submit New Ticket/ Query" />
				</td></tr>
			</table>
		</form>
 

<?php
		} //if(isset($comp_DB_ID_obj[0])){
	} // if(isset($_GET['customer_company_slug']) && !empty($_GET['customer_company_slug'])
die();
} // function mam_open_company_ticket_ajaxCBF()

?>