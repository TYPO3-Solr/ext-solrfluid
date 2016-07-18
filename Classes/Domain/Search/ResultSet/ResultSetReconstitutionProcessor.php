<?php
namespace ApacheSolrForTypo3\Solrfluid\Domain\Search\ResultSet;

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

use ApacheSolrForTypo3\Solr\Domain\Search\ResultSet\SearchResultSet as SolrSearchResultSet;
use ApacheSolrForTypo3\Solr\Domain\Search\ResultSet\SearchResultSetProcessor;
use ApacheSolrForTypo3\Solrfluid\Domain\Search\ResultSet\Facets\FacetParserRegistry;
use ApacheSolrForTypo3\Solrfluid\Domain\Search\ResultSet\Facets\OptionsFacet\OptionsFacetParser;
use ApacheSolrForTypo3\Solrfluid\Domain\Search\ResultSet\Grouped\Group;
use ApacheSolrForTypo3\Solrfluid\Domain\Search\ResultSet\Grouped\Section;
use ApacheSolrForTypo3\Solrfluid\Domain\Search\ResultSet\Sorting\Sorting;
use ApacheSolrForTypo3\Solrfluid\Domain\Search\ResultSet\Spellchecking\Suggestion;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Utility\DebuggerUtility;

/**
 * This processor is used to transform the solr response into a
 * domain object hierarchy that can be used in the application (controller and view).
 *
 * @todo: the logic in this class can go into the SearchResultSetService after moving the
 * code of EXT:solrfluid to EXT:solr
 *
 * @author Frans Saris <frans@beech.it>
 * @author Timo Hund <timo.hund@dkd.de>
 * @package ApacheSolrForTypo3\Solrfluid\Domain\Search\ResultSet
 */
class ResultSetReconstitutionProcessor implements SearchResultSetProcessor
{

    /**
     * The implementation can be used to influence a SearchResultSet that is
     * created and processed in the SearchResultSetService.
     *
     * @param SolrSearchResultSet $resultSet
     * @return SolrSearchResultSet
     */
    public function process(SolrSearchResultSet $resultSet)
    {
        if (!$resultSet instanceof SearchResultSet) {
            return $resultSet;
        }

        if ($resultSet->getResponse()->grouped) {
            $resultSet = $this->parseGroupedResult($resultSet);
        } else {
            $resultSet = $this->parseResultCount($resultSet);
        }

        $resultSet = $this->parseSpellCheckingResponseIntoObjects($resultSet);
        $resultSet = $this->parseSortingIntoObjects($resultSet);

        // here we can reconstitute other domain objects from the solr response
        $resultSet = $this->parseFacetsIntoObjects($resultSet);

        DebuggerUtility::var_dump($resultSet);
        $this->storeLastSearches($resultSet);

        return $resultSet;
    }

    /**
     * @param SearchResultSet $resultSet
     * @return SearchResultSet
     */
    protected function parseResultCount(SearchResultSet $resultSet)
    {
        $response = $resultSet->getResponse();
        if (!isset($response->response->numFound)) {
            return $resultSet;
        }

        $resultSet->setAllResultCount($response->response->numFound);
        return $resultSet;
    }

    /**
     * Parse response.grouped
     *
     * @param SearchResultSet $resultSet
     * @return SearchResultSet
     */
    protected function parseGroupedResult(SearchResultSet $resultSet)
    {
        $groupedResponse = $resultSet->getResponse()->grouped;
        $resultCount = 0;
        
        $groupedConfiguration = $resultSet->getUsedSearchRequest()->getContextTypoScriptConfiguration()->$this->getValueByPathOrDefaultValue('plugin.tx_solr.search.faceting.removeFacetLinkText', $defaultIfEmpty);

        foreach ($groupedConfiguration as $name => $options) {
            if (!is_array($options)) {
                continue;
            }
            $facetName = rtrim($name, '.');
            $type = !empty($options['type']) ? $options['type'] : '';

            $parser = $facetParserRegistry->getParser($type);
            $facet = $parser->parse($resultSet, $facetName, $options);
            if ($facet !== null) {
                $resultSet->addFacet($facet);
            }
        }

        return $resultSet;
//        
//DebuggerUtility::var_dump($groupedResponse, 'grouped');
//        foreach ($groupedResponse as $rawGroupedSection) {
//
//            $resultCount += $rawGroupedSection->matches;
//
//            /** @var Section $section */
//            $section = GeneralUtility::makeInstance(Section::class);
//
//            foreach ($rawGroupedSection->groups as $rawGroup) {
//                /** @var Group $group */
//                $group = GeneralUtility::makeInstance(
//                    Group::class,
//                    $rawGroup->groupValue,
//                    $rawGroup->doclist->numFound,
//                    $rawGroup->doclist->start,
//                    $rawGroup->doclist->maxScore
//                );
//                foreach ($rawGroup->doclist->docs as $rawDoc) {
//                    $group->addDocument($rawDoc);
//                }
//                $section->addGroup($group);
//            }
//        }
//
//        $resultSet->addGroupedSection($section);

        $resultSet->setAllResultCount($resultCount);

        return $resultSet;
    }

