<?php
	$hd_admin_settings_arr = get_option('APF_MyFirstFrom');
		
	$helpdesk_rewriterule_slug = isset($hd_admin_settings_arr['m_helpdesk_form']['helpdesk_rewriterule_slug']) && $hd_admin_settings_arr['m_helpdesk_form']['helpdesk_rewriterule_slug'] ? $hd_admin_settings_arr['m_helpdesk_form']['helpdesk_rewriterule_slug'] : 'company';
?>
<div class="top_hd_menu">
         <a href="<?php echo site_url().'/'.$helpdesk_rewriterule_slug?>" class="button button-default btn btn-default"><?php _e('Helpdesk Home','mhelpdesk');?> </a>  
         <a href="<?php echo get_permalink($comp_DB_ID)?>" class="button button-default btn btn-default"><?php _e('Create Helpdesk Ticket','mhelpdesk');?></a> 
</div>

	<div role="tabpanel">
			  <!-- Nav tabs -->
			  <ul class="nav nav-tabs" id="myTab" role="tablist">
				<li role="presentation" class="active"><a href="tickets/?pagenumber=1" aria-controls="hotel" role="tab" ><?php _e('Tickets','mhelpdesk');?></a></li>
                <li role="presentation"><a href="profile" aria-controls="profile" role="tab" ><?php _e('My Profile','mhelpdesk');?></a></li>
			  </ul> <!-- class="nav nav-tabs" id="myTab" role="tablist</li>" -->

			  <!-- Tab panes -->
			  <div class="tab-content">
				<div role="tabpanel" class="tab-pane active" id="tickets">
                	<div id="ticketsContent" class="tabed_content">
                    	<a href="<?php echo get_permalink($comp_DB_ID)."customer/tickets/?pagenumber=1" ?>"><?php _e('Show All My Tickets','mhelpdesk');?></a>
                    </div>
                </div>
				
			  </div> <!-- class="tab-content" -->
			</div> <!-- role="tabpanel" -->
<!-- END tab section -->