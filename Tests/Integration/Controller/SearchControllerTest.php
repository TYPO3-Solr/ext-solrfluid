<?php
namespace ApacheSolrForTypo3\Solr\Tests\Integration\Plugin\Results;

/***************************************************************
 *  Copyright notice
 *
 *  (c) 2010-2015 Timo Schmidt <timo.schmidt@dkd.de>
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

use ApacheSolrForTypo3\Solr\Site;
use ApacheSolrForTypo3\Solr\Tests\Integration\IntegrationTest;
use ApacheSolrForTypo3\Solrfluid\Controller\SearchController;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Mvc\Request;
use TYPO3\CMS\Extbase\Mvc\Web\RequestBuilder;
use TYPO3\CMS\Extbase\Mvc\Web\Response;
use TYPO3\CMS\Fluid\View\Exception\InvalidTemplateResourceException;
use TYPO3\CMS\Frontend\ContentObject\ContentObjectRenderer;
use TYPO3\CMS\Frontend\Page\PageGenerator;

/**
 * Integration testcase to test for the SearchController
 *
 * @author Timo Schmidt
 */
class SearchControllerTest extends IntegrationTest
{
    /**
     * @var SearchController
     */
    protected $searchController;

    /**
     * @var Request
     */
    protected $searchRequest;

    /**
     * @var Response
     */
    protected $searchResponse;

    /**
     * @var array
     */
    protected $testExtensionsToLoad = array('typo3conf/ext/solr', 'typo3conf/ext/solrfluid');

    public function setUp()
    {
        parent::setUp();
        $GLOBALS['TT'] = $this->getMock('\\TYPO3\\CMS\\Core\\TimeTracker\\TimeTracker', array(), array(), '', false);

        /** @var  $searchController SearchController */
        $this->searchController = $this->objectManager->get(SearchController::class);
        $this->searchRequest = $this->getPreparedRequest();
        $this->searchResponse = $this->getPreparedResponse();
    }

    /**
     * Executed after each test. Emptys solr and checks if the index is empty
     */
    public function tearDown()
    {
        $this->cleanUpSolrServerAndAssertEmpty();
        parent::tearDown();
    }

    /**
     * @test
     */
    public function canShowSearchForm()
    {
        $this->importDataSetFromFixture('can_render_search_controller.xml');
        $GLOBALS['TSFE'] = $this->getConfiguredTSFE(array(), 1);
        $this->indexPages(array(1, 2));
        $this->searchController->processRequest($this->searchRequest, $this->searchResponse);
        $content = $this->searchResponse->getContent();
        $this->assertContains('id="tx-solr-search-form-pi-results"', $content, 'Response did not contain search css selector');
    }

    /**
     * @test
     */
    public function canSearchForPrices()
    {
        $_GET['q'] = 'prices';
        $this->importDataSetFromFixture('can_render_search_controller.xml');
        $GLOBALS['TSFE'] = $this->getConfiguredTSFE(array(), 1);
        $this->indexPages(array(1, 2, 3));

        $this->searchController->processRequest($this->searchRequest, $this->searchResponse);
        $result = $this->searchResponse->getContent();

        $this->assertContains('pages/3/0/0/0', $result, 'Could not find page 3 in result set');
        $this->assertContains('pages/2/0/0/0', $result, 'Could not find page 2 in result set');
    }

    /**
     * @test
     */
    public function canDoAPaginatedSearch()
    {
        $this->importDataSetFromFixture('can_render_search_controller.xml');
        $GLOBALS['TSFE'] = $this->getConfiguredTSFE(array(), 1);

        $this->indexPages(array(1, 2, 3, 4, 5, 6, 7, 8));

        $_GET['q'] = '*';

        $this->searchController->processRequest($this->searchRequest, $this->searchResponse);
        $resultPage1 = $this->searchResponse->getContent();

        $this->assertPaginationVisible($resultPage1);
        $this->assertContains('Results 1 until 5 of 8', $resultPage1, 'Wrong result count indicated in template');
    }

