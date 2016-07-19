<?php
namespace ApacheSolrForTypo3\Solrfluid\Domain\Search\ResultSet\Grouped\GroupedResultParser;

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
use ApacheSolrForTypo3\Solrfluid\Domain\Search\ResultSet\Grouped\Group;
use ApacheSolrForTypo3\Solrfluid\Domain\Search\ResultSet\Grouped\GroupedResult;
use ApacheSolrForTypo3\Solrfluid\Domain\Search\ResultSet\SearchResultSet;
use TYPO3\CMS\Core\Utility\GeneralUtility;

/**
 * Class GroupedResultParser
 *
 * @author Frans Saris <frans@beech.it>
 */
class GroupedByQueryParser extends AbstractGroupedResultParser
{
    /**
     * Check if parser can handle given configuration
     *
     * @param string $groupedResultName
     * @param array $groupedResultConfiguration
     *
     * @return bool
     */
    public function canParse($groupedResultName, array $groupedResultConfiguration)
    {
        return !empty($groupedResultConfiguration['queries.']);
    }

    /**
     * Parse grouped result
     *
     * @param string $groupedResultName
     * @param array $groupedResultConfiguration
     *
     * @return GroupedResult|null
     */
    public function parse($groupedResultName, array $groupedResultConfiguration)
    {
        $groupedResult = null;
        $groups = [];

        foreach ($groupedResultConfiguration['queries.'] as $queryKey => $queryString) {
            $rawGroup = $this->getGroupedResultForQuery($this->searchResultSet, $queryString);

            if ($rawGroup === null) {
                continue;
            }

            /** @var Group $group */
            $group = GeneralUtility::makeInstance(
                Group::class,
                $queryString,
                $rawGroup->doclist->numFound,
                $rawGroup->doclist->start,
                $rawGroup->doclist->maxScore
            );

            foreach ($rawGroup->doclist->docs as $rawDoc) {
                $document = $this->searchResultService->parseRawDocument($rawDoc);
                $group->addDocument($document);
            }

            if ($group->getDocuments()) {
                $groups[] = $group;
            }
        }

        if ($groups !== []) {
            /** @var GroupedResult $groupedResult */
            $groupedResult = GeneralUtility::makeInstance(GroupedResult::class, $groupedResultName);
            foreach ($groups as $group) {
                $groupedResult->addGroup($group);
            }
        }
        return $groupedResult;
    }

    /**
     * @param SearchResultSet $resultSet
     * @param string $queryString
     * @return \stdClass|null
     */
    protected function getGroupedResultForQuery(SearchResultSet $resultSet, $queryString)
    {
        if (!empty($resultSet->getResponse()->grouped->{$queryString})) {
            return $resultSet->getResponse()->grouped->{$queryString};
        } else {
            return null;
        }

    }
}
