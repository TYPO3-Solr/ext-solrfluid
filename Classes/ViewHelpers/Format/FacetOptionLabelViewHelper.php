<?php
namespace ApacheSolrForTypo3\Solrfluid\ViewHelpers\Format;

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
use ApacheSolrForTypo3\Solr\Util;
use ApacheSolrForTypo3\Solrfluid\ViewHelpers\AbstractViewHelper;
use TYPO3\CMS\Core\SingletonInterface;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Frontend\ContentObject\ContentObjectRenderer;

/**
 * Class facetOptionLabelViewHelper
 */
class FacetOptionLabelViewHelper extends AbstractViewHelper implements SingletonInterface
{

    /**
     * @var ContentObjectRenderer
     */
    protected $contentObjectRenderer;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->contentObjectRenderer = GeneralUtility::makeInstance('TYPO3\\CMS\\Frontend\\ContentObject\\ContentObjectRenderer');
    }

    /**
     * Get facet option label
     *
     * @param \ApacheSolrForTypo3\Solr\Facet\Facet $facet
     * @param string $optionValue
     * @return string
     */
    public function render(\ApacheSolrForTypo3\Solr\Facet\Facet $facet, $optionValue = null)
    {
        if ($optionValue === null) {
            $optionValue = $this->renderChildren();
        }

        /** @var \ApacheSolrForTypo3\Solr\Facet\FacetOption $facetOption */
        $facetOption = GeneralUtility::makeInstance('ApacheSolrForTypo3\Solr\Facet\FacetOption',
            $facet->getName(),
            $optionValue
        );

        $facetConfiguration = $this->getTypoScriptConfiguration()->getSearchFacetingFacets();

        // FIXME decouple this
        if ($facetConfiguration[$facet->getName() . '.']['type'] == 'hierarchy') {
            $filterEncoder = GeneralUtility::makeInstance('ApacheSolrForTypo3\Solr\Query\FilterEncoder\Hierarchy');
            $facetRenderer = GeneralUtility::makeInstance('ApacheSolrForTypo3\Solr\Facet\HierarchicalFacetRenderer', $facet);
            $optionValueLabel = $facetRenderer->getLastPathSegmentFromHierarchicalFacetOption($filterEncoder->decodeFilter($optionValue));
        } else {
            $optionValueLabel = $facetOption->render();
        }

        return $optionValueLabel;
    }
}
