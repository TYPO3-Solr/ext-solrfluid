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

use ApacheSolrForTypo3\Solrfluid\Domain\Search\FacetRenderer\FacetFluidRendererInterface;
use ApacheSolrForTypo3\Solrfluid\ViewHelpers\AbstractViewHelper;
use TYPO3\CMS\Core\Utility\GeneralUtility;

/**
 * Class RenderViewHelper
 *
 * @author Frans Saris <frans@beech.it>
 * @author Timo Schmidt <timo.schmidt@dkd.de>
 * @package ApacheSolrForTypo3\Solrfluid\ViewHelpers\Facet
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
            \ApacheSolrForTypo3\Solr\Facet\FacetRendererFactory::class,
            $configuredFacets
        );

        /** @var FacetFluidRendererInterface $facetRenderer */
        $facetRenderer = $facetRendererFactory->getFacetRendererByFacet($facet);
        if (!$facetRenderer instanceof FacetFluidRendererInterface) {
            $resultsTemplate = $this->getTypoScriptConfiguration()->getTemplateByFileKey('results');
            /** @var \ApacheSolrForTypo3\Solr\Template $template */
            $template = GeneralUtility::makeInstance(
                \ApacheSolrForTypo3\Solr\Template::class,
                GeneralUtility::makeInstance(\TYPO3\CMS\Frontend\ContentObject\ContentObjectRenderer::class),
                $resultsTemplate,
                'available_facets'
            );

            $facetRenderer->setTemplate($template);
            $facetRenderer->setLinkTargetPageId($this->getTypoScriptConfiguration()->getSearchTargetPage());
            $facet = $facetRenderer->getFacetProperties();
            $template->addVariable('facet', $facet);
        } else {
            $facetRenderer->setControllerContext($this->controllerContext);
        }

        return $facetRenderer->renderFacet();
    }
}
