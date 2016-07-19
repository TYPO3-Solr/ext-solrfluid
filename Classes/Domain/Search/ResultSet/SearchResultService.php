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
use ApacheSolrForTypo3\Solr\Domain\Search\ResultSet\SearchResult;
use ApacheSolrForTypo3\Solr\Search;
use TYPO3\CMS\Core\SingletonInterface;
use TYPO3\CMS\Core\Utility\GeneralUtility;

/**
 * Class SearchResultService
 *
 * @author Frans Saris <frans@beech.it>
 */
class SearchResultService implements SingletonInterface
{
    /**
     * Search instance
     *
     * @var Search
     */
    protected $search;

    /**
     * Constructor
     *
     * @param Search $search
     */
    public function __construct(Search $search)
    {
        $this->search = $search;
    }

    /**
     * @param \stdClass $rawDocument
     * @return SearchResult
     */
    public function parseRawDocument(\stdClass $rawDocument)
    {
        $apacheDocument = $this->createApacheSolrDocument($rawDocument);
        return $this->wrapResultDocumentInResultObject($apacheDocument);
    }

    /**
     * Creates an Apache_Solr_Document from a raw stdClass object as parsed by
     * SolrPhpClient.
     *
     * For compatibility reasons taken from Apache_Solr_Response->_parseData()
     *
     * @param \stdClass $rawDocument The raw document as initially returned by SolrPhpClient
     * @return \Apache_Solr_Document Apache Solr Document
     */
    private function createApacheSolrDocument(\stdClass $rawDocument)
    {
        $collapseSingleValueArrays = $this->search->getSolrConnection()->getCollapseSingleValueArrays();

        $document = new \Apache_Solr_Document();
        foreach ($rawDocument as $key => $value) {
            // If a result is an array with only a single value
            // then its nice to be able to access it
            // as if it were always a single value
            if ($collapseSingleValueArrays && is_array($value) && count($value) <= 1) {
                $value = array_shift($value);
            }

            $document->$key = $value;
        }

        return $document;
    }

    /**
     * @param \Apache_Solr_Document $originalDocument
     * @return SearchResult
     */
    public function wrapResultDocumentInResultObject(\Apache_Solr_Document $originalDocument)
    {
        $searchResultClassName = $this->getResultClassName();
        $resultSet = GeneralUtility::makeInstance($searchResultClassName, $originalDocument);
        if (!$resultSet instanceof SearchResult) {
            throw new \InvalidArgumentException(
                'Could not create result object with class: ' . (string)$searchResultClassName,
                1468908731
            );
        }
        return $resultSet;
    }

    /**
     * @return string
     */
    protected function getResultClassName()
    {
        return isset($GLOBALS['TYPO3_CONF_VARS']['EXTCONF']['solr']['searchResultClassName ']) ?
            $GLOBALS['TYPO3_CONF_VARS']['EXTCONF']['solr']['searchResultClassName '] :
            SearchResult::class;
    }
}