    /**
     * @test
     */
    public function canOpenSecondPageOfPaginatedSearch()
    {
        $this->importDataSetFromFixture('can_render_search_controller.xml');
        $GLOBALS['TSFE'] = $this->getConfiguredTSFE(array(), 1);
        $this->indexPages(array(1, 2, 3, 4, 5, 6, 7, 8));

        //now we jump to the second page
        $_GET['q'] = '*';

        $this->searchRequest->setArgument('page', 2);
        $this->searchController->processRequest($this->searchRequest, $this->searchResponse);
        $resultPage2 = $this->searchResponse->getContent();

        $this->assertContains('pages/8/0/0/0', $resultPage2, 'Could not find page 8 in result set');
        $this->assertContains('Results 6 until 8 of 8', $resultPage2, 'Wrong result count indicated in template');
    }

    /**
     * @test
     */
    public function canGetADidYouMeanProposalForATypo()
    {
        $this->importDataSetFromFixture('can_render_search_controller.xml');
        $GLOBALS['TSFE'] = $this->getConfiguredTSFE(array(), 1);

        $this->indexPages(array(1, 2, 3, 4, 5, 6, 7, 8));

        //not in the content but we expect to get shoes suggested
        $_GET['q'] = 'shoo';

        $this->searchController->processRequest($this->searchRequest, $this->searchResponse);
        $resultPage1 = $this->searchResponse->getContent();

        $this->assertContains("Did you mean", $resultPage1, 'Could not find did you mean in response');
        $this->assertContains("shoes", $resultPage1, 'Could not find shoes in response');
    }

    /**
     * @test
     */
    public function canRenderAFacetWithFluid()
    {
        $this->importDataSetFromFixture('can_render_search_controller.xml');
        $GLOBALS['TSFE'] = $this->getConfiguredTSFE(array(), 1);

        $this->indexPages(array(1, 2));

        //not in the content but we expect to get shoes suggested
        $_GET['q'] = '*';

            // since we overwrite the configuration in the testcase from outside we want to avoid that it will be resetted
        $this->searchController->setResetConfigurationBeforeInitialize(false);
        $this->searchController->processRequest($this->searchRequest, $this->searchResponse);
        $resultPage1 = $this->searchResponse->getContent();

        $this->assertContains('class="facet-option-list fluidfacet"', $resultPage1, 'Could not find fluidfacet class that indicates the facet was rendered with fluid');
        $this->assertContains('pages</a> <span class="facet-result-count">', $resultPage1, 'Could not find facet option for pages');
    }

    /**
     * @test
     */
    public function removeOptionLinkWillBeShownWhenFacetWasSelected()
    {
        $this->importDataSetFromFixture('can_render_search_controller.xml');
        $GLOBALS['TSFE'] = $this->getConfiguredTSFE(array(), 1);

        $this->indexPages(array(1, 2, 3, 4, 5, 6, 7, 8));

        //not in the content but we expect to get shoes suggested
        $_GET['q'] = '*';
        $_GET['tx_solr']['filter'][0] = urlencode('type:pages');

        // since we overwrite the configuration in the testcase from outside we want to avoid that it will be resetted
        $this->searchController->setResetConfigurationBeforeInitialize(false);
        $this->searchController->processRequest($this->searchRequest, $this->searchResponse);
        $resultPage1 = $this->searchResponse->getContent();

        $this->assertContains('fluidfacet', $resultPage1, 'Could not find fluidfacet class that indicates the facet was rendered with fluid');
        $this->assertContains('remove-facet-option', $resultPage1, 'No link to remove facet option found');
    }

    /**
     * @test
     */
    public function exceptionWillBeThrownWhenAWrongTemplateIsConfiguredForTheFacet()
    {
        // we expected that an exception will be thrown when a facet is rendered
        // where an unknown partialName is referenced
        $this->setExpectedExceptionRegExp(InvalidTemplateResourceException::class, '#.*The partial files.*NotFound.*#');

        $this->importDataSetFromFixture('can_render_search_controller.xml');
        $GLOBALS['TSFE'] = $this->getConfiguredTSFE(array(), 1);

        $this->indexPages(array(1, 2, 3, 4, 5, 6, 7, 8));

        // now we set the facet type for "type" facet to fluid and expect that we get a rendered facet
        $overwriteConfiguration = array();
        $overwriteConfiguration['search.']['faceting.']['facets.']['type.']['partialName'] = 'NotFound';

        /** @var $configurationManager \ApacheSolrForTypo3\Solr\System\Configuration\ConfigurationManager */
        $configurationManager = GeneralUtility::makeInstance('ApacheSolrForTypo3\Solr\System\Configuration\ConfigurationManager');
        $configurationManager->getTypoScriptConfiguration()->mergeSolrConfiguration($overwriteConfiguration);

        //not in the content but we expect to get shoes suggested
        $_GET['q'] = '*';

        // since we overwrite the configuration in the testcase from outside we want to avoid that it will be resetted
        $this->searchController->setResetConfigurationBeforeInitialize(false);
        $this->searchController->processRequest($this->searchRequest, $this->searchResponse);
    }

