<?php
namespace ApacheSolrForTypo3\Solrfluid\Domain\Search\ResultSet\Facets\HierarchyFacet;

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
use ApacheSolrForTypo3\Solrfluid\Domain\Search\ResultSet\Facets\AbstractFacetItemCollection;

/**
 * Collection for facet options.
 *
 * @author Frans Saris <frans@beech.it>
 * @author Timo Schmidt <timo.schmidt@dkd.de>
 * @package ApacheSolrForTypo3\Solrfluid\Domain\Search\ResultSet\Facets\OptionsFacet
 */
class NodeCollection extends AbstractFacetItemCollection
{
    /**
     * @var array
     */
    protected static $nodesByKey;

    /**
     * @param Node $node
     * @return NodeCollection
     */
    public function add($node)
    {
        self::$nodesByKey[$node->getKey()] = $node;
        return parent::add($node);
    }

    /**
     * @param string $key
     * @return Node|null
     */
    public function getByKey($key)
    {
        return isset(self::$nodesByKey[$key]) ? self::$nodesByKey[$key] : null;
    }
}
