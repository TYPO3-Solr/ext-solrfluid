<?php
namespace ApacheSolrForTypo3\Solrfluid\Controller;

use ApacheSolrForTypo3\Solr\ConnectionManager;
use ApacheSolrForTypo3\Solr\Domain\Search\ResultSet\SearchResultSetService;
use ApacheSolrForTypo3\Solr\Search;
use ApacheSolrForTypo3\Solr\System\Configuration\ConfigurationManager;
use ApacheSolrForTypo3\Solr\System\Configuration\TypoScriptConfiguration;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Configuration\ConfigurationManagerInterface;
use TYPO3\CMS\Extbase\Mvc\Controller\ActionController;
use TYPO3\CMS\Extbase\Service\FlexFormService;
use TYPO3\CMS\Extbase\Service\TypoScriptService;
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
     * @var ContentObjectRenderer
     */
    protected $contentObjectRenderer;

    /**
     * @var TypoScriptFrontendController
     */
    protected $typoScriptFrontendController;

    /**
     * @var TypoScriptConfiguration
     */
    protected $typoScriptConfiguration;

    /**
     * @var \TYPO3\CMS\Extbase\Configuration\ConfigurationManagerInterface
     */
    protected $configurationManager;

    /**
     * @var ConfigurationManager
     */
    protected $solrConfigurationManager;

    /**
     * @var \ApacheSolrForTypo3\Solr\Domain\Search\ResultSet\SearchResultSetService
     */
    protected $searchService;

    /**
     * @param \TYPO3\CMS\Extbase\Configuration\ConfigurationManagerInterface $configurationManager
     * @return void
     */
    public function injectConfigurationManager(ConfigurationManagerInterface $configurationManager) {
        $this->configurationManager = $configurationManager;
        $this->contentObjectRenderer = $this->configurationManager->getContentObject();
    }

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
        // Reset configuration (to reset flexform overrides)
        $this->solrConfigurationManager->reset();
        // Override configuration with flexform settings
        if (!empty($this->contentObjectRenderer->data['pi_flexform'])) {
            $flexFormService = $this->objectManager->get(FlexFormService::class);
            $flexFormConfiguration = $flexFormService->convertFlexFormContentToArray(
                $this->contentObjectRenderer->data['pi_flexform']
            );
            $typoScriptService = $this->objectManager->get(TypoScriptService::class);
            $flexFormConfiguration = $typoScriptService->convertPlainArrayToTypoScriptArray(
                $flexFormConfiguration
            );

            $this->solrConfigurationManager->getTypoScriptConfiguration()->mergeSolrConfiguration(
                $flexFormConfiguration,
                true,
                false
            );
        }

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
        $solrConnection = GeneralUtility::makeInstance(ConnectionManager::class)->getConnectionByPageId(
            $this->typoScriptFrontendController->id,
            $this->typoScriptFrontendController->sys_language_uid,
            $this->typoScriptFrontendController->MP
        );
        $search = GeneralUtility::makeInstance(Search::class, $solrConnection);

        /** @var $searchService \ApacheSolrForTypo3\Solr\Domain\Search\ResultSet\SearchResultSetService */
        $this->searchService = GeneralUtility::makeInstance(SearchResultSetService::class, $this->typoScriptConfiguration, $search);
    }
}
