<?php
/**
 Admin Page Framework v3.5.12 by Michael Uno
 Generated by PHP Class Files Script Generator <https://github.com/michaeluno/PHP-Class-Files-Script-Generator>
 <http://en.michaeluno.jp/admin-page-framework>
 Copyright (c) 2013-2015, Michael Uno; Licensed under MIT <http://opensource.org/licenses/MIT>
 Included Components: Admin Pages
 Generated on 2015-10-07 */
abstract class mhelpdeskAdminPageFramework_Registry_Base {
    const VERSION = '3.5.12';
    const NAME = 'Admin Page Framework';
    const DESCRIPTION = 'Facilitates WordPress Support.';
    const URI = 'http://gigsix.com';
    const AUTHOR = 'Gigsix Solution';
    const AUTHOR_URI = 'http://gigsix.com/';
    const COPYRIGHT = 'Copyright (c) 2020-2025, Alisaleem252';
    const LICENSE = 'MIT <http://opensource.org/licenses/MIT>';
    const CONTRIBUTORS = '';
}
final class mhelpdeskAdminPageFramework_Registry extends mhelpdeskAdminPageFramework_Registry_Base {
    const TEXT_DOMAIN = 'admin-page-framework';
    const TEXT_DOMAIN_PATH = '/language';
    static public $bIsMinifiedVersion = true;
    static public $bIsDevelopmentVersion = true;
    static public $sAutoLoaderPath;
    static public $sIncludeClassListPath;
    static public $aClassFiles = array();
    static public $sFilePath = '';
    static public $sDirPath = '';
    static public $sFileURI = '';
    static public function setUp($sFilePath = __FILE__) {
        self::$sFilePath = $sFilePath;
        self::$sDirPath = dirname(self::$sFilePath);
        self::$sFileURI = plugins_url('', self::$sFilePath);
        self::$sIncludeClassListPath = self::$sDirPath . '/admin-page-framework-include-class-list.php';
        self::$aClassFiles = self::_getClassFilePathList(self::$sIncludeClassListPath);
        self::$sAutoLoaderPath = isset(self::$aClassFiles['mhelpdeskAdminPageFramework_RegisterClasses']) ? self::$aClassFiles['mhelpdeskAdminPageFramework_RegisterClasses'] : '';
        self::$bIsMinifiedVersion = class_exists('mhelpdeskAdminPageFramework_MinifiedVersionHeader');
    }
    static private function _getClassFilePathList($sInclusionClassListPath) {
        $aClassFiles = array();
        include ($sInclusionClassListPath);
        return $aClassFiles;
    }
    static public function getVersion() {
        if (!isset(self::$sAutoLoaderPath)) {
            trigger_error('Admin Page Framework: ' . ' : ' . sprintf(__('The method is called too early. Perform <code>%2$s</code> earlier.', 'admin-page-framework'), __METHOD__, 'setUp()'), E_USER_WARNING);
            return self::VERSION;
        }
        $_aMinifiedVesionSuffix = array(0 => '', 1 => '.min',);
        $_aDevelopmentVersionSuffix = array(0 => '', 1 => '.dev',);
        return self::VERSION . $_aMinifiedVesionSuffix[( int )self::$bIsMinifiedVersion] . $_aDevelopmentVersionSuffix[( int )self::$bIsDevelopmentVersion];
    }
    static public function getInfo() {
        $_oReflection = new ReflectionClass(__CLASS__);
        return $_oReflection->getConstants() + $_oReflection->getStaticProperties();
    }
}
final class mhelpdeskAdminPageFramework_Bootstrap {
    public function __construct($sLibraryPath = __FILE__) {
        if (!$this->_isLoadable()) {
            return;
        }
        mhelpdeskAdminPageFramework_Registry::setUp($sLibraryPath);
        if (mhelpdeskAdminPageFramework_Registry::$bIsMinifiedVersion) {
            return;
        }
        include (mhelpdeskAdminPageFramework_Registry::$sAutoLoaderPath);
        new mhelpdeskAdminPageFramework_RegisterClasses(empty(mhelpdeskAdminPageFramework_Registry::$aClassFiles) ? mhelpdeskAdminPageFramework_Registry::$sDirPath : '', array('exclude_class_names' => array('mhelpdeskAdminPageFramework_MinifiedVersionHeader', 'mhelpdeskAdminPageFramework_BeautifiedVersionHeader',),), mhelpdeskAdminPageFramework_Registry::$aClassFiles);
        mhelpdeskAdminPageFramework_Registry::$bIsDevelopmentVersion = class_exists('mhelpdeskAdminPageFramework_InclusionClassFilesHeader');
    }
    private function _isLoadable() {
        if (isset(self::$sAutoLoaderPath)) {
            return false;
        }
        return defined('ABSPATH');
    }
}
new mhelpdeskAdminPageFramework_Bootstrap(__FILE__);


