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

use \ApacheSolrForTypo3\Solr\Domain\Search\ResultSet\SearchResultSet as SolrSearchResultSet;
use ApacheSolrForTypo3\Solrfluid\Domain\Search\ResultSet\Facets\AbstractFacet;
use ApacheSolrForTypo3\Solrfluid\Domain\Search\ResultSet\Spellchecking\Suggestion;

/**
 * Adds the domain model to the SearchResult set that is missing in EXT:solr by now.
 *
 * @todo: the logic in this class can be added to the SearchResultSet after adding EXT:solrfluid to EXT:solr
 *
 * @author Frans Saris <frans@beech.it>
 * @author Timo Schmidt <timo.schmidt@dkd.de>
 * @package ApacheSolrForTypo3\Solrfluid\Domain\Search\ResultSet
 */
class SearchResultSet extends SolrSearchResultSet
{

    /**
     * @var Suggestion[]
     */
    protected $spellCheckingSuggestions = [];

    /**
     * @var AbstractFacet[]
     */
    protected $facets = [];

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
     * @return Facets\AbstractFacet[]
     */
    public function getFacets()
    {
        return $this->facets;
    }

    /**
     * @param AbstractFacet $facet
     */
    public function addFacet(AbstractFacet $facet) {
        $this->facets[$facet->getName()] = $facet;
    }
}
