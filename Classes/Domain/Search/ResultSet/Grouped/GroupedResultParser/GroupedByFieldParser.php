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
use ApacheSolrForTypo3\Solrfluid\Domain\Search\ResultSet\Grouped\GroupedResultParserInterface;
use ApacheSolrForTypo3\Solrfluid\Domain\Search\ResultSet\SearchResultSet;
use TYPO3\CMS\Core\Utility\GeneralUtility;

/**
 * Class GroupedByFieldParser
 *
 * @author Frans Saris <frans@beech.it>
 */
class GroupedByFieldParser extends AbstractGroupedResultParser
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
        return !empty($groupedResultConfiguration['field']);
    }

    /**
     * Parse grouped result
     *
     * @param \ApacheSolrForTypo3\Solrfluid\Domain\Search\ResultSet\SearchResultSet $this->searchResultSet
     * @param string $groupedResultName
     * @param array $groupedResultConfiguration
     *
     * @return GroupedResult|null
     */
    public function parse($groupedResultName, array $groupedResultConfiguration)
    {
        if (empty($this->searchResultSet->getResponse()->grouped->{$groupedResultConfiguration['field']})) {
            return null;
        }

        $rawGroupedResult = $this->searchResultSet->getResponse()->grouped->{$groupedResultConfiguration['field']};
        $groupedResult = GeneralUtility::makeInstance(GroupedResult::class, $groupedResultName);

        foreach ($rawGroupedResult->groups as $rawGroup) {
            /** @var Group $group */
            $group = GeneralUtility::makeInstance(
                Group::class,
                $rawGroup->groupValue,
                $rawGroup->doclist->numFound,
                $rawGroup->doclist->start,
                $rawGroup->doclist->maxScore
            );

            foreach ($rawGroup->doclist->docs as $rawDoc) {
                $document = $this->searchResultService->parseRawDocument($rawDoc);
                $group->addDocument($document);
            }

            $groupedResult->addGroup($group);
        }

        return $groupedResult;
    }


}
