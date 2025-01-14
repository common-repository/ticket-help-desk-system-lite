<?php
/**
 Admin Page Framework v3.5.12 by Michael Uno
 Generated by PHP Class Files Script Generator <https://github.com/michaeluno/PHP-Class-Files-Script-Generator>
 <http://en.michaeluno.jp/admin-page-framework>
 Copyright (c) 2013-2015, Michael Uno; Licensed under MIT <http://opensource.org/licenses/MIT>
 */
class mhelpdeskAdminPageFramework_FieldType_submit extends mhelpdeskAdminPageFramework_FieldType {
    public $aFieldTypeSlugs = array('submit',);
    protected $aDefaultKeys = array('redirect_url' => null, 'href' => null, 'reset' => null, 'email' => null, 'attributes' => array('class' => 'button button-primary',),);
    protected function getStyles() {
        return <<<CSSRULES
/* Submit Buttons */
.admin-page-framework-field input[type='submit'] {
    margin-bottom: 0.5em;
}
CSSRULES;
        
    }
    protected function getField($aField) {
        $aField = $this->_getFormatedFieldArray($aField);
        $_aInputAttributes = $this->_getInputAttributes($aField);
        $_aLabelAttributes = $this->_getLabelAttributes($aField, $_aInputAttributes);
        $_aLabelContainerAttributes = $this->_getLabelContainerAttributes($aField);
        return $aField['before_label'] . "<div " . $this->generateAttributes($_aLabelContainerAttributes) . ">" . $this->_getExtraFieldsBeforeLabel($aField) . "<label " . $this->generateAttributes($_aLabelAttributes) . ">" . $aField['before_input'] . $this->_getExtraInputFields($aField) . "<input " . $this->generateAttributes($_aInputAttributes) . " />" . $aField['after_input'] . "</label>" . "</div>" . $aField['after_label'];
    }
    private function _getFormatedFieldArray(array $aField) {
        $aField['label'] = $aField['label'] ? $aField['label'] : $this->oMsg->get('submit');
        if (isset($aField['attributes']['src'])) {
            $aField['attributes']['src'] = $this->resolveSRC($aField['attributes']['src']);
        }
        return $aField;
    }
    private function _getLabelAttributes(array $aField, array $aInputAttributes) {
        return array('style' => $aField['label_min_width'] ? "min-width:" . $this->sanitizeLength($aField['label_min_width']) . ";" : null, 'for' => $aInputAttributes['id'], 'class' => $aInputAttributes['disabled'] ? 'disabled' : null,);
    }
    private function _getLabelContainerAttributes(array $aField) {
        return array('style' => $aField['label_min_width'] ? "min-width:" . $this->sanitizeLength($aField['label_min_width']) . ";" : null, 'class' => 'admin-page-framework-input-label-container' . ' admin-page-framework-input-button-container' . ' admin-page-framework-input-container',);
    }
    private function _getInputAttributes(array $aField) {
        $_bIsImageButton = isset($aField['attributes']['src']) && filter_var($aField['attributes']['src'], FILTER_VALIDATE_URL);
        $_sValue = $this->_getInputFieldValueFromLabel($aField);
        return array('type' => $_bIsImageButton ? 'image' : 'submit', 'value' => $_sValue,) + $aField['attributes'] + array('title' => $_sValue, 'alt' => $_bIsImageButton ? 'submit' : '',);
    }
    protected function _getExtraFieldsBeforeLabel(&$aField) {
        return '';
    }
    protected function _getExtraInputFields(&$aField) {
        $_aOutput = array();
        $_aOutput[] = $this->generateHTMLTag('input', array('type' => 'hidden', 'name' => "__submit[{$aField['input_id']}][input_id]", 'value' => $aField['input_id'],));
        $_aOutput[] = $this->generateHTMLTag('input', array('type' => 'hidden', 'name' => "__submit[{$aField['input_id']}][field_id]", 'value' => $aField['field_id'],));
        $_aOutput[] = $this->generateHTMLTag('input', array('type' => 'hidden', 'name' => "__submit[{$aField['input_id']}][name]", 'value' => $aField['_input_name_flat'],));
        $_aOutput[] = $this->_getHiddenInput_SectionID($aField);
        $_aOutput[] = $this->_getHiddenInputByKey($aField, 'redirect_url');
        $_aOutput[] = $this->_getHiddenInputByKey($aField, 'href');
        $_aOutput[] = $this->_getHiddenInput_Reset($aField);
        $_aOutput[] = $this->_getHiddenInput_Email($aField);
        return implode(PHP_EOL, array_filter($_aOutput));
    }
    private function _getHiddenInput_SectionID(array $aField) {
        return $this->generateHTMLTag('input', array('type' => 'hidden', 'name' => "__submit[{$aField['input_id']}][section_id]", 'value' => isset($aField['section_id']) && '_default' !== $aField['section_id'] ? $aField['section_id'] : '',));
    }
    private function _getHiddenInputByKey(array $aField, $sKey) {
        return isset($aField[$sKey]) ? $this->generateHTMLTag('input', array('type' => 'hidden', 'name' => "__submit[{$aField['input_id']}][{$sKey}]", 'value' => $aField[$sKey],)) : '';
    }
    private function _getHiddenInput_Reset(array $aField) {
        if (!$aField['reset']) {
            return '';
        }
        return !$this->_checkConfirmationDisplayed($aField['_input_name_flat'], 'reset') ? $this->generateHTMLTag('input', array('type' => 'hidden', 'name' => "__submit[{$aField['input_id']}][is_reset]", 'value' => '1',)) : $this->generateHTMLTag('input', array('type' => 'hidden', 'name' => "__submit[{$aField['input_id']}][reset_key]", 'value' => is_array($aField['reset']) ? implode('|', $aField['reset']) : $aField['reset'],));
    }
    private function _getHiddenInput_Email(array $aField) {
        if (empty($aField['email'])) {
            return '';
        }
        $this->setTransient('apf_em_' . md5($aField['_input_name_flat'] . get_current_user_id()), $aField['email']);
        return !$this->_checkConfirmationDisplayed($aField['_input_name_flat'], 'email') ? $this->generateHTMLTag('input', array('type' => 'hidden', 'name' => "__submit[{$aField['input_id']}][confirming_sending_email]", 'value' => '1',)) : $this->generateHTMLTag('input', array('type' => 'hidden', 'name' => "__submit[{$aField['input_id']}][confirmed_sending_email]", 'value' => '1',));
    }
    private function _checkConfirmationDisplayed($sFlatFieldName, $sType = 'reset') {
        switch ($sType) {
            default:
            case 'reset':
                $_sTransientKey = 'apf_rc_' . md5($sFlatFieldName . get_current_user_id());
            break;
            case 'email':
                $_sTransientKey = 'apf_ec_' . md5($sFlatFieldName . get_current_user_id());
            break;
        }
        $_bConfirmed = false === $this->getTransient($_sTransientKey) ? false : true;
        if ($_bConfirmed) {
            $this->deleteTransient($_sTransientKey);
        }
        return $_bConfirmed;
    }
    protected function _getInputFieldValueFromLabel($aField) {
        if (isset($aField['value']) && $aField['value'] != '') {
            return $aField['value'];
        }
        if (isset($aField['label'])) {
            return $aField['label'];
        }
        if (isset($aField['default'])) {
            return $aField['default'];
        }
    }
}