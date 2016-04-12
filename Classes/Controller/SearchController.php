<?php
namespace ApacheSolrForTypo3\Solrfluid\Controller;

use ApacheSolrForTypo3\Solr\Domain\Search\SearchRequest;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Mvc\Controller\ActionController;

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

        $this->view->assignMultiple(array(
             // @todo: we should avoid to pass the search directly and use the resultSet instead
            'search' => $this->searchService->getSearch(),
            'hasSearched' => $this->searchService->getHasSearched(),
            'additionalFilters' => $this->searchService->getAdditionalFilters(),
            'resultSet' => $searchResultSet
        ));
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
        $this->view->assignMultiple(array(
            'search' => $this->searchService->getSearch(),
            'additionalFilters' => $this->searchService->getAdditionalFilters(),
        ));
    }

    /**
     * Frequently Searched
     */
    public function frequentlySearchedAction()
    {
    }
}
