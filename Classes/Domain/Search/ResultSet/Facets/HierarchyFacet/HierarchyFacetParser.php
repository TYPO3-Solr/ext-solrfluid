<?php
namespace ApacheSolrForTypo3\Solrfluid\Domain\Search\ResultSet\Facets\HierarchyFacet;

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
class HierarchyFacetParser extends AbstractFacetParser
{
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

        /** @var $facet HierarchyFacet */
        $facet = GeneralUtility::makeInstance(
            HierarchyFacet::class,
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
        foreach ($optionsToCreate as $value => $count) {
            $isOptionsActive = in_array($value, $optionsFromRequest);

            $delimiterPosition = strpos($value, '-');
            $depth = intval(substr($value, 0, $delimiterPosition));

            $path = substr($value, $delimiterPosition + 1);
            $path = str_replace('\/', '@@@', $path);
            $segments = explode('/', $path);
            $segments = array_map(function ($item) { return str_replace('@@@', '/', $item);}, $segments);

            $key = array_pop($segments);
            $parentKey = array_pop($segments);
            $label = $this->getLabelFromRenderingInstructions($value, $count, $facetName, $facetConfiguration);

            $parentNode = $facet->getChildNodes()->getByKey($parentKey);

            if ($parentNode === null) {
                $facet->addChildNode(new Node($facet, $parentNode, $key, $label, $value, $count, $isOptionsActive));
            } else {
                $parentNode->addChildNode(new Node($facet, $parentNode, $key, $label, $value, $count, $isOptionsActive));
            }
        }

        return $facet;
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
