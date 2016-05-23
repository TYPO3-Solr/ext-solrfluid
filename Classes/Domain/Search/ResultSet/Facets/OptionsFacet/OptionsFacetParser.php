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
use ApacheSolrForTypo3\Solrfluid\Domain\Search\ResultSet\Facets\AbstractFacetParser;
use ApacheSolrForTypo3\Solrfluid\Domain\Search\ResultSet\Facets\FacetParserInterface;
use ApacheSolrForTypo3\Solrfluid\Domain\Search\ResultSet\SearchResultSet;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Frontend\ContentObject\ContentObjectRenderer;

/**
 * Class OptionsFacetParser
 */
class OptionsFacetParser extends AbstractFacetParser
{
    /**
     * Static array to cache the extracted options by fieldName
     *
     * @var array
     */
    protected static $usedFacetOptionsByFieldName;

    /**
     * @param SearchResultSet $resultSet
     * @param string $facetName
     * @param array $facetConfiguration
     * @return AbstractFacet|null
     */
    public function parse(SearchResultSet $resultSet, $facetName, array $facetConfiguration)
    {
        $response = $resultSet->getResponse();
        $fieldName = $facetConfiguration['field'];
        $label = $this->getPlainLabelOrApplyCObject($facetConfiguration);
        $optionsFromSolrResponse = isset($response->facet_counts->facet_fields->{$fieldName}) ? get_object_vars($response->facet_counts->facet_fields->{$fieldName}) : [];
        $optionsFromRequest = $this->getActiveFacetOptionValuesFromRequest($resultSet, $facetName);

        $hasOptionsInResponse = !empty($optionsFromSolrResponse);
        $hasSelectedOptionsInRequest = count($optionsFromRequest) > 0;
        $hasNoOptionsToShow = !$hasOptionsInResponse && !$hasSelectedOptionsInRequest;
        $hideEmpty = !$resultSet->getUsedSearchRequest()->getContextTypoScriptConfiguration()->getSearchFacetingShowEmptyFacetsByName($facetName);

        if ($hasNoOptionsToShow && $hideEmpty) {
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

        $hasActiveOptions = count($optionsFromRequest) > 0;
        $facet->setIsUsed($hasActiveOptions);
        $facet->setIsAvailable($hasOptionsInResponse);

        $optionsToCreate = $this->getMergedOptionsFromRequestAndResponse($optionsFromSolrResponse, $optionsFromRequest);
        foreach ($optionsToCreate as $optionsValue => $count) {
           $isOptionsActive = in_array($optionsValue, $optionsFromRequest);
           $label = $this->getLabelFromRenderingInstructions($optionsValue, $count, $facetName, $facetConfiguration);
           $facet->addOption(new Option($facet, $label, $optionsValue, $count, $isOptionsActive));
        }

        // after all options have been created we apply a manualSortOrder if configured
        // the sortBy (lex,..) is done by the solr server and triggered by the query, therefore it does not
        // need to be handled in the frontend.
        $facet = $this->applyManualSortOrder($facet, $facetConfiguration);

        return $facet;
    }

    /**
     * @param OptionsFacet $facet
     * @param array $facetConfiguration
     * @return OptionsFacet
     */
    protected function applyManualSortOrder($facet, array $facetConfiguration)
    {
        if (!isset($facetConfiguration['manualSortOrder'])) {
            return $facet;
        }
        $fields = GeneralUtility::trimExplode(',', $facetConfiguration['manualSortOrder']);
        $sortedOptions = $facet->getOptions()->getManualSortedCopy($fields);
        $facet->setOptions($sortedOptions);

        return $facet;
    }

    /**
     * Retrieves the active facetValue for a facet from the search request.
     * @param SearchResultSet $resultSet
     * @param string $facetName
     * @return array
     */
    protected function getActiveFacetOptionValuesFromRequest(SearchResultSet $resultSet, $facetName)
    {
        $activeFacetValues = $resultSet->getUsedSearchRequest()->getActiveFacetValuesByName($facetName);
        $activeFacetValues = is_array($activeFacetValues) ? $activeFacetValues : [];

        return $activeFacetValues;
    }

    /**
     * @param $optionsFromSolrResponse
     * @param $optionsFromRequest
     * @return mixed
     */
    protected function getMergedOptionsFromRequestAndResponse($optionsFromSolrResponse, $optionsFromRequest)
    {
        $optionsToCreate = $optionsFromSolrResponse;

        foreach ($optionsFromRequest as $optionFromRequest) {
            // if we have options in the request that have not been in the response we add them with a count of 0
            if (!isset($optionsToCreate[$optionFromRequest])) {
                $optionsToCreate[$optionFromRequest] = 0;
            }
        }
        return $optionsToCreate;
    }
}
