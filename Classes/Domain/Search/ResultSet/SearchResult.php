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

    //@todo: implement native getters for common schema fields to accelerate the retrieval and avoid __call processing
}
