<?php
namespace ApacheSolrForTypo3\Solrfluid\Test\Domain\Search\ResultSet;

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
use ApacheSolrForTypo3\Solrfluid\Domain\Search\ResultSet\Facets\OptionsFacet\Option;
use ApacheSolrForTypo3\Solrfluid\Domain\Search\ResultSet\Facets\OptionsFacet\OptionsFacet;
use ApacheSolrForTypo3\Solrfluid\Domain\Search\ResultSet\ResultSetReconstitutionProcessor;
use ApacheSolrForTypo3\Solrfluid\Domain\Search\ResultSet\SearchResultSet;

/**
 * Unit test case for the ObjectReconstitutionProcessor.
 *
 * @author Timo Schmidt <timo.schmidt@dkd.de>
 */
class ResultSetReconstitutionProcessorTest extends UnitTest
{
    /**
     * @param $fixtureFile
     * @return SearchResultSet
     */
    protected function initializeSearchResultSetFromFakeResponse($fixtureFile)
    {
        $fakeResponseJson = $this->getFixtureContent($fixtureFile);
        $httpResponseMock = $this->getDumbMock('\Apache_Solr_HttpTransport_Response');
        $httpResponseMock->expects($this->any())->method('getBody')->will($this->returnValue($fakeResponseJson));

        $searchRequestMock = $this->getDumbMock(SearchRequest::class);

        $fakeResponse = new \Apache_Solr_Response($httpResponseMock);

        $searchResultSet = new SearchResultSet();
        $searchResultSet->setUsedSearchRequest($searchRequestMock);
        $searchResultSet->setResponse($fakeResponse);

        return $searchResultSet;
    }

    /**
     * @test
     */
    public function canReconstituteSpellCheckingModelsFromResponse()
    {
        $searchResultSet = $this->initializeSearchResultSetFromFakeResponse('fake_solr_response_with_spellCheck.json');

        // before the reconstitution of the domain object from the response we expect that no spelling suggestions
        // are present
        $this->assertFalse($searchResultSet->getHasSpellCheckingSuggestions());


        $processor = new ResultSetReconstitutionProcessor();
        $processor->process($searchResultSet);

        // after the reconstitution they should be present
        $this->assertTrue($searchResultSet->getHasSpellCheckingSuggestions());
    }

    /**
     * @test
     */
    public function canReconstituteFacetModelFromResponse()
    {
        $searchResultSet = $this->initializeSearchResultSetFromFakeResponse('fake_solr_response_with_one_fields_facet.json');

        // before the reconstitution of the domain object from the response we expect that no facets
        // are present
        $this->assertEquals([], $searchResultSet->getFacets()->getArrayCopy());

        $facetConfiguration = [
            'type.' => [
                'label' => 'My Type',
                'field' => 'type',
            ]
        ];

        $typoScriptConfiguration = $this->getDumbMock(TypoScriptConfiguration::class);
        $typoScriptConfiguration->expects($this->once())->method('getSearchFacetingFacets')->will($this->returnValue($facetConfiguration));
        $searchResultSet->getUsedSearchRequest()->expects($this->any())->method('getContextTypoScriptConfiguration')->will($this->returnValue($typoScriptConfiguration));

        $processor = new ResultSetReconstitutionProcessor();
        $processor->process($searchResultSet);

        // after the reconstitution they should be 1 facet present
        $this->assertCount(1, $searchResultSet->getFacets());
    }

    /**
     * @test
     */
    public function canReconstituteFacetModelsFromResponse()
    {
        $searchResultSet = $this->initializeSearchResultSetFromFakeResponse('fake_solr_response_with_multiple_fields_facets.json');

        // before the reconstitution of the domain object from the response we expect that no facets
        // are present
        $this->assertEquals([], $searchResultSet->getFacets()->getArrayCopy());

        $facetConfiguration = [
            'type.' => [
                'label' => 'My Type',
                'field' => 'type_stringS',
            ],
            'category.' => [
                'label' => 'My Category',
                'field' => 'category_stringM',
            ]
        ];

        $typoScriptConfiguration = $this->getDumbMock(TypoScriptConfiguration::class);
        $typoScriptConfiguration->expects($this->once())->method('getSearchFacetingFacets')->will($this->returnValue($facetConfiguration));
        $searchResultSet->getUsedSearchRequest()->expects($this->any())->method('getContextTypoScriptConfiguration')->will($this->returnValue($typoScriptConfiguration));

        $processor = new ResultSetReconstitutionProcessor();
        $processor->process($searchResultSet);

        // after the reconstitution they should be 1 facet present
        $this->assertCount(2, $searchResultSet->getFacets());
    }

