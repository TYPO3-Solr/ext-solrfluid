<?php

namespace ApacheSolrForTypo3\Solrfluid\Domain\Search\ResultSet\Facets;

use ApacheSolrForTypo3\Solrfluid\System\Data\AbstractCollection;

/**
 * Class FacetCollection
 * @package ApacheSolrForTypo3\Solrfluid\Domain\Search\ResultSet\Facets
 */
class FacetCollection extends AbstractCollection
{

    /**
     * @param AbstractFacet $facet
     */
    public function addFacet(AbstractFacet $facet)
    {
        $this->data[] = $facet;
    }

    /**
     * @return FacetCollection
     */
    public function getUsed()
    {
        return $this->getFilteredCopy(
            function (AbstractFacet $facet) { return $facet->getIsUsed(); }
        );
    }

    /**
     * @return FacetCollection
     */
    public function getAvailable()
    {
        return $this->getFilteredCopy(
            function (AbstractFacet $facet) { return $facet->getIsAvailable(); }
        );
    }

    /**
     * @param $position
     * @return AbstractFacet
     */
    public function getByPosition($position)
    {
        return parent::getByPosition($position);
    }
}