    /**
     * @test
     */
    public function canRenderAScoreAnalysisWhenBackendUserIsLoggedIn()
    {
        $this->importDataSetFromFixture('can_render_search_controller.xml');
        $GLOBALS['TSFE'] = $this->getConfiguredTSFE(array(), 1);


        $this->indexPages(array(1, 2));

        //not in the content but we expect to get shoes suggested
        $_GET['q'] = '*';
        // fake that a backend user is logged in
        $GLOBALS['TSFE']->beUserLogin = true;

        $this->searchController->processRequest($this->searchRequest, $this->searchResponse);
        $resultPage1 = $this->searchResponse->getContent();

        $this->assertContains('document-score-analysis', $resultPage1, 'No score analysis in response');
    }

    /**
     * @test
     */
    public function canSortFacetsByLex()
    {
        $this->importDataSetFromFixture('can_render_search_controller.xml');
        $GLOBALS['TSFE'] = $this->getConfiguredTSFE(array(), 1);

        $womanPages = [4,5,8];
        $menPages = [2];
        $this->indexPages($womanPages);
        $this->indexPages($menPages);

        //not in the content but we expect to get shoes suggested
        $_GET['q'] = '*';

        // when we sort by lex "men" should appear before "woman" even when only one option is available
        $overwriteConfiguration = array();
        $overwriteConfiguration['search.']['faceting.']['facets.']['subtitle.']['sortBy'] = 'lex';


        /** @var $configurationManager \ApacheSolrForTypo3\Solr\System\Configuration\ConfigurationManager */
        $configurationManager = GeneralUtility::makeInstance('ApacheSolrForTypo3\Solr\System\Configuration\ConfigurationManager');
        $configurationManager->getTypoScriptConfiguration()->mergeSolrConfiguration($overwriteConfiguration);

        // since we overwrite the configuration in the testcase from outside we want to avoid that it will be resetted
        $this->searchController->setResetConfigurationBeforeInitialize(false);
        $this->searchController->processRequest($this->searchRequest, $this->searchResponse);
        $resultPage1 = $this->searchResponse->getContent();
        $subtitleMenPosition = strpos($resultPage1, 'subtitle%3Amen&foo=bar">men</a> <span class="facet-result-count">(1)</span>');
        $subtitleWomanPosition =  strpos($resultPage1, 'subtitle%3Awoman&foo=bar">woman</a> <span class="facet-result-count">(3)</span>');

        $this->assertGreaterThan(0, $subtitleMenPosition);
        $this->assertGreaterThan(0, $subtitleWomanPosition);
        $this->assertGreaterThan($subtitleMenPosition, $subtitleWomanPosition);

        $this->assertContains('class="facet-option-list fluidfacet"', $resultPage1, 'Could not find fluidfacet class that indicates the facet was rendered with fluid');
        $this->assertContains('pages</a> <span class="facet-result-count">', $resultPage1, 'Could not find facet option for pages');
    }

