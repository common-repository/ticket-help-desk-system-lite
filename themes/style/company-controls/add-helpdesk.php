		<form name="companyRegistration" method="post">
			<?php global $message; global_logged_in_user_is_admin(); echo $message?>
			<table>
            	<tr><td colspan="2"><h2><?php _e('Create New Company/ Helpdesk','mhelpdesk');?></h2></td></tr>
				<tr>
					<th class="tick_gen_cols"><?php _e('Company Name','mhelpdesk');?></th>
					<td><input class="tick_textarea" name="companyTitle" type="text" required="required" /></td>
				</tr>
				<tr>
					<th class="tick_gen_cols"><?php _e('Company Site Identification','mhelpdesk');?></th>
					<td><input name="slug" type="text"  class="tick_textarea"
                    title="<?php _e('Enter site identification alphabets/ numeric letters WITHOUT spaces and special characters. Underscore is allowed','mhelpdesk');?>" 
                    pattern="\w+" onchange="this.setCustomValidity(this.validity.patternMismatch ? '' : '');" required="required" />
					<p class="helpnote"><?php _e('e.g. Company Name: Shopping Mart AND Company Site Identification: shoppingmart or sm','mhelpdesk');?></p>
					</td>
				</tr>
				<tr>
					<th class="tick_gen_cols"><?php _e('Company Introduction','mhelpdesk');?></th>
					<td>
                    <?php
                    $contnt = '';
					wp_editor($contnt,'intro');
					?>
                    </td>
				</tr>
				<tr>
					<th class="tick_gen_cols"><?php _e('Company FAQs','mhelpdesk');?></th>
					<td>
                    <?php 
					$faq = "";
					wp_editor($faq,'faqs');
					?>
                   </td>
				</tr>
				<tr>
					<th class="tick_gen_cols"><?php _e('Company Groups (Seperated by Commas without spaces between them.)','mhelpdesk');?></th>
					<td>
                       <input name="departments" class="form-control" type="text" />
       					<p><?php _e('e.g. General,Registration Department,Payment Department,Sale and Service Department,....,etc','mhelpdesk');?></p>

                   </td>
				</tr>
                <tr>
                    <th class="tick_gen_cols"><?php _e('The Email Addresses When New Tickets Created For Notifications Purpose','mhelpdesk');?></th>
                    <td>
                    <?php $curUserEmail = $wpdb->get_results("SELECT user_email FROM $wpdb->users WHERE ID = '$curUserID'"); 
                    $notificationEmails = '';?>
            <textarea name="notificationEmails" class="tick_textarea"
            placeholder="e.g. <?php echo esc_html($curUserEmail[0]->user_email);?>, example1@example.com,example2@example.com"><?php echo esc_html($notificationEmails)?></textarea>
                    </td>
				</tr>

				<tr>
					<th class="tick_gen_cols"><?php _e('Company Support Team/ Agents','mhelpdesk');?></th>
					<td>
						<div class="agentBlock">
							<div align="left" class="tick_gen_cols">
                        <!--a href="javascript:void(0);"><span class="dashicons dashicons-plus-alt"></span> <?php _e('Add More Agents','mhelpdesk');?></a-->
                             </div>
							<table class="firstAgent">
									<th class="tick_gen_cols"><?php _e('Agent Email Address','mhelpdesk');?></th>
									<td><input name="agentemail[]" type="email" class="tick_textarea" required="required" /></td>
								</tr>
								<tr>
									<td colspan="2"><!--a href="#"><span class="dashicons dashicons-dismiss"></span></a--></td>
								</tr>
							</table>
						</div>
					</td>
				</tr>
                <tr>
                	<td colspan="2">
					<?php wp_nonce_field('companyregistration', 'company-registration'); ?>
					<input type="submit" name="companySubmitBtn" value="Create Company/ Helpdesk" />
					</td>
                </tr>
			</table>
		</form>
<!--script>
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
			alert('<?php _e('You cannot remove first agent!','mhelpdesk')?>');
			
		
		});
});
</script-->