

<div class="top_hd_menu">
 	<?php $checkAuthorization = check_user_status($curUserID,$comp_DB_ID);
	 
	echo '<style>'.get_metadata('post',$comp_DB_ID,'company_custom_css',true).'</style>';
	 
	if($checkAuthorization == 'admin'){?>
    	<a href="<?php echo get_permalink($comp_DB_ID); ?>admin" class="button button-default btn btn-default"><?php _e('View Admin Panel','mhelpdesk');?></a>
<?php 
	} // if($checkAuthorization == 'admin') 
	
	
	
	
	
	if($checkAuthorization == 'agent'){?>
    	<a href="<?php echo get_permalink($comp_DB_ID); ?>agent" class="button button-default btn btn-default"><?php _e('View Agent Panel','mhelpdesk');?></a>
<?php 
	} // if($checkAuthorization == 'agent') 
	
	
	
	
	
	if($checkAuthorization == 'customer'){?>
    	<a href="<?php echo get_permalink($comp_DB_ID); ?>customer" class="button button-default btn btn-default"><?php _e('View Customer Panel','mhelpdesk');?> </a>
<?php 
	} // if($checkAuthorization == 'customer') 
	?>
 </div>
 
         	<div>
            		<section>
					<h1><?php echo $viewPostObj->post_title; ?></h1>
					</section>
				
					<div class="Intro">
					<h4><?php _e('Introduction','mhelpdesk');?></h4>
					<?php echo $viewPostObj->post_content; ?>
                	</div>
                    
                    
                	<section>
                    <h4><?php _e('FAQs','mhelpdesk');?></h4>
                    <?php echo (get_post_meta($comp_DB_ID,'company-faqs',true)) ; ?>
                    </section>
            </div>

<script>
<?php echo get_metadata('post',$comp_DB_ID,'company_custom_script',true);?>

	jQuery(document).ready(function() {
			jQuery('#showTicketForm').click(function() {
				if(jQuery('#ticketDiv').is(":hidden"))
					jQuery('#ticketDiv').slideDown(1000);
				else
					jQuery('#ticketDiv').slideUp(1000);	
		});
	}); // END jQuery(document).ready(function()

jQuery(function() {

   function setHeight() {
     jQuery(".response").each(function(index, element) {
       var target = jQuery(element);
       target.removeClass("fixed-height");
       var height = target.innerHeight();
       target.attr("data-height", height)
         .addClass("fixed-height");
     });
   };

   jQuery("input[name=question]").on("change", function() {
     jQuery("p.response").removeAttr("style");

     var target = jQuery(this).next().next();
     target.height(target.attr("data-height"));
   })

   setHeight();
 });
</script>


<button class="button button-default btn btn-default" id="showTicketForm"><?php _e('Create New Ticket','mhelpdesk');?></button>        
        
 <div id="ticketDiv" style="display:none">    
		<form name="ticketRegistration" method="post" action="#" enctype="multipart/form-data">
			<table>
				<tr>
					<th class="tick_gen_cols"><?php _e('Select Group','mhelpdesk');?></th>
					<td>    <select name="selected_department">
					<?php 		foreach($company_departments as $company_department){?>
                                <option value="<?php echo $company_department?>"><?php echo $company_department?></option>
                    <?php 		} //foreach($company_departments as $company_department){?>
                            </select>
</td>
				</tr>
				<tr>
					<th class="tick_gen_cols"><?php _e('Ticket Title','mhelpdesk');?></th>
					<td><input name="title" class="tick_textarea" type="text" required="required" /></td>
				</tr>
				<tr>
					<th class="tick_gen_cols"><?php _e('Enter Query','mhelpdesk');?></th>
					<td><?php wp_editor('','customerComment'); ?><!--textarea name="customerComment" rows="10" required="required" ></textarea--></td>
				</tr>
                <tr>
					<th class="tick_gen_cols"><?php _e('Supported Attachment (if any)','mhelpdesk');?></th>
					<td><input type="file" name="supportedDoc" id="supportedDoc" accept=".zip, .rar" 
                    title="<?php _e('Upload Supported Attachment if any as zip or rar format','mhelpdesk');?>" />
                    <p id="displaysize"></p>
                    </td>
				</tr>
                <tr><td colspan="2">
					<?php wp_nonce_field('ticketregistration', 'ticket-registration'); ?> <input type="submit" name="ticketSubmitBtn" value="Submit New Ticket/ Query" />
				</td></tr>
			</table>
		</form>
 </div>
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
 

