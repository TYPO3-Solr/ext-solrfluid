<?php
namespace ApacheSolrForTypo3\Solrfluid\Widget;

/**
 * This source file is proprietary property of Beech Applications B.V.
 * Date: 01-04-2015 10:08
 * All code (c) Beech Applications B.V. all rights reserved
 */
use ApacheSolrForTypo3\Solr\System\Configuration\ConfigurationManager;
use ApacheSolrForTypo3\Solrfluid\Mvc\Controller\SolrControllerContext;

/**
 * Class AbstractWidgetController
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
        $controllerContext->setTypoScriptConfiguration($this->solrConfigurationManager->getTypoScriptConfiguration());

        return $controllerContext;
    }
}
