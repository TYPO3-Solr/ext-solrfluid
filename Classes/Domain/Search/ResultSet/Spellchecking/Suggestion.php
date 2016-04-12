<?php
namespace ApacheSolrForTypo3\Solrfluid\Domain\Search\ResultSet\Spellchecking;

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

/**
 * Value object that represent a spellchecking suggestion.
 *
 * @package ApacheSolrForTypo3\Solrfluid\Domain\Search\ResultSet\Spellchecking
 */
class Suggestion
{

    /**
     * @var string
     */
    protected $suggestion = '';

    /**
     * @var int
     */
    protected $numFound = 1;

    /**
     * @var int
     */
    protected $startOffset = 0;

    /**
     * @var int
     */
    protected $endOffset = 0;

    /**
     * @param string $suggestion the suggested term
     * @param string $missSpelled the misspelled original term
     * @param int $numFound
     * @param int $startOffset
     * @param int $endOffset
     */
    public function __construct($suggestion = '', $missSpelled = '', $numFound = 1, $startOffset = 0, $endOffset = 0)
    {
        $this->suggestion = $suggestion;
        $this->numFound = $numFound;
        $this->startOffset = $startOffset;
        $this->endOffset = $endOffset;
    }

    /**
     * @param int $endOffset
     */
    public function setEndOffset($endOffset)
    {
        $this->endOffset = $endOffset;
    }

    /**
     * @return int
     */
    public function getEndOffset()
    {
        return $this->endOffset;
    }

    /**
     * @param int $numFound
     */
    public function setNumFound($numFound)
    {
        $this->numFound = $numFound;
    }

    /**
     * @return int
     */
    public function getNumFound()
    {
        return $this->numFound;
    }

    /**
     * @param int $startOffset
     */
    public function setStartOffset($startOffset)
    {
        $this->startOffset = $startOffset;
    }

    /**
     * @return int
     */
    public function getStartOffset()
    {
        return $this->startOffset;
    }

    /**
     * @param string $term
     */
    public function setSuggestion($term)
    {
        $this->suggestion = $term;
    }

    /**
     * @return string
     */
    public function getSuggestion()
    {
        return $this->suggestion;
    }
}
