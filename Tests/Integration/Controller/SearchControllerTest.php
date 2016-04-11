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
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Mvc\Request;
use TYPO3\CMS\Extbase\Mvc\Web\RequestBuilder;
use TYPO3\CMS\Extbase\Mvc\Web\Response;
use TYPO3\CMS\Frontend\ContentObject\ContentObjectRenderer;
use TYPO3\CMS\Frontend\Page\PageGenerator;

/**
 * Integration testcase to test for the SearchController
 *
 * @author Timo Schmidt
 * @package TYPO3
 * @subpackage solr
 */
class SearchControllerTest extends IntegrationTest
{

    /**
     * @var array
     */
    protected $testExtensionsToLoad = array('typo3conf/ext/solr', 'typo3conf/ext/solrfluid');

    public function setUp()
    {
        parent::setUp();
        $GLOBALS['TT'] = $this->getMock('\\TYPO3\\CMS\\Core\\TimeTracker\\TimeTracker', array(), array(), '', false);
    }

    /**
     * @test
     */
    public function canShowSearchForm()
    {
        $this->importDataSetFromFixture('can_render_search_controller.xml');
        $GLOBALS['TSFE'] = $this->getConfiguredTSFE(array(), 1);
        $this->indexPages(array(1, 2));

            /** @var  $searchController \ApacheSolrForTypo3\Solrfluid\Controller\SearchController */
        $searchController = $this->objectManager->get(\ApacheSolrForTypo3\Solrfluid\Controller\SearchController::class);
        $request = $this->getPreparedRequest();
        $response = $this->getPreparedResponse();
        $searchController->processRequest($request, $response);

        $this->assertContains('id="tx-solr-search-form-pi-results"', $response->getContent(), 'Response did not contain search css selector');
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

        /** @var  $searchController \ApacheSolrForTypo3\Solrfluid\Controller\SearchController */
        $searchController = $this->objectManager->get(\ApacheSolrForTypo3\Solrfluid\Controller\SearchController::class);
        $request = $this->getPreparedRequest();
        $response = $this->getPreparedResponse();
        $searchController->processRequest($request, $response);

        $result = $response->getContent();
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
        /** @var  $searchController \ApacheSolrForTypo3\Solrfluid\Controller\SearchController */
        $searchController = $this->objectManager->get(\ApacheSolrForTypo3\Solrfluid\Controller\SearchController::class);
        $request = $this->getPreparedRequest();
        $response = $this->getPreparedResponse();
        $searchController->processRequest($request, $response);

        $resultPage1 = $response->getContent();
        $this->assertPaginationVisible($resultPage1);
        $this->assertContains('Results 1 until 5 of 8', $resultPage1, 'Wrong result count indicated in template');
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
