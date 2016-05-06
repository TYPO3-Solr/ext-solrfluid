<?php
namespace ApacheSolrForTypo3\Solrfluid\Domain\Search\ResultSet\Facets\OptionsFacet;

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

        $noOptionsInResponse = empty($response->facet_counts->facet_fields->{$fieldName});
        $hideEmpty = !$resultSet->getUsedSearchRequest()->getContextTypoScriptConfiguration()->getSearchFacetingShowEmptyFacetsByName($facetName);

        if ($noOptionsInResponse && $hideEmpty) {
            return null;
        }

        /** @var $facet OptionsFacet */
        $facet = GeneralUtility::makeInstance(
            OptionsFacet::class,
            $resultSet,
            $facetName,
            $fieldName,
            $label,
            $facetConfiguration
        );

        $activeFacetValues = $this->getUsedFacetOptionValues($response, $fieldName);
        $hasActiveOptions = count($activeFacetValues) > 0;
        $facet->setIsUsed($hasActiveOptions);

        if (!$noOptionsInResponse) {
            $facet->setIsAvailable(true);
            foreach ($response->facet_counts->facet_fields->{$fieldName} as $value => $count) {
                // todo; use configuration to enhance option label/sorting/etc
                $isOptionsActive = in_array($value, $activeFacetValues);
                $facet->addOption(new Option($facet, $value, $value, $count, $isOptionsActive));
            }
        }

        return $facet;
    }

    /**
     * @param $response
     * @param $fieldName
     * @return array
     */
    protected function getUsedFacetOptionValues($response, $fieldName)
    {
        $activeFacetValues = [];
        if (!isset($response->responseHeader->params->fq)) {
            return $activeFacetValues;
        }

        foreach ($response->responseHeader->params->fq as $filterQuery) {
            $expectedFilterStartSnipped = '(' .  $fieldName . ':"';
            if (strpos($filterQuery, $expectedFilterStartSnipped) === 0) {
                $facetValue = substr($filterQuery, strlen($expectedFilterStartSnipped), -2);
                $activeFacetValues[] = $facetValue;
            }
        }

        return $activeFacetValues;
    }
}