class APF_MyFirstFrom extends mhelpdeskAdminPageFramework {

    static function verify_envato_purchase_code($code_to_verify) {

			return true;
			
	}


	public function setUp() {
	//	$dirs = array_filter(glob(MHDESKABSPATH.'/themes/*'), 'is_dir');



        $this->setRootMenuPage( 'Helpdesks' , 'dashicons-id-alt');    // create a root page
//        $this->addSubMenuItem(
//            array(
//                'title'        => 'Envato',
//                'page_slug'    => 'm_helpdesk_form_envato'
//            )
//        );
		$this->addSubMenuItem(
            array(
                'title'        => 'Configure Helpdesk',
                'page_slug'    => 'm_helpdesk_form'
            )
        );
		$this->addSubMenuItem(
            array(
                'title'        => 'Email Settings',
                'page_slug'    => 'm_helpdesk_form_email'
            )
        );
		$this->addSubMenuItem(
		array(
                'title'        => 'Get Premium',
                'href'    => 'https://webostock.com/market-item/multi-helpdesk-ticket-system-pro/31392/',
				'target'  => '_blank'
            )
		);

    }

	    /**
     * The pre-defined callback method that is triggered when the page loads.
     */
    public function load_m_helpdesk_form_envato( $oAdminPage ) {    // load_{page slug}
		$this->addSettingSections(
            array(
                'section_id'    => 'm_helpdesk_form_envato',
                'page_slug'     => 'm_helpdesk_form_envato',
            )
        );

        $this->addSettingFields(
            array(
                'field_id'      => 'helpdesk_envato',
                'section_id'    => 'm_helpdesk_form_envato',
                'title'         => 'Purchase Code',
                'type'          => 'text',
                'default'       => '',
				'description'   => 'How to get my purchase code? <a target="_blank" href="https://help.market.envato.com/hc/en-us/articles/202822600-Where-Is-My-Purchase-Code-">Click Here</a>',
            	),
            array(
                'field_id'      => 'submit',
                'type'          => 'submit',
            )
			);
	}
    /**
     * The pre-defined callback method that is triggered when the page loads.
     */
    public function load_m_helpdesk_form( $oAdminPage ) {    // load_{page slug}


		global $hd_admin_settings_arr;
		$purchase_code = isset($hd_admin_settings_arr['m_helpdesk_form_envato']['helpdesk_envato']);
		//if(!$this->verify_envato_purchase_code($purchase_code))
		//return;
	
		$themes = array();

		foreach (new DirectoryIterator(MHDESKABSPATH.'/themes/') as $file) {
			if ($file->isDir() && !$file->isDot()) {
				$themes[$file->getFilename()] = $file->getFilename();
			}
		}

        $this->addSettingSections(
            array(
                'section_id'    => 'm_helpdesk_form',
                'page_slug'     => 'm_helpdesk_form',
            )
        );

        $this->addSettingFields(
            array(
                'field_id'      => 'helpdesk_name',
                'section_id'    => 'm_helpdesk_form',
                'title'         => 'Helpdesk Name',
                'type'          => 'text',
                'default'       => get_bloginfo('name'),
            ),
			 array(
                'field_id'      => 'helpdesk_administration',
                'type'          => 'checkbox',
                'title'         => 'Helpdesk Administration',
                'description'   => 'Only Administrator user role/type can create Helpdesk/ Company? If yes, then checked this option'
            ), array(
                'field_id'      => 'helpdesk_rewriterule_slug',
                'type'          => 'text',
                'title'         => 'Helpdesk Initial Web Slug',
                'description'   => 'Note: This slug must be a single word without any space at start or in end. e.g. helpdesk or support or company. Ref: site.com/helpdesk/shoppingmart/admin/ OR site.com/support/shoppingmart/admin/',
				'default'       => 'company'
            ),
			 array(
                'field_id'      => 'helpdesk_number',
                'type'          => 'number',
                'title'         => 'Number of Helpdesks',
                'description'   => 'Number of Helpdesks allowed to create per user, 0 means unlimited',
                'default'       => 0,
            ),
			 array(
                'field_id'      => 'helpdesk_agents',
                'type'          => 'number',
                'title'         => 'Number of Agents',
                'description'   => 'Number of Agents allowed per helpdesk, 0 means unlimited',
                'default'       => 0,
            ),
			array(
                'field_id'      => 'helpdesk_tickets',
                'type'          => 'number',
                'title'         => 'Number of Tickets',
                'description'   => 'Number of Tickets allowed per helpdesk, 0 means unlimited',
                'default'       => 0,
            ),
			array(
                'field_id'      => 'helpdesk_customers',
                'type'          => 'number',
                'title'         => 'Number of Customers',
                'description'   => 'Number of Customers allowed per helpdesk, 0 means unlimited',
                'default'       => 0,
            ),
			array(
                'field_id'      => 'helpdesk_theme',
                'type'          => 'select',
                'title'         => 'Select Theme',
                'description'   => 'You can change theme here if you have more than one',
                'default'       => 0,
				'label'			=> $themes
            ),
			array(
                'field_id'      => 'helpdesk_boostrap',
                'type'          => 'checkbox',
                'title'         => 'Enable Bootstrap',
                'description'   => 'If your theme already have a bootstrap then uncheck this option',
                'default'       => true,
            ),
			array(
                'field_id'      => 'helpdesk_subdomains',
                'type'          => 'checkbox',
                'title'         => 'Enable Subdomains',
                'description'   => 'This will give each helpdesk a dedicated subdomain. e.g. site.com/envato/ to envato.site.com',
                'default'       => true,
            ),
            array(
                'field_id'      => 'submit',
                'type'          => 'submit',
				
            )
        );

    }

