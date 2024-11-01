<?php
/**
 Admin Page Framework v3.5.12 by Michael Uno
 Generated by PHP Class Files Script Generator <https://github.com/michaeluno/PHP-Class-Files-Script-Generator>
 <http://en.michaeluno.jp/admin-page-framework>
 Copyright (c) 2013-2015, Michael Uno; Licensed under MIT <http://opensource.org/licenses/MIT>
 */
abstract class mhelpdeskAdminPageFramework_Utility_Array extends mhelpdeskAdminPageFramework_Utility_String {
    static public function getElement($aSubject, $aisKey, $mDefault = null, $asToDefault = array(null)) {
        $_aToDefault = is_null($asToDefault) ? array(null) : self::getAsArray($asToDefault, true);
        $_mValue = self::getArrayValueByArrayKeys($aSubject, self::getAsArray($aisKey, true), $mDefault);
        return in_array($_mValue, $_aToDefault, true) ? $mDefault : $_mValue;
    }
    static public function getElementAsArray($aSubject, $aisKey, $mDefault = null, $asToDefault = array(null)) {
        return self::getAsArray(self::getElement($aSubject, $aisKey, $mDefault, $asToDefault), true);
    }
    public static function castArrayContents(array $aModel, array $aSubject) {
        $_aCast = array();
        foreach ($aModel as $_isKey => $_v) {
            $_aCast[$_isKey] = self::getElement($aSubject, $_isKey, null);
        }
        return $_aCast;
    }
    public static function invertCastArrayContents(array $aModel, array $aSubject) {
        $_aInvert = array();
        foreach ($aModel as $_isKey => $_v) {
            if (array_key_exists($_isKey, $aSubject)) {
                continue;
            }
            $_aInvert[$_isKey] = $_v;
        }
        return $_aInvert;
    }
    public static function uniteArrays() {
        $_aArray = array();
        foreach (array_reverse(func_get_args()) as $_aArg) {
            $_aArray = self::uniteArraysRecursive(self::getAsArray($_aArg), $_aArray);
        }
        return $_aArray;
    }
    public static function uniteArraysRecursive($aPrecedence, $aDefault) {
        if (is_null($aPrecedence)) {
            $aPrecedence = array();
        }
        if (!is_array($aDefault) || !is_array($aPrecedence)) {
            return $aPrecedence;
        }
        foreach ($aDefault as $sKey => $v) {
            if (!array_key_exists($sKey, $aPrecedence) || is_null($aPrecedence[$sKey])) {
                $aPrecedence[$sKey] = $v;
            } else {
                if (is_array($aPrecedence[$sKey]) && is_array($v)) {
                    $aPrecedence[$sKey] = self::uniteArraysRecursive($aPrecedence[$sKey], $v);
                }
            }
        }
        return $aPrecedence;
    }
    static public function isLastElement(array $aArray, $sKey) {
        end($aArray);
        return $sKey === key($aArray);
    }
    static public function isFirstElement(array $aArray, $sKey) {
        reset($aArray);
        return $sKey === key($aArray);
    }
    static public function getIntegerKeyElements(array $aParse) {
        foreach ($aParse as $_isKey => $_v) {
            if (!is_numeric($_isKey)) {
                unset($aParse[$_isKey]);
                continue;
            }
            $_isKey = $_isKey + 0;
            if (!is_int($_isKey)) {
                unset($aParse[$_isKey]);
            }
        }
        return $aParse;
    }
    static public function getNonIntegerKeyElements(array $aParse) {
        foreach ($aParse as $_isKey => $_v) {
            if (is_numeric($_isKey) && is_int($_isKey + 0)) {
                unset($aParse[$_isKey]);
            }
        }
        return $aParse;
    }
    static public function numerizeElements(array $aSubject) {
        $_aNumeric = self::getIntegerKeyElements($aSubject);
        $_aAssociative = self::invertCastArrayContents($aSubject, $_aNumeric);
        foreach ($_aNumeric as & $_aElem) {
            $_aElem = self::uniteArrays($_aElem, $_aAssociative);
        }
        if (!empty($_aAssociative)) {
            array_unshift($_aNumeric, $_aAssociative);
        }
        return $_aNumeric;
    }
    static public function getArrayValueByArrayKeys($aArray, $aKeys, $vDefault = null) {
        $_sKey = array_shift($aKeys);
        if (isset($aArray[$_sKey])) {
            if (empty($aKeys)) {
                return $aArray[$_sKey];
            }
            if (is_array($aArray[$_sKey])) {
                return self::getArrayValueByArrayKeys($aArray[$_sKey], $aKeys, $vDefault);
            }
            return $aArray[$_sKey];
        }
        return $vDefault;
    }
    static public function unsetDimensionalArrayElement(&$mSubject, array $aKeys) {
        $_sKey = array_shift($aKeys);
        if (!empty($aKeys)) {
            if (isset($mSubject[$_sKey]) && is_array($mSubject[$_sKey])) {
                self::unsetDimensionalArrayElement($mSubject[$_sKey], $aKeys);
            }
            return;
        }
        if (is_array($mSubject)) {
            unset($mSubject[$_sKey]);
        }
    }
    static public function setMultiDimensionalArray(&$mSubject, array $aKeys, $mValue) {
        $_sKey = array_shift($aKeys);
        if (!empty($aKeys)) {
            if (!isset($mSubject[$_sKey]) || !is_array($mSubject[$_sKey])) {
                $mSubject[$_sKey] = array();
            }
            self::setMultiDimensionalArray($mSubject[$_sKey], $aKeys, $mValue);
            return;
        }
        $mSubject[$_sKey] = $mValue;
    }
    static public function getAsArray($mValue, $bPreserveEmpty = false) {
        if (is_array($mValue)) {
            return $mValue;
        }
        if ($bPreserveEmpty) {
            return ( array )$mValue;
        }
        if (empty($mValue)) {
            return array();
        }
        return ( array )$mValue;
    }
    static public function getReadableListOfArray(array $aArray) {
        $_aOutput = array();
        foreach ($aArray as $_sKey => $_vValue) {
            $_aOutput[] = self::getReadableArrayContents($_sKey, $_vValue, 32) . PHP_EOL;
        }
        return implode(PHP_EOL, $_aOutput);
    }
    static public function getReadableArrayContents($sKey, $vValue, $sLabelCharLengths = 16, $iOffset = 0) {
        $_aOutput = array();
        $_aOutput[] = ($iOffset ? str_pad(' ', $iOffset) : '') . ($sKey ? '[' . $sKey . ']' : '');
        if (!in_array(gettype($vValue), array('array', 'object'))) {
            $_aOutput[] = $vValue;
            return implode(PHP_EOL, $_aOutput);
        }
        foreach ($vValue as $_sTitle => $_asDescription) {
            if (!in_array(gettype($_asDescription), array('array', 'object'))) {
                $_aOutput[] = str_pad(' ', $iOffset) . $_sTitle . str_pad(':', $sLabelCharLengths - self::getStringLength($_sTitle)) . $_asDescription;
                continue;
            }
            $_aOutput[] = str_pad(' ', $iOffset) . $_sTitle . ": {" . self::getReadableArrayContents('', $_asDescription, 16, $iOffset + 4) . PHP_EOL . str_pad(' ', $iOffset) . "}";
        }
        return implode(PHP_EOL, $_aOutput);
    }
    static public function getReadableListOfArrayAsHTML(array $aArray) {
        $_aOutput = array();
        foreach ($aArray as $_sKey => $_vValue) {
            $_aOutput[] = "<ul class='array-contents'>" . self::getReadableArrayContentsHTML($_sKey, $_vValue) . "</ul>" . PHP_EOL;
        }
        return implode(PHP_EOL, $_aOutput);
    }
    static public function getReadableArrayContentsHTML($sKey, $vValue) {
        $_aOutput = array();
        $_aOutput[] = $sKey ? "<h3 class='array-key'>" . $sKey . "</h3>" : "";
        if (!in_array(gettype($vValue), array('array', 'object'))) {
            $_aOutput[] = "<div class='array-value'>" . html_entity_decode(nl2br(str_replace(' ', '&nbsp;', $vValue)), ENT_QUOTES) . "</div>";
            return "<li>" . implode(PHP_EOL, $_aOutput) . "</li>";
        }
        foreach ($vValue as $_sKey => $_vValue) {
            $_aOutput[] = "<ul class='array-contents'>" . self::getReadableArrayContentsHTML($_sKey, $_vValue) . "</ul>";
        }
        return implode(PHP_EOL, $_aOutput);
    }
    static public function dropElementsByType(array $aArray, $aTypes = array('array')) {
        foreach ($aArray as $isKey => $vValue) {
            if (in_array(gettype($vValue), $aTypes)) {
                unset($aArray[$isKey]);
            }
        }
        return $aArray;
    }
    static public function dropElementByValue(array $aArray, $vValue) {
        foreach (self::getAsArray($vValue, true) as $_vValue) {
            $_sKey = array_search($_vValue, $aArray, true);
            if ($_sKey === false) {
                continue;
            }
            unset($aArray[$_sKey]);
        }
        return $aArray;
    }
    static public function dropElementsByKey(array $aArray, $asKeys) {
        foreach (self::getAsArray($asKeys, true) as $_isKey) {
            unset($aArray[$_isKey]);
        }
        return $aArray;
    }
    static public function getArrayElementsByKeys(array $aSubject, array $aKeys) {
        return array_intersect_key($aSubject, array_flip($aKeys));
    }
}