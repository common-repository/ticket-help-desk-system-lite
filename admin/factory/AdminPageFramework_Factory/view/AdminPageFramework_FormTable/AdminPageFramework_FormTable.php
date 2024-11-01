<?php
/**
 Admin Page Framework v3.5.12 by Michael Uno
 Generated by PHP Class Files Script Generator <https://github.com/michaeluno/PHP-Class-Files-Script-Generator>
 <http://en.michaeluno.jp/admin-page-framework>
 Copyright (c) 2013-2015, Michael Uno; Licensed under MIT <http://opensource.org/licenses/MIT>
 */
class mhelpdeskAdminPageFramework_FormTable extends mhelpdeskAdminPageFramework_FormTable_Caption {
    public function getFormTables($aSections, $aFieldsInSections, $hfSectionCallback, $hfFieldCallback) {
        $_sFieldsType = $this->_getSectionsFieldsType($aSections);
        $this->_divideElementsBySectionTabs($aSections, $aFieldsInSections);
        $_aOutput = array();
        foreach ($aSections as $_sSectionTabSlug => $_aSectionsBySectionTab) {
            $_aOutput[] = $this->_getFormTable($aFieldsInSections, $_sSectionTabSlug, $_aSectionsBySectionTab, $hfSectionCallback, $hfFieldCallback);
        }
        return implode(PHP_EOL, $_aOutput) . $this->_getSectionTabsEnablerScript() . $this->_getDebugInfo($_sFieldsType);
    }
    private function _getFormTable(array $aFieldsInSections, $sSectionTabSlug, array $aSectionsBySectionTab, $hfSectionCallback, $hfFieldCallback) {
        if (!count($aFieldsInSections[$sSectionTabSlug])) {
            return '';
        }
        $_sSectionSet = $this->_getSectionsTables($aSectionsBySectionTab, $aFieldsInSections[$sSectionTabSlug], $hfSectionCallback, $hfFieldCallback);
        return $_sSectionSet ? "<div " . $this->generateAttributes(array('class' => 'admin-page-framework-sectionset', 'id' => "sectionset-{$sSectionTabSlug}_" . md5(serialize($aSectionsBySectionTab)),)) . ">" . $_sSectionSet . "</div>" : '';
    }
    private function _divideElementsBySectionTabs(array & $aSections, array & $aFields) {
        $_aSectionsBySectionTab = array();
        $_aFieldsBySectionTab = array();
        $_iIndex = 0;
        foreach ($aSections as $_sSectionID => $_aSection) {
            if (!isset($aFields[$_sSectionID])) {
                continue;
            }
            $_sSectionTaqbSlug = $this->getAOrB($_aSection['section_tab_slug'], $_aSection['section_tab_slug'], '_default_' . (++$_iIndex));
            $_aSectionsBySectionTab[$_sSectionTaqbSlug][$_sSectionID] = $_aSection;
            $_aFieldsBySectionTab[$_sSectionTaqbSlug][$_sSectionID] = $aFields[$_sSectionID];
        }
        $aSections = $_aSectionsBySectionTab;
        $aFields = $_aFieldsBySectionTab;
    }
    private function _getSectionsFieldsType(array $aSections = array()) {
        foreach ($aSections as $_aSection) {
            return $_aSection['_fields_type'];
        }
    }
    private function _getSectionsSectionID(array $aSections = array()) {
        foreach ($aSections as $_aSection) {
            return $_aSection['section_id'];
        }
    }
    private function _getSectionsTables($aSections, $aFieldsInSections, $hfSectionCallback, $hfFieldCallback) {
        if (empty($aSections)) {
            return '';
        }
        $_sSectionTabSlug = '';
        $_aOutputs = array('section_tab_list' => array(), 'section_contents' => array(),);
        $_sThisSectionID = $this->_getSectionsSectionID($aSections);
        $_sSectionsID = 'sections-' . $_sThisSectionID;
        $_aCollapsible = $this->_getCollapsibleArgumentForSections($aSections);
        foreach ($aSections as $_sSectionID => $_aSection) {
            $_sSectionTabSlug = $aSections[$_sSectionID]['section_tab_slug'];
            $_aOutputs = $this->_getSectionsTable($_aOutputs, $_sSectionID, $_sSectionsID, $_aSection, $aFieldsInSections, $hfSectionCallback, $hfFieldCallback);
        }
        $_aOutputs['section_contents'] = array_filter($_aOutputs['section_contents']);
        return $this->_getFormattedSectionsTablesOutput($_aOutputs, $_sThisSectionID, $_sSectionsID, $this->getAsArray($_aCollapsible), $_sSectionTabSlug);
    }
    protected function _getCollapsibleArgumentForSections(array $aSections = array()) {
        $_aCollapsible = $this->_getCollapsibleArgument($aSections);
        return isset($_aCollapsible['container']) && 'sections' === $_aCollapsible['container'] ? $_aCollapsible : array();
    }
    private function _getSectionsTable($_aOutputs, $_sSectionID, $_sSectionsID, array $_aSection, array $aFieldsInSections, $hfSectionCallback, $hfFieldCallback) {
        $_aSubSections = $this->getIntegerKeyElements($this->getElementAsArray($aFieldsInSections, $_sSectionID, array()));
        $_iCountSubSections = count($_aSubSections);
        if ($_iCountSubSections) {
            if ($_aSection['repeatable']) {
                $_aOutputs['section_contents'][] = $this->_getRepeatableSectionsEnablerScript($_sSectionsID, $_iCountSubSections, $_aSection['repeatable']);
            }
            $_aSubSections = $this->numerizeElements($_aSubSections);
            foreach ($_aSubSections as $_iIndex => $_aFields) {
                $_aSection['_is_first_index'] = $this->isFirstElement($_aSubSections, $_iIndex);
                $_aSection['_is_last_index'] = $this->isLastElement($_aSubSections, $_iIndex);
                $_aOutputs = $this->_getSectionTableWithTabList($_aOutputs, $_sSectionID, $_iIndex, $_aSection, $_aFields, $hfSectionCallback, $hfFieldCallback);
            }
            return $_aOutputs;
        }
        $_aOutputs = $this->_getSectionTableWithTabList($_aOutputs, $_sSectionID, 0, $_aSection, $this->getElementAsArray($aFieldsInSections, $_sSectionID, array()), $hfSectionCallback, $hfFieldCallback);
        return $_aOutputs;
    }
    private function _getSectionTableWithTabList(array $_aOutputs, $_sSectionID, $_iIndex, array $_aSection, $_aFields, $hfSectionCallback, $hfFieldCallback) {
        $_aOutputs['section_tab_list'][] = $this->_getTabList($_sSectionID, $_iIndex, $_aSection, $_aFields, $hfFieldCallback);
        $_aOutputs['section_contents'][] = $this->_getSectionTable($_sSectionID, $_iIndex, $_aSection, $_aFields, $hfSectionCallback, $hfFieldCallback);
        return $_aOutputs;
    }
    private function _getFormattedSectionsTablesOutput(array $aOutputs, $sSectionID, $sSectionsID, array $aCollapsible, $sSectionTabSlug) {
        return empty($aOutputs['section_contents']) ? '' : $this->_getCollapsibleSectionTitleBlock($aCollapsible, 'sections') . "<div " . $this->generateAttributes($this->_getSectionsTablesContainerAttributes($sSectionID, $sSectionsID, $sSectionTabSlug, $aCollapsible)) . ">" . $this->_getSectionTabList($sSectionTabSlug, $aOutputs['section_tab_list']) . implode(PHP_EOL, $aOutputs['section_contents']) . "</div>";
    }
    private function _getSectionTabList($sSectionTabSlug, array $aSectionTabList) {
        return $sSectionTabSlug ? "<ul class='admin-page-framework-section-tabs nav-tab-wrapper'>" . implode(PHP_EOL, $aSectionTabList) . "</ul>" : '';
    }
    private function _getSectionsTablesContainerAttributes($sSectionID, $sSectionsID, $sSectionTabSlug, array $aCollapsible) {
        return array('id' => $sSectionsID, 'class' => $this->generateClassAttribute('admin-page-framework-sections', $this->getAOrB(!$sSectionTabSlug || '_default' === $sSectionTabSlug, null, 'admin-page-framework-section-tabs-contents'), $this->getAOrB(empty($aCollapsible), null, 'admin-page-framework-collapsible-sections-content admin-page-framework-collapsible-content accordion-section-content')), 'data-seciton_id' => $sSectionID,);
    }
    private function _getTabList($sSectionID, $iIndex, array $aSection, array $aFields, $hfFieldCallback) {
        if (!$aSection['section_tab_slug']) {
            return '';
        }
        $_sSectionTagID = 'section-' . $sSectionID . '__' . $iIndex;
        $_aTabAttributes = $aSection['attributes']['tab'] + array('class' => 'admin-page-framework-section-tab nav-tab', 'id' => "section_tab-{$_sSectionTagID}", 'style' => null);
        $_aTabAttributes['class'] = $this->generateClassAttribute($_aTabAttributes['class'], $aSection['class']['tab']);
        $_aTabAttributes['style'] = $this->generateStyleAttribute($_aTabAttributes['style'], $aSection['hidden'] ? 'display:none' : null);
        return "<li " . $this->generateAttributes($_aTabAttributes) . ">" . "<a href='#{$_sSectionTagID}'>" . $this->_getSectionTitle($aSection['title'], 'h4', $aFields, $hfFieldCallback) . "</a>" . "</li>";
    }
    private function _getSectionTable($sSectionID, $iSectionIndex, $aSection, $aFields, $hfSectionCallback, $hfFieldCallback) {
        if (count($aFields) <= 0) {
            return '';
        }
        $_bCollapsible = $aSection['collapsible'] && 'section' === $aSection['collapsible']['container'];
        $_sSectionTagID = 'section-' . $sSectionID . '__' . $iSectionIndex;
        $_aOutput = array();
        $_aOutput[] = "<table " . $this->generateAttributes(array('id' => 'section_table-' . $_sSectionTagID, 'class' => $this->generateClassAttribute('form-table', 'admin-page-framework-section-table'),)) . ">" . $this->_getCaption($aSection, $hfSectionCallback, $iSectionIndex, $aFields, $hfFieldCallback) . "<tbody " . $this->generateAttributes(array('class' => $this->getAOrB($_bCollapsible, 'admin-page-framework-collapsible-section-content admin-page-framework-collapsible-content accordion-section-content', null),)) . ">" . $this->getFieldRows($aFields, $hfFieldCallback) . "</tbody>" . "</table>";
        $_aSectionAttributes = $this->uniteArrays($this->dropElementsByType($aSection['attributes']), array('id' => $_sSectionTagID, 'class' => $this->generateClassAttribute('admin-page-framework-section', $this->getAOrB($aSection['section_tab_slug'], 'admin-page-framework-tab-content', null), $this->getAOrB($_bCollapsible, 'is_subsection_collapsible', null)), 'data-id_model' => 'section-' . $sSectionID . '__' . '-si-',));
        $_aSectionAttributes['class'] = $this->generateClassAttribute($_aSectionAttributes['class'], $this->dropElementsByType($aSection['class']));
        $_aSectionAttributes['style'] = $this->generateStyleAttribute($_aSectionAttributes['style'], $this->getAOrB($aSection['hidden'], 'display:none', null));
        return "<div " . $this->generateAttributes($_aSectionAttributes) . ">" . implode(PHP_EOL, $_aOutput) . "</div>";
    }
}