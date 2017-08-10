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
use ApacheSolrForTypo3\Solrfluid\Domain\Search\ResultSet\Facets\AbstractFacet;
use ApacheSolrForTypo3\Solrfluid\Domain\Search\ResultSet\Facets\FacetCollection;
use ApacheSolrForTypo3\Solrfluid\Domain\Search\ResultSet\Sorting\Sorting;
use ApacheSolrForTypo3\Solrfluid\Domain\Search\ResultSet\Sorting\SortingCollection;
use ApacheSolrForTypo3\Solrfluid\Domain\Search\ResultSet\Spellchecking\Suggestion;

/**
 * SearchResultSet
 *
 * Aggregate root object for all result related entities
 *
 *  - Documents
 *  - Facets
 *  - Spellchecking suggestions
 *
 * @author Frans Saris <frans@beech.it>
 * @author Timo Hund <timo.hund@dkd.de>
 * @package ApacheSolrForTypo3\Solrfluid\Domain\Search\ResultSet
 */
class SearchResultSet extends SolrSearchResultSet
{
    /**
     * @var int
     */
    protected $allResultCount = 0;

    /**
     * @var Suggestion[]
     */
    protected $spellCheckingSuggestions = [];

    /**
     * @var FacetCollection
     */
    protected $facets = null;

    /**
     * @var SortingCollection
     */
    protected $sortings = null;

    /**
     * @var bool
     */
    protected $isAutoCorrected = false;

    /**
     * @var string
     */
    protected $initialQueryString = '';

    /**
     * @var string
     */
    protected $correctedQueryString = '';

    /**
     * SearchResultSet constructor.
     */
    public function __construct()
    {
        $this->facets = new FacetCollection();
        $this->sortings = new SortingCollection();
    }

    /**
     * @param int $allResultCount
     */
    public function setAllResultCount($allResultCount)
    {
        $this->allResultCount = $allResultCount;
    }

    /**
     * @return int
     */
    public function getAllResultCount()
    {
        return $this->allResultCount;
    }

    /**
     * @param Suggestion $suggestion
     */
    public function addSpellCheckingSuggestion(Suggestion $suggestion)
    {
        $this->spellCheckingSuggestions[$suggestion->getSuggestion()] = $suggestion;
    }

    /**
     * @return bool
     */
    public function getHasSpellCheckingSuggestions()
    {
        return count($this->spellCheckingSuggestions) > 0;
    }

    /**
     * @param \ApacheSolrForTypo3\Solrfluid\Domain\Search\ResultSet\Spellchecking\Suggestion[] $spellCheckingSuggestions
     */
    public function setSpellCheckingSuggestions($spellCheckingSuggestions)
    {
        $this->spellCheckingSuggestions = $spellCheckingSuggestions;
    }

    /**
     * @return \ApacheSolrForTypo3\Solrfluid\Domain\Search\ResultSet\Spellchecking\Suggestion[]
     */
    public function getSpellCheckingSuggestions()
    {
        return $this->spellCheckingSuggestions;
    }

    /**
     * @return FacetCollection
     */
    public function getFacets()
    {
        return $this->facets;
    }

    /**
     * @param AbstractFacet $facet
     */
    public function addFacet(AbstractFacet $facet)
    {
        $this->facets->addFacet($facet);
    }

    /**
     * @param Sorting $sorting
     */
    public function addSorting(Sorting $sorting)
    {
        $this->sortings->addSorting($sorting);
    }

    /**
     * @return SortingCollection
     */
    public function getSortings()
    {
        return $this->sortings;
    }

    /**
     * @return bool
     */
    public function getIsAutoCorrected()
    {
        return $this->isAutoCorrected;
    }

    /**
     * @param bool $wasAutoCorrected
     */
    public function setIsAutoCorrected($wasAutoCorrected)
    {
        $this->isAutoCorrected = $wasAutoCorrected;
    }

    /**
     * @return string
     */
    public function getInitialQueryString()
    {
        return $this->initialQueryString;
    }

    /**
     * @param string $initialQueryString
     */
    public function setInitialQueryString($initialQueryString)
    {
        $this->initialQueryString = $initialQueryString;
    }

    /**
     * @return string
     */
    public function getCorrectedQueryString()
    {
        return $this->correctedQueryString;
    }

    /**
     * @param string $correctedQueryString
     */
    public function setCorrectedQueryString($correctedQueryString)
    {
        $this->correctedQueryString = $correctedQueryString;
    }
}
