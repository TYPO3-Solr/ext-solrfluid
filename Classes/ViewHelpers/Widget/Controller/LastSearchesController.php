<?php
namespace ApacheSolrForTypo3\Solrfluid\ViewHelpers\Widget\Controller;

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

use ApacheSolrForTypo3\Solr\Domain\Search\LastSearches\LastSearchesService;
use ApacheSolrForTypo3\Solr\Util;
use ApacheSolrForTypo3\Solrfluid\Widget\AbstractWidgetController;
use TYPO3\CMS\Core\Database\DatabaseConnection;
use TYPO3\CMS\Core\Utility\GeneralUtility;

/**
 * Class LastSearchesController
 */
class LastSearchesController extends AbstractWidgetController
{
    /**
     * @var LastSearchesService
     */
    protected $lastSearchesService;

    /**
     * Constructor
     */
    public function __construct()
    {
        // todo: fetch from ControllerContext
        $typoScriptConfiguration = Util::getSolrConfiguration();
        $databaseConnection= $GLOBALS['TYPO3_DB'];
        $tsfe = $GLOBALS['TSFE'];

        $this->lastSearchesService = GeneralUtility::makeInstance(LastSearchesService::class, $typoScriptConfiguration, $tsfe, $databaseConnection);
    }

    /**
     * Last searches
     */
    public function indexAction()
    {
        $this->view->assign('contentArguments', array('lastSearches' => $this->lastSearchesService->getLastSearches()));
    }
}
