<?php
	$hd_admin_settings_arr = get_option('APF_MyFirstFrom');
		
	$helpdesk_rewriterule_slug = isset($hd_admin_settings_arr['m_helpdesk_form']['helpdesk_rewriterule_slug']) && $hd_admin_settings_arr['m_helpdesk_form']['helpdesk_rewriterule_slug'] ? $hd_admin_settings_arr['m_helpdesk_form']['helpdesk_rewriterule_slug'] : 'company';
?>
		<div class="top_hd_menu">
        	 <a href="<?php echo site_url().'/'.$helpdesk_rewriterule_slug?>" class="button button-default btn btn-default"><?php _e('Helpdesk Home','mhelpdesk');?></a> 
             <a href="<?php echo get_permalink($comp_DB_ID)?>" class="button button-default btn btn-default"><?php _e('Create Helpdesk Ticket','mhelpdesk');?></a> 
        </div>

	<div role="tabpanel">
			  <!-- Nav tabs -->
			  <ul class="nav nav-tabs" id="myTab" role="tablist">
				<li role="presentation" class="active"><a href="tickets/?pagenumber=1" aria-controls="hotel" role="tab" ><?php _e('Tickets','mhelpdesk');?></a></li>
				<li role="presentation"><a href="agents" aria-controls="agents" role="tab" ><?php _e('Agents','mhelpdesk');?></a></li>
				<li role="presentation"><a href="customers" aria-controls="customers" role="tab" ><?php _e('Customers','mhelpdesk');?></a></li>
                <li role="presentation"><a href="settings" aria-controls="settings" role="tab" ><?php _e('Settings','mhelpdesk');?></a></li>
                <li role="presentation"><a href="profile" aria-controls="profile" role="tab" ><?php _e('My Profile','mhelpdesk');?></a></li>
			  </ul> <!-- class="nav nav-tabs" id="myTab" role="tablist</li>" -->

			  <!-- Tab panes -->
			  <div class="tab-content dash">
				<div role="tabpanel" class="tab-pane active" id="tickets">
                	<div id="ticketsContent" class="tabed_content">
                    	<h2><?php _e('Statistics','mhelpdesk');?></h2>
                    	<table>
                        <tr>
 	                       <th><?php _e('Total Tickets','mhelpdesk');?></th>
                           <th><?php _e('Open Tickets','mhelpdesk');?></th>
                           <th><?php _e('Closed Tickets','mhelpdesk');?></th>
                           <th><?php _e('Un-Answered Tickets','mhelpdesk');?></th>
                        </tr>
                        <tr>
    	                    <td><?php echo count($all_tickets); ?></td>
                            <td><?php echo count($all_open_tickets); ?></td>
                            <td><?php echo count($all_solved_tickets); ?></td>
                            <td><?php echo count($all_unaswered_tickets); ?></td>
                        </tr>
						
                        </table>
                        
                        
                        <table>
                        <tr>
 	                       <th><?php _e('Total Customers','mhelpdesk');?></th>
                           <th><?php _e('Total Agents','mhelpdesk');?></th>
                           <th><?php _e('Tickets Allowed','mhelpdesk');?></th>
                           <th><?php _e('Users Blocked','mhelpdesk');?></th>
                        </tr>
                        <tr>
    	                    <td><?php echo esc_html($total_customers) ?></td>
                            <td><?php echo count($total_agents); ?></td>
                            <td><?php echo (isset($hd_admin_settings_arr['m_helpdesk_form']['helpdesk_tickets']) 
											&& $hd_admin_settings_arr['m_helpdesk_form']['helpdesk_tickets'] > 0 ? $hd_admin_settings_arr['m_helpdesk_form']['helpdesk_tickets'] : 'Un-Limitted') ?></td>
                            <td><?php echo esc_html($total_customers_blocked) ?></td>
                        </tr>
						
                        </table>
                        
                        
                        <a href="<?php echo get_permalink($comp_DB_ID)."admin/tickets/?pagenumber=1" ?>"><?php _e('Show All Tickets','mhelpdesk');?></a>
                    </div>
                </div>
				
			  </div> <!-- class="tab-content" -->
			</div> <!-- role="tabpanel" -->
<!-- END tab section -->