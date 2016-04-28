<?php
namespace ApacheSolrForTypo3\Solrfluid\ViewHelpers\Debug;

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

use ApacheSolrForTypo3\Solrfluid\ViewHelpers\AbstractViewHelper;
use TYPO3\CMS\Core\Utility\GeneralUtility;

/**
 * Class QueryViewHelper
 *
 * @author Frans Saris <frans@beech.it>
 * @author Timo Schmidt <timo.schmidt@dkd.de>
 */
class QueryViewHelper extends AbstractViewHelper
{

    /**
     * Get parsed query debug output
     * only visible for logged in BE users
     *
     * @return string
     */
    public function render()
    {
        $content = '';
        if (!empty($GLOBALS['TSFE']->beUserLogin) && $this->getSearchResultSet() !== null && $this->getSearchResultSet()->getUsedSearch() !== null) {
            $content = '<br><strong>Parsed Query:</strong><br>' . $this->getSearchResultSet()->getUsedSearch()->getDebugResponse()->parsedquery;
        }
        return $content;
    }
}
