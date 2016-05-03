<?php
namespace ApacheSolrForTypo3\Solrfluid\Domain\Search\ResultSet\Facets\OptionsFacet;

    /*
     * This source file is proprietary property of Beech Applications B.V.
     * Date: 03-05-2016
     * All code (c) Beech Applications B.V. all rights reserved
     */
use ApacheSolrForTypo3\Solrfluid\Domain\Search\ResultSet\Facets\AbstractFacet;
use ApacheSolrForTypo3\Solrfluid\Domain\Search\ResultSet\Facets\FacetParserInterface;
use ApacheSolrForTypo3\Solrfluid\Domain\Search\ResultSet\SearchResultSet;
use TYPO3\CMS\Core\Utility\GeneralUtility;

/**
 * Class OptionsFacetParser
 */
class OptionsFacetParser implements FacetParserInterface
{
    /**
     * @param SearchResultSet $resultSet
     * @param $facetName
     * @param array $facetConfiguration
     * @return AbstractFacet|null
     */
    public function parse(SearchResultSet $resultSet, $facetName, array $facetConfiguration)
    {
        $response = $resultSet->getResponse();
        $fieldName = $facetConfiguration['field'];
        $label = $facetConfiguration['label'];

        $facet = GeneralUtility::makeInstance(
            OptionsFacet::class,
            $resultSet,
            $facetName,
            $fieldName,
            $label,
            $facetConfiguration
        );

        if (!empty($response->facet_counts->facet_fields->{$fieldName})) {
            foreach ($response->facet_counts->facet_fields->{$fieldName} as $value => $count) {
                // todo; use configuration to enhance option label/sorting/etc
                $facet->addOption(new Option($facet, $value, $value, $count));
            }
        }

        return $facet;
    }

}
