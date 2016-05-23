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
 * Abstract item that represent a value of a facet. E.g. an option or a node
 *
 * @author Frans Saris <frans@beech.it>
 * @author Timo Schmidt <timo.schmidt@dkd.de>
 * @package ApacheSolrForTypo3\Solrfluid\Domain\Search\ResultSet\Facets\OptionsFacet
 */
abstract class AbstractFacetItem
{
    /**
     * @var string
     */
    protected $label = '';

    /**
     * @var string
     */
    protected $value = '';

    /**
     * @var int
     */
    protected $count = 0;

    /**
     * @var bool
     */
    protected $selected = false;

    /**
     * @var AbstractFacet
     */
    protected $facet;

    /**
     * @param AbstractFacet $facet
     * @param string $label
     * @param string $value
     * @param int $count
     * @param bool $selected
     */
    public function __construct(AbstractFacet $facet, $label = '', $value = '', $count = 0, $selected = false)
    {
        $this->facet = $facet;
        $this->label = $label;
        $this->value = $value;
        $this->count = $count;
        $this->selected = $selected;
    }

    /**
     * @return int
     */
    public function getCount()
    {
        return $this->count;
    }

    /**
     * @return \ApacheSolrForTypo3\Solrfluid\Domain\Search\ResultSet\Facets\AbstractFacet
     */
    public function getFacet()
    {
        return $this->facet;
    }

    /**
     * @return string
     */
    public function getLabel()
    {
        return $this->label;
    }

    /**
     * @return boolean
     */
    public function getSelected()
    {
        return $this->selected;
    }

    /**
     * @return string
     */
    public function getValue()
    {
        return $this->value;
    }
}
