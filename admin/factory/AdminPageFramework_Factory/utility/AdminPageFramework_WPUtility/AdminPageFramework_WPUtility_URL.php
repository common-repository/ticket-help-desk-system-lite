<?php
/**
 Admin Page Framework v3.5.12 by Michael Uno
 Generated by PHP Class Files Script Generator <https://github.com/michaeluno/PHP-Class-Files-Script-Generator>
 <http://en.michaeluno.jp/admin-page-framework>
 Copyright (c) 2013-2015, Michael Uno; Licensed under MIT <http://opensource.org/licenses/MIT>
 */
class mhelpdeskAdminPageFramework_WPUtility_URL extends mhelpdeskAdminPageFramework_Utility {
    static public function getCurrentAdminURL() {
        $sRequestURI = $GLOBALS['is_IIS'] ? $_SERVER['PATH_INFO'] : $_SERVER["REQUEST_URI"];
        $sPageURL = 'on' == @$_SERVER["HTTPS"] ? "https://" : "http://";
        if ("80" != $_SERVER["SERVER_PORT"]) {
            $sPageURL.= $_SERVER["SERVER_NAME"] . ":" . $_SERVER["SERVER_PORT"] . $sRequestURI;
        } else {
            $sPageURL.= $_SERVER["SERVER_NAME"] . $sRequestURI;
        }
        return $sPageURL;
    }
    static public function getQueryAdminURL($aAddingQueries = array(), $aRemovingQueryKeys = array(), $sSubjectURL = '') {
        $_sAdminURL = is_network_admin() ? network_admin_url(mhelpdeskAdminPageFramework_WPUtility_Page::getPageNow()) : admin_url(mhelpdeskAdminPageFramework_WPUtility_Page::getPageNow());
        $sSubjectURL = $sSubjectURL ? $sSubjectURL : add_query_arg($_GET, $_sAdminURL);
        return self::getQueryURL($aAddingQueries, $aRemovingQueryKeys, $sSubjectURL);
    }
    static public function getQueryURL($aAddingQueries, $aRemovingQueryKeys, $sSubjectURL) {
        $sSubjectURL = empty($aRemovingQueryKeys) ? $sSubjectURL : remove_query_arg(( array )$aRemovingQueryKeys, $sSubjectURL);
        $sSubjectURL = add_query_arg($aAddingQueries, $sSubjectURL);
        return $sSubjectURL;
    }
    static public function getSRCFromPath($sFilePath) {
        $oWPStyles = new WP_Styles();
        $sRelativePath = mhelpdeskAdminPageFramework_Utility::getRelativePath(ABSPATH, $sFilePath);
        $sRelativePath = preg_replace("/^\.[\/\\\]/", '', $sRelativePath, 1);
        $sHref = trailingslashit($oWPStyles->base_url) . $sRelativePath;
        unset($oWPStyles);
        return esc_url($sHref);
    }
    static public function resolveSRC($sSRC, $bReturnNullIfNotExist = false) {
        if (!$sSRC) {
            return $bReturnNullIfNotExist ? null : $sSRC;
        }
        if (filter_var($sSRC, FILTER_VALIDATE_URL)) {
            return esc_url($sSRC);
        }
        if (file_exists(realpath($sSRC))) {
            return self::getSRCFromPath($sSRC);
        }
        if ($bReturnNullIfNotExist) {
            return null;
        }
        return $sSRC;
    }
}