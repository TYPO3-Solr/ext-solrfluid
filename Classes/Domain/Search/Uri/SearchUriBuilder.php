<?php
namespace ApacheSolrForTypo3\Solrfluid\Domain\Search\Uri;


use ApacheSolrForTypo3\Solr\Domain\Search\ResultSet\SearchResultSetService;
use ApacheSolrForTypo3\Solr\Domain\Search\SearchRequest;
use TYPO3\CMS\Extbase\Mvc\Web\Routing\UriBuilder;


/**
 * Class UriBuilder
 */
class SearchUriBuilder {


    /**
     * @var UriBuilder
     */
    protected $uriBuilder;


    /**
     * @param UriBuilder $uriBuilder
     */
    public function  injectUriBuilder(UriBuilder $uriBuilder) {
        $this->uriBuilder = $uriBuilder;
    }

    /**
     * @param SearchRequest $searchRequest
     * @param $facetName
     * @param $optionValue
     * @return string
     */
    public function getAddFacetOptionUri(SearchRequest $searchRequest, $facetName, $optionValue) {
        $persistentAndFacetArguments = $searchRequest
            ->getCopyForSubRequest()->addFacetValue($facetName, $optionValue)
            ->getAsArray();

        return $this->uriBuilder
                ->setArguments($persistentAndFacetArguments)
                ->setUseCacheHash(false)->build();
    }

    /**
     * @param SearchRequest $searchRequest
     * @param $facetName
     * @param $optionValue
     * @return string
     */
    public function getRemoveFacetOptionUri(SearchRequest $searchRequest, $facetName, $optionValue) {
        $persistentAndFacetArguments = $searchRequest
            ->getCopyForSubRequest()->removeFacetValue($facetName, $optionValue)
            ->getAsArray();

        return $this->uriBuilder
            ->setArguments($persistentAndFacetArguments)
            ->setUseCacheHash(false)->build();
    }
}