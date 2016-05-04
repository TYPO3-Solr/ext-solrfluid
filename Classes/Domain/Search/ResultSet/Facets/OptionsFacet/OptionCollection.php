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

/**
 * Collection for facet options.
 *
 * @author Frans Saris <frans@beech.it>
 * @author Timo Schmidt <timo.schmidt@dkd.de>
 * @package ApacheSolrForTypo3\Solrfluid\Domain\Search\ResultSet\Facets\OptionsFacet
 */
class OptionCollection extends \ArrayObject
{
    /**
     * @param Option $option
     * @return OptionCollection
     */
    public function addOption(Option $option)
    {
        $this->append($option);
        return $this;
    }

    /**
     * Retrieves the count (with get prefixed to be usable in fluid).
     *
     * @return int
     */
    public function getCount() {
        return $this->count();
    }

    /**
     * @return OptionCollection
     */
    public function getSelected()
    {
        $available = new OptionCollection();
        foreach($this as $option) {
            /** @var $option Option */
            if($option->getSelected()) {
                $available->addOption($option);
            }
        }

        return $available;
    }
}