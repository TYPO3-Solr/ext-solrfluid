<?php

\TYPO3\CMS\Extbase\Utility\ExtensionUtility::configurePlugin(
    'ApacheSolrForTypo3.solrfluid',
    'pi_result',
    array(
        'Search' => 'results,form'
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
    array(
        'Search' => 'form'
    )
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

// registering facet types
\ApacheSolrForTypo3\Solr\Facet\FacetRendererFactory::registerFacetType(
    'fluid',
    'ApacheSolrForTypo3\\Solrfluid\\FluidFacetRenderer'
);
\ApacheSolrForTypo3\Solr\Facet\FacetRendererFactory::registerFacetType(
    'fluidQueryGroup',
    'ApacheSolrForTypo3\\Solrfluid\\FluidQueryGroupFacetRenderer',
    'ApacheSolrForTypo3\\Solr\\Query\\FilterEncoder\\QueryGroup',
    'ApacheSolrForTypo3\\Solr\\Query\\FilterEncoder\\QueryGroup'
);

$GLOBALS['TYPO3_CONF_VARS']['EXTCONF']['solr']['searchResultClassName '] = 'ApacheSolrForTypo3\\Solrfluid\\Domain\\Search\\ResultSet\\SearchResult';
$GLOBALS['TYPO3_CONF_VARS']['EXTCONF']['solr']['searchResultSetClassName '] = 'ApacheSolrForTypo3\\Solrfluid\\Domain\\Search\\ResultSet\\SearchResultSet';
$GLOBALS['TYPO3_CONF_VARS']['EXTCONF']['solr']['afterSearch'][] = 'ApacheSolrForTypo3\\Solrfluid\\Domain\\Search\\ResultSet\\ResultSetReconstitutionProcessor';