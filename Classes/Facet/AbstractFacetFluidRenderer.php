<?php
namespace ApacheSolrForTypo3\Solrfluid\Facet;

/**
 * This source file is proprietary property of Beech Applications B.V.
 * Date: 30-03-2015 15:55
 * All code (c) Beech Applications B.V. all rights reserved
 */
use ApacheSolrForTypo3\Solr\Facet\AbstractFacetRenderer;
use ApacheSolrForTypo3\Solr\Facet\Facet;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use ApacheSolrForTypo3\Solr\View\StandaloneView;
use TYPO3\CMS\Extbase\Utility\DebuggerUtility;
use TYPO3\CMS\Fluid\View\Exception\InvalidTemplateResourceException;

/**
 * Class AbstractFacetFluidRenderer
 */
abstract class AbstractFacetFluidRenderer extends AbstractFacetRenderer implements FacetFluidRendererInterface
{

    /**
     * @var \TYPO3\CMS\Extbase\Service\TypoScriptService
     */
    protected $typoScriptService;

    /**
     * @var array TypoScript settings
     */
    protected $settings = array();

    /**
     * @var StandaloneView
     */
    protected $view;

    /**
     * Constructor
     *
     * @param Facet $facet The facet to render.
     */
    public function __construct(Facet $facet)
    {
        $this->search = GeneralUtility::makeInstance('ApacheSolrForTypo3\Solr\Search');

        $this->facet              = $facet;
        $this->facetName          = $facet->getName();

        $this->solrConfiguration  = \ApacheSolrForTypo3\Solr\Util::getSolrConfiguration();
        $this->facetConfiguration = $this->solrConfiguration->getSearchFacetingFacetByName($facet->getName());

        $this->typoScriptService = GeneralUtility::makeInstance(
            'TYPO3\\CMS\\Extbase\\Service\\TypoScriptService'
        );
     /*   $this->settings = $this->typoScriptService->convertTypoScriptArrayToPlainArray(
            \ApacheSolrForTypo3\Solr\Util::getSolrConfiguration()
        );*/

        $this->queryLinkBuilder = GeneralUtility::makeInstance('ApacheSolrForTypo3\\Solr\\Query\\LinkBuilder',
            $this->search->getQuery());
        $this->initView();
    }

    /**
     * Init view
     */
    protected function initView()
    {
        /** @var StandaloneView view */
        $this->view = GeneralUtility::makeInstance('ApacheSolrForTypo3\\Solrfluid\\View\\StandaloneView');
        $paths = $this->settings['view']['layoutRootPaths'];
        $this->view->setLayoutRootPaths($this->fixPaths($paths ?: array('EXT:solrfluid/Resources/Private/Layouts')));
        $paths = $this->settings['view']['partialRootPaths'];
        $this->view->setPartialRootPaths($this->fixPaths($paths ?: array('EXT:solrfluid/Resources/Private/Partials')));
        $paths = $this->settings['view']['templateRootPaths'];
        $this->view->setTemplateRootPaths($this->fixPaths($paths ?: array('EXT:solrfluid/Resources/Private/Templates')));
    }

    /**
     * Get abs paths
     *
     * @param array $paths
     * @return array
     */
    protected function fixPaths($paths)
    {
        foreach ($paths as $key => $path) {
            $paths[$key] = GeneralUtility::getFileAbsFileName($path);
        }
        return $paths;
    }

    /**
     * Renders the complete facet.
     *
     * @return	string	Facet markup.
     */
    public function renderFacet()
    {
        $facetContent = '';

        $showEmptyFacets = $this->solrConfiguration->getSearchFacetingShowEmptyFacetsByName($this->facetName);

        // if the facet doesn't provide any options, don't render it unless
        // it is configured to be rendered nevertheless
        if (!$this->facet->isEmpty() || $showEmptyFacets) {
            try {
                $this->view->setTemplateName('Facets/' . ($this->facetConfiguration['fluid.']['template'] ?: 'Default'));
            } catch (InvalidTemplateResourceException $e) {
                return $e->getMessage();
            }
            /** @var \TYPO3\CMS\Frontend\ContentObject\ContentObjectRenderer $contentObject */
            $contentObject = GeneralUtility::makeInstance('TYPO3\\CMS\\Frontend\\ContentObject\\ContentObjectRenderer');
            $label = $contentObject->stdWrap(
                $this->facetConfiguration['label'],
                $this->facetConfiguration['label.']
            );
            $this->view->assign('label', $label);
            $this->view->assign('facet', $this->facet);
            $this->view->assign('settings', $this->settings);
            $this->renderFacetOptions();
            $facetContent = $this->view->render();
        }

        return $facetContent;
    }
}
