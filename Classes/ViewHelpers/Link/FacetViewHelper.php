<?php
namespace ApacheSolrForTypo3\Solrfluid\ViewHelpers\Link;

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

use TYPO3\CMS\Core\Utility\GeneralUtility;

/**
 * Class FacetViewHelper
 *
 * @author Frans Saris <frans@beech.it>
 * @author Timo Schmidt <timo.schmidt@dkd.de>
 * @package ApacheSolrForTypo3\Solrfluid\ViewHelpers\Link
 */
class FacetViewHelper extends \TYPO3\CMS\Fluid\ViewHelpers\Link\ActionViewHelper
{

    /**
     * @var \ApacheSolrForTypo3\Solr\Search
     */
    protected $search;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->search = GeneralUtility::makeInstance('ApacheSolrForTypo3\Solr\Search');
    }

    /**
     * Create add facet link
     *
     * @param \ApacheSolrForTypo3\Solr\Facet\Facet $facet
     * @param \ApacheSolrForTypo3\Solr\Facet\FacetOption $facetOption
     * @param int $pageUid
     * @param bool $returnUrl
     * @param string $section The anchor to be added to the url
     * @return string
     */
    public function render(\ApacheSolrForTypo3\Solr\Facet\Facet $facet, \ApacheSolrForTypo3\Solr\Facet\FacetOption $facetOption, $pageUid = null, $returnUrl = false, $section = '')
    {
        $linkBuilder = $this->getLinkBuilder($facet, $facetOption);
        if ($pageUid) {
            $linkBuilder->setLinkTargetPageId($pageUid);
        }
        $uri = $linkBuilder->getAddFacetOptionUrl();
        if ($section) {
            $uri .= '#' . $section;
        }
        if (!$returnUrl) {
            $this->tag->addAttribute('href', $uri, false);
            $this->tag->setContent($this->renderChildren());
            $this->tag->forceClosingTag(true);
            return $this->tag->render();
        } else {
            return $uri;
        }
    }

    /**
     * Get LinkBuilder
     *
     * @param \ApacheSolrForTypo3\Solr\Facet\Facet $facet
     * @param \ApacheSolrForTypo3\Solr\Facet\FacetOption $facetOption
     * @return \ApacheSolrForTypo3\Solr\Facet\LinkBuilder
     */
    protected function getLinkBuilder(\ApacheSolrForTypo3\Solr\Facet\Facet $facet, \ApacheSolrForTypo3\Solr\Facet\FacetOption $facetOption)
    {
        /** @var \ApacheSolrForTypo3\Solr\Facet\LinkBuilder $linkBuilder */
        $linkBuilder = GeneralUtility::makeInstance('ApacheSolrForTypo3\Solr\Facet\LinkBuilder', $this->search->getQuery(), $facet->getName(), $facetOption);
        return $linkBuilder;
    }
}
