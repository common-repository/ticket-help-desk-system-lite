<?php
/**
 Admin Page Framework v3.5.12 by Michael Uno
 Generated by PHP Class Files Script Generator <https://github.com/michaeluno/PHP-Class-Files-Script-Generator>
 <http://en.michaeluno.jp/admin-page-framework>
 Copyright (c) 2013-2015, Michael Uno; Licensed under MIT <http://opensource.org/licenses/MIT>
 */
class mhelpdeskAdminPageFramework_WPUtility_Option extends mhelpdeskAdminPageFramework_WPUtility_File {
    static private $_bIsNetworkAdmin;
    static public function deleteTransient($sTransientKey) {
        global $_wp_using_ext_object_cache;
        $_bWpUsingExtObjectCacheTemp = $_wp_using_ext_object_cache;
        $_wp_using_ext_object_cache = false;
        self::$_bIsNetworkAdmin = isset(self::$_bIsNetworkAdmin) ? self::$_bIsNetworkAdmin : is_network_admin();
        $sTransientKey = self::_getCompatibleTransientKey($sTransientKey, self::$_bIsNetworkAdmin ? 40 : 45);
        $_vTransient = self::$_bIsNetworkAdmin ? delete_site_transient($sTransientKey) : delete_transient($sTransientKey);
        $_wp_using_ext_object_cache = $_bWpUsingExtObjectCacheTemp;
        return $_vTransient;
    }
    static public function getTransient($sTransientKey, $vDefault = null) {
        global $_wp_using_ext_object_cache;
        $_bWpUsingExtObjectCacheTemp = $_wp_using_ext_object_cache;
        $_wp_using_ext_object_cache = false;
        self::$_bIsNetworkAdmin = isset(self::$_bIsNetworkAdmin) ? self::$_bIsNetworkAdmin : is_network_admin();
        $sTransientKey = self::_getCompatibleTransientKey($sTransientKey, self::$_bIsNetworkAdmin ? 40 : 45);
        $_vTransient = self::$_bIsNetworkAdmin ? get_site_transient($sTransientKey) : get_transient($sTransientKey);
        $_wp_using_ext_object_cache = $_bWpUsingExtObjectCacheTemp;
        return null === $vDefault ? $_vTransient : (false === $_vTransient ? $vDefault : $_vTransient);
    }
    static public function setTransient($sTransientKey, $vValue, $iExpiration = 0) {
        global $_wp_using_ext_object_cache;
        $_bWpUsingExtObjectCacheTemp = $_wp_using_ext_object_cache;
        $_wp_using_ext_object_cache = false;
        self::$_bIsNetworkAdmin = isset(self::$_bIsNetworkAdmin) ? self::$_bIsNetworkAdmin : is_network_admin();
        $sTransientKey = self::_getCompatibleTransientKey($sTransientKey, self::$_bIsNetworkAdmin ? 40 : 45);
        $_bIsSet = self::$_bIsNetworkAdmin ? set_site_transient($sTransientKey, $vValue, $iExpiration) : set_transient($sTransientKey, $vValue, $iExpiration);
        $_wp_using_ext_object_cache = $_bWpUsingExtObjectCacheTemp;
        return $_bIsSet;
    }
    static public function _getCompatibleTransientKey($sSubject, $iAllowedCharacterLength = 45) {
        if (strlen($sSubject) <= $iAllowedCharacterLength) {
            return $sSubject;
        }
        $_iPrefixLengthToKeep = $iAllowedCharacterLength - 33;
        $_sPrefixToKeep = substr($sSubject, 0, $_iPrefixLengthToKeep - 1);
        return $_sPrefixToKeep . '_' . md5($sSubject);
    }
    static public function getOption($sOptionKey, $asKey = null, $vDefault = null, array $aAdditionalOptions = array()) {
        return self::_getOptionByFunctionName($sOptionKey, $asKey, $vDefault, $aAdditionalOptions);
    }
    static public function getSiteOption($sOptionKey, $asKey = null, $vDefault = null, array $aAdditionalOptions = array()) {
        return self::_getOptionByFunctionName($sOptionKey, $asKey, $vDefault, $aAdditionalOptions, 'get_site_option');
    }
    static private function _getOptionByFunctionName($sOptionKey, $asKey = null, $vDefault = null, array $aAdditionalOptions = array(), $sFunctionName = 'get_option') {
        if (!isset($asKey)) {
            $_aOptions = $sFunctionName($sOptionKey, isset($vDefault) ? $vDefault : array());;
            return empty($aAdditionalOptions) ? $_aOptions : self::uniteArrays($_aOptions, $aAdditionalOptions);
        }
        return self::getArrayValueByArrayKeys(self::uniteArrays(self::getAsArray($sFunctionName($sOptionKey, array()), true), $aAdditionalOptions), self::getAsArray($asKey, true), $vDefault);
    }
}