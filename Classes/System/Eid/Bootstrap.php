<?php
namespace ApacheSolrForTypo3\Solrfluid\System\Eid;

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

use \TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Mvc\Web\FrontendRequestHandler;
use TYPO3\CMS\Frontend\Controller\TypoScriptFrontendController;

/**
 * Boostrapper for EID requests.
 *
 * @package ApacheSolrForTypo3\Solrfluid\System\Eid
 */
class Bootstrap
{
    /**
     * configuration
     *
     * @var \array
     */
    protected $configuration;

    /**
     * bootstrap
     *
     * @var \array
     */
    protected $bootstrap;

    /**
     * @param $TYPO3_CONF_VARS
     * @param \TYPO3\CMS\Extbase\Core\Bootstrap $extBaseBootstrap
     */
    public function __construct($TYPO3_CONF_VARS, $extBaseBootstrap)
    {
        $this->typo3ConfVars = $TYPO3_CONF_VARS;
        $this->extBaseBootstrapper = $extBaseBootstrap;
    }

    /**
     * @param int $pageId
     * @param string $pluginName
     * @param string $controllerName
     * @param string $actionName
     * @return string
     */
    public function run($pageId = 1, $pluginName = 'pi_result', $controllerName = 'Search', $actionName = 'search')
    {
        $this->configuration = $this->buildConfiguration($pluginName, $controllerName, $actionName);
        $GLOBALS['TSFE'] = $this->buildTSFE($pageId);
        return $this->extBaseBootstrapper->run('', $this->configuration);
    }

    /**
     * @param $pid
     * @return TypoScriptFrontendController
     */
    protected function buildTSFE($pid)
    {
        $feUserObj = \TYPO3\CMS\Frontend\Utility\EidUtility::initFeUser();
            /** @var $TSFE TypoScriptFrontendController */
        $TSFE = GeneralUtility::makeInstance(TypoScriptFrontendController::class, $this->typo3ConfVars, $pid, 0, true);
        $TSFE->connectToDB();
        $TSFE->fe_user = $feUserObj;
        $TSFE->id = $pid;
        $TSFE->determineId();
        $TSFE->initTemplate();
        $TSFE->getConfigArray();

        return $TSFE;
    }

    /**
     * @param string $pluginName
     * @param string $controllerName
     * @param string $actionName
     * @return array
     */
    protected function buildConfiguration($pluginName, $controllerName, $actionName)
    {
        return [
            'pluginName'    => $pluginName,
            'vendorName'    => 'ApacheSolrForTypo3',
            'extensionName' => 'Solrfluid',
            'controller'    => $controllerName,
            'action'        => $actionName,
            'mvc'           => [
                'requestHandlers' => [
                    FrontendRequestHandler::class => FrontendRequestHandler::class
                ]
            ],
            'settings'      => [],
            'persistence'   => []
        ];
    }
}
