<?php
namespace ApacheSolrForTypo3\Solrfluid\Test\ViewHelpers\Facet\Uri;

/***************************************************************
 *  Copyright notice
 *
 *  (c) 2015-2016 Timo Schmidt <timo.schmidt@dkd.de>
 *  All rights reserved
 *
 *  This script is part of the TYPO3 project. The TYPO3 project is
 *  free software; you can redistribute it and/or modify
 *  it under the terms of the GNU General Public License as published by
 *  the Free Software Foundation; either version 2 of the License, or
 *  (at your option) any later version.
 *
 *  The GNU General Public License can be found at
 *  http://www.gnu.org/copyleft/gpl.html.
 *
 *  This script is distributed in the hope that it will be useful,
 *  but WITHOUT ANY WARRANTY; without even the implied warranty of
 *  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *  GNU General Public License for more details.
 *
 *  This copyright notice MUST APPEAR in all copies of the script!
 ***************************************************************/

use ApacheSolrForTypo3\Solr\Domain\Search\SearchRequest;
use ApacheSolrForTypo3\Solr\System\Configuration\TypoScriptConfiguration;
use ApacheSolrForTypo3\Solr\Tests\Unit\UnitTest;
use ApacheSolrForTypo3\Solrfluid\Domain\Search\ResultSet\Facets\OptionBased\Options\Option;
use ApacheSolrForTypo3\Solrfluid\Domain\Search\ResultSet\Facets\OptionBased\Options\OptionsFacet;
use ApacheSolrForTypo3\Solrfluid\Domain\Search\ResultSet\SearchResultSet;
use ApacheSolrForTypo3\Solrfluid\Domain\Search\Uri\SearchUriBuilder;
use ApacheSolrForTypo3\Solrfluid\Mvc\Controller\SolrControllerContext;
use ApacheSolrForTypo3\Solrfluid\ViewHelpers\Uri\Facet\AddFacetItemViewHelper;
use ApacheSolrForTypo3\Solrfluid\ViewHelpers\Uri\Facet\RemoveAllFacetsViewHelper;
use ApacheSolrForTypo3\Solrfluid\ViewHelpers\Uri\Facet\RemoveFacetItemViewHelper;
use ApacheSolrForTypo3\Solrfluid\ViewHelpers\Uri\Facet\SetFacetItemViewHelper;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Mvc\Web\Routing\UriBuilder;
use TYPO3\CMS\Fluid\Core\Rendering\RenderingContextInterface;
use TYPO3\CMS\Fluid\Core\ViewHelper\TemplateVariableContainer;
use TYPO3\CMS\Form\Mvc\Controller\ControllerContext;
use TYPO3\CMS\Frontend\Controller\TypoScriptFrontendController;

/**
 * @author Timo Schmidt <timo.schmidt@dkd.de>
 */
class RemoveAllFacetsViewHelperTest extends AbstractFacetItemViewHelperTest
{

    /**
     * @test
     */
    public function setFacetItemWillUseUriBuilderAsExpected()
    {
        $mockedPreviousFakedRequest = $this->getDumbMock(SearchRequest::class);
        $searchResultSetMock = $this->getDumbMock(SearchResultSet::class);
        $searchResultSetMock->expects($this->once())->method('getUsedSearchRequest')->will($this->returnValue($mockedPreviousFakedRequest));

        $controllerContextMock = $this->getDumbMock(SolrControllerContext::class);
        $controllerContextMock->expects($this->once())->method('getSearchResultSet')->will($this->returnValue($searchResultSetMock));
        $renderContextMock = $this->getDumbMock(RenderingContextInterface::class);
        $renderContextMock->expects($this->any())->method('getControllerContext')->will($this->returnValue($controllerContextMock));

        $viewHelper = new RemoveAllFacetsViewHelper();
        $viewHelper->setRenderingContext($renderContextMock);

        $searchUriBuilderMock = $this->getDumbMock(SearchUriBuilder::class);

            // we expected that the getRemoveAllFacetsUri will be called on the searchUriBuilder in the end.
        $searchUriBuilderMock->expects($this->once())->method('getRemoveAllFacetsUri')->with($mockedPreviousFakedRequest);
        $viewHelper->injectSearchUriBuilder($searchUriBuilderMock);

        $viewHelper->render();
    }
}
