<?php
	function global_logged_in_user_is_admin(){
		
		$hd_admin_settings_arr = get_option('APF_MyFirstFrom');

		global $current_user,$global_logged_in_user_is_admin,$message;
		$message = '';
		$logged_in_uid = $current_user->ID;

		$global_logged_in_user_is_admin = true;

		$user = get_userdata( $logged_in_uid );
		$is_super_admin_user = $user->roles;
		
		if(!in_array( 'administrator', $is_super_admin_user) && isset($hd_admin_settings_arr['m_helpdesk_form']['helpdesk_administration']) 
		   && $hd_admin_settings_arr['m_helpdesk_form']['helpdesk_administration'] == true){
			 $message.='<div class="alert alert-danger bg-danger error"><strong>Error! </strong> Only Administrator can create Helpdesk or Company. Please contact to your administrator in this regard.</div>';
			$global_logged_in_user_is_admin = false;

		}
	return $global_logged_in_user_is_admin;
	} // function global_logged_in_user_is_admin()

	function show_company_admin_tickets(){ 
		global $wpdb;  // UPDATE wp_posts SET post_author = 'new-author-id' WHERE post_author = 'old-author-id';
		$urlCompSlug = get_query_var('companyname'); 

		$comp_DB_ID_obj = $wpdb->get_results("SELECT ID FROM $wpdb->posts WHERE post_type = 'companies' AND post_status = 'publish' AND post_name = '$urlCompSlug'");

		$comp_DB_ID = $comp_DB_ID_obj[0]->ID;


		$all_tickets = $wpdb->get_results("SELECT ID, post_title, post_author, post_modified FROM $wpdb->posts ticketposts, $wpdb->postmeta companymeta
		WHERE ticketposts.ID = companymeta.post_id
		AND (companymeta.meta_key = 'ticket-selectedCompany' AND companymeta.meta_value = '$comp_DB_ID')
		AND ticketposts.post_type = 'tickets'
		AND ticketposts.post_status = 'publish' ORDER BY ticketposts.post_modified DESC");




		$new_tickets = $wpdb->get_results("SELECT ID, post_title, post_author, post_modified FROM $wpdb->posts ticketposts, $wpdb->postmeta companymeta
		WHERE ticketposts.ID = companymeta.post_id
		AND (companymeta.meta_key = 'ticket-selectedCompany' AND companymeta.meta_value = '$comp_DB_ID')
		AND NOT EXISTS (
					  SELECT post_id FROM $wpdb->postmeta
					   WHERE $wpdb->postmeta.post_id=ticketposts.ID
						AND $wpdb->postmeta.meta_key = 'ticket-selectedAgent'
					) 
		AND ticketposts.post_type = 'tickets'
		AND ticketposts.post_status = 'publish' ORDER BY ticketposts.post_modified DESC");



		$rated_tickets = $wpdb->get_results("SELECT ID, post_title, post_author, post_modified FROM $wpdb->posts ticketposts, $wpdb->postmeta companymeta
				WHERE ticketposts.ID = companymeta.post_id
				AND (companymeta.meta_key = 'ticket-selectedCompany' AND companymeta.meta_value = '$comp_DB_ID')
				AND EXISTS (
							  SELECT post_id FROM $wpdb->postmeta
							  WHERE $wpdb->postmeta.meta_key = 'ticket-rating' AND $wpdb->postmeta.meta_value > 0
							  AND ticketposts.ID = $wpdb->postmeta.post_id
							)
		AND ticketposts.post_type = 'tickets'
		AND ticketposts.post_status = 'publish' ORDER BY ticketposts.post_modified DESC");


		$opened_tickets = $wpdb->get_results("SELECT ID, post_title, post_author, post_modified FROM $wpdb->posts ticketposts, $wpdb->postmeta companymeta, $wpdb->postmeta ticketStatusmeta
		WHERE ticketposts.ID = companymeta.post_id
		AND ticketposts.ID = ticketStatusmeta.post_id
		AND (companymeta.meta_key = 'ticket-selectedCompany' AND companymeta.meta_value = '$comp_DB_ID')
		AND (ticketStatusmeta.meta_key = 'ticket-status' AND ticketStatusmeta.meta_value = 'Opened')
		AND ticketposts.post_type = 'tickets'
		AND ticketposts.post_status = 'publish' ORDER BY ticketposts.post_modified DESC");

		$unanswered_tickets = $wpdb->get_results("SELECT ID, post_title, post_author, post_modified FROM $wpdb->posts ticketposts, $wpdb->postmeta companymeta, 
		$wpdb->postmeta ticketStatusmeta
		WHERE ticketposts.ID = companymeta.post_id
		AND ticketposts.ID = ticketStatusmeta.post_id
		AND (companymeta.meta_key = 'ticket-selectedCompany' AND companymeta.meta_value = '$comp_DB_ID')
		AND (ticketStatusmeta.meta_key = 'ticket-action-status' AND ticketStatusmeta.meta_value = 'Un-Answered')
		AND ticketposts.post_type = 'tickets'
		AND ticketposts.post_status = 'publish' ORDER BY ticketposts.post_modified DESC");


		$answered_tickets = $wpdb->get_results("SELECT ID, post_title, post_author, post_modified FROM $wpdb->posts ticketposts, $wpdb->postmeta companymeta, 
		$wpdb->postmeta ticketStatusmeta
		WHERE ticketposts.ID = companymeta.post_id
		AND ticketposts.ID = ticketStatusmeta.post_id
		AND (companymeta.meta_key = 'ticket-selectedCompany' AND companymeta.meta_value = '$comp_DB_ID')
		AND (ticketStatusmeta.meta_key = 'ticket-action-status' AND ticketStatusmeta.meta_value = 'Answered')
		AND ticketposts.post_type = 'tickets'
		AND ticketposts.post_status = 'publish' ORDER BY ticketposts.post_modified DESC");

		$solved_tickets = $wpdb->get_results("SELECT ID, post_title, post_author, post_modified FROM $wpdb->posts ticketposts, $wpdb->postmeta companymeta, $wpdb->postmeta ticketStatusmeta
		WHERE ticketposts.ID = companymeta.post_id
		AND ticketposts.ID = ticketStatusmeta.post_id
		AND (companymeta.meta_key = 'ticket-selectedCompany' AND companymeta.meta_value = '$comp_DB_ID')
		AND (ticketStatusmeta.meta_key = 'ticket-action-status' AND ticketStatusmeta.meta_value = 'Solved')
		AND ticketposts.post_type = 'tickets'
		AND ticketposts.post_status = 'publish' ORDER BY ticketposts.post_modified DESC");


		$closed_tickets = $wpdb->get_results("SELECT ID, post_title, post_author, post_modified FROM $wpdb->posts ticketposts, $wpdb->postmeta companymeta, $wpdb->postmeta ticketStatusmeta
		WHERE ticketposts.ID = companymeta.post_id
		AND ticketposts.ID = ticketStatusmeta.post_id
		AND (companymeta.meta_key = 'ticket-selectedCompany' AND companymeta.meta_value = '$comp_DB_ID')
		AND (ticketStatusmeta.meta_key = 'ticket-status' AND ticketStatusmeta.meta_value = 'Closed')
		AND ticketposts.post_type = 'tickets'
		AND ticketposts.post_status = 'publish' ORDER BY ticketposts.post_modified DESC");

		$company_tickets = array('all_tickets'=>$all_tickets,'new_tickets'=>$new_tickets,'rated_tickets'=>$rated_tickets,'opened_tickets'=>$opened_tickets,
								'unanswered_tickets'=>$unanswered_tickets,'answered_tickets'=>$answered_tickets,'solved_tickets'=>$solved_tickets,'closed_tickets'=>$closed_tickets);

		return $company_tickets;
	} // function show_company_admin_tickets()








	function show_company_agent_tickets(){ 
		global $wpdb, $current_user;  // UPDATE wp_posts SET post_author = 'new-author-id' WHERE post_author = 'old-author-id';
		$urlCompSlug = get_query_var('companyname'); 

		$comp_DB_ID_obj = $wpdb->get_results("SELECT ID FROM $wpdb->posts WHERE post_type = 'companies' AND post_status = 'publish' AND post_name = '$urlCompSlug'");

		$comp_DB_ID = isset($comp_DB_ID_obj[0]) ? $comp_DB_ID_obj[0]->ID : 0;

		$agent_id = $current_user->ID;

		$all_tickets = $wpdb->get_results("SELECT ID, post_title, post_author, post_modified FROM $wpdb->posts ticketposts, $wpdb->postmeta companymeta, $wpdb->postmeta agentmeta
		WHERE ticketposts.ID = companymeta.post_id
		AND ticketposts.ID = agentmeta.post_id
		AND (companymeta.meta_key = 'ticket-selectedCompany' AND companymeta.meta_value = '$comp_DB_ID')
		AND (agentmeta.meta_key = 'ticket-selectedAgent' AND agentmeta.meta_value = '$agent_id')
		AND ticketposts.post_type = 'tickets'
		AND ticketposts.post_status = 'publish' ORDER BY ticketposts.post_modified DESC");




		$new_tickets = $wpdb->get_results("SELECT ID, post_title, post_author, post_modified FROM $wpdb->posts ticketposts, $wpdb->postmeta companymeta
		WHERE ticketposts.ID = companymeta.post_id
		AND (companymeta.meta_key = 'ticket-selectedCompany' AND companymeta.meta_value = '$comp_DB_ID')
		AND NOT EXISTS (
					  SELECT post_id FROM $wpdb->postmeta
					   WHERE $wpdb->postmeta.post_id=ticketposts.ID
						AND $wpdb->postmeta.meta_key = 'ticket-selectedAgent'
					) 
		AND ticketposts.post_type = 'tickets'
		AND ticketposts.post_status = 'publish' ORDER BY ticketposts.post_modified DESC");



		$rated_tickets = $wpdb->get_results("SELECT ID, post_title, post_author, post_modified FROM $wpdb->posts ticketposts, $wpdb->postmeta companymeta, $wpdb->postmeta agentmeta
				WHERE ticketposts.ID = companymeta.post_id AND ticketposts.ID = agentmeta.post_id
				AND (companymeta.meta_key = 'ticket-selectedCompany' AND companymeta.meta_value = '$comp_DB_ID')
				AND (agentmeta.meta_key = 'ticket-selectedAgent' AND agentmeta.meta_value = '$agent_id')
				AND EXISTS (
							  SELECT post_id FROM $wpdb->postmeta
							  WHERE $wpdb->postmeta.meta_key = 'ticket-rating' AND $wpdb->postmeta.meta_value > 0
							  AND ticketposts.ID = $wpdb->postmeta.post_id
							)
		AND ticketposts.post_type = 'tickets'
		AND ticketposts.post_status = 'publish' ORDER BY ticketposts.post_modified DESC");


		$opened_tickets = $wpdb->get_results("SELECT ID, post_title, post_author, post_modified FROM $wpdb->posts ticketposts, $wpdb->postmeta companymeta, 
		$wpdb->postmeta ticketStatusmeta, $wpdb->postmeta agentmeta
		WHERE ticketposts.ID = companymeta.post_id AND ticketposts.ID = ticketStatusmeta.post_id AND ticketposts.ID = agentmeta.post_id
		AND (agentmeta.meta_key = 'ticket-selectedAgent' AND agentmeta.meta_value = '$agent_id')
		AND (companymeta.meta_key = 'ticket-selectedCompany' AND companymeta.meta_value = '$comp_DB_ID')
		AND (ticketStatusmeta.meta_key = 'ticket-status' AND ticketStatusmeta.meta_value = 'Opened')
		AND ticketposts.post_type = 'tickets'
		AND ticketposts.post_status = 'publish' ORDER BY ticketposts.post_modified DESC");

		$unanswered_tickets = $wpdb->get_results("SELECT ID, post_title, post_author, post_modified FROM $wpdb->posts ticketposts, $wpdb->postmeta companymeta, 
		$wpdb->postmeta ticketStatusmeta, $wpdb->postmeta agentmeta
		WHERE ticketposts.ID = companymeta.post_id AND ticketposts.ID = ticketStatusmeta.post_id AND ticketposts.ID = agentmeta.post_id
		AND (agentmeta.meta_key = 'ticket-selectedAgent' AND agentmeta.meta_value = '$agent_id')
		AND (companymeta.meta_key = 'ticket-selectedCompany' AND companymeta.meta_value = '$comp_DB_ID')
		AND (ticketStatusmeta.meta_key = 'ticket-action-status' AND ticketStatusmeta.meta_value = 'Un-Answered')
		AND ticketposts.post_type = 'tickets'
		AND ticketposts.post_status = 'publish' ORDER BY ticketposts.post_modified DESC");


		$answered_tickets = $wpdb->get_results("SELECT ID, post_title, post_author, post_modified FROM $wpdb->posts ticketposts, $wpdb->postmeta companymeta, 
		$wpdb->postmeta ticketStatusmeta, $wpdb->postmeta agentmeta
		WHERE ticketposts.ID = companymeta.post_id AND ticketposts.ID = ticketStatusmeta.post_id AND ticketposts.ID = agentmeta.post_id
		AND (agentmeta.meta_key = 'ticket-selectedAgent' AND agentmeta.meta_value = '$agent_id')
		AND (companymeta.meta_key = 'ticket-selectedCompany' AND companymeta.meta_value = '$comp_DB_ID')
		AND (ticketStatusmeta.meta_key = 'ticket-action-status' AND ticketStatusmeta.meta_value = 'Answered')
		AND ticketposts.post_type = 'tickets'
		AND ticketposts.post_status = 'publish' ORDER BY ticketposts.post_modified DESC");

		$solved_tickets = $wpdb->get_results("SELECT ID, post_title, post_author, post_modified FROM $wpdb->posts ticketposts, $wpdb->postmeta companymeta, 
		$wpdb->postmeta ticketStatusmeta, $wpdb->postmeta agentmeta
		WHERE ticketposts.ID = companymeta.post_id AND ticketposts.ID = ticketStatusmeta.post_id AND ticketposts.ID = agentmeta.post_id
		AND (agentmeta.meta_key = 'ticket-selectedAgent' AND agentmeta.meta_value = '$agent_id')
		AND (companymeta.meta_key = 'ticket-selectedCompany' AND companymeta.meta_value = '$comp_DB_ID')
		AND (ticketStatusmeta.meta_key = 'ticket-action-status' AND ticketStatusmeta.meta_value = 'Solved')
		AND ticketposts.post_type = 'tickets'
		AND ticketposts.post_status = 'publish' ORDER BY ticketposts.post_modified DESC");


		$closed_tickets = $wpdb->get_results("SELECT ID, post_title, post_author, post_modified FROM $wpdb->posts ticketposts, $wpdb->postmeta companymeta,
		$wpdb->postmeta ticketStatusmeta, $wpdb->postmeta agentmeta
		WHERE ticketposts.ID = companymeta.post_id AND ticketposts.ID = ticketStatusmeta.post_id AND ticketposts.ID = agentmeta.post_id
		AND (agentmeta.meta_key = 'ticket-selectedAgent' AND agentmeta.meta_value = '$agent_id')
		AND (companymeta.meta_key = 'ticket-selectedCompany' AND companymeta.meta_value = '$comp_DB_ID')
		AND (ticketStatusmeta.meta_key = 'ticket-status' AND ticketStatusmeta.meta_value = 'Closed')
		AND ticketposts.post_type = 'tickets'
		AND ticketposts.post_status = 'publish' ORDER BY ticketposts.post_modified DESC");

		$company_tickets = array('all_tickets'=>$all_tickets,'new_tickets'=>$new_tickets,'rated_tickets'=>$rated_tickets,'opened_tickets'=>$opened_tickets,
								'unanswered_tickets'=>$unanswered_tickets,'answered_tickets'=>$answered_tickets,'solved_tickets'=>$solved_tickets,'closed_tickets'=>$closed_tickets);

		return $company_tickets;
	} // function show_company_agent_tickets()








	function show_company_customer_tickets(){ 
	global $wpdb;
	$curUserID = get_current_user_id();


	$urlCompSlug = get_query_var('companyname'); 

	$comp_DB_ID_obj = $wpdb->get_results("SELECT ID FROM $wpdb->posts WHERE post_type = 'companies' AND post_status = 'publish' AND post_name = '$urlCompSlug'");

	$comp_DB_ID = $comp_DB_ID_obj[0]->ID;


	$all_tickets = $wpdb->get_results("SELECT ID FROM $wpdb->posts ticketposts, $wpdb->postmeta companymeta, $wpdb->postmeta customermeta
	WHERE ticketposts.ID = companymeta.post_id
	AND ticketposts.ID = customermeta.post_id
	AND (companymeta.meta_key = 'ticket-selectedCompany' AND companymeta.meta_value = '$comp_DB_ID')
	AND (customermeta.meta_key = 'ticket-authorityID' AND customermeta.meta_value = '$curUserID')
	AND ticketposts.post_type = 'tickets'
	AND ticketposts.post_status = 'publish' ORDER BY ticketposts.post_modified DESC");


	$rated_tickets = $wpdb->get_results("SELECT ID, post_title, post_author, post_modified FROM $wpdb->posts ticketposts, $wpdb->postmeta companymeta, $wpdb->postmeta customermeta
			WHERE ticketposts.ID = companymeta.post_id AND ticketposts.ID = customermeta.post_id
			AND (companymeta.meta_key = 'ticket-selectedCompany' AND companymeta.meta_value = '$comp_DB_ID')
			AND (customermeta.meta_key = 'ticket-authorityID' AND customermeta.meta_value = '$curUserID')
			AND EXISTS (
						  SELECT post_id FROM $wpdb->postmeta
						  WHERE $wpdb->postmeta.meta_key = 'ticket-rating' AND $wpdb->postmeta.meta_value > 0
						  AND ticketposts.ID = $wpdb->postmeta.post_id
						)
	AND ticketposts.post_type = 'tickets'
	AND ticketposts.post_status = 'publish' ORDER BY ticketposts.post_modified DESC");


	$opened_tickets = $wpdb->get_results("SELECT ID FROM $wpdb->posts ticketposts, $wpdb->postmeta companymeta, $wpdb->postmeta customermeta, $wpdb->postmeta ticketStatusmeta
	WHERE ticketposts.ID = companymeta.post_id
	AND ticketposts.ID = ticketStatusmeta.post_id
	AND ticketposts.ID = customermeta.post_id
	AND (companymeta.meta_key = 'ticket-selectedCompany' AND companymeta.meta_value = '$comp_DB_ID')
	AND (customermeta.meta_key = 'ticket-authorityID' AND customermeta.meta_value = '$curUserID')
	AND (ticketStatusmeta.meta_key = 'ticket-status' AND ticketStatusmeta.meta_value = 'Opened')
	AND ticketposts.post_type = 'tickets'
	AND ticketposts.post_status = 'publish' ORDER BY ticketposts.post_modified DESC");


	$closed_tickets = $wpdb->get_results("SELECT ID FROM $wpdb->posts ticketposts, $wpdb->postmeta companymeta, $wpdb->postmeta customermeta, $wpdb->postmeta ticketStatusmeta
	WHERE ticketposts.ID = companymeta.post_id
	AND ticketposts.ID = ticketStatusmeta.post_id
	AND ticketposts.ID = customermeta.post_id
	AND (companymeta.meta_key = 'ticket-selectedCompany' AND companymeta.meta_value = '$comp_DB_ID')
	AND (customermeta.meta_key = 'ticket-authorityID' AND customermeta.meta_value = '$curUserID')
	AND (ticketStatusmeta.meta_key = 'ticket-status' AND ticketStatusmeta.meta_value = 'Closed')
	AND ticketposts.post_type = 'tickets'
	AND ticketposts.post_status = 'publish' ORDER BY ticketposts.post_modified DESC");


	$unanswered_tickets = $wpdb->get_results("SELECT ID FROM $wpdb->posts ticketposts, $wpdb->postmeta companymeta, $wpdb->postmeta customermeta, $wpdb->postmeta ticketStatusmeta
	WHERE ticketposts.ID = companymeta.post_id
	AND ticketposts.ID = ticketStatusmeta.post_id
	AND ticketposts.ID = customermeta.post_id
	AND (companymeta.meta_key = 'ticket-selectedCompany' AND companymeta.meta_value = '$comp_DB_ID')
	AND (customermeta.meta_key = 'ticket-authorityID' AND customermeta.meta_value = '$curUserID')
	AND (ticketStatusmeta.meta_key = 'ticket-action-status' AND ticketStatusmeta.meta_value = 'Un-Answered')
	AND ticketposts.post_type = 'tickets'
	AND ticketposts.post_status = 'publish' ORDER BY ticketposts.post_modified DESC");


	$answered_tickets = $wpdb->get_results("SELECT ID FROM $wpdb->posts ticketposts, $wpdb->postmeta companymeta, $wpdb->postmeta customermeta, $wpdb->postmeta ticketStatusmeta
	WHERE ticketposts.ID = companymeta.post_id
	AND ticketposts.ID = ticketStatusmeta.post_id
	AND ticketposts.ID = customermeta.post_id
	AND (companymeta.meta_key = 'ticket-selectedCompany' AND companymeta.meta_value = '$comp_DB_ID')
	AND (customermeta.meta_key = 'ticket-authorityID' AND customermeta.meta_value = '$curUserID')
	AND (ticketStatusmeta.meta_key = 'ticket-action-status' AND ticketStatusmeta.meta_value = 'Answered')
	AND ticketposts.post_type = 'tickets'
	AND ticketposts.post_status = 'publish' ORDER BY ticketposts.post_modified DESC");

	$solved_tickets = $wpdb->get_results("SELECT ID FROM $wpdb->posts ticketposts, $wpdb->postmeta companymeta, $wpdb->postmeta customermeta, $wpdb->postmeta ticketStatusmeta
	WHERE ticketposts.ID = companymeta.post_id
	AND ticketposts.ID = ticketStatusmeta.post_id
	AND ticketposts.ID = customermeta.post_id
	AND (companymeta.meta_key = 'ticket-selectedCompany' AND companymeta.meta_value = '$comp_DB_ID')
	AND (customermeta.meta_key = 'ticket-authorityID' AND customermeta.meta_value = '$curUserID')
	AND (ticketStatusmeta.meta_key = 'ticket-action-status' AND ticketStatusmeta.meta_value = 'Solved')
	AND ticketposts.post_type = 'tickets'
	AND ticketposts.post_status = 'publish' ORDER BY ticketposts.post_modified DESC");

	$company_tickets = array('all_tickets'=>$all_tickets,'rated_tickets'=>$rated_tickets,'opened_tickets'=>$opened_tickets,
							'unanswered_tickets'=>$unanswered_tickets,'answered_tickets'=>$answered_tickets,'solved_tickets'=>$solved_tickets,'closed_tickets'=>$closed_tickets);

	return $company_tickets;
	} // function show_company_customer_tickets()








	//add_action('init','get_plugin_header');
	function get_plugin_header(){

	global $mtheme;
	/*$source = file_get_contents( $mtheme.'index.php' );

	$tokens = explode(' ',$source);
		foreach( $tokens as $token ) {

			// Do something with the comment
			$txt[] = $token;
		}*/	
		if(file_exists( $mtheme.'header.php' )){
		include($mtheme.'header.php');
		return;
		}
		get_header();


	}

	function get_plugin_footer(){

	global $mtheme;
	/*$source = file_get_contents( $mtheme.'index.php' );

	$tokens = explode(' ',$source);
		foreach( $tokens as $token ) {

			// Do something with the comment
			$txt[] = $token;
		}	
		if($txt[2] == 'header'){
		include($mtheme.'footer.php');
		return;
		}*/

		if(file_exists( $mtheme.'footer.php' )){
		include($mtheme.'footer.php');
		return;
		}
		get_footer();


	}








	function showConversation($param_postID){

	$approveArgs = array('status' => 'approve', 'post_id' => $param_postID, 'order'	=>	'ASC'); 

	$approveComments = get_comments($approveArgs);

		if(isset($approveComments[0])){
			foreach($approveComments as $approveComment){
				$tick_commentAttachmentID = get_metadata('comment', $approveComment->comment_ID, 'ticket-commentAttachment', true);?>
				<table>
					<tr>
						<th class="tick_gen_cols"><?php echo $approveComment->comment_author; ?></th><td class="tick_gen_cols"><?php echo $approveComment->comment_date; ?></td>
					</tr>
					<tr>
						<td colspan="2" class="tick_gen_cols"><?php
							$gravatar_link = 'http://www.gravatar.com/avatar/' . md5($approveComment->comment_author_email) . '?s=150';
					   echo '<div class="avatar"><img src="' . $gravatar_link . '" /></div>';
						 echo $approveComment->comment_content; ?></td>
					</tr>
		 <?php if($tick_commentAttachmentID){?>
					<tr>
						<th class="tick_gen_cols"><?php echo $approveComment->comment_author."'s "; ?> <?php _e('Supported Attachment','mhelpdesk');?></th>
						<td class="tick_gen_cols"><a href="<?php echo wp_get_attachment_url($tick_commentAttachmentID); ?>" > <?php _e('Click Here to Download','mhelpdesk');?> </a></td>
					</tr>
		 <?php }// if($tick_commentAttachmentID != '')?>	
				</table>
	<?php		//echo('<p><strong>'.$approveComment->comment_author.'</strong>&nbsp;&nbsp;'.$approveComment->comment_date.'<br />'.$approveComment->comment_content.'</p>'); 
			}// foreach($approveComments as $approveComment)
		}// if(isset($approveComments[0])) 
	} // function showConversation($param_postID)


	function get_conversation_object($param_postID){

	$approveArgs = array('status' => 'approve', 'post_id' => $param_postID, 'order'	=>	'ASC'); 
	$post = get_post($param_postID);
	$POSTuseremail = get_user_by('id',$post->post_author);
	$POSTuseremail = $POSTuseremail->user_email;

	$approveComments = get_comments($approveArgs);

		if(isset($approveComments[0])){
			foreach($approveComments as $approveComment){
				$tick_commentAttachmentID = get_metadata('comment', $approveComment->comment_ID, 'ticket-commentAttachment', true);

				$discussions[]= array(
							'comment_author' 	=> 	$approveComment->comment_author,
							'comment_author_email'	=>	$approveComment->comment_author_email,
							'comment_date' 		=> 	$approveComment->comment_date,
							'comment_content' 	=> 	$approveComment->comment_content,
							'comment_by' 		=>	(($POSTuseremail == $approveComment->comment_author_email) ? 'customer' : 'other'),
							'comment_attachment'=> ($tick_commentAttachmentID) ? wp_get_attachment_url($tick_commentAttachmentID) : false
								); 



			}// foreach($approveComments as $approveComment)
		}// if(isset($approveComments[0]))
		return $discussions; 
	} // function showConversation($param_postID)









	////////////////////////////
	/// Show Admin Tab Menu
	///////////////////////////
	function showAdmin_tabmenu(){
	$urlAAction = get_query_var('tabaction');
		?>
				  <ul class="nav nav-tabs" id="myTab" role="tablist">
					<li role="presentation" <?php echo ($urlAAction == 'tickets' ? 'class="active"' : ''); ?> >
						<a href="../tickets/?pagenumber=1" aria-controls="hotel" role="tab"><?php _e('Tickets','mhelpdesk');?></a>
					</li>
					<li role="presentation" <?php echo ($urlAAction == 'agents' ? 'class="active"' : ''); ?> >
						<a href="../agents" aria-controls="agents" role="tab"><?php _e('Agents','mhelpdesk');?></a>
					</li>
					<li role="presentation" <?php echo ($urlAAction == 'customers' ? 'class="active"' : ''); ?> >
						<a href="../customers" aria-controls="customers" role="tab" ><?php _e('Customers','mhelpdesk');?></a></li>
					<li role="presentation" <?php echo ($urlAAction == 'settings' ? 'class="active"' : ''); ?> >
						<a href="../settings" aria-controls="settings" role="tab" ><?php _e('Settings','mhelpdesk');?></a></li>
					<li role="presentation" <?php echo ($urlAAction == 'profile' ? 'class="active"' : ''); ?> >
						<a href="../profile" aria-controls="profile" role="tab"><?php _e('My Profile','mhelpdesk');?></a></li>
				  </ul><?php
	} // function showAdmin_tabmenu()






	////////////////////////////
	/// Show Agent Tab Menu
	///////////////////////////
	function showAgent_tabmenu(){
	$urlAAction = get_query_var('tabaction');
		?>
				  <ul class="nav nav-tabs" id="myTab" role="tablist">
					<li role="presentation" <?php echo ($urlAAction == 'tickets' ? 'class="active"' : ''); ?> >
						<a href="../tickets/?pagenumber=1" aria-controls="hotel" role="tab" ><?php _e('Tickets','mhelpdesk');?></a>
					</li>
					<li role="presentation" <?php echo ($urlAAction == 'customers' ? 'class="active"' : ''); ?> >
						<a href="../customers" aria-controls="customers" role="tab" ><?php _e('Customers','mhelpdesk');?></a></li>
					<li role="presentation" <?php echo ($urlAAction == 'settings' ? 'class="active"' : ''); ?> >
					<li role="presentation" <?php echo ($urlAAction == 'profile' ? 'class="active"' : ''); ?> >
						<a href="../profile" aria-controls="profile" role="tab" ><?php _e('My Profile','mhelpdesk');?></a></li>
				  </ul><?php
	} // function showAdmin_tabmenu()









	////////////////////////////
	/// Show Customer Tab Menu
	///////////////////////////
	function showCustomer_tabmenu(){
	$urlAAction = get_query_var('tabaction');
		?>
				  <ul class="nav nav-tabs" id="myTab" role="tablist">
					<li role="presentation" <?php echo ($urlAAction == 'tickets' ? 'class="active"' : ''); ?> >
						<a href="../tickets/?pagenumber=1" aria-controls="hotel" role="tab" ><?php _e('Tickets','mhelpdesk');?></a>
					</li>
					<li role="presentation" <?php echo ($urlAAction == 'profile' ? 'class="active"' : ''); ?> >
						<a href="../profile" aria-controls="profile" role="tab" ><?php _e('My Profile','mhelpdesk');?></a>
					</li>
				  </ul><?php
	} // function showAdmin_tabmenu()







	////////////////////////////////////////
	/// Check Helpdesk Access Authorization
	///////////////////////////////////////
	function check_user_status($funcParam_loggedUserID,$funcParam_curCompID){
	global $wpdb;
	$admin_authCheck_obj = $wpdb->get_results("SELECT post_author FROM $wpdb->posts WHERE ID = $funcParam_curCompID AND post_type = 'companies' AND post_status = 'publish'");

		if(isset($admin_authCheck_obj[0]) && $admin_authCheck_obj[0]->post_author == $funcParam_loggedUserID)
		return 'admin';




	$DBcomp_agentEmails = get_metadata('post',$funcParam_curCompID,'company-agentEmail',true);		
	$agent_emailObj = $wpdb->get_results("SELECT user_email FROM $wpdb->users WHERE ID = $funcParam_loggedUserID");

		if( isset($agent_emailObj[0]) && isset($DBcomp_agentEmails[0]) ){
			$agent_authCheck = array_search($agent_emailObj[0]->user_email, $DBcomp_agentEmails);
			if($agent_authCheck > -1)
			return 'agent';
		} // if(isset($agent_emailObj[0]))





		$compCustomerBlocked_DB_arr = get_metadata('post', $funcParam_curCompID, 'company-customers-blocked', true);		
		$compCustomerOpened_DB_arr = get_metadata('post', $funcParam_curCompID, 'company-customers-opened', true);

		if(!isset($compCustomerBlocked_DB_arr[$funcParam_loggedUserID])) {
			if(isset($compCustomerOpened_DB_arr[0])){
				$customerID_foundIndex = array_search($funcParam_loggedUserID, $compCustomerOpened_DB_arr);	
				if($customerID_foundIndex > -1){
					return 'customer';
				} // if($customerID_foundIndex > -1)
			} // if($compCustomerOpened_DB_arr)
		  } // if(!isset($compCustomerBlocked_DB_arr[$funcParam_loggedUserID]))

		  return 'false'; 

	} // function check_authorization($funcParam_loggedUserID,$funcParam_curCompID)






	//////////////
	// Pagination
	//////////////
	function insert_pagination($funcParam_queryString, $funcParam_tickPerPage){
		//if(is_array($funcParam_queryString))
		//{echo 'array'; break;} else echo 'no an array';

	global $wpdb;


			$viewedPageNum = isset($_GET['pagenumber']) ? sanitize_text_field($_GET['pagenumber']) : 1 ;
		//if(isset($_GET['searchtickets'])){
			$searchTickets = isset($_GET['searchtickets']) ? sanitize_text_field($_GET['searchtickets']) : '';
			$actionString = isset($_GET['action']) ? sanitize_text_field($_GET['action']) : '';
			$agent_and_customerID = isset($_GET['id']) ? sanitize_text_field($_GET['id']) : '';

			//echo "$funcParam_queryString";
				if(is_array($funcParam_queryString))
					$totalPages = (count($funcParam_queryString)/ $funcParam_tickPerPage);
				else{
						$queryedResultObj = $wpdb->get_results($funcParam_queryString);
						$totalPages = (count($queryedResultObj)/ $funcParam_tickPerPage);
				}

				//echo $totalPages;
				if($totalPages > 0){
					if(is_float($totalPages)) $totalPages = ((int)$totalPages) + 1;
						$paginationHTML = _e('Pages: ','mhelpdesk');
						for($pageIndex = 1; $pageIndex <= $totalPages; $pageIndex++){
							if($viewedPageNum == $pageIndex)
								$paginationHTML .= $pageIndex.'&nbsp;&nbsp;';
							elseif($searchTickets && $viewedPageNum)
								$paginationHTML .= '<a href="?searchtickets='.$searchTickets.'&pagenumber='.$pageIndex.'">'.$pageIndex.'</a>&nbsp;&nbsp;';
							elseif($actionString && !$agent_and_customerID)
								$paginationHTML .= '<a href="?action='.$actionString.'&pagenumber='.$pageIndex.'">'.$pageIndex.'</a>&nbsp;&nbsp;';
							elseif($actionString && $agent_and_customerID)
								$paginationHTML .= '<a href="?action='.$actionString.'&id='.$agent_and_customerID.'&pagenumber='.$pageIndex.'">'.$pageIndex.'</a>&nbsp;&nbsp;';
									else $paginationHTML .= '<a href="?pagenumber='.$pageIndex.'">'.$pageIndex.'</a>&nbsp;&nbsp;';
						} // for($pageIndex = 1; $pageIndex <= $totalPages; $pageIndex++)
					} // if($totalPages > 0)

			return $paginationHTML;
		//}
	} // function insert_pagination($funcParam_tickObj,$funcParam_pageNum)





	function shortcode_faq( $atts ) {
		$atts = shortcode_atts( array(
			'q' => 'Question',
			'a' => 'Answer',
			'n' => 1
		), $atts, 'bartag' );

		return '<input class="animate" type="radio" name="question" id="q'.$atts['n'].'"/>
						<label class="animate" for="q'.$atts['n'].'">Q: '.$atts['q'].'</label><p class="response animate">'.$atts['a'].'</p>';
	}
	add_shortcode( 'faq', 'shortcode_faq' );

?>