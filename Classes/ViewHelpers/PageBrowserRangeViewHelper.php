<?php
namespace ApacheSolrForTypo3\Solrfluid\ViewHelpers;

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

use ApacheSolrForTypo3\Solrfluid\Domain\Search\ResultSet\SearchResultSet;
use TYPO3\CMS\Fluid\Core\Rendering\RenderingContextInterface;
use TYPO3\CMS\Fluid\Core\ViewHelper\Facets\CompilableInterface;

/**
 * Class PageBrowserRangeViewHelper
 *
 * @author Frans Saris <frans@beech.it>
 * @author Timo Schmidt <timo.schmidt@dkd.de> *
 * @package ApacheSolrForTypo3\Solrfluid\ViewHelpers
 */
class PageBrowserRangeViewHelper extends AbstractViewHelper implements CompilableInterface
{

    /**
     * @param string $from variable name for from value
     * @param string $to variable name for to value
     * @param string $total variable name for total value
     * @return string
     * @throws \TYPO3\CMS\Fluid\Core\ViewHelper\Exception\InvalidVariableException
     */
    public function render($from = 'from', $to = 'to', $total = 'total')
    {
        return self::renderStatic(
            array(
                'from' => $from,
                'to' => $to,
                'total' => $total
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
        $from = $arguments['from'];
        $to = $arguments['to'];
        $total = $arguments['total'];

        $search = $renderingContext->getControllerContext()->getSearchResultSet()->getUsedSearch();
        $templateVariableContainer = $renderingContext->getTemplateVariableContainer();

        $resultsFrom = $search->getResponseBody()->start + 1;
        $resultsTo = $resultsFrom + count($search->getResultDocumentsRaw()) - 1;
        $templateVariableContainer->add($from, $resultsFrom);
        $templateVariableContainer->add($to, $resultsTo);
        $templateVariableContainer->add($total, $search->getNumberOfResults());

        $content = $renderChildrenClosure();

        $templateVariableContainer->remove($from);
        $templateVariableContainer->remove($to);
        $templateVariableContainer->remove($total);

        return $content;
    }
}
