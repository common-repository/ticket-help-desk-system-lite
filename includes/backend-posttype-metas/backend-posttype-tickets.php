<?php
class create_ticket_posttype_class{
	function __construct(){
		add_action('init', array(&$this,'custom_ticket_posttype_func'));
		add_action('add_meta_boxes',array(&$this,'init_custom_ticket_metaboxCBF'));
	} // function __construct()
	
	
	
		function custom_ticket_posttype_func(){
			//die(print_r($_SERVER));
				register_post_type( 'tickets', array(
				'labels' => array(
				'name_admin_bar' => _x( 'Tickets', 'Add New Ticket','ticket' ),
				'name'=> __( 'All Tickets','ticket' ),
				'singular_name' => __( 'Ticket','ticket' ),
				'add_new' => __( 'Add New Ticket','ticket' ),
				'add_new_item' => __( 'Add New Ticket','ticket' ),
				'edit' => __( 'Edit Ticket','ticket' ),
				'edit_item' => __( 'Edit Ticket','ticket' ),
				'new-item' => __( 'New Ticket','ticket' ),
				'view' => __( 'View Ticket','ticket' ),
				'view_item' => __( 'View Ticket','ticket' ),
				),
				'public'  => true,
				'show_ui'  => true,
				'menu_icon' => 'dashicons-share-alt',
				'capability_type' => 'post',
				'map_meta_cap' => true,
				'query_var' => false,
				'delete_with_user' => false,
				'supports' => false, //'supports' => array( 'author'),
				'rewrite'  => false//array( 'slug' => 'ticket' ),
				)
				);
				flush_rewrite_rules();
		}   ///custom_ticket_posttype_func end
		
	function init_custom_ticket_metaboxCBF($this_post_type){
	
		 //add_meta_box( $id, $title, $callback, $post_type, $context,$priority, $callback_args );
		 add_meta_box('custom_company_metabox_id', __('Ticket Info:','mhelpdesk'),array(&$this,'add_custom_ticket_metaboxCBF'),'tickets','normal','high');
		 
	} // function init_custom_company_metaboxCBF()
	


	function add_custom_ticket_metaboxCBF($this_post_obj){?>
    <script>
		jQuery(document).ready(function(e) {
           var commentdiv = jQuery.find('#commentsdiv-hide');
		   //jQuery(commentdiv).parent().hide();
		   //jQuery('#commentsdiv').hide();
		   jQuery('.wp-heading-inline').hide();
		   jQuery('.page-title-action').hide();
        });
	</script>
<?php
		$ticket_selectedCompany = get_metadata('post',$this_post_obj->ID,'ticket-selectedCompany',true);
		$ticket_selectedAgent = get_metadata('post',$this_post_obj->ID,"ticket-selectedAgent",true);
		$ticketAgent_object = get_user_by('id',$ticket_selectedAgent);
		$ticket_status = get_metadata('post',$this_post_obj->ID,"ticket-status",true);
		$ticket_conversation_status = get_metadata('post',$this_post_obj->ID,"ticket-action-status",true);
		$ticket_group = get_metadata('post',$this_post_obj->ID,"ticket-selected_department",true);
		$ticket_author = $this_post_obj->post_author;
		$ticket_author_object = get_user_by('id',$ticket_author);


?>
    <table>
    	<tr>
        	<th style="width:200px;text-align:left;vertical-align: top;"><?php _e('Associated Company','mhelpdesk');?></th>
            <td style="padding-left:20px"><?php echo get_the_title($ticket_selectedCompany)?></td>
        </tr>
    	<tr>
        	<th style="width:200px;text-align:left;vertical-align: top;"><?php _e('Assigned Agent','mhelpdesk');?></th>
            <td style="padding-left:20px"><?php echo ($ticketAgent_object->display_name) ? $ticketAgent_object->display_name : 'Not Assigned'?></td>
        </tr>
    	<tr>
        	<th style="text-align:left;vertical-align: top;"><?php _e('Ticket ID','mhelpdesk');?></th>
            <td style="padding-left:20px"><?php echo $this_post_obj->ID?></td>
        </tr>
    	<tr>
        	<th style="text-align:left;vertical-align: top;"><?php _e('Ticket Group','mhelpdesk');?></th>
            <td style="padding-left:20px"><?php echo $ticket_group?></td>
        </tr>
    	<tr>
        	<th style="text-align:left;vertical-align: top;"><?php _e('Created Date','mhelpdesk');?></th>
            <td style="padding-left:20px"><?php echo $this_post_obj->post_date?></td>
        </tr>
    	<tr>
        	<th style="text-align:left;vertical-align: top;"><?php _e('Ticket Owner Email','mhelpdesk');?></th>
            <td style="padding-left:20px"><?php echo $ticket_author_object->user_email ?></td>
        </tr>
    	<tr>
        	<th style="text-align:left;vertical-align: top;"><?php _e('Ticket Title','mhelpdesk');?></th>
            <td style="padding-left:20px"><?php echo $this_post_obj->post_title?></td>
        </tr>
    	<tr>
        	<th style="text-align:left;vertical-align: top;"><?php _e('Ticket Conversation Status','mhelpdesk');?></th>
            <td style="padding-left:20px"><?php  echo $ticket_conversation_status?></td>
        </tr>
    	<tr>
        	<th style="text-align:left;vertical-align: top;"><?php _e('Ticket Status','mhelpdesk');?></th>
            <td style="padding-left:20px"><?php  echo $ticket_status?></td>
        </tr>
    </table>
	
<?php    
	} // add_custom_ticket_metaboxCBF

		
} //close class
$object = new create_ticket_posttype_class();
?>