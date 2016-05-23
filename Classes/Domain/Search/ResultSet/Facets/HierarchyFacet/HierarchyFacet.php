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

use ApacheSolrForTypo3\Solrfluid\Domain\Search\ResultSet\Facets\AbstractFacet;
use ApacheSolrForTypo3\Solrfluid\Domain\Search\ResultSet\SearchResultSet;

/**
 * Value object that represent a options facet.
 *
 * @author Frans Saris <frans@beech.it>
 * @author Timo Schmidt <timo.schmidt@dkd.de>
 * @package ApacheSolrForTypo3\Solrfluid\Domain\Search\ResultSet\Facets\OptionsFacet
 */
class HierarchyFacet extends AbstractFacet
{
    const TYPE_HIERARCHY = 'hierarchy';

    /**
     * String
     * @var string
     */
    protected static $type = self::TYPE_HIERARCHY;

    /**
     * @var NodeCollection
     */
    protected $childNodes;

    /**
     * OptionsFacet constructor
     *
     * @param SearchResultSet $resultSet
     * @param string $name
     * @param string $field
     * @param string $label
     * @param array $configuration Facet configuration passed from typoscript
     */
    public function __construct(SearchResultSet $resultSet, $name, $field, $label = '', array $configuration = [])
    {
        parent::__construct($resultSet, $name, $field, $label, $configuration);
        $this->childNodes = new NodeCollection();
    }

    /**
     * @param Node $node
     */
    public function addChildNode(Node $node)
    {
        $this->childNodes->add($node);
    }

    /**
     * @param NodeCollection $nodes
     */
    public function setChildNodes($nodes)
    {
        $this->childNodes = $nodes;
    }

    /**
     * @return NodeCollection
     */
    public function getChildNodes()
    {
        return $this->childNodes;
    }

    /**
     * Get facet partial name used for rendering the facet
     *
     * @return string
     */
    public function getPartialName()
    {
        return !empty($this->configuration['partialName']) ? $this->configuration['partialName'] : 'Hierarchy';
    }
}
