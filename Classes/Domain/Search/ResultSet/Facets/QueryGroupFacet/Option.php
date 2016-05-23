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
use ApacheSolrForTypo3\Solrfluid\Domain\Search\ResultSet\Facets\AbstractFacetItem;

/**
 * Value object that represent an option of a queryGroup facet.
 *
 * @author Frans Saris <frans@beech.it>
 * @author Timo Schmidt <timo.schmidt@dkd.de>
 * @package ApacheSolrForTypo3\Solrfluid\Domain\Search\ResultSet\Facets\QueryGroupFacet
 */
class Option extends AbstractFacetItem
{

    /**
     * @param QueryGroupFacet $facet
     * @param string $label
     * @param string $value
     * @param int $count
     * @param bool $selected
     */
    public function __construct(QueryGroupFacet $facet, $label = '', $value = '', $count = 0, $selected = false)
    {
        parent::__construct($facet, $label, $value, $count, $selected);
    }
}
