<?php
namespace ApacheSolrForTypo3\Solrfluid\ViewHelpers\Document;

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

use ApacheSolrForTypo3\Solr\Util;
use ApacheSolrForTypo3\Solrfluid\ViewHelpers\AbstractViewHelper;
use TYPO3\CMS\Core\Utility\GeneralUtility;

/**
 * Class HighlightResultViewHelper
 *
 * @author Frans Saris <frans@beech.it>
 * @author Timo Schmidt <timo.schmidt@dkd.de>
 * @package ApacheSolrForTypo3\Solrfluid\ViewHelpers\Document
 */
class HighlightResultViewHelper extends AbstractViewHelper
{

    /**
     * Get high lighted field
     *
     * @param \Apache_Solr_Document $document
     * @param string $field
     * @return string
     */
    public function render(\Apache_Solr_Document $document, $field)
    {
        /** @var \ApacheSolrForTypo3\Solr\Search $search */
        $search = GeneralUtility::makeInstance('ApacheSolrForTypo3\Solr\Search');
        $fragmentSeparator = $this->getTypoScriptConfiguration()->getSearchResultsHighlightingFragmentSeparator();
        $content = call_user_func(array($document, 'get' . $field));

        $highlightedContent = $search->getHighlightedContent();
        if (!empty($highlightedContent->{$document->getId()}->{$field}[0])) {
            $content = implode(' ' . $fragmentSeparator . ' ', $highlightedContent->{$document->getId()}->{$field});
        }
        return $content;
    }
}
