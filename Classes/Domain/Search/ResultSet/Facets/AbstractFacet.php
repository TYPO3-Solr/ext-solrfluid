<?php
namespace ApacheSolrForTypo3\Solrfluid\Domain\Search\ResultSet\Facets;

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
 * Value object that represent a options facet.
 *
 * @author Frans Saris <frans@beech.it>
 * @author Timo Schmidt <timo.schmidt@dkd.de>
 * @package ApacheSolrForTypo3\Solrfluid\Domain\Search\ResultSet\Facets\OptionsFacet
 */
abstract class AbstractFacet
{
    const TYPE_ABSTRACT = 'abstract';

    /**
     * String
     * @var string
     */
    protected $type = self::TYPE_ABSTRACT;

    /**
     * The resultSet where this facet belongs to.
     *
     * @var null
     */
    protected $resultSet = null;

    /**
     * @var string
     */
    protected $title;

    /**
     * @param SearchResultSet $resultSet
     * @param string $title
     */
    public function __construct(SearchResultSet $resultSet, $title = '') {
        $this->resultSet = $resultSet;
        $this->title = $title;
    }

    /**
     * @param string $title
     */
    public function setTitle($title)
    {
        $this->title = $title;
    }

    /**
     * @return string
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * @return string
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * @return null
     */
    public function getResultSet()
    {
        return $this->resultSet;
    }
}