    /**
     * @test
     */
    public function canSortFacetsByOptionCountWhenNothingIsConfigured()
    {
        $this->importDataSetFromFixture('can_render_search_controller.xml');
        $GLOBALS['TSFE'] = $this->getConfiguredTSFE(array(), 1);

        $womanPages = [4,5,8];
        $menPages = [2];
        $this->indexPages($womanPages);
        $this->indexPages($menPages);

        //not in the content but we expect to get shoes suggested
        $_GET['q'] = '*';

        // since we overwrite the configuration in the testcase from outside we want to avoid that it will be resetted
        $this->searchController->setResetConfigurationBeforeInitialize(false);
        $this->searchController->processRequest($this->searchRequest, $this->searchResponse);
        $resultPage1 = $this->searchResponse->getContent();

        $subtitleMenPosition = strpos($resultPage1, 'subtitle%3Amen&foo=bar">men</a> <span class="facet-result-count">(1)</span>');
        $subtitleWomanPosition =  strpos($resultPage1, 'subtitle%3Awoman&foo=bar">woman</a> <span class="facet-result-count">(3)</span>');

        $this->assertGreaterThan(0, $subtitleMenPosition);
        $this->assertGreaterThan(0, $subtitleWomanPosition);
        $this->assertGreaterThan($subtitleWomanPosition, $subtitleMenPosition);

        $this->assertContains('class="facet-option-list fluidfacet"', $resultPage1, 'Could not find fluidfacet class that indicates the facet was rendered with fluid');
        $this->assertContains('pages</a> <span class="facet-result-count">', $resultPage1, 'Could not find facet option for pages');
    }

    /**
     * @test
     */
    public function canDefineAManualSortOrder()
    {
        $this->importDataSetFromFixture('can_render_search_controller.xml');
        $GLOBALS['TSFE'] = $this->getConfiguredTSFE(array(), 1);

        $womanPages = [4,5,8];
        $menPages = [2];
        $this->indexPages($womanPages);
        $this->indexPages($menPages);

        //not in the content but we expect to get shoes suggested
        $_GET['q'] = '*';

        // when we sort by lex "men" should appear before "woman" even when only one option is available
        $overwriteConfiguration = array();
        $overwriteConfiguration['search.']['faceting.']['facets.']['subtitle.']['manualSortOrder'] = 'men, woman';


        /** @var $configurationManager \ApacheSolrForTypo3\Solr\System\Configuration\ConfigurationManager */
        $configurationManager = GeneralUtility::makeInstance('ApacheSolrForTypo3\Solr\System\Configuration\ConfigurationManager');
        $configurationManager->getTypoScriptConfiguration()->mergeSolrConfiguration($overwriteConfiguration);

        // since we overwrite the configuration in the testcase from outside we want to avoid that it will be resetted
        $this->searchController->setResetConfigurationBeforeInitialize(false);
        $this->searchController->processRequest($this->searchRequest, $this->searchResponse);
        $resultPage1 = $this->searchResponse->getContent();

        $subtitleMenPosition = strpos($resultPage1, 'subtitle%3Amen&foo=bar">men</a> <span class="facet-result-count">(1)</span>');
        $subtitleWomanPosition =  strpos($resultPage1, 'subtitle%3Awoman&foo=bar">woman</a> <span class="facet-result-count">(3)</span>');

        $this->assertGreaterThan(0, $subtitleMenPosition);
        $this->assertGreaterThan(0, $subtitleWomanPosition);
        $this->assertGreaterThan($subtitleMenPosition, $subtitleWomanPosition);

        $this->assertContains('class="facet-option-list fluidfacet"', $resultPage1, 'Could not find fluidfacet class that indicates the facet was rendered with fluid');
        $this->assertContains('pages</a> <span class="facet-result-count">', $resultPage1, 'Could not find facet option for pages');
    }


    /**
     * @test
     */
    public function canSeeTheParsedQueryWhenABackendUserIsLoggedIn()
    {
        $this->importDataSetFromFixture('can_render_search_controller.xml');
        $GLOBALS['TSFE'] = $this->getConfiguredTSFE(array(), 1);

        $this->indexPages(array(1, 2));

        //not in the content but we expect to get shoes suggested
        $_GET['q'] = '*';
        // fake that a backend user is logged in
        $GLOBALS['TSFE']->beUserLogin = true;

        $this->searchController->processRequest($this->searchRequest, $this->searchResponse);
        $resultPage1 = $this->searchResponse->getContent();

        $this->assertContains('Parsed Query:', $resultPage1, 'No parsed query in response');
    }

