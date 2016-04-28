<?php
namespace ApacheSolrForTypo3\Solrfluid\Controller;

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

use ApacheSolrForTypo3\Solr\Domain\Search\SearchRequest;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Mvc\Controller\ActionController;

/**
 * Class SearchController
 *
 * @author Frans Saris <frans@beech.it>
 * @author Timo Schmidt <timo.schmidt@dkd.de>
 * @package ApacheSolrForTypo3\Solrfluid\Controller
 */
class SearchController extends AbstractBaseController
{

    /**
     * Results
     */
    public function resultsAction()
    {
        if (!$this->searchService->getIsSolrAvailable()) {
            $this->forward('solrNotAvailable');
        }

        // perform the current search.
        $this->searchService->setUsePluginAwareComponents(false);
        $searchRequest = $this->buildSearchRequest();
        $searchResultSet = $this->searchService->search($searchRequest);

        $this->view->assignMultiple(array('hasSearched' => $this->searchService->getHasSearched(), 'additionalFilters' => $this->searchService->getAdditionalFilters(), 'resultSet' => $searchResultSet));
    }

    /**
     * @return SearchRequest
     */
    private function buildSearchRequest()
    {
        /** @var $searchRequest \ApacheSolrForTypo3\Solr\Domain\Search\SearchRequest */
        $searchRequest = GeneralUtility::makeInstance('ApacheSolrForTypo3\Solr\Domain\Search\SearchRequest');

        $rawUserQuery = GeneralUtility::_GET('q');
        $page = $this->request->hasArgument('page') ? $this->request->getArgument('page') - 1 : 0;
        $page = max($page, 0);

        $searchRequest->mergeArguments(array('q' => $rawUserQuery, 'page' => $page));
        return $searchRequest;
    }

    /**
     * Form
     */
    public function formAction()
    {
        $this->view->assignMultiple(array('search' => $this->searchService->getSearch(), 'additionalFilters' => $this->searchService->getAdditionalFilters(),));
    }

    /**
     * Frequently Searched
     */
    public function frequentlySearchedAction()
    {
    }
}
