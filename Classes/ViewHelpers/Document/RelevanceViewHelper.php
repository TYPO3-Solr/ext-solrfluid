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

use ApacheSolrForTypo3\Solrfluid\ViewHelpers\AbstractViewHelper;
use ApacheSolrForTypo3\Solrfluid\Domain\Search\ResultSet\SearchResultSet;
use TYPO3\CMS\Fluid\Core\Rendering\RenderingContextInterface;
use TYPO3\CMS\Fluid\Core\ViewHelper\Facets\CompilableInterface;

/**
 * Class RelevanceViewHelper
 *
 * @author Frans Saris <frans@beech.it>
 * @author Timo Hund <timo.hund@dkd.de>
 * @package ApacheSolrForTypo3\Solrfluid\ViewHelpers\Document
 */
class RelevanceViewHelper extends AbstractViewHelper implements CompilableInterface
{

    /**
     * Initializes the arguments
     */
    public function initializeArguments()
    {
        parent::initializeArguments();
        $this->registerArgument('resultSet', SearchResultSet::class, 'The context searchResultSet', true);
        $this->registerArgument('document', \Apache_Solr_Document::class, 'The document to highlight', true);
    }

    /**
     * Get document relevance percentage
     *
     * Default render method - simply calls renderStatic() with a
     * prepared set of arguments.
     *
     * @return string Rendered string
     * @api
     */
    public function render()
    {
        return static::renderStatic(
            $this->arguments,
            $this->buildRenderChildrenClosure(),
            $this->renderingContext
        );
    }

    /**
     * @param array $arguments
     * @param \Closure $renderChildrenClosure
     * @param RenderingContextInterface $renderingContext
     * @return string
     */
    public static function renderStatic(array $arguments, \Closure $renderChildrenClosure, RenderingContextInterface $renderingContext)
    {
        /** @var $document \Apache_Solr_Document */
        $document = $arguments['document'];
        /** @var $resultSet SearchResultSet */
        $resultSet = $arguments['resultSet'];
        $maximumScore = $document->__solr_grouping_groupMaximumScore ?: $resultSet->getUsedSearch()->getMaximumResultScore();
        $content = 0;
        if ($maximumScore <= 0) {
            return $content;
        }
        $documentScore = $document->getScore();
        $score = floatval($documentScore);
        $multiplier = 100 / $maximumScore;
        $scorePercentage = round($score * $multiplier);
        $content = $scorePercentage;
        return $content;
    }
}
