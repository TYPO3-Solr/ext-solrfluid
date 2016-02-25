<?php
namespace ApacheSolrForTypo3\Solrfluid\Controller;

use ApacheSolrForTypo3\Solr\System\Configuration\ConfigurationManager;
use ApacheSolrForTypo3\Solr\System\Configuration\TypoScriptConfiguration;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Configuration\ConfigurationManagerInterface;
use TYPO3\CMS\Extbase\Mvc\Controller\ActionController;
use TYPO3\CMS\Extbase\Utility\ArrayUtility;
use TYPO3\CMS\Frontend\ContentObject\ContentObjectRenderer;
use TYPO3\CMS\Frontend\Controller\TypoScriptFrontendController;

/**
 * Class AbstractBaseController
 * @package ApacheSolrForTypo3\Solrfluid\Controller
 */
abstract class AbstractBaseController extends ActionController
{
    /**
     * @var string
     */
    protected $pluginName = 'search';

    /**
     * Flexform information
     *
     * @var array
     */
    public $flexformData = array();

    /**
     * @var ContentObjectRenderer
     */
    protected $contentObjectRenderer;

    /**
     * @var TypoScriptFrontendController
     */
    protected $typoScriptFrontendController;

    /**
     * An instance of Tx_Solr_Search
     *
     * @var \Tx_Solr_Search
     */
    protected $search;

    /**
     * Determines whether the solr server is available or not.
     */
    protected $solrAvailable;

    /**
     * @var TypoScriptConfiguration
     */
    protected $typoScriptConfiguration;

    /**
     * @var ConfigurationManager
     */
    protected $solrConfigurationManager;

    /**
     * @param \ApacheSolrForTypo3\Solr\System\Configuration\ConfigurationManager
     */
    public function injectSolrConfigurationManager(ConfigurationManager $configurationManager)
    {
        $this->solrConfigurationManager = $configurationManager;
    }

    /**
     * Initialize action
     */
    protected function initializeAction()
    {
        parent::initializeAction();
        $this->typoScriptFrontendController = $GLOBALS['TSFE'];
        $this->typoScriptConfiguration = $this->solrConfigurationManager->getTypoScriptConfiguration();
        $this->initializeSearch();
    }

    /**
     * Initialize the Solr connection and
     * test the connection through a ping
     */
    protected function initializeSearch()
    {
        /** @var \Tx_Solr_ConnectionManager $solrConnection */
        $solrConnection = GeneralUtility::makeInstance('Tx_Solr_ConnectionManager')->getConnectionByPageId(
            $this->typoScriptFrontendController->id,
            $this->typoScriptFrontendController->sys_language_uid,
            $this->typoScriptFrontendController->MP
        );

        $this->search = GeneralUtility::makeInstance('Tx_Solr_Search', $solrConnection);
        $this->solrAvailable = $this->search->ping();
    }
}
