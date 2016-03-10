<?php
if (!defined('TYPO3_MODE')) {
    die('Access denied.');
}

// Add TypoScript template
\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addStaticFile(
    'solrfluid',
    'Configuration/TypoScript/FluidRendering/',
    'Apache Solr - Fluid rendering (include after Default Configuration)s'
);
