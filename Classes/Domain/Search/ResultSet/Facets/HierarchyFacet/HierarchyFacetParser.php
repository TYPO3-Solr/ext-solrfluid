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
     * @return HierarchyFacet|null
     */
    public function parse(SearchResultSet $resultSet, $facetName, array $facetConfiguration)
    {
        $response = $resultSet->getResponse();
        $fieldName = $facetConfiguration['field'];
        $label = $this->getPlainLabelOrApplyCObject($facetConfiguration);
        $optionsFromSolrResponse = isset($response->facet_counts->facet_fields->{$fieldName}) ? get_object_vars($response->facet_counts->facet_fields->{$fieldName}) : [];
        $optionsFromRequest = $this->getActiveFacetValuesFromRequest($resultSet, $facetName);

        $hasOptionsInResponse = !empty($optionsFromSolrResponse);
        $hasSelectedOptionsInRequest = count($optionsFromRequest) > 0;
        $hasNoOptionsToShow = !$hasOptionsInResponse && !$hasSelectedOptionsInRequest;
        $hideEmpty = !$resultSet->getUsedSearchRequest()->getContextTypoScriptConfiguration()->getSearchFacetingShowEmptyFacetsByName($facetName);

        if ($hasNoOptionsToShow && $hideEmpty) {
            return null;
        }

        /** @var $facet HierarchyFacet */
        $facet = GeneralUtility::makeInstance(HierarchyFacet::class, $resultSet, $facetName, $fieldName, $label, $facetConfiguration);

        $hasActiveOptions = count($optionsFromRequest) > 0;
        $facet->setIsUsed($hasActiveOptions);

        $facet->setIsAvailable($hasOptionsInResponse);

        $nodesToCreate = $this->getMergedFacetValueFromSearchRequestAndSolrResponse($optionsFromSolrResponse, $optionsFromRequest);

        foreach ($nodesToCreate as $value => $count) {
            $isActive = in_array($value, $optionsFromRequest);
            $delimiterPosition = strpos($value, '-');
            $path = substr($value, $delimiterPosition + 1);
            $pathArray = $this->getPathAsArray($path);
            $key = array_pop($pathArray);
            $parentKey = array_pop($pathArray);
            $value = '/' . $path;
            $label = $this->getLabelFromRenderingInstructions($key, $count, $facetName, $facetConfiguration);

            $facet->createNode($parentKey, $key, $label, $value, $count, $isActive);
        }

        return $facet;
    }

    /**
     * This method is used to get the path array from a hierarchical facet. It substitutes escaped slashes to keep them
     * when they are used inside a facetValue.
     *
     * @param string $path
     * @return array
     */
    protected function getPathAsArray($path)
    {
        $path = str_replace('\/', '@@@', $path);
        $segments = explode('/', $path);
        return array_map(function ($item) {
            return str_replace('@@@', '/', $item);
        }, $segments);
    }
}
