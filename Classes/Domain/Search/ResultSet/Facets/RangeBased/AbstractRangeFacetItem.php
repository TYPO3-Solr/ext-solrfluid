<?php
namespace ApacheSolrForTypo3\Solrfluid\Domain\Search\ResultSet\Facets\RangeBased;

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

use ApacheSolrForTypo3\Solrfluid\Domain\Search\ResultSet\Facets\AbstractFacetItem;

/**
 * Abstract class that is used as base class for range facet items
 *
 * @author Frans Saris <frans@beech.it>
 * @author Timo Hund <timo.hund@dkd.de>
 * @package ApacheSolrForTypo3\Solrfluid\Domain\Search\ResultSet\Facets\OptionsFacet
 */
abstract class AbstractRangeFacetItem extends AbstractFacetItem
{

    /**
     * @var array
     */
    protected $rangeCounts;


    /**
     * @var string
     */
    protected $gap;

    /**
     * @return string
     */
    public function getUriValue()
    {
        return $this->getRangeString();
    }

    /**
     * @return array
     */
    public function getRangeCounts()
    {
        return $this->rangeCounts;
    }

    /**
     * @return string
     */
    public function getGap()
    {
        return $this->gap;
    }

    /**
     * @return string
     */
    abstract protected function getRangeString();
}
