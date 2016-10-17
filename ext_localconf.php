<?php

\TYPO3\CMS\Extbase\Utility\ExtensionUtility::configurePlugin(
    'ApacheSolrForTypo3.solrfluid',
    'pi_result',
    array(
        'Search' => 'results,form,detail'
    ),
    array(
        'Search' => 'results'
    )
);

\TYPO3\CMS\Extbase\Utility\ExtensionUtility::configurePlugin(
    'ApacheSolrForTypo3.solrfluid',
    'pi_search',
    array(
        'Search' => 'form'
    ),
    array()
);

\TYPO3\CMS\Extbase\Utility\ExtensionUtility::configurePlugin(
    'ApacheSolrForTypo3.solrfluid',
    'pi_frequentlySearched',
    array(
        'Search' => 'frequentlySearched'
    ),
    array(
        'Search' => 'frequentlySearched'
    )
);

$GLOBALS['TYPO3_CONF_VARS']['EXTCONF']['solr']['searchResultClassName '] = 'ApacheSolrForTypo3\\Solrfluid\\Domain\\Search\\ResultSet\\SearchResult';
$GLOBALS['TYPO3_CONF_VARS']['EXTCONF']['solr']['searchResultSetClassName '] = 'ApacheSolrForTypo3\\Solrfluid\\Domain\\Search\\ResultSet\\SearchResultSet';
$GLOBALS['TYPO3_CONF_VARS']['EXTCONF']['solr']['afterSearch'][] = 'ApacheSolrForTypo3\\Solrfluid\\Domain\\Search\\ResultSet\\ResultSetReconstitutionProcessor';
