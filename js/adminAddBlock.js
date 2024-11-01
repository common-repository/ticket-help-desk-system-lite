jQuery(document).ready(function(){
		
    var wrapper         = jQuery(".mainBlock");
    var x = 1;

		jQuery(document.body).on('click','.addBlock',function(e){ //on add input button click
			x++; 
			var origTable = jQuery('table.firstBlock').html();

			jQuery(wrapper).append('<table>'+origTable+'</table>');
			///jQuery('input[name="hd_wci_SettingsBtn"]').click();
		update_slidenum();			  
		});
    
		jQuery(document.body).on("click",'.removeBlock',function(e){ //user click on remove text
			
			tableclass = jQuery(this).closest('table').attr('class');
			if(tableclass != 'firstBlock'){
			jQuery(this).closest('table').remove();
			x--;
			//jQuery('input[name="hd_wci_SettingsBtn"]').click();
			
			update_slidenum();
			}
			else
			alert('You cannot remove first block!');		
		});	
		
		
	function update_slidenum(){
		jQuery(document.body).find('.slidenum').each( function(index,e){
			var plus = index+1;
			jQuery(this).text('Package/ Setting '+plus);
		});
	}
	
	
		
}); // jQuery(document).ready(function()