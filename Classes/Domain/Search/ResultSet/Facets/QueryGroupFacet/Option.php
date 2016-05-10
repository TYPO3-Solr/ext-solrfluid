<?php
namespace ApacheSolrForTypo3\Solrfluid\Domain\Search\ResultSet\Facets\QueryGroupFacet;

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
 * Value object that represent an option of a queryGroup facet.
 *
 * @author Frans Saris <frans@beech.it>
 * @author Timo Schmidt <timo.schmidt@dkd.de>
 * @package ApacheSolrForTypo3\Solrfluid\Domain\Search\ResultSet\Facets\QueryGroupFacet
 */
class Option
{
    /**
     * @var QueryGroupFacet
     */
    protected $facet;

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
     * @param QueryGroupFacet $facet
     * @param string $label
     * @param string $value
     * @param int $count
     * @param bool $selected
     */
    public function __construct(QueryGroupFacet $facet, $label = '', $value = '', $count = 0, $selected = false)
    {
        $this->facet = $facet;
        $this->label = $label;
        $this->value = $value;
        $this->count = $count;
        $this->selected = $selected;
    }

    /**
     * @param int $count
     */
    public function setCount($count)
    {
        $this->count = $count;
    }

    /**
     * @return int
     */
    public function getCount()
    {
        return $this->count;
    }

    /**
     * @return QueryGroupFacet
     */
    public function getFacet()
    {
        return $this->facet;
    }

    /**
     * @param string $label
     */
    public function setLabel($label)
    {
        $this->label = $label;
    }

    /**
     * @return string
     */
    public function getLabel()
    {
        return $this->label;
    }

    /**
     * @param boolean $selected
     */
    public function setSelected($selected)
    {
        $this->selected = $selected;
    }

    /**
     * @return boolean
     */
    public function getSelected()
    {
        return $this->selected;
    }

    /**
     * @param string $value
     */
    public function setValue($value)
    {
        $this->value = $value;
    }

    /**
     * @return string
     */
    public function getValue()
    {
        return $this->value;
    }
}
