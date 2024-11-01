<?php
	$hd_admin_settings_arr = get_option('APF_MyFirstFrom');
		
	$helpdesk_rewriterule_slug = isset($hd_admin_settings_arr['m_helpdesk_form']['helpdesk_rewriterule_slug']) && $hd_admin_settings_arr['m_helpdesk_form']['helpdesk_rewriterule_slug'] ? $hd_admin_settings_arr['m_helpdesk_form']['helpdesk_rewriterule_slug'] : 'company';
?>
<div class="top_hd_menu">
        	 <a href="<?php echo site_url().'/'.$helpdesk_rewriterule_slug?>" class="button button-default btn btn-default"><?php _e('Helpdesk Home','mhelpdesk');?></a> 
             <a href="<?php echo get_permalink($comp_DB_ID)?>" class="button button-default btn btn-default"><?php _e('Create Helpdesk Ticket','mhelpdesk');?></a>  
</div>
	<div role="tabpanel"><?php
        showAdmin_tabmenu();
		?>
          <div class="tab-content">
            <div role="tabpanel" class="tab-pane active" id="settings">
                <div id="settingsContent" class="tabed_content"><?php
				
	if(!isset($_GET['action'])){?>
        <?php if(isset($_POST['company-companyUpdateBtn']) ) {?>
        <table>
        	<tr>
					<th class="tick_gen_cols"><?php _e('Setting Saved','mhelpdesk');?></th>
					
			</tr>
        </table>
        <?php } ?>

		<form name="companyRegistration" method="post">
			<table>
				<tr>
					<th class="tick_gen_cols"><?php _e('Company Name','mhelpdesk');?></th>
					<td><input class="tick_textarea" name="companyTitle" type="text" 
                    	value="<?php echo esc_html($comp_authCheck_obj[0]->post_title); ?>" required="required" /></td>
				</tr>
				<tr>
					<th class="tick_gen_cols"><?php _e('Company Site Identification','mhelpdesk');?></th>
					<td class="tick_gen_cols"><?php echo esc_html($comp_authCheck_obj[0]->post_name); ?></td>
				</tr>
				<tr>
					<th class="tick_gen_cols"><?php _e('Company Introduction','mhelpdesk');?></th>
					<td>
                    <?php
                    $contnt = esc_html($comp_authCheck_obj[0]->post_content);
					wp_editor($contnt,'intro');
					?>
                    </td>
				</tr>
				<tr>
					<th class="tick_gen_cols"><?php _e('Company FAQs','mhelpdesk');?></th>
					<td>
                    <?php 
					$faq = get_metadata('post', $comp_DB_ID, 'company-faqs', true) ? get_metadata('post', $comp_DB_ID, 'company-faqs', true) : "";
					wp_editor($faq,'faqs');
					?>
					<p><a target="_blank" href="https://codepen.io/tag/faq">Sample FAQ Templates with css/html/javascript</a></p>
                    </td>
				</tr>

				<tr>
					<th><?php _e('Company Groups','mhelpdesk');?></th>
					<td><input name="departments" class="form-control" type="text" value="<?php echo $departments?>" />
                        <p><?php _e('<strong>Note:</strong> (Seperated by Commas without spaces between them.)e.g. General,Registration Department,Payment Department,Sale and Service Department,....,etc','mhelpdesk');?></td>
				</tr>

     <tr>
     	<th><?php _e("Specify Registered Agents's Email OR<br />Including youself, for the New<br /> Tickets Notification",'mhelpdesk');?></th>
        <td>
        <?php $curUserEmail = $wpdb->get_results("SELECT user_email FROM $wpdb->users WHERE ID = '$curUserID'"); 
		$notificationEmails = get_metadata('post',$comp_DB_ID, 'company-notificationEmails', true);?>
		<textarea name="notificationEmails" class="tick_textarea"
        	placeholder="e.g. <?php echo esc_html($curUserEmail[0]->user_email);?>, example1@example.com, example2@example.com"><?php echo esc_html($notificationEmails)?></textarea>
        </td>
     </tr>
	
				<tr>
					<th><?php _e('Show Tickets Per Page','mhelpdesk');?></th>
					<td><input class="tick_textarea" name="ticketsperpage" type="number" min="1" value="<?php echo $ticketsperpage; ?>" required="required" /></td>
				</tr>
                <tr>
                	<th><?php _e('Widget','mhelpdesk');?></th>
                	<td><textarea disabled="disabled" rows="3" cols="150" ><iframe onload="resizeIframe(this)" style='width:500px' src="<?php echo admin_url('admin-ajax.php').'?action=mam_open_company_ticket_ajax&customer_company_slug='.$urlCompSlug?>"></iframe><script type="text/javascript">function resizeIframe(obj){obj.style.height = 0;obj.style.height = obj.contentWindow.document.body.scrollHeight + 'px';}</script></textarea></td>
               </tr>
     			<tr>
                        <th><?php _e('Custom CSS','mhelpdesk');?></th>
                        <td>
							<textarea rows="3" cols="150" id="code_editor_page_css" class="mam_code_editor_textarea" name="company_custom_css"><?php echo get_metadata('post',$comp_DB_ID,'company_custom_css',true);?></textarea>
						</td>
               </tr>
                    <tr>
                        <th><?php _e('Custom Script','mhelpdesk');?></th>
                        <td>
							<textarea rows="3" cols="150" id="code_editor_page_js" class="mam_code_editor_textarea" name="company_custom_script"><?php echo get_metadata('post',$comp_DB_ID,'company_custom_script',true);?></textarea>
						</td>
                    </tr>   
                <tr>
            <tr><td colspan="2">
            	<?php wp_nonce_field('companyUpdateBtn', 'company-companyUpdateBtn'); ?>
				<input type="submit" name="companyUpdateBtn" value="Update Helpdesk" />

            </td></tr>
        </table>
		</form>	<?php
} // if(!$_GET['action'])               
echo '				</div>';
echo '            </div>';
echo '        </div>';
echo '    </div> ';