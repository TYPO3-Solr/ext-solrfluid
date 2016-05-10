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

use ApacheSolrForTypo3\Solr\Search;
use ApacheSolrForTypo3\Solrfluid\ViewHelpers\AbstractViewHelper;
use ApacheSolrForTypo3\Solrfluid\Domain\Search\ResultSet\SearchResultSet;
use TYPO3\CMS\Fluid\Core\Rendering\RenderingContextInterface;
use TYPO3\CMS\Fluid\Core\ViewHelper\Facets\CompilableInterface;

/**
 * Class RelevanceViewHelper
 *
 * @author Frans Saris <frans@beech.it>
 * @author Timo Schmidt <timo.schmidt@dkd.de>
 * @package ApacheSolrForTypo3\Solrfluid\ViewHelpers\Document
 */
class RelevanceViewHelper extends AbstractViewHelper implements CompilableInterface
{
    /**
     * Get document relevance percentage
     *
     * @param SearchResultSet $resultSet
     * @param \Apache_Solr_Document $document
     * @return int
     */
    public function render(SearchResultSet $resultSet, \Apache_Solr_Document $document)
    {
        return self::renderStatic(
            array(
                'resultSet' => $resultSet,
                'document' => $document
            ),
            $this->buildRenderChildrenClosure(),
            $this->renderingContext
        );
    }

    /**
     * @param array $arguments
     * @param callable $renderChildrenClosure
     * @param RenderingContextInterface $renderingContext
     * @return string
     */
    public static function renderStatic(array $arguments, \Closure $renderChildrenClosure, RenderingContextInterface $renderingContext)
    {
        /** @var $document \Apache_Solr_Document */
        $document = $arguments['document'];

            /** @var $resultSet SearchResultSet */
        $resultSet = $arguments['resultSet'];

        $maximumScore = $document->__solr_grouping_groupMaximumScore ? : $resultSet->getUsedSearch()->getMaximumResultScore();
        $documentScore = $document->getScore();
        $content = 0;
        if ($maximumScore > 0) {
            $score = floatval($documentScore);
            $scorePercentage = round($score * 100 / $maximumScore);
            $content = $scorePercentage;
        }
        return $content;
    }
}
