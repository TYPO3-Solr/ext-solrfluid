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
    'Configuration/TypoScript/Examples/QueryGroup/',
    'Search - (Example) Fluid queryGroup facet on the created field'
);

\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addStaticFile(
    'solrfluid',
    'Configuration/TypoScript/Examples/Hierarchy/',
    'Search - (Example) Fluid hierarchy facet on the rootline field'
);

\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addStaticFile(
    'solrfluid',
    'Configuration/TypoScript/Examples/DateRange/',
    'Search - (Example) Fluid dateRange facet with jquery ui datepicker on created field'
);

\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addStaticFile(
    'solrfluid',
    'Configuration/TypoScript/Examples/NumericRange/',
    'Search - (Example) Fluid numericRange facet with jquery ui slider on pid field'
);

