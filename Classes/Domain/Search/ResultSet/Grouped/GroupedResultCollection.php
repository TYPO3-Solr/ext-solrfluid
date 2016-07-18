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
use ApacheSolrForTypo3\Solrfluid\System\Data\AbstractCollection;

/**
 * Class GroupedResultCollection
 *
 * @author Frans Saris <frans@beech.it>
 * @package ApacheSolrForTypo3\Solrfluid\Domain\Search\ResultSet\Grouped
 */
class GroupedResultCollection extends AbstractCollection
{
    /**
     * @param GroupedResult $groupedResult
     */
    public function addGroupedResult(GroupedResult $groupedResult)
    {
        $this->data[$groupedResult->getName()] = $groupedResult;
    }
}
