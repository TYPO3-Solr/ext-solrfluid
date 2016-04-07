<?php
namespace ApacheSolrForTypo3\Solrfluid\ViewHelpers\Widget\Controller;

/*
 * This file is part of the TYPO3 CMS project.
 *
 * It is free software; you can redistribute it and/or modify it under
 * the terms of the GNU General Public License, either version 2
 * of the License, or any later version.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 *
 * The TYPO3 project - inspiring people to share!
 */
use ApacheSolrForTypo3\Solr\Domain\Search\FrequentSearches\FrequentSearchesService;
use ApacheSolrForTypo3\Solr\Util;
use ApacheSolrForTypo3\Solrfluid\Widget\AbstractWidgetController;
use TYPO3\CMS\Core\Cache\Exception\NoSuchCacheException;
use TYPO3\CMS\Core\Database\DatabaseConnection;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Utility\DebuggerUtility;

/**
 * Class FrequentlySearchedController
 */
class FrequentlySearchedController extends AbstractWidgetController
{
    /**
     * @var array
     */
    protected $solrConfiguration = array();

    /**
     * @var FrequentSearchesService
     */
    protected $frequentSearchesService;

    /**
     * Constructor
     */
    public function __construct()
    {
        // todo: fetch from ControllerContext
        $this->solrConfiguration = Util::getSolrConfiguration();
        $databaseConnection = $GLOBALS['TYPO3_DB'];
        $tsfe = $GLOBALS['TSFE'];
        $cache = $this->getInitializedCache();

        $this->frequentSearchesService = GeneralUtility::makeInstance(FrequentSearchesService::class,
            $this->solrConfiguration,
            $cache,
            $tsfe,
            $databaseConnection);
    }

    /**
     * Initializes the cache for this command.
     *
     * @return \TYPO3\CMS\Core\Cache\Frontend\AbstractFrontend
     */
    protected function getInitializedCache()
    {
        $cacheIdentifier = 'tx_solr';
        try {
            $cacheInstance = GeneralUtility::makeInstance('TYPO3\\CMS\\Core\\Cache\\CacheManager')->getCache($cacheIdentifier);
        } catch (NoSuchCacheException $e) {
            /** @var t3lib_cache_Factory $typo3CacheFactory */
            $typo3CacheFactory = $GLOBALS['typo3CacheFactory'];
            $cacheInstance = $typo3CacheFactory->create(
                $cacheIdentifier,
                $GLOBALS['TYPO3_CONF_VARS']['SYS']['caching']['cacheConfigurations'][$cacheIdentifier]['frontend'],
                $GLOBALS['TYPO3_CONF_VARS']['SYS']['caching']['cacheConfigurations'][$cacheIdentifier]['backend'],
                $GLOBALS['TYPO3_CONF_VARS']['SYS']['caching']['cacheConfigurations'][$cacheIdentifier]['options']
            );
        }

        return $cacheInstance;
    }

    /**
     * Last searches
     */
    public function indexAction()
    {
        $frequentSearches = $this->frequentSearchesService->getFrequentSearchTerms();
        $this->view->assign('contentArguments', array('frequentSearches' => $this->enrichFrequentSearchesInfo($frequentSearches)));
    }

    /**
     * Enrich the frequentSearches
     *
     * @param array Frequent search terms as array with terms as keys and hits as the value
     * @return array An array with content for the frequent terms markers
     */
    protected function enrichFrequentSearchesInfo(array $frequentSearchTerms)
    {
        $frequentSearches = array();

        $minimumSize = $this->solrConfiguration['search.']['frequentSearches.']['minSize'];
        $maximumSize = $this->solrConfiguration['search.']['frequentSearches.']['maxSize'];

        if (count($frequentSearchTerms)) {
            $maximumHits = max(array_values($frequentSearchTerms));
            $minimumHits = min(array_values($frequentSearchTerms));
            $spread = $maximumHits - $minimumHits;
            $step = ($spread == 0) ? 1 : ($maximumSize - $minimumSize) / $spread;

            foreach ($frequentSearchTerms as $term => $hits) {
                $size = round($minimumSize + (($hits - $minimumHits) * $step));
                $frequentSearches[] = array(
                    'q' => $term,
                    'hits' => $hits,
                    'style' => 'font-size: ' . $size . 'px',
                    'class' => 'tx-solr-frequent-term-' . $size,
                    'size' => $size,
                );
            }
        }

        return $frequentSearches;
    }
}
