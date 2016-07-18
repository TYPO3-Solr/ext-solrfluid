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

/**
 * Class Group
 *
 * @author Frans Saris <frans@beech.it>
 * @author Timo Schmidt <timo.schmidt@dkd.de>
 * @package ApacheSolrForTypo3\Solrfluid\Domain\Search\ResultSet\Grouped
 */
class Group
{
    /**
     * @var string
     */
    protected $groupValue = '';

    /**
     * @var int
     */
    protected $numFound = 0;

    /**
     * @var int
     */
    protected $start = 0;

    /**
     * @var float
     */
    protected $maxScore = 0;

    /**
     * @var array
     */
    protected $documents;

    /**
     * @param string $groupValue
     * @param int $numFound
     * @param int $start
     * @param float $maxScore
     */
    public function __construct($groupValue, $numFound, $start, $maxScore)
    {
        $this->groupValue = $groupValue;
        $this->numFound = $numFound;
        $this->start = $start;
        $this->maxScore = $maxScore;
    }

    /**
     * Get groupValue
     *
     * @return string
     */
    public function getGroupValue()
    {
        return $this->groupValue;
    }

    /**
     * Get numFound
     *
     * @return int
     */
    public function getNumFound()
    {
        return $this->numFound;
    }

    /**
     * Get start
     *
     * @return int
     */
    public function getStart()
    {
        return $this->start;
    }

    /**
     * Get maxScore
     *
     * @return float
     */
    public function getMaxScore()
    {
        return $this->maxScore;
    }

    /**
     * Get documents
     *
     * @return array
     */
    public function getDocuments()
    {
        return $this->documents;
    }

    /**
     * Add documents
     *
     * @param array $document
     */
    public function addDocument($document)
    {
        $this->documents[] = $document;
    }
    
    

}
