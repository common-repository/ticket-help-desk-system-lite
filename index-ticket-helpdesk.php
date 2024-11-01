<?php
/*
	Plugin Name: (Lite) Ticket Help Desk System Pro
	Version: 4.5.2
	Plugin URI: http://multihelpdesk.com/
	Description: Easily Setup Helpdesk Support System for Multiple Companies
	Author: Gigsix | Alisaleem252
	Author URI: http://alisaleem252.com
 	Text Domain: mhelpdesk
	
*/



define('MHDESKABSPATH', dirname(__FILE__) );
define('MHDESKURLPATH', plugin_dir_url( __FILE__ ) );


$hd_admin_settings_arr = get_option('APF_MyFirstFrom');
//print_r($hd_admin_settings_arr);

$helpdesk_theme = $hd_admin_settings_arr['m_helpdesk_form']['helpdesk_theme'];

$helpdesk_theme = isset($_COOKIE['hd_theme_is']) && $_COOKIE['hd_theme_is'] == '1' ? 'Blue-Line' : (isset($_COOKIE['hd_theme_is']) && $_COOKIE['hd_theme_is'] == '2' ? 'Core-UI' :$helpdesk_theme);


$mtheme = MHDESKABSPATH."/themes/$helpdesk_theme/";
$mthemeurl = MHDESKURLPATH."themes/$helpdesk_theme/";


require_once(MHDESKABSPATH.'/admin/admin-page-framework.php');
$hd_initiate = new APF_MyFirstFrom;
require_once(MHDESKABSPATH.'/includes/backend-posttype-metas/backend-posttype-tickets.php');
require_once(MHDESKABSPATH.'/includes/backend-posttype-metas/backend-posttype-companies.php');

require_once(MHDESKABSPATH.'/includes/template-rewrite-rules-hook.php');
require_once(MHDESKABSPATH.'/includes/check-hd-boundaries.php');
require_once(MHDESKABSPATH.'/includes/ticket-helpdesk-functions.php');
require_once(MHDESKABSPATH.'/includes/ticket-helpdesk-notifications.php');
require_once(MHDESKABSPATH.'/includes/init.php');

require_once( ABSPATH . 'wp-admin/includes/image.php' );
require_once( ABSPATH . 'wp-admin/includes/file.php' );
require_once( ABSPATH . 'wp-admin/includes/media.php' );

require_once(MHDESKABSPATH.'/includes/open_company_ticket_ajax.php');
