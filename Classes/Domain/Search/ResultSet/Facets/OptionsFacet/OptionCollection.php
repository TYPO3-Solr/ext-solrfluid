<?php
namespace ApacheSolrForTypo3\Solrfluid\Domain\Search\ResultSet\Facets\OptionsFacet;

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
 * Collection for facet options.
 *
 * @author Frans Saris <frans@beech.it>
 * @author Timo Schmidt <timo.schmidt@dkd.de>
 * @package ApacheSolrForTypo3\Solrfluid\Domain\Search\ResultSet\Facets\OptionsFacet
 */
class OptionCollection extends AbstractCollection
{
    /**
     * @param Option $option
     * @return OptionCollection
     */
    public function addOption(Option $option)
    {
        if ($option === null) {
            return $this;
        }

        $this->data[$option->getValue()] = $option;
        return $this;
    }

    /**
     * @param string $value
     * @return null
     */
    public function getByValue($value)
    {
        return isset($this->data[$value]) ? $this->data[$value] : null;
    }

    /**
     * Retrieves the count (with get prefixed to be usable in fluid).
     *
     * @return int
     */
    public function getCount()
    {
        return $this->count();
    }

    /**
     * @return OptionCollection
     */
    public function getSelected()
    {
        return $this->getFilteredCopy(function (Option $option) {
            return $option->getSelected();
        });
    }

    /**
     * @param array $manualSorting
     * @return OptionCollection
     */
    public function getManualSortedCopy(array $manualSorting)
    {
        $result = clone $this;
        $copiedOptions = $result->data;
        $sortedOptions = [];
        foreach ($manualSorting as $item) {
            if (isset($copiedOptions[$item])) {
                $sortedOptions[] = $copiedOptions[$item];
                unset($copiedOptions[$item]);
            }
        }
            // in the end all items get appended that are not configured in the manual sort order
        $sortedOptions = $sortedOptions + $copiedOptions;
        $result->data = $sortedOptions;

        return $result;
    }
}
