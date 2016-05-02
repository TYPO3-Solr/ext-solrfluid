<?php
namespace ApacheSolrForTypo3\Solrfluid\Domain\Search\FacetRenderer;

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
use ApacheSolrForTypo3\Solrfluid\Mvc\Controller\SolrControllerContext;

/**
 * Interface FacetFluidRendererInterface
 *
 * @author Frans Saris <frans@beech.it>
 * @author Timo Schmidt <timo.schmidt@dkd.de>
 * @package ApacheSolrForTypo3\Solrfluid\Facet
 */
interface FacetFluidRendererInterface
{
    /**
     * Set Controller context
     *
     * @param SolrControllerContext $controllerContext
     * @return void
     */
    public function setControllerContext(SolrControllerContext $controllerContext);

    /**
     * Renders the complete facet.
     *
     * @return string The rendered facet
     */
    public function renderFacet();

    /**
     * Provides the internal type of facets the renderer handles.
     * The type is one of field, range, or query.
     *
     * @return string Facet internal type
     */
    public static function getFacetInternalType();

    /**
     * Gets the facet object markers for use in templates.
     *
     * @return array An array with facet object markers.
     */
    public function getFacetProperties();

    /**
     * Gets the facet's options
     *
     * @return array An array with facet options.
     */
    public function getFacetOptions();

    /**
     * Gets the number of options for a facet.
     *
     * @return integer Number of facet options for the current facet.
     */
    public function getFacetOptionsCount();
}
