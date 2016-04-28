<?php
namespace ApacheSolrForTypo3\Solrfluid\ViewHelpers;

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

use ApacheSolrForTypo3\Solr\System\Configuration\TypoScriptConfiguration;
use ApacheSolrForTypo3\Solrfluid\Domain\Search\ResultSet\SearchResultSet;
use TYPO3\CMS\Core\Utility\GeneralUtility;

/**
 * Class FacetsViewHelper
 *
 * @author Frans Saris <frans@beech.it>
 * @author Timo Schmidt <timo.schmidt@dkd.de>
 * @package ApacheSolrForTypo3\Solrfluid\ViewHelpers
 */
class FacetsViewHelper extends AbstractViewHelper
{
    /**
     * @var \ApacheSolrForTypo3\Solr\Search
     */
    protected $search;

    /**
     * @var \ApacheSolrForTypo3\Solr\Facet\FacetRendererFactory
     */
    protected $facetRendererFactory;

    /**
     * @var bool
     */
    protected $facetsActive = false;

    /**
     * Get facets
     *
     * @param SearchResultSet $resultSet
     * @param string $facets variable name for the facets
     * @param string $usedFacets variable name for usedFacets
     * @return string
     */
    public function render(SearchResultSet $resultSet, $facets = 'facets', $usedFacets = 'usedFacets')
    {
        $configuredFacets = $this->getTypoScriptConfiguration()->getSearchFacetingFacets();
        $this->search = $resultSet->getUsedSearch();
        $this->facetRendererFactory = GeneralUtility::makeInstance('ApacheSolrForTypo3\Solr\Facet\FacetRendererFactory', $configuredFacets);

        $templateVariableContainer = $this->renderingContext->getTemplateVariableContainer();
        $templateVariableContainer->add($facets, $this->getAvailableFacets($configuredFacets));
        $templateVariableContainer->add($usedFacets, $this->getUsedFacets($configuredFacets));

        $content = $this->renderChildren();

        $templateVariableContainer->remove($facets);
        $templateVariableContainer->remove($usedFacets);

        return $content;
    }

    /**
     * Get available facet objects
     *
     * @param array $configuredFacets
     * @return \ApacheSolrForTypo3\Solr\Facet\Facet[]
     */
    protected function getAvailableFacets(array $configuredFacets)
    {
        $facets = array();
        foreach ($configuredFacets as $facetName => $facetConfiguration) {
            $facetName = substr($facetName, 0, -1);
            /** @var \ApacheSolrForTypo3\Solr\Facet\Facet $facet */
            $facet = GeneralUtility::makeInstance('ApacheSolrForTypo3\Solr\Facet\Facet', $facetName, $this->facetRendererFactory->getFacetInternalType($facetName));

            if ((isset($facetConfiguration['includeInAvailableFacets']) && $facetConfiguration['includeInAvailableFacets'] == '0') || !$facet->isRenderingAllowed()
            ) {
                // don't render facets that should not be included in available facets
                // or that do not meet their requirements to be rendered
                continue;
            }

            if ($facet->isActive()) {
                $this->facetsActive = true;
            }
            $facets[] = $facet;
        }

        return $facets;
    }

    /**
     * @param array $configuredFacets
     * @return array
     */
    protected function getUsedFacets(array $configuredFacets)
    {
        $resultParameters = GeneralUtility::_GET('tx_solr');
        $filterParameters = array();
        if (isset($resultParameters['filter'])) {
            $filterParameters = (array)array_map('urldecode', $resultParameters['filter']);
        }

        $facetsInUse = array();
        foreach ($filterParameters as $filter) {
            // only split by the first ":" to allow the use of colons in the filter value
            list($facetName, $filterValue) = explode(':', $filter, 2);

            $facetConfiguration = $configuredFacets[$facetName . '.'];

            // don't render facets that should not be included in used facets
            if (empty($facetConfiguration) || (isset($facetConfiguration['includeInUsedFacets']) && $facetConfiguration['includeInUsedFacets'] == '0')
            ) {
                continue;
            }

            /** @var \ApacheSolrForTypo3\Solr\Facet\Facet $facet */
            $facet = GeneralUtility::makeInstance('ApacheSolrForTypo3\Solr\Facet\Facet', $facetName, $this->facetRendererFactory->getFacetInternalType($facetName));

            $facetsInUse[] = $facet;
        }

        return $facetsInUse;
    }
}
