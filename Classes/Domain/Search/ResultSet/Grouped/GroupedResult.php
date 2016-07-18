<?php
namespace ApacheSolrForTypo3\Solrfluid\Domain\Search\ResultSet\Grouped;
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
 * Class GroupedResult
 *
 * @author Frans Saris <frans@beech.it>
 * @package ApacheSolrForTypo3\Solrfluid\Domain\Search\ResultSet\Grouped
 */
class GroupedResult
{
    /**
     * @var string
     */
    protected $name;

    /**
     * @var Group[]
     */
    protected $groups;

    /**
     * @var int
     */
    protected $numFound = 0;

    /**
     * Section constructor
     *
     * @param string $name
     */
    public function __construct($name)
    {
        $this->name = $name;
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
     * @param Group $group
     */
    public function addGroup(Group $group)
    {
        $this->groups[] = $group;
        $this->numFound += $group->getNumFound();
    }

    /**
     * Get groups
     *
     * @return Group[]
     */
    public function getGroups()
    {
        return $this->groups;
    }

    /**
     * @todo: implement count
     *
     * @return int
     */
    public function getCount()
    {
        return $this->numFound;
    }
}
