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
    'pi_facets',
    array(
        'Search' => 'frequentlySearched'
    ),
    array(
        'Search' => 'frequentlySearched'
    )
);
