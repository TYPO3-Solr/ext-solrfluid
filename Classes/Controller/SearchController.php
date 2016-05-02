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

        // we pass the search result set to the controller context, to have the possibility
        // to access it without passing it from partial to partial
        $this->controllerContext->setSearchResultSet($searchResultSet);

        $this->view->assignMultiple(
            array(
                'hasSearched' => $this->searchService->getHasSearched(),
                'additionalFilters' => $this->searchService->getAdditionalFilters(),
                'resultSet' => $searchResultSet
            )
        );
    }

    /**
     * @return SearchRequest
     */
    private function buildSearchRequest()
    {
        /** @var $searchRequest \ApacheSolrForTypo3\Solr\Domain\Search\SearchRequest */
        $searchRequest = GeneralUtility::makeInstance(SearchRequest::class);
        $rawUserQuery = GeneralUtility::_GET('q');

        $arguments = $this->request->getArguments();
        $page = isset($arguments['page']) ? $arguments['page'] -1 : 0;
        $arguments['page'] = max($page, 0);

        $searchRequest->mergeArguments(
            array(
                'q' => $rawUserQuery,
                'tx_solr' => $arguments
            )
        );

        return $searchRequest;
    }

    /**
     * Form
     */
    public function formAction()
    {
        $this->view->assignMultiple(
            array(
                'search' => $this->searchService->getSearch(),
                'additionalFilters' => $this->searchService->getAdditionalFilters()
            )
        );
    }

    /**
     * Frequently Searched
     */
    public function frequentlySearchedAction()
    {
    }
}
