<?php
namespace ApacheSolrForTypo3\Solrfluid\Domain\Search\ResultSet\Facets;

    /*
     * This source file is proprietary property of Beech Applications B.V.
     * Date: 03-05-2016
     * All code (c) Beech Applications B.V. all rights reserved
     */
use ApacheSolrForTypo3\Solrfluid\Domain\Search\ResultSet\SearchResultSet;

/**
 * Interface FacetParserInterface
 */
interface FacetParserInterface
{
    /**
     * @param SearchResultSet $resultSet
     * @param $facetName
     * @param array $facetConfiguration
     * @return AbstractFacet|null
     */
    public function parse(SearchResultSet $resultSet, $facetName, array $facetConfiguration);


}
