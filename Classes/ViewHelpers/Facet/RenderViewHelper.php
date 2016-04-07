<?php
namespace ApacheSolrForTypo3\Solrfluid\ViewHelpers\Facet;

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
use ApacheSolrForTypo3\Solr\Facet\FacetFluidRendererInterface;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Fluid\Core\ViewHelper\AbstractViewHelper;

/**
 * Class RenderViewHelper
 */
class RenderViewHelper extends AbstractViewHelper
{

    /**
     * Render facet
     *
     * @param \ApacheSolrForTypo3\Solr\Facet\Facet $facet
     * @return string
     */
    public function render(\ApacheSolrForTypo3\Solr\Facet\Facet $facet)
    {
        // todo: fetch from ControllerContext
        $configuration = \ApacheSolrForTypo3\Solr\Util::getSolrConfiguration();
        $configuredFacets = $configuration['search.']['faceting.']['facets.'];
        /** @var \ApacheSolrForTypo3\Solr\Facet\FacetRendererFactory $facetRendererFactory */
        $facetRendererFactory = GeneralUtility::makeInstance(
            'ApacheSolrForTypo3\Solr\Facet\FacetRendererFactory',
            $configuredFacets
        );


        /** @var \ApacheSolrForTypo3\Solr\FacetRenderer $renderer */
        $facetRenderer = $facetRendererFactory->getFacetRendererByFacet($facet);
        if (!$facetRenderer instanceof FacetFluidRendererInterface) {
            /** @var \ApacheSolrForTypo3\Solr\Template $template */
            $template = GeneralUtility::makeInstance(
                'ApacheSolrForTypo3\\Solr\\Template',
                GeneralUtility::makeInstance('TYPO3\\CMS\\Frontend\\ContentObject\\ContentObjectRenderer'),
                $configuration['templateFiles.']['results'],
                'available_facets'
            );


            $facetRenderer->setTemplate($template);
            $facetRenderer->setLinkTargetPageId($configuration->getSearchTargetPage());
            $facet = $facetRenderer->getFacetProperties();
            $template->addVariable('facet', $facet);
        }

        return $facetRenderer->renderFacet();
    }
}
