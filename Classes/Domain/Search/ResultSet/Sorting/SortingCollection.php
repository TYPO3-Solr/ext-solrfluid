<?php

namespace ApacheSolrForTypo3\Solrfluid\Domain\Search\ResultSet\Sorting;

use ApacheSolrForTypo3\Solrfluid\System\Data\AbstractCollection;

/**
 * Class SortingCollection
 * @package ApacheSolrForTypo3\Solrfluid\Domain\Search\ResultSet\Facets
 */
class SortingCollection extends AbstractCollection
{
    /**
     * @var Sorting
     */
    protected $selected;

    /**
     * @param Sorting $sorting
     */
    public function addSorting(Sorting $sorting)
    {
        if ($sorting->getSelected()) {
            $this->selected = $sorting;
        }

        $this->data[$sorting->getName()] = $sorting;
    }

    /**
     * @param array $data
     */
    public function setData($data)
    {
        $this->data = $data;
    }

    /**
     * @return array
     */
    public function getData()
    {
        return $this->data;
    }

    /**
     * @param Sorting $selected
     */
    protected function setSelected(Sorting $selected)
    {
        $this->selected = $selected;
    }

    /**
     * @return Sorting
     */
    public function getSelected()
    {
        return $this->selected;
    }

    /**
     * @return bool
     */
    public function getHasSelected()
    {
        return $this->selected !== null;
    }
}
