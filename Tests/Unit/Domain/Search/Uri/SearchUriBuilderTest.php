<?php
namespace ApacheSolrForTypo3\Solrfluid\Test\Domain\Search\Uri;

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
use ApacheSolrForTypo3\Solrfluid\Domain\Search\ResultSet\SearchResultSet;
use ApacheSolrForTypo3\Solrfluid\Domain\Search\Uri\SearchUriBuilder;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Mvc\Web\Routing\UriBuilder;
use TYPO3\CMS\Frontend\Controller\TypoScriptFrontendController;

/**
 * Unit test case for the ObjectReconstitutionProcessor.
 *
 * @author Timo Schmidt <timo.schmidt@dkd.de>
 */
class SearchUriBuilderTest extends UnitTest
{

    /**
     * @var SearchUriBuilder
     */
    protected $searchUrlBuilder;

    /**
     * @var UriBuilder
     */
    protected $extBaseUriBuilderMock;

    /**
     * @return void
     */
    protected function setUp()
    {
        $this->extBaseUriBuilderMock = $this->getDumbMock(UriBuilder::class);
        $this->searchUrlBuilder = new SearchUriBuilder();
        $this->searchUrlBuilder->injectUriBuilder($this->extBaseUriBuilderMock);
    }

    /**
     * @test
     */
    public function addFacetLinkIsCalledWithSubstitutedArguments()
    {
        $expectedArguments = ['tx_solr' => ['filter' => ['###tx_solr:filter:0###']]];
        $this->extBaseUriBuilderMock->expects($this->once())->method('setArguments')->with($expectedArguments)->will($this->returnValue($this->extBaseUriBuilderMock));
        $this->extBaseUriBuilderMock->expects($this->once())->method('setUseCacheHash')->with(false)->will($this->returnValue($this->extBaseUriBuilderMock));

        $previousRequest =  new SearchRequest();
        $this->searchUrlBuilder->getAddFacetOptionUri($previousRequest, 'foo', 'bar');
    }

    /**
     * @test
     */
    public function setArgumentsIsOnlyCalledOnceEvenWhenMultipleFacetsGetRendered()
    {
        $expectedArguments = ['tx_solr' => ['filter' => ['###tx_solr:filter:0###']]];
        $this->extBaseUriBuilderMock->expects($this->once())->method('setArguments')->with($expectedArguments)->will($this->returnValue($this->extBaseUriBuilderMock));
        $this->extBaseUriBuilderMock->expects($this->once())->method('setUseCacheHash')->with(false)->will($this->returnValue($this->extBaseUriBuilderMock));
        $this->extBaseUriBuilderMock->expects($this->once())->method('build')->will($this->returnValue(urlencode('tx_solr[filter][0]=###tx_solr:filter:0###')));
        $previousRequest =  new SearchRequest();
        $previousRequest->removeAllFacets();
        $this->searchUrlBuilder->getAddFacetOptionUri($previousRequest, 'color', 'green');

        $previousRequest->removeAllFacets();
        $this->searchUrlBuilder->getAddFacetOptionUri($previousRequest, 'color', 'blue');

        $previousRequest->removeAllFacets();
        $this->searchUrlBuilder->getAddFacetOptionUri($previousRequest, 'color', 'red');
    }
}
