<?php
namespace ApacheSolrForTypo3\Solrfluid\Domain\Search\ResultSet;

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

use ApacheSolrForTypo3\Solr\Domain\Search\ResultSet\SearchResult as SolrSearchResult;

/**
 * SearchResult with extensions needed for solrfluid.
 *
 * @package ApacheSolrForTypo3\Solrfluid\Domain\Search\ResultSet
 */
class SearchResult extends SolrSearchResult
{

    /**
     * @return string
     */
    public function getContent()
    {
        return $this->_fields['content'];
    }

    /**
     * @return boolean
     */
    public function getIsElevated()
    {
        return $this->_fields['isElevated'];
    }

    /**
     * @return string
     */
    public function getType()
    {
        return $this->_fields['type'];
    }

    /**
     * @return integer
     */
    public function getId()
    {
        return $this->_fields['id'];
    }

    /**
     * @return float
     */
    public function getScore()
    {
        return $this->_fields['score'];
    }

    /**
     * @return string
     */
    public function getUrl()
    {
        return $this->_fields['url'];
    }

    /**
     * @return string
     */
    public function getTitle()
    {
        return $this->_fields['title'];
    }
}
