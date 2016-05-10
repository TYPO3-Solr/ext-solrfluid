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
        $label = $this->getPlainLabelOrApplyCObject($facetConfiguration);
        $rawOptions = isset($response->facet_counts->facet_fields->{$fieldName}) ? $response->facet_counts->facet_fields->{$fieldName} : new \stdClass();

        $noOptionsInResponse = empty(get_object_vars($rawOptions));
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
            foreach ($rawOptions as $value => $count) {
                $isOptionsActive = in_array($value, $activeFacetValues);
                $label = $this->getLabelFromRenderingInstructions($value, $count, $facetName, $facetConfiguration);
                $facet->addOption(new Option($facet, $label, $value, $count, $isOptionsActive));
            }
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
     * @param array $configuration
     * @return string
     */
    protected function getPlainLabelOrApplyCObject($configuration)
    {
        // when no label is configured we return an empty string
        if (!isset($configuration['label'])) {
            return '';
        }

        // when no sub configuration is set, we use the string, configured as label
        if (!isset($configuration['label.'])) {
            return $configuration['label'];
        }

        // when label and label. was set, we apply the cObject
        return $this->getReUseAbleContentObject()->cObjGetSingle($configuration['label'], $configuration['label.']);
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
            // (title:"foo")
            // (title:"foo" AND title:"bar")
            $pattern = '~(\(|\s[A-Z]*\s)((?<fieldName>[^:]*):"(?<fieldValue>.*)(?<!\\\))"~U';
            $matches = [];
            preg_match_all($pattern, $filterQuery, $matches);
            $matchedFieldsName = isset($matches['fieldName']) ? $matches['fieldName'] : [];
            $matchedFieldsValues = isset($matches['fieldValue']) ? $matches['fieldValue'] : [];

            foreach ($matchedFieldsName as $key => $fieldNamesInResponse) {
                if ($fieldNamesInResponse === $fieldName && isset($matchedFieldsValues[$key])) {
                    $activeFacetValues[] = stripslashes($matchedFieldsValues[$key]);
                }
            }
        }

        return self::$usedFacetOptionsByFieldName[$fieldName] = $activeFacetValues;
    }
}