    /**
     * @test
     */
    public function canReconstituteFacetModelsWithSameFieldNameFromResponse()
    {
        $searchResultSet = $this->initializeSearchResultSetFromFakeResponse('fake_solr_response_with_multiple_fields_facets.json');

        // before the reconstitution of the domain object from the response we expect that no facets
        // are present
        $this->assertEquals([], $searchResultSet->getFacets()->getArrayCopy());

        $facetConfiguration = [
            'type.' => [
                'label' => 'My Type',
                'field' => 'type_stringS',
            ],
            'category.' => [
                'label' => 'My Category',
                'field' => 'category_stringM'
            ],
            'category2.' => [
                'label' => 'My Category again',
                'field' => 'category_stringM'
            ]
        ];

        $typoScriptConfiguration = $this->getDumbMock(TypoScriptConfiguration::class);
        $typoScriptConfiguration->expects($this->once())->method('getSearchFacetingFacets')->will($this->returnValue($facetConfiguration));
        $searchResultSet->getUsedSearchRequest()->expects($this->any())->method('getContextTypoScriptConfiguration')->will($this->returnValue($typoScriptConfiguration));

        $processor = new ResultSetReconstitutionProcessor();
        $processor->process($searchResultSet);

        // after the reconstitution they should be 1 facet present
        $this->assertCount(3, $searchResultSet->getFacets());

        /** @var OptionsFacet $facet */
        $facet = reset($searchResultSet->getFacets());
        $this->assertCount(2, $facet->getOptions());
    }


    /**
     * @test
     */
    public function canReconstituteUsedFacet()
    {
        $searchResultSet = $this->initializeSearchResultSetFromFakeResponse('fake_solr_response_with_used_facet.json');

        // before the reconstitution of the domain object from the response we expect that no facets
        // are present
        $this->assertEquals([], $searchResultSet->getFacets()->getArrayCopy());

        $facetConfiguration = [
            'type.' => [
                'label' => 'My Type',
                'field' => 'type',
            ],
            'category.' => [
                'label' => 'My Category',
                'field' => 'category'
            ]
        ];

        $typoScriptConfiguration = $this->getDumbMock(TypoScriptConfiguration::class);
        $typoScriptConfiguration->expects($this->once())->method('getSearchFacetingFacets')->will($this->returnValue($facetConfiguration));
        $searchResultSet->getUsedSearchRequest()->expects($this->any())->method('getContextTypoScriptConfiguration')->will($this->returnValue($typoScriptConfiguration));

        $processor = new ResultSetReconstitutionProcessor();
        $processor->process($searchResultSet);

        // after the reconstitution we should have two facets present
        $this->assertCount(2, $searchResultSet->getFacets());

        $facets = $searchResultSet->getFacets();

        /** @var OptionsFacet $facet1 */
        $facet1 = $facets[0];
        $this->assertEquals('My Type', $facet1->getLabel());
        $this->assertTrue($facet1->getIsUsed());

        /** @var OptionsFacet $facet2 */
        $facet2 = $facets[1];
        $this->assertEquals('My Category', $facet2->getLabel());
        $this->assertFalse($facet2->getIsUsed());
    }

    /**
     * @test
     */
    public function canMarkUsedOptionAsSelected()
    {
        $searchResultSet = $this->initializeSearchResultSetFromFakeResponse('fake_solr_response_with_used_facet.json');

        // before the reconstitution of the domain object from the response we expect that no facets
        // are present
        $this->assertEquals([], $searchResultSet->getFacets()->getArrayCopy());

        $facetConfiguration = [
            'type.' => [
                'label' => 'My Type',
                'field' => 'type',
            ],
            // category is configured but not available
            'category.' => [
                'label' => 'My Category',
                'field' => 'category'
            ]
        ];

        $typoScriptConfiguration = $this->getDumbMock(TypoScriptConfiguration::class);
        $typoScriptConfiguration->expects($this->once())->method('getSearchFacetingFacets')->will($this->returnValue($facetConfiguration));
        $searchResultSet->getUsedSearchRequest()->expects($this->any())->method('getContextTypoScriptConfiguration')->will($this->returnValue($typoScriptConfiguration));

        $processor = new ResultSetReconstitutionProcessor();
        $processor->process($searchResultSet);

        $facets = $searchResultSet->getFacets();

        $this->assertCount(2, $facets, 'we have two facets at all');
        $this->assertCount(1, $facets->getAvailable(), 'but only "type" is available');
        $this->assertCount(1, $facets->getUsed(), 'and also "type" is the only used facet');
    }

    /**
     * @test
     */
    public function canGetConfiguredFacetNotInResponseAsUnavailableFacet()
    {
        $searchResultSet = $this->initializeSearchResultSetFromFakeResponse('fake_solr_response_with_used_facet.json');

        // before the reconstitution of the domain object from the response we expect that no facets
        // are present
        $this->assertEquals([], $searchResultSet->getFacets()->getArrayCopy());

        $facetConfiguration = [
            'type.' => [
                'label' => 'My Type',
                'field' => 'type',
            ]

        ];

        $typoScriptConfiguration = $this->getDumbMock(TypoScriptConfiguration::class);
        $typoScriptConfiguration->expects($this->once())->method('getSearchFacetingFacets')->will($this->returnValue($facetConfiguration));
        $searchResultSet->getUsedSearchRequest()->expects($this->any())->method('getContextTypoScriptConfiguration')->will($this->returnValue($typoScriptConfiguration));

        $processor = new ResultSetReconstitutionProcessor();
        $processor->process($searchResultSet);

        $facets = $searchResultSet->getFacets();

        /** @var OptionsFacet $facet1 */
        $facet1 = $facets[0];

        /** @var $firstOption Option */
        $firstOption = $facet1->getOptions()->offsetGet(0);
        $this->assertEquals('pages', $firstOption->getValue());
        $this->assertEquals(5,$firstOption->getCount());
        $this->asserttrue($firstOption->getSelected());
    }
}
