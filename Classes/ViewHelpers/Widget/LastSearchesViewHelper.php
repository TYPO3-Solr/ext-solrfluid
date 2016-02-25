<?php
namespace ApacheSolrForTypo3\Solrfluid\ViewHelpers\Widget;

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
use ApacheSolrForTypo3\Solrfluid\Widget\AbstractWidgetViewHelper;

/**
 * Class Last searches ViewHelper
 */
class LastSearchesViewHelper extends AbstractWidgetViewHelper
{

    /**
     * @var \ApacheSolrForTypo3\Solrfluid\ViewHelpers\Widget\Controller\LastSearchesController
     * @inject
     */
    protected $controller;

    /**
     * @return \TYPO3\CMS\Extbase\Mvc\ResponseInterface
     * @throws \TYPO3\CMS\Fluid\Core\Widget\Exception\MissingControllerException
     */
    public function render()
    {
        return $this->initiateSubRequest();
    }
}