    /**
     * @test
     */
    public function frontendWillRenderErrorMessageWhenSolrIsNotAvailable()
    {
        $this->importDataSetFromFixture('can_render_error_message_when_solr_unavailable.xml');
        $GLOBALS['TSFE'] = $this->getConfiguredTSFE(array(), 1);

        $this->indexPages(array(1));

        $this->searchRequest->setControllerActionName('solrNotAvailable');
        $this->searchController->processRequest($this->searchRequest, $this->searchResponse);
        $resultPage1 = $this->searchResponse->getContent();

        $this->assertEquals('503 Service Unavailable', $this->searchResponse->getStatus());
        $this->assertContains("Search is currently not available.", $resultPage1, 'Response did not contain solr unavailable error message');
    }

    /**
     * @test
     */
    public function canShowLastSearchesFromSessionInResponse()
    {
        $this->importDataSetFromFixture('can_render_search_controller.xml');
        $GLOBALS['TSFE'] = $this->getConfiguredTSFE(array(), 1);

        $this->indexPages(array(1, 2, 3, 4, 5, 6, 7, 8));

        //not in the content but we expect to get shoes suggested
        $_GET['q'] = 'shoe';
        $this->searchController->processRequest($this->searchRequest, $this->searchResponse);

        $searchRequest2 = $this->getPreparedRequest();
        $searchResponse2 = $this->getPreparedResponse();
        $this->searchController->processRequest($searchRequest2, $searchResponse2);
        $resultPage2 = $this->searchResponse->getContent();


        $this->assertContainerByIdContains('>shoe</a>', $resultPage2, 'tx-solr-lastsearches');
    }

    /**
     * @test
     */
    public function canChangeResultsPerPage()
    {
        $this->importDataSetFromFixture('can_render_search_controller.xml');
        $GLOBALS['TSFE'] = $this->getConfiguredTSFE(array(), 1);

        $this->indexPages(array(1, 2, 3, 4, 5, 6, 7, 8));

        //not in the content but we expect to get shoes suggested
        $_GET['q'] = '*';

        $this->searchRequest->setArgument('resultsPerPage', 10);
        $this->searchController->processRequest($this->searchRequest, $this->searchResponse);

        $resultPage = $this->searchResponse->getContent();
        $this->assertContains("Results 1 until 8 of 8", $resultPage, '');
        $this->assertContainerByIdContains('<option selected="selected" value="10">10</option>', $resultPage, 'results-per-page');
    }


    /**
     * @test
     */
    public function canShowLastSearchesFromDatabaseInResponse()
    {
        $this->importDataSetFromFixture('can_render_search_controller.xml');
        $GLOBALS['TSFE'] = $this->getConfiguredTSFE(array(), 1);

        $this->indexPages(array(1, 2, 3, 4, 5, 6, 7, 8));

        $overwriteConfiguration = array();
        $overwriteConfiguration['search.']['lastSearches.']['mode'] = 'global';

        /** @var $configurationManager \ApacheSolrForTypo3\Solr\System\Configuration\ConfigurationManager */
        $configurationManager = GeneralUtility::makeInstance('ApacheSolrForTypo3\Solr\System\Configuration\ConfigurationManager');
        $configurationManager->getTypoScriptConfiguration()->mergeSolrConfiguration($overwriteConfiguration);
        $this->searchController->setResetConfigurationBeforeInitialize(false);

        //not in the content but we expect to get shoes suggested
        $_GET['q'] = 'shoe';
        $this->searchController->processRequest($this->searchRequest, $this->searchResponse);

        $searchRequest2 = $this->getPreparedRequest();
        $searchResponse2 = $this->getPreparedResponse();
        $this->searchController->processRequest($searchRequest2, $searchResponse2);
        $resultPage2 = $this->searchResponse->getContent();

        $this->assertContainerByIdContains('>shoe</a>', $resultPage2, 'tx-solr-lastsearches');
    }

