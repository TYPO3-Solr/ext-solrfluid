<?php
namespace ApacheSolrForTypo3\Solrfluid\Domain\Search\ResultSet;

/***************************************************************
 *  Copyright notice
 *
 *  (c) 2015-2016 Timo Schmidt <timo.schmidt@dkd.de>
 *  All rights reserved
 *
 *  This script is part of the TYPO3 project. The TYPO3 project is
 *  free software; you can redistribute it and/or modify
 *  it under the terms of the GNU General Public License as published by
 *  the Free Software Foundation; either version 2 of the License, or
 *  (at your option) any later version.
 *
 *  The GNU General Public License can be found at
 *  http://www.gnu.org/copyleft/gpl.html.
 *
 *  This script is distributed in the hope that it will be useful,
 *  but WITHOUT ANY WARRANTY; without even the implied warranty of
 *  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *  GNU General Public License for more details.
 *
 *  This copyright notice MUST APPEAR in all copies of the script!
 ***************************************************************/

use \ApacheSolrForTypo3\Solr\Domain\Search\ResultSet\SearchResultSet as SolrSearchResultSet;
use ApacheSolrForTypo3\Solrfluid\Domain\Search\ResultSet\Spellchecking\Suggestion;

/**
 * Adds the domain model to the SearchResult set that is missing in EXT:solr by now.
 *
 * @todo: the logic in this class can be added to the SearchResultSet after adding EXT:solrfluid to EXT:solr
 * @package ApacheSolrForTypo3\Solrfluid\Domain\Search\ResultSet
 */
class SearchResultSet extends SolrSearchResultSet
{

    /**
     * @var Suggestion[]
     */
    protected $spellCheckingSuggestions = array();

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
}
