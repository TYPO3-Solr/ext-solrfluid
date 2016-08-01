<?php
namespace ApacheSolrForTypo3\Solrfluid\Widget;

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

use ApacheSolrForTypo3\Solr\ConnectionManager;
use ApacheSolrForTypo3\Solr\Domain\Search\ResultSet\SearchResultSetService;
use ApacheSolrForTypo3\Solr\Search;
use ApacheSolrForTypo3\Solr\System\Configuration\ConfigurationManager;
use ApacheSolrForTypo3\Solrfluid\Mvc\Controller\SolrControllerContext;
use TYPO3\CMS\Core\Utility\GeneralUtility;

/**
 * Class AbstractWidgetController
 *
 * @author Frans Saris <frans@beech.it>
 * @author Timo Hund <timo.hund@dkd.de>
 * @package ApacheSolrForTypo3\Solrfluid\Widget
 */
class AbstractWidgetController extends \TYPO3\CMS\Fluid\Core\Widget\AbstractWidgetController
{

    /**
     * @var array
     */
    protected $supportedRequestTypes = array('ApacheSolrForTypo3\\Solrfluid\\Widget\\WidgetRequest');

    /**
     * @var ConfigurationManager
     */
    private $solrConfigurationManager;

    /**
     * @var \ApacheSolrForTypo3\Solrfluid\Mvc\Controller\SolrControllerContext
     */
    protected $controllerContext;

    /**
     * @param \ApacheSolrForTypo3\Solr\System\Configuration\ConfigurationManager
     */
    public function injectSolrConfigurationManager(ConfigurationManager $configurationManager)
    {
        $this->solrConfigurationManager = $configurationManager;
    }

    /**
     * Initialize the controller context
     *
     * @return \TYPO3\CMS\Extbase\Mvc\Controller\ControllerContext ControllerContext to be passed to the view
     * @api
     */
    protected function buildControllerContext()
    {
        /** @var $controllerContext \ApacheSolrForTypo3\Solrfluid\Mvc\Controller\SolrControllerContext */
        $controllerContext = $this->objectManager->get(SolrControllerContext::class);
        $controllerContext->setRequest($this->request);
        $controllerContext->setResponse($this->response);
        if ($this->arguments !== null) {
            $controllerContext->setArguments($this->arguments);
        }
        $controllerContext->setUriBuilder($this->uriBuilder);

        $typoScriptConfiguration = $this->solrConfigurationManager->getTypoScriptConfiguration();
        $controllerContext->setTypoScriptConfiguration($typoScriptConfiguration);

        $searchService = $this->initializeSearch($typoScriptConfiguration);
        $resultSet = $searchService->getLastResultSet();

        if ($resultSet !== null) {
            $controllerContext->setSearchResultSet($resultSet);
        }

        return $controllerContext;
    }

    /**
     * @param $typoScriptConfiguration
     * @return SearchResultSetService
     */
    protected function initializeSearch($typoScriptConfiguration)
    {
        /** @var \ApacheSolrForTypo3\Solr\ConnectionManager $solrConnection */
        $solrConnection = GeneralUtility::makeInstance(ConnectionManager::class)->getConnectionByPageId($GLOBALS['TSFE']->id, $GLOBALS['TSFE']->sys_language_uid, $GLOBALS['TSFE']->MP);
        $search = GeneralUtility::makeInstance(Search::class, $solrConnection);

        return GeneralUtility::makeInstance(SearchResultSetService::class, $typoScriptConfiguration, $search);
    }
}
