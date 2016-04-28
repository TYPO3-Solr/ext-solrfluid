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

use ApacheSolrForTypo3\Solr\Facet\Facet;
use ApacheSolrForTypo3\Solr\Facet\FacetOption;
use TYPO3\CMS\Core\Utility\GeneralUtility;

/**
 * Class RemoveFacetViewHelper
 *
 * @author Frans Saris <frans@beech.it>
 * @author Timo Schmidt <timo.schmidt@dkd.de>
 * @package ApacheSolrForTypo3\Solrfluid\ViewHelpers\Link
 */
class RemoveFacetViewHelper extends FacetViewHelper
{

    /**
     * Create remove facet link
     *
     * @param Facet $facet
     * @param FacetOption $facetOption
     * @param string $optionValue
     * @param int $pageUid
     * @param bool $returnUrl
     * @param string $section The anchor to be added to the url
     * @return string
     */
    public function render(Facet $facet, FacetOption $facetOption = null, $optionValue = null, $pageUid = null, $returnUrl = false, $section = '')
    {
        if ($facetOption === null) {
            /** @var FacetOption $facetOption */
            $facetOption = GeneralUtility::makeInstance('ApacheSolrForTypo3\Solr\Facet\FacetOption', $facet->getName(), $optionValue);
        }
        $linkBuilder = $this->getLinkBuilder($facet, $facetOption);
        if ($pageUid) {
            $linkBuilder->setLinkTargetPageId($pageUid);
        }
        $uri = $linkBuilder->getRemoveFacetOptionUrl();
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
}
