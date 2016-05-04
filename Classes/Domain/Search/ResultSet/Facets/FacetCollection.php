<?php

namespace ApacheSolrForTypo3\Solrfluid\Domain\Search\ResultSet\Facets;

/**
 * Class FacetCollection
 * @package ApacheSolrForTypo3\Solrfluid\Domain\Search\ResultSet\Facets
 */
class FacetCollection extends \ArrayObject {

    /**
     * @param AbstractFacet $facet
     */
    public function addFacet(AbstractFacet $facet) {
        $this->append($facet);
    }

    /**
     * @return FacetCollection
     */
    public function getUsed()
    {
        $used = new FacetCollection();
        foreach($this as $facet) {
            /** @var $facet AbstractFacet */
            if($facet->getIsUsed()) {
                $used->addFacet($facet);
            }
        }

        return $used;
    }

    /**
     * @return FacetCollection
     */
    public function getAvailable()
    {
        $available = new FacetCollection();
        foreach($this as $facet) {
            /** @var $facet AbstractFacet */
            if($facet->getIsAvailable()) {
                $available->addFacet($facet);
            }
        }

        return $available;

    }
}