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
use ApacheSolrForTypo3\Solrfluid\Domain\Search\ResultSet\Facets\AbstractFacetItem;

/**
 * Value object that represent an option of a options facet.
 *
 * @author Frans Saris <frans@beech.it>
 * @author Timo Schmidt <timo.schmidt@dkd.de>
 * @package ApacheSolrForTypo3\Solrfluid\Domain\Search\ResultSet\Facets\OptionsFacet
 */
class Node extends AbstractFacetItem
{

    /**
     * @var NodeCollection
     */
    protected $childNodes;

    /**
     * @var Node
     */
    protected $parentNode;

    /**
     * @var integer
     */
    protected $depth;

    /**
     * @var string
     */
    protected $key;

    /**
     * @param HierarchyFacet $facet
     * @param Node $parentNode
     * @param string $key
     * @param string $label
     * @param string $value
     * @param int $count
     * @param bool $selected
     */
    public function __construct(HierarchyFacet $facet, $parentNode = null, $key = '', $label = '', $value = '', $count = 0, $selected = false)
    {
        parent::__construct($facet, $label, $value, $count, $selected);
        $this->childNodes = new NodeCollection();
        $this->parentNode = $parentNode;
        $this->key = $key;
    }

    /**
     * @return string
     */
    public function getKey()
    {
        return $this->key;
    }

    /**
     * @param Node $node
     */
    public function addChildNode(Node $node)
    {
        $this->childNodes->add($node);
    }
}
