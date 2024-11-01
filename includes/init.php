<?php
class HelpDesk_pro_Initialize{

	function __construct(){

	//	add_action('admin_notices',array(&$this,'helpdesk_admin_notices'));
		add_action('wp_enqueue_scripts', array(&$this,'ticket_helpdesk_enqueue_scripts'));
		add_action('admin_enqueue_scripts', array(&$this,'helpdesk_admin_enqueue_scripts'));
	}

	function ticket_helpdesk_enqueue_scripts() {
 		
		$hd_admin_settings_arr = get_option('APF_MyFirstFrom');
		wp_enqueue_style('mtheme', MHDESKURLPATH.'css/theme.css');

		if($hd_admin_settings_arr['m_helpdesk_form']['helpdesk_boostrap'] == true){
			wp_enqueue_style('tickHelp-bootstrap-v3.3.1-HD-css', MHDESKURLPATH.'css/bootstrap.min.v3.3.1.css');
			wp_enqueue_script('addBock-bootstrap-v3.3.1-HD-JS', MHDESKURLPATH.'js/bootstrap.min.v3.3.1.js', array('jquery'));
		}
		
		wp_enqueue_code_editor( array( 'type' => 'text/html' ) );
		wp_enqueue_script('hd-code-editor-JS', MHDESKURLPATH.'js/hd_code_editor_js.js', array('jquery'));
		
		
		wp_enqueue_style('HD-rating-css', MHDESKURLPATH.'css/rating-review.css');
		
		

	} // forntend wp_enqueue_scripts




	function helpdesk_admin_enqueue_scripts() {

		wp_enqueue_script('addBock-backend', MHDESKURLPATH.'/js/adminAddBlock.js' , array('jquery'));



	} // usp_admin_enqueue_scripts

	function helpdesk_admin_notices(){
		global $hd_admin_settings_arr,$hd_initiate;
		$purchase_code = $hd_admin_settings_arr['m_helpdesk_form_envato']['helpdesk_envato'];

		if(!$hd_initiate->verify_envato_purchase_code($purchase_code)){
			?>
			<div id="message" class="error notice is-dismissible">
				<p>Multi Helpdesk Ticket System Pro needs Valid <strong><?php _e('Purchase Code', 'mhelpdesk'); ?></strong>. <a href="admin.php?page=m_helpdesk_form_envato"><?php _e('Enter Code', 'mhelpdesk'); ?></a></p>
				<button type="button" class="notice-dismiss"><span class="screen-reader-text"><?php _e('Dismiss this notice.', 'mhelpdesk'); ?></span></button>
			</div>
	<?php
		}

	}
}
new HelpDesk_pro_Initialize;
