<?php
namespace ApacheSolrForTypo3\Solrfluid\Domain\Search\Uri;

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

use ApacheSolrForTypo3\Solr\Domain\Search\ResultSet\SearchResultSetService;
use ApacheSolrForTypo3\Solr\Domain\Search\SearchRequest;
use TYPO3\CMS\Extbase\Mvc\Web\Routing\UriBuilder;

/**
 * SearchUriBuilder
 *
 * Responsibility:
 *
 * The SearchUriBuilder is responsible to build uris, that are used in the
 * searchContext. It can use the previous request with it's persistent
 * arguments to build the url for a search sub request.
 *
 * @author Frans Saris <frans@beech.it>
 * @author Timo Schmidt <timo.schmidt@dkd.de>
 * @package ApacheSolrForTypo3\Solrfluid\Domain\Search\Uri
 */

class SearchUriBuilder
{

    /**
     * @var UriBuilder
     */
    protected $uriBuilder;

    /**
     * @param UriBuilder $uriBuilder
     */
    public function injectUriBuilder(UriBuilder $uriBuilder)
    {
        $this->uriBuilder = $uriBuilder;
    }

    /**
     * @param SearchRequest $searchRequest
     * @param $facetName
     * @param $optionValue
     * @return string
     */
    public function getAddFacetOptionUri(SearchRequest $searchRequest, $facetName, $optionValue)
    {
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
    public function getRemoveFacetOptionUri(SearchRequest $searchRequest, $facetName, $optionValue)
    {
        $persistentAndFacetArguments = $searchRequest
            ->getCopyForSubRequest()->removeFacetValue($facetName, $optionValue)
            ->getAsArray();

        return $this->uriBuilder
            ->setArguments($persistentAndFacetArguments)
            ->setUseCacheHash(false)->build();
    }
}