    /**
     * @param SearchResultSet $resultSet
     * @return SearchResultSet
     */
    protected function parseSortingIntoObjects(SearchResultSet $resultSet)
    {
        $configuration = $resultSet->getUsedSearchRequest()->getContextTypoScriptConfiguration();
        $hasSorting = $resultSet->getUsedSearchRequest()->getHasSorting();
        $activeSortingName = $resultSet->getUsedSearchRequest()->getSortingName();
        $activeSortingDirection = $resultSet->getUsedSearchRequest()->getSortingDirection();

        // no configuration available
        if (!isset($configuration)) {
            return $resultSet;
        }

        // no sorting enabled
        if (!$configuration->getSearchSorting()) {
            return $resultSet;
        }
        foreach ($configuration->getSearchSortingOptionsConfiguration() as $sortingKeyName => $sortingOptions) {
            $sortingName = rtrim($sortingKeyName, '.');
            $selected = false;
            $direction = $configuration->getSearchSortingDefaultOrderBySortOptionName($sortingName);

            // when we have an active sorting in the request we compare the sortingName and mark is as active and
            // use the direction from the request
            if ($hasSorting && $activeSortingName == $sortingName) {
                $selected = true;
                $direction = $activeSortingDirection;
            }

            $field = $sortingOptions['field'];
            $label = $sortingOptions['label'];

            $isResetOption = $field === 'relevance';
            // @todo allow stdWrap on label
            $sorting = new Sorting($resultSet, $sortingName, $field, $direction, $label, $selected, $isResetOption);
            $resultSet->addSorting($sorting);
        }

        return $resultSet;
    }

    /**
     * @param SearchResultSet $resultSet
     * @return SearchResultSet
     */
    private function parseSpellCheckingResponseIntoObjects(SearchResultSet $resultSet)
    {
        //read the response
        $response = $resultSet->getResponse();
        if (!is_object($response->spellcheck->suggestions)) {
            return $resultSet;
        }

        foreach ($response->spellcheck->suggestions as $key => $suggestionData) {
            if (!isset($suggestionData->suggestion) && !is_array($suggestionData->suggestion)) {
                continue;
            }

            // the key contains the misspelled word expect the internal key "collation"
            if ($key == 'collation') {
                continue;
            }
            //create the spellchecking object structure
            $misspelledTerm = $key;
            foreach ($suggestionData->suggestion as $suggestedTerm) {
                $suggestion = $this->createSuggestionFromResponseFragment($suggestionData, $suggestedTerm, $misspelledTerm);

                //add it to the resultSet
                $resultSet->addSpellCheckingSuggestion($suggestion);
            }
        }

        return $resultSet;
    }

    /**
     * @param \stdClass $suggestionData
     * @param string $suggestedTerm
     * @param string $misspelledTerm
     * @return Suggestion
     */
    private function createSuggestionFromResponseFragment($suggestionData, $suggestedTerm, $misspelledTerm)
    {
        $numFound = isset($suggestionData->numFound) ? $suggestionData->numFound : 0;
        $startOffset = isset($suggestionData->startOffset) ? $suggestionData->startOffset : 0;
        $endOffset = isset($suggestionData->endOffset) ? $suggestionData->endOffset : 0;

        // by now we avoid to use GeneralUtility::makeInstance, since we only create a value object
        // and the usage might be a overhead.
        $suggestion = new Suggestion($suggestedTerm, $misspelledTerm, $numFound, $startOffset, $endOffset);
        return $suggestion;
    }

    /**
     * Parse available facets into objects
     *
     * @param SearchResultSet $resultSet
     * @return SearchResultSet
     */
    private function parseFacetsIntoObjects(SearchResultSet $resultSet)
    {
        // Make sure we can access the facet configuration
        if (!$resultSet->getUsedSearchRequest() || !$resultSet->getUsedSearchRequest()->getContextTypoScriptConfiguration()) {
            return $resultSet;
        }

        // Read the response
        $response = $resultSet->getResponse();
        if (!is_object($response->facet_counts)) {
            return $resultSet;
        }

        /** @var FacetParserRegistry $facetParserRegistry */
        $facetParserRegistry = GeneralUtility::makeInstance(FacetParserRegistry::class);
        $facetsConfiguration = $resultSet->getUsedSearchRequest()->getContextTypoScriptConfiguration()->getSearchFacetingFacets();

        foreach ($facetsConfiguration as $name => $options) {
            if (!is_array($options)) {
                continue;
            }
            $facetName = rtrim($name, '.');
            $type = !empty($options['type']) ? $options['type'] : '';

            $parser = $facetParserRegistry->getParser($type);
            $facet = $parser->parse($resultSet, $facetName, $options);
            if ($facet !== null) {
                $resultSet->addFacet($facet);
            }
        }

        return $resultSet;
    }

    /**
     * @param SearchResultSet $resultSet
     */
    protected function storeLastSearches(SearchResultSet $resultSet)
    {
        if ($resultSet->getAllResultCount() === 0) {
            // when the search does not produce a result we do not store the last searches
            return;
        }

        if (!isset($GLOBALS['TSFE']) || !isset($GLOBALS['TYPO3_DB'])) {
            return;
        }

        /** @var $lastSearchesService \ApacheSolrForTypo3\Solr\Domain\Search\LastSearches\LastSearchesService */
        $lastSearchesService = GeneralUtility::makeInstance('ApacheSolrForTypo3\Solr\Domain\Search\LastSearches\LastSearchesService',
            $resultSet->getUsedSearchRequest()->getContextTypoScriptConfiguration(),
            $GLOBALS['TSFE'],
            $GLOBALS['TYPO3_DB']);


        $lastSearchesService->addToLastSearches($resultSet->getUsedSearchRequest()->getRawUserQuery());
    }
}
