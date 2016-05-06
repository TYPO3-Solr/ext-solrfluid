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
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Frontend\Controller\TypoScriptFrontendController;

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
            'showEmptyFacets' => 1,
            'facets.' => [
                'type.' => [
                    'label' => 'My Type',
                    'field' => 'type',
                ]
             ]
        ];

        $processor = $this->getConfiguredReconstitutionProcessor($facetConfiguration, $searchResultSet);
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
            'showEmptyFacets' => 1,
            'facets.' => [
                'type.' => [
                    'label' => 'My Type',
                    'field' => 'type_stringS',
                ],
                'category.' => [
                    'label' => 'My Category',
                    'field' => 'category_stringM',
                ]
            ]
        ];

        $processor = $this->getConfiguredReconstitutionProcessor($facetConfiguration, $searchResultSet);
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
            'showEmptyFacets' => 1,
            'facets.' => [
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
            ]
        ];

        $processor = $this->getConfiguredReconstitutionProcessor($facetConfiguration, $searchResultSet);
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
            'showEmptyFacets' => 1,
            'facets.' => [
                'type.' => [
                    'label' => 'My Type',
                    'field' => 'type',
                ],
                'category.' => [
                    'label' => 'My Category',
                    'field' => 'category'
                ]
            ]
        ];

        $processor = $this->getConfiguredReconstitutionProcessor($facetConfiguration, $searchResultSet);
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
            'showEmptyFacets' => 1,
            'facets.' => [
                'type.' => [
                    'label' => 'My Type',
                    'field' => 'type',
                ],
                 // category is configured but not available
                'category.' => [
                    'label' => 'My Category',
                    'field' => 'category'
                ]
            ]
        ];

        $processor = $this->getConfiguredReconstitutionProcessor($facetConfiguration, $searchResultSet);
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
            'showEmptyFacets' => 1,
            'facets.' => [
                'type.' => [
                    'label' => 'My Type',
                    'field' => 'type',
                ]
            ]

        ];

        $processor = $this->getConfiguredReconstitutionProcessor($facetConfiguration, $searchResultSet);
        $processor->process($searchResultSet);

        $facets = $searchResultSet->getFacets();

        /** @var OptionsFacet $facet1 */
        $facet1 = $facets[0];

        /** @var $firstOption Option */
        $firstOption = $facet1->getOptions()->offsetGet(0);
        $this->assertEquals('pages', $firstOption->getValue());
        $this->assertEquals(5, $firstOption->getCount());
        $this->asserttrue($firstOption->getSelected());
    }

    /**
     * @test
     */
    public function emptyFacetsAreNotReconstitutedWhenDisabled()
    {
        $searchResultSet = $this->initializeSearchResultSetFromFakeResponse('fake_solr_response_with_used_facet.json');

        // before the reconstitution of the domain object from the response we expect that no facets
        // are present
        $this->assertEquals([], $searchResultSet->getFacets()->getArrayCopy());

        $facetConfiguration = [
            'showEmptyFacets' => 0,
            'facets.' => [
                'type.' => [
                    'label' => 'My Type',
                    'field' => 'type',
                ],
                // category is configured but not available
                'category.' => [
                    'label' => 'My Category',
                    'field' => 'category'
                ]
            ]
        ];

        $processor = $this->getConfiguredReconstitutionProcessor($facetConfiguration, $searchResultSet);
        $processor->process($searchResultSet);

        $facets = $searchResultSet->getFacets();
        $this->assertCount(1, $facets, 'we have two facets at all');
    }

    /**
     * @test
     */
    public function emptyFacetIsKeptWhenNothingIsConfiguredGloballyButKeepingIsEnabledOnFacetLevel()
    {
        $searchResultSet = $this->initializeSearchResultSetFromFakeResponse('fake_solr_response_with_used_facet.json');

        // before the reconstitution of the domain object from the response we expect that no facets
        // are present
        $this->assertEquals([], $searchResultSet->getFacets()->getArrayCopy());

        $facetConfiguration = [
            'facets.' => [
                'type.' => [
                    'label' => 'My Type',
                    'field' => 'type',
                ],
                // category is configured but not available
                'category.' => [
                    'label' => 'My Category',
                    'field' => 'category',
                    'showEvenWhenEmpty' => 1
                ]
            ]
        ];

        $processor = $this->getConfiguredReconstitutionProcessor($facetConfiguration, $searchResultSet);
        $processor->process($searchResultSet);

        $facets = $searchResultSet->getFacets();
        $this->assertCount(2, $facets, 'we have two facets at all');
    }


    /**
     * @test
     */
    public function canApplyRenderingInstructionsOnOptions()
    {
        $this->fakeTSFEToUseCObject();

        $searchResultSet = $this->initializeSearchResultSetFromFakeResponse('fake_solr_response_with_multiple_fields_facets.json');

        // before the reconstitution of the domain object from the response we expect that no facets
        // are present
        $this->assertEquals([], $searchResultSet->getFacets()->getArrayCopy());

        $facetConfiguration = [
            'facets.' => [
                'type.' => [
                    'label' => 'My Type with special rendering',
                    'field' => 'type_stringS',
                    'renderingInstruction' => 'CASE',
                    'renderingInstruction.' => [
                        'key.' => [
                            'field' => 'optionValue'
                        ],
                        'page' => 'TEXT',
                        'page.' => [
                            'value' => 'Pages'
                        ],
                        'event' => 'TEXT',
                        'event.' => [
                            'value' => 'Events'
                        ]

                    ]
                ]
            ]
        ];

        $processor = $this->getConfiguredReconstitutionProcessor($facetConfiguration, $searchResultSet);
        $processor->process($searchResultSet);

        /** @var $facet OptionsFacet */
        $facet = $searchResultSet->getFacets()->offsetGet(0);

        /** @var $option1 Option */
        $option1 = $facet->getOptions()->offsetGet(0);
        $this->assertSame('Pages', $option1->getLabel(), 'Rendering instructions have not been applied on the facet options');
    }

    /**
     * @test
     */
    public function labelCanBeConfiguredAsAPlainText()
    {
        $searchResultSet = $this->initializeSearchResultSetFromFakeResponse('fake_solr_response_with_multiple_fields_facets.json');

        // before the reconstitution of the domain object from the response we expect that no facets
        // are present
        $this->assertEquals([], $searchResultSet->getFacets()->getArrayCopy());

        $facetConfiguration = [
            'facets.' => [
                'type.' => [
                    'label' => 'My Type with special rendering',
                    'field' => 'type_stringS',
                ]
            ]
        ];

        $processor = $this->getConfiguredReconstitutionProcessor($facetConfiguration, $searchResultSet);
        $processor->process($searchResultSet);

        /** @var $facet OptionsFacet */
        $facet = $searchResultSet->getFacets()->offsetGet(0);
        $this->assertSame('My Type with special rendering', $facet->getLabel(), 'Could not get label for facet');
    }

    /**
     * @test
     */
    public function labelCanBeUsedAsCObject()
    {
        $this->fakeTSFEToUseCObject();
        $searchResultSet = $this->initializeSearchResultSetFromFakeResponse('fake_solr_response_with_multiple_fields_facets.json');

        // before the reconstitution of the domain object from the response we expect that no facets
        // are present
        $this->assertEquals([], $searchResultSet->getFacets()->getArrayCopy());

        $facetConfiguration = [
            'facets.' => [
                'type.' => [
                    'label' => 'TEXT',
                    'label.' => [
                        'value' => 'My Type with special rendering',
                        'stdWrap.' => ['case' => 'upper']
                    ],
                    'field' => 'type_stringS',
                ]
            ]
        ];

        $processor = $this->getConfiguredReconstitutionProcessor($facetConfiguration, $searchResultSet);
        $processor->process($searchResultSet);

        /** @var $facet OptionsFacet */
        $facet = $searchResultSet->getFacets()->offsetGet(0);
        $this->assertSame('MY TYPE WITH SPECIAL RENDERING', $facet->getLabel(), 'Rendering instructions have not been applied on the facet options');
    }


    /**
     * @param $facetConfiguration
     * @param $searchResultSet
     * @return ResultSetReconstitutionProcessor
     */
    protected function getConfiguredReconstitutionProcessor($facetConfiguration, $searchResultSet)
    {
        $configuration = array();
        $configuration['plugin.']['tx_solr.']['search.']['faceting.'] = $facetConfiguration;
        $typoScriptConfiguration = new TypoScriptConfiguration($configuration);
        $searchResultSet->getUsedSearchRequest()->expects($this->any())->method('getContextTypoScriptConfiguration')->will($this->returnValue($typoScriptConfiguration));

        $processor = new ResultSetReconstitutionProcessor();
        return $processor;
    }

    /**
     *
     */
    protected function fakeTSFEToUseCObject()
    {
        $GLOBALS['TYPO3_CONF_VARS']['FE']['ContentObjects'] = array_merge($GLOBALS['TYPO3_CONF_VARS']['FE']['ContentObjects'], array('TEXT' => \TYPO3\CMS\Frontend\ContentObject\TextContentObject::class, 'CASE' => \TYPO3\CMS\Frontend\ContentObject\CaseContentObject::class, ));

        $TSFE = GeneralUtility::makeInstance(TypoScriptFrontendController::class, array(), 1, 0);
        $TSFE->cObjectDepthCounter = 5;
        $GLOBALS['TSFE'] = $TSFE;
        $GLOBALS['TT'] = $this->getMock('\\TYPO3\\CMS\\Core\\TimeTracker\\TimeTracker', array(), array(), '', false);
    }
}
