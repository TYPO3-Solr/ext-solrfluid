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

use ApacheSolrForTypo3\Solr\Util;
use ApacheSolrForTypo3\Solrfluid\Domain\Search\ResultSet\SearchResultSet;
use ApacheSolrForTypo3\Solrfluid\ViewHelpers\AbstractViewHelper;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Fluid\Core\Rendering\RenderingContextInterface;
use TYPO3\CMS\Fluid\Core\ViewHelper\Facets\CompilableInterface;

/**
 * Class DocumentScoreAnalyzerViewHelper
 *
 * @author Frans Saris <frans@beech.it>
 * @author Timo Schmidt <timo.schmidt@dkd.de>
 * @package ApacheSolrForTypo3\Solrfluid\ViewHelpers\Debug
 */
class DocumentScoreAnalyzerViewHelper extends AbstractViewHelper implements CompilableInterface
{
    /**
     * Get document relevance percentage
     *
     * @param \Apache_Solr_Document $document
     * @return string
     */
    public function render(\Apache_Solr_Document $document)
    {
        return self::renderStatic(
            ['document' => $document],
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
        $content = '';
        $document = $arguments['document'];

        // only check whether a BE user is logged in, don't need to check
        // for enabled score analysis as we wouldn't be here if it was disabled
        if (!empty($GLOBALS['TSFE']->beUserLogin)) {

                /** @var $resultSet SearchResultSet */
            $resultSet = $renderingContext->getControllerContext()->getSearchResultSet();
            $debugData = $resultSet->getUsedSearch()->getDebugResponse()->explain->{$document->getId()};
            $highScores = self::getHighScores($debugData);
            $score = $document->getField('score');
            $queryFields = $resultSet->getUsedSearchRequest()->getContextTypoScriptConfiguration()->getSearchQueryQueryFields();

            $content = self::renderScoreAnalysis($highScores, (float)($score ? $score['value'] : 0), $debugData, $queryFields);
        }

        return $content;
    }

    /**
     * Get high scores
     *
     * @param string $debugData
     * @return array
     */
    protected static function getHighScores($debugData)
    {
        $highScores = array();

        /* TODO Provide better parsing
         *
         * parsing could be done line by line,
         * 		* recording indentation level
         * 		* replacing abbreviations
         * 		* replacing phrases like "product of" by mathematical symbols (* or x)
         * 		* ...
         */

        // matches search term weights, ex: 0.42218783 = (MATCH) weight(content:iPod^40.0 in 43), product of:
        $pattern = '/(.*) = \(MATCH\) weight\((.*)\^/';
        $matches = array();
        preg_match_all($pattern, $debugData, $matches);

        foreach ($matches[0] as $key => $value) {
            // split field from search term
            list($field, $searchTerm) = explode(':', $matches[2][$key]);

            // keep track of highest score per search term
            if (!isset($highScores[$field]) || $highScores[$field]['score'] < $matches[1][$key]) {
                $highScores[$field] = array('score' => $matches[1][$key], 'field' => $field, 'searchTerm' => $searchTerm);
            }
        }

        // Function query
        $pattern = '/(.*) = \(MATCH\) FunctionQuery\((.*)\),/';
        preg_match($pattern, $debugData, $match);
        if (!empty($match[1])) {
            $field = 'FunctionQuery (misses boost value)';
            $highScores[$field] = array('score' => $match[1], 'field' => $field, 'searchTerm' => $match[2]);
        }

        return $highScores;
    }

    /**
     * Renders an overview of how the score for a certain document has been
     * calculated.
     *
     * @param array $highScores The result document which to analyse
     * @param float $realScore
     * @param string $debugData
     * @param string $queryFields
     * @return string The HTML showing the score analysis
     */
    protected static function renderScoreAnalysis(array $highScores, $realScore, $debugData, $queryFields)
    {
        $scores = array();
        $totalScore = 0;

        foreach ($highScores as $field => $highScore) {
            $pattern = '/' . $highScore['field'] . '\^([\d.]*)/';
            $matches = array();
            preg_match_all($pattern, $queryFields, $matches);

            $scores[] = '
				<td>+ ' . $highScore['score'] . '</td>
				<td>' . $highScore['field'] . '</td>
				<td>' . $matches[1][0] . '</td>';

            $totalScore += $highScore['score'];
        }

        $content = '<table class="document-score-analysis" style="width: 100%; border: 1px solid #aaa; font-size: 11px; background-color: #eee;">
			<tr style="border-bottom: 2px solid #aaa; font-weight: bold;"><td>Score</td><td>Field</td><td>Boost</td></tr><tr>' . implode('</tr><tr>', $scores) . '</tr>
			<tr><td colspan="3"><hr style="border-top: 1px solid #aaa; height: 0px; padding: 0px; margin: 0px;" /></td></tr>
			<tr><td colspan="3">= ' . $totalScore . ' (Inaccurate analysis! Not all parts of the score have been taken into account.)</td></tr>
			<tr><td colspan="3">= ' . $realScore . ' (real score)</td></tr>
			<tr class="raw" style="display:none"><td colspan="3"><pre>' . htmlspecialchars($debugData) . '</pre></td></tr>
			</table>';

        return $content;
    }
}