    /**
     * @test
     */
    public function canNotStoreQueyStringInLastSearchesWhenQueryDoesNotReturnAResult()
    {
        $this->importDataSetFromFixture('can_render_search_controller.xml');
        $GLOBALS['TSFE'] = $this->getConfiguredTSFE(array(), 1);

        $this->indexPages(array(1, 2, 3, 4, 5, 6, 7, 8));

        $overwriteConfiguration = array();
        $overwriteConfiguration['search.']['lastSearches.']['mode'] = 'global';

        /** @var $configurationManager \ApacheSolrForTypo3\Solr\System\Configuration\ConfigurationManager */
        $configurationManager = GeneralUtility::makeInstance('ApacheSolrForTypo3\Solr\System\Configuration\ConfigurationManager');
        $configurationManager->getTypoScriptConfiguration()->mergeSolrConfiguration($overwriteConfiguration);
        $this->searchController->setResetConfigurationBeforeInitialize(false);

        //not in the content but we expect to get shoes suggested
        $_GET['q'] = 'nothingwillbefound';
        $this->searchController->processRequest($this->searchRequest, $this->searchResponse);

        $searchRequest2 = $this->getPreparedRequest();
        $searchResponse2 = $this->getPreparedResponse();
        $this->searchController->processRequest($searchRequest2, $searchResponse2);
        $resultPage2 = $this->searchResponse->getContent();

        $this->assertContainerByIdNotContains('>nothingwillbefound</a>', $resultPage2, 'tx-solr-lastsearches');
    }

    /**
     * @param string $content
     * @param string $id
     * @return string
     */
    protected function getIdContent($content, $id)
    {
        $dom = new \DOMDocument('1.0', 'UTF-8');
        $dom->loadHTML('<?xml version="1.0" encoding="UTF-8"?>' . PHP_EOL . $content);

        return $dom->saveXML($dom->getElementById($id));
    }

    /**
     * Assert that a docContainer with a specific id contains an expected content snipped.
     *
     * @param string $expectedToContain
     * @param string $content
     * @param $id
     */
    protected function assertContainerByIdContains($expectedToContain, $content, $id)
    {
        $containerContent = $this->getIdContent($content, $id);
        $this->assertContains($expectedToContain, $containerContent, 'Failed asserting that container with id ' . $id .' contains ' . $expectedToContain);
    }

    /**
     * Assert that a docContainer with a specific id contains an expected content snipped.
     *
     * @param string $expectedToContain
     * @param string $content
     * @param $id
     */
    protected function assertContainerByIdNotContains($expectedToContain, $content, $id)
    {
        $containerContent = $this->getIdContent($content, $id);
        $this->assertNotContains($expectedToContain, $containerContent, 'Failed asserting that container with id ' . $id .' not contains ' . $expectedToContain);
    }


    /**
     * Assertion to check if the pagination markup is present in the response.
     *
     * @param string $content
     */
    protected function assertPaginationVisible($content)
    {
        $this->assertContains('id="solr-pagination"', $content, 'No pagination container visible');
        $this->assertContains('ul class="pagination"', $content, 'Could not see pagination list');
    }

    /**
     * @param $importPageIds
     */
    protected function indexPages($importPageIds)
    {
        foreach ($importPageIds as $importPageId) {
            $fakeTSFE = $this->getConfiguredTSFE(array(), $importPageId);
            $GLOBALS['TSFE'] = $fakeTSFE;
            $fakeTSFE->newCObj();
            PageGenerator::pagegenInit();
            PageGenerator::renderContent();
            /** @var $pageIndexer \ApacheSolrForTypo3\Solr\Typo3PageIndexer */
            $pageIndexer = GeneralUtility::makeInstance('ApacheSolrForTypo3\Solr\Typo3PageIndexer', $fakeTSFE);
            $pageIndexer->indexPage();
        }

        /** @var $beUser  \TYPO3\CMS\Core\Authentication\BackendUserAuthentication */
        $beUser = GeneralUtility::makeInstance('TYPO3\CMS\Core\Authentication\BackendUserAuthentication');
        $GLOBALS['BE_USER'] = $beUser;
        sleep(1);
    }

    /**
     * @return Request
     */
    protected function getPreparedRequest()
    {
        /** @var Request $request */
        $request = $this->objectManager->get(Request::class);
        $request->setControllerName('Search');
        $request->setControllerActionName('results');
        $request->setControllerVendorName('ApacheSolrForTypo3');
        $request->setPluginName('pi_result');
        $request->setFormat('html');
        $request->setControllerExtensionName('Solrfluid');

        return $request;
    }


    /**
     * @return Response
     */
    protected function getPreparedResponse()
    {
        /** @var $response Response */
        $response = $this->objectManager->get(Response::class);

        return $response;
    }
}
