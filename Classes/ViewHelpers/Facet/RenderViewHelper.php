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
use ApacheSolrForTypo3\Solrfluid\ViewHelpers\AbstractViewHelper;
use TYPO3\CMS\Core\Utility\GeneralUtility;

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
        $configuredFacets = $this->getTypoScriptConfiguration()->getSearchFacetingFacets();
        /** @var \ApacheSolrForTypo3\Solr\Facet\FacetRendererFactory $facetRendererFactory */
        $facetRendererFactory = GeneralUtility::makeInstance(
            'ApacheSolrForTypo3\Solr\Facet\FacetRendererFactory',
            $configuredFacets
        );


        /** @var \ApacheSolrForTypo3\Solr\FacetRenderer $renderer */
        $facetRenderer = $facetRendererFactory->getFacetRendererByFacet($facet);
        if (!$facetRenderer instanceof FacetFluidRendererInterface) {
            $resultsTemplate = $this->getTypoScriptConfiguration()->getTemplateByFileKey('results');
            /** @var \ApacheSolrForTypo3\Solr\Template $template */
            $template = GeneralUtility::makeInstance(
                'ApacheSolrForTypo3\\Solr\\Template',
                GeneralUtility::makeInstance('TYPO3\\CMS\\Frontend\\ContentObject\\ContentObjectRenderer'),
                $resultsTemplate,
                'available_facets'
            );


            $facetRenderer->setTemplate($template);
            $facetRenderer->setLinkTargetPageId($this->getTypoScriptConfiguration()->getSearchTargetPage());
            $facet = $facetRenderer->getFacetProperties();
            $template->addVariable('facet', $facet);
        }

        return $facetRenderer->renderFacet();
    }
}
