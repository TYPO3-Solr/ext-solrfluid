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
 * @author Timo Hund <timo.hund@dkd.de>
 * @package ApacheSolrForTypo3\Solrfluid\Domain\Search\ResultSet\Facets\OptionsFacet
 */
abstract class AbstractFacetItem
{
    /**
     * @var string
     */
    protected $label = '';

    /**
     * @var int
     */
    protected $documentCount = 0;

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
     * @param int $documentCount
     * @param bool $selected
     */
    public function __construct(AbstractFacet $facet, $label = '', $documentCount = 0, $selected = false)
    {
        $this->facet = $facet;
        $this->label = $label;
        $this->documentCount = $documentCount;
        $this->selected = $selected;
    }

    /**
     * @return int
     */
    public function getDocumentCount()
    {
        return $this->documentCount;
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
    abstract public function getUriValue();
}