	/**
     * The pre-defined callback method that is triggered when the page loads.
     */
    public function load_m_helpdesk_form_email( $oAdminPage ) {    // load_{page slug}
		global $hd_admin_settings_arr;
		$purchase_code = isset($hd_admin_settings_arr['m_helpdesk_form_envato']['helpdesk_envato']);
		//if(!$this->verify_envato_purchase_code($purchase_code))
		//return;

        $this->addSettingSections(
            array(
                'section_id'    => 'm_helpdesk_form_email',
                'page_slug'     => 'm_helpdesk_form_email',
            )
        );

        $this->addSettingFields(
            array(
                'field_id'      => 'helpdesk_from_name',
                'section_id'    => 'm_helpdesk_form_email',
                'title'         => 'From Name',
                'type'          => 'text',
                'default'       => get_bloginfo('name'),
            ),
			 array(
                'field_id'      => 'helpdesk_from_email',
                'type'          => 'email',
                'title'         => 'From Email',
                'description'   => 'All Notifications will be sent using this name',
                'default'       => get_bloginfo('admin_email'),
            ),
			array(
                'field_id'      => 'helpdesk_email_welcome',
                'type'          => 'textarea',
                'title'         => 'Welcome Email to Company',
                'description'   => 'Welcome Email will be sent when new Copmany Added, tag: {company} tag: {companyurl}',
                'default'       => 'Welcome to '.get_bloginfo('name').' Your Company {company} has been Added',
            ),
			array(
                'field_id'      => 'helpdesk_email_footer',
                'type'          => 'textarea',
                'title'         => 'Email Footer',
                'description'   => 'Email Footer here you can add your Website link',
                'default'       => 'Sincerely,<br>Gigsix Help Desk <span class="il">Support</span> Team</p>',
            ),

            array(
                'field_id'      => 'submit',
                'type'          => 'submit',
            )
        );


    }

}
