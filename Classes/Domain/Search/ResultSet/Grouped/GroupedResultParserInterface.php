<?php
namespace ApacheSolrForTypo3\Solrfluid\Domain\Search\ResultSet\Grouped;

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
use ApacheSolrForTypo3\Solrfluid\Domain\Search\ResultSet\SearchResultSet;

/**
 * Class GroupedResultParserInterface
 */
interface GroupedResultParserInterface
{
    /**
     * Check if parser can handle given configuration
     *
     * @param string $groupedResultName
     * @param array $groupedResultConfiguration
     *
     * @return bool
     */
    public function canParse($groupedResultName, array $groupedResultConfiguration);

    /**
     * Parse grouped result
     *
     * @param SearchResultSet $resultSet
     * @param string $groupedResultName
     * @param array $groupedResultConfiguration
     *
     * @return GroupedResult|null
     */
    public function parse(SearchResultSet $resultSet, $groupedResultName, array $groupedResultConfiguration);
}
