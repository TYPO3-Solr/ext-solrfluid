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
    static protected $type = self::TYPE_ABSTRACT;

    /**
     * The resultSet where this facet belongs to.
     *
     * @var null
     */
    protected $resultSet = null;

    /**
     * @var string
     */
    protected $name;

    /**
     * @var string
     */
    protected $field;

    /**
     * @var string
     */
    protected $label;

    /**
     * @var []
     */
    protected $configuration;

    /**
     * AbstractFacet constructor.
     *
     * @param SearchResultSet $resultSet
     * @param string $name
     * @param string $field
     * @param string $label
     * @param array $configuration Facet configuration passed from typoscript
     */
    public function __construct(SearchResultSet $resultSet, $name, $field, $label = '', array $configuration = [])
    {
        $this->resultSet = $resultSet;
        $this->name = $name;
        $this->field = $field;
        $this->label = $label;
        $this->configuration = $configuration;
    }

    /**
     * Get name
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Get solr field name
     *
     * @return string
     */
    public function getField()
    {
        return $this->field;
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
     * @return string
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * @return SearchResultSet
     */
    public function getResultSet()
    {
        return $this->resultSet;
    }

    /**
     * Get configuration
     *
     * @return mixed
     */
    public function getConfiguration()
    {
        return $this->configuration;
    }

    /**
     * Get facet partial name used for rendering the facet
     *
     * @return string
     */
    public function getPartialName() {
        return 'Default';
    }
}
