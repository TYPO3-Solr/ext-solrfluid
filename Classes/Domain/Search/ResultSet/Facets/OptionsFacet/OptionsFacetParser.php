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
use TYPO3\CMS\Frontend\ContentObject\ContentObjectRenderer;

/**
 * Class OptionsFacetParser
 */
class OptionsFacetParser implements FacetParserInterface
{
    /**
     * @var ContentObjectRenderer
     */
    protected static $reUseAbleContentObject;

    /**
     * Static array to cache the extracted options by fieldName

     * @var array
     */
    protected static $usedFacetOptionsByFieldName;

    /**
     * @return ContentObjectRenderer
     */
    protected function getReUseAbleContentObject()
    {
        /** @var $contentObject ContentObjectRenderer */
        if (self::$reUseAbleContentObject !== null) {
            return self::$reUseAbleContentObject;
        }

        self::$reUseAbleContentObject = GeneralUtility::makeInstance(ContentObjectRenderer::class);
        return self::$reUseAbleContentObject;
    }

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

        // todo; allow content object for label
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
                // todo; use configuration to enhance option sorting/etc
                $isOptionsActive = in_array($value, $activeFacetValues);
                $label = $this->getLabelFromRenderingInstructions($value, $count, $facetName, $facetConfiguration);
                $facet->addOption(new Option($facet, $label, $value, $count, $isOptionsActive));
            }
        }

        return $facet;
    }

    /**
     * @param mixed $value
     * @param integer $count
     * @param string $facetName
     * @param array $facetConfiguration
     * @return string
     */
    protected function getLabelFromRenderingInstructions($value, $count, $facetName, $facetConfiguration)
    {
        $hasRenderingInstructions = isset($facetConfiguration['renderingInstruction']) && isset($facetConfiguration['renderingInstruction.']);
        if (!$hasRenderingInstructions) {
            return $value;
        }

        $this->getReUseAbleContentObject()->start(array('optionValue' => $value, 'optionCount' => $count, 'facetName' => $facetName));
        return $this->getReUseAbleContentObject()->cObjGetSingle(
            $facetConfiguration['renderingInstruction'],
            $facetConfiguration['renderingInstruction.']
        );
    }

    /**
     * @param \Apache_Solr_Response $response
     * @param string $fieldName
     * @return array
     */
    protected function getUsedFacetOptionValues(\Apache_Solr_Response $response, $fieldName)
    {
        if (isset(self::$usedFacetOptionsByFieldName[$fieldName])) {
            return self::$usedFacetOptionsByFieldName[$fieldName];
        }

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

        return self::$usedFacetOptionsByFieldName[$fieldName] = $activeFacetValues;
    }
}
