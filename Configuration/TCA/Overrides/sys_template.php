<?php
if (!defined('TYPO3_MODE')) {
    die('Access denied.');
}

// Add TypoScript templates
\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addStaticFile(
    'solrfluid',
    'Configuration/TypoScript/FluidRendering/',
    'Search - Fluid rendering (include after Default Configuration)'
);
\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addStaticFile(
    'solrfluid',
    'Configuration/TypoScript/Examples/Suggest/',
    'Search - (Example) Fluid suggest/autocomplete'
);
\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addStaticFile(
    'solrfluid',
    'Configuration/TypoScript/Examples/DateRange/',
    'Search - (Example) Fluid dateRange facet with jquery ui'
);