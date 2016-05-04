<?php
namespace ApacheSolrForTypo3\Solrfluid\ViewHelpers\Link\Facet;

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

use ApacheSolrForTypo3\Solr\Domain\Search\ResultSet\SearchResultSet;
use ApacheSolrForTypo3\Solrfluid\Domain\Search\ResultSet\Facets\AbstractFacet;
use ApacheSolrForTypo3\Solrfluid\Domain\Search\ResultSet\Facets\OptionsFacet\Option;
use ApacheSolrForTypo3\Solrfluid\Domain\Search\ResultSet\Facets\OptionsFacet\OptionsFacet;
use ApacheSolrForTypo3\Solrfluid\Domain\Search\Uri\SearchUriBuilder;
use ApacheSolrForTypo3\Solrfluid\Mvc\Controller\SolrControllerContext;
use ApacheSolrForTypo3\Solrfluid\ViewHelpers\AbstractTagBasedViewHelper;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Mvc\Web\Routing\UriBuilder;
use TYPO3\CMS\Fluid\Core\Rendering\RenderingContextInterface;
use TYPO3\CMS\Fluid\Core\ViewHelper\Facets\CompilableInterface;

/**
 * Class FacetAddOptionViewHelper
 *
 * @author Frans Saris <frans@beech.it>
 * @author Timo Schmidt <timo.schmidt@dkd.de>
 * @package ApacheSolrForTypo3\Solrfluid\ViewHelpers\Link
 */
class AddOptionViewHelper extends AbstractOptionViewHelper
{

    /**
     * Render
     *
     * @return string
     */
    public function render()
    {
        /** @var  $facet OptionsFacet */
        $facet = $this->arguments['facet'];
        $optionValue = $this->getOptionValueFromArguments();
        $previousRequest = $facet->getResultSet()->getUsedSearchRequest();
        $uri = $this->searchUriBuilder->getAddFacetOptionUri($previousRequest, $facet->getName(), $optionValue);
        $this->returnTagOrUri($uri);
        return $this->returnTagOrUri($uri);
    }
}
