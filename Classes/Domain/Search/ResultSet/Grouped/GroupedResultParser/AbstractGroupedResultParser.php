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
use ApacheSolrForTypo3\Solrfluid\Domain\Search\ResultSet\Grouped\GroupedResultParserInterface;
use ApacheSolrForTypo3\Solrfluid\Domain\Search\ResultSet\SearchResultService;
use ApacheSolrForTypo3\Solrfluid\Domain\Search\ResultSet\SearchResultSet;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Utility\DebuggerUtility;

/**
 * Class AbstractGroupedResultParser
 *
 * @author Frans Saris <frans@beech.it>
 */
abstract class AbstractGroupedResultParser implements GroupedResultParserInterface
{
    /**
     * @var SearchResultSet
     */
    protected $searchResultSet;

    /**
     * @var SearchResultService
     */
    protected $searchResultService;

    /**
     * Constructor
     *
     * @param SearchResultSet $searchResultSet
     */
    public function __construct(SearchResultSet $searchResultSet)
    {
       $this->searchResultSet = $searchResultSet;
        $this->searchResultService = GeneralUtility::makeInstance(
            SearchResultService::class,
            $this->searchResultSet->getUsedSearch()
        );
    }


}
