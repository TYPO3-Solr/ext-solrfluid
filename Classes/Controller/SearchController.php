<?php
namespace ApacheSolrForTypo3\Solrfluid\Controller;


use ApacheSolrForTypo3\Solr\Domain\Search\SearchRequest;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Mvc\Controller\ActionController;

class SearchController extends AbstractBaseController {

    /**
     * Results
     */
    public function resultsAction() {
        if ($this->solrAvailable) {
            // perform the current search.
            /** @var $searchService \ApacheSolrForTypo3\Solr\Domain\Search\ResultSet\SearchResultSetService */
            $searchService = GeneralUtility::makeInstance('ApacheSolrForTypo3\Solr\Domain\Search\ResultSet\SearchResultSetService', $this->typoScriptConfiguration, $this->search);
            $searchService->setUsePluginAwardComponents(false);
            $searchRequest = $this->buildSearchRequest();
            $searchResultSet = $searchService->search($searchRequest);

            if($searchResultSet->getUsedQuery() == null) {
                $this->redirect('form');
            }
            $this->view->assignMultiple(array(
                'search' => $this->search,
                'additionalFilters' => $searchResultSet->getUsedAdditionalFilters(),
            ));
        }
    }

    /**
     * @return SearchRequest
     */
    private function buildSearchRequest()
    {
        /** @var $searchRequest \ApacheSolrForTypo3\Solr\Domain\Search\SearchRequest */
        $searchRequest = GeneralUtility::makeInstance('ApacheSolrForTypo3\Solr\Domain\Search\SearchRequest');
        $searchRequest->mergeArguments($this->arguments->getArrayCopy());

        $rawUserQuery = GeneralUtility::_GET('q');
        $searchRequest->mergeArguments(array('q' => $rawUserQuery));

        return $searchRequest;
    }

    /**
     * Form
     */
    public function formAction() {
        $this->view->assignMultiple(array(
            'search' => $this->search,
            'additionalFilters' => $this->additionalFilters,
        ));
    }

    /**
     * Frequently Searched
     */
    public function frequentlySearchedAction() {

    }
}