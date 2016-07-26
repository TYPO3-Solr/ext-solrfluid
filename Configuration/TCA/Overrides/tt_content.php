<?php

if (!defined ('TYPO3_MODE')) {
    die ('Access denied.');
}

// Register the plugins
$pluginSignature = 'solrfluid_pi_form';
\TYPO3\CMS\Extbase\Utility\ExtensionUtility::registerPlugin(
    'solrfluid',
    'pi_search',
    'LLL:EXT:solrfluid/Resources/Private/Language/locallang_db.xlf:tt_content.list_type_pi_search'
);
$GLOBALS['TCA']['tt_content']['types']['list']['subtypes_excludelist'][$pluginSignature]
    = 'layout,select_key,pages,recursive';


$pluginSignature = 'solrfluid_pi_frequentlysearched';
\TYPO3\CMS\Extbase\Utility\ExtensionUtility::registerPlugin(
    'solrfluid',
    'pi_frequentlySearched',
    'LLL:EXT:solrfluid/Resources/Private/Language/locallang_db.xlf:tt_content.list_type_pi_frequentsearches'
);
$GLOBALS['TCA']['tt_content']['types']['list']['subtypes_excludelist'][$pluginSignature]
    = 'layout,select_key,pages,recursive';


$pluginSignature = 'solrfluid_pi_result';
\TYPO3\CMS\Extbase\Utility\ExtensionUtility::registerPlugin(
    'solrfluid',
    'pi_result',
    'LLL:EXT:solrfluid/Resources/Private/Language/locallang_db.xlf:tt_content.list_type_pi_results'
);
$GLOBALS['TCA']['tt_content']['types']['list']['subtypes_excludelist'][$pluginSignature]
    = 'layout,select_key,pages,recursive';
$GLOBALS['TCA']['tt_content']['types']['list']['subtypes_addlist'][$pluginSignature]
    = 'pi_flexform';
\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addPiFlexFormValue(
    $pluginSignature,
    'FILE:EXT:solrfluid/Configuration/FlexForms/Results.xml'
);