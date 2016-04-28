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

use ApacheSolrForTypo3\Solr\Facet\Facet;
use ApacheSolrForTypo3\Solr\Util;
use ApacheSolrForTypo3\Solrfluid\ViewHelpers\AbstractViewHelper;
use TYPO3\CMS\Core\SingletonInterface;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Frontend\ContentObject\ContentObjectRenderer;

/**
 * Class FacetLabelViewHelper
 *
 * @author Frans Saris <frans@beech.it>
 * @author Timo Schmidt <timo.schmidt@dkd.de>
 * @package ApacheSolrForTypo3\Solrfluid\ViewHelpers\Format
 */
class FacetLabelViewHelper extends AbstractViewHelper implements SingletonInterface
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
     * @param Facet $facet
     * @return string
     */
    public function render(Facet $facet)
    {
        $facetConfiguration = $this->getTypoScriptConfiguration()->getSearchFacetingFacets();
        $currentFacet = $facetConfiguration[$facet->getName() . '.'];
        $facetLabel = $this->contentObjectRenderer->stdWrap($currentFacet['label'], $currentFacet['label.']);
        return $facetLabel ? : $facet->getName();
    }
}
