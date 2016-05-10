<?php
namespace ApacheSolrForTypo3\Solrfluid\ViewHelpers\Uri\Search;

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

use ApacheSolrForTypo3\Solr\Domain\Search\ResultSet\SearchResultSet;
use ApacheSolrForTypo3\Solrfluid\ViewHelpers\Uri\AbstractUriViewHelper;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Mvc\Web\Routing\UriBuilder;
use TYPO3\CMS\Fluid\Core\Rendering\RenderingContextInterface;
use TYPO3\CMS\Fluid\Core\ViewHelper\Facets\CompilableInterface;

/**
 * Class FacetAddOptionViewHelper
 *
 * @author Frans Saris <frans@beech.it>
 * @author Timo Schmidt <timo.schmidt@dkd.de>
 * @package ApacheSolrForTypo3\Solrfluid\ViewHelpers\Link
 */
class StartNewSearchViewHelper extends AbstractUriViewHelper implements CompilableInterface
{

    /**
     * @param string $queryString
     * @return string
     */
    public function render($queryString = '')
    {
        return self::renderStatic(
            array(
                'queryString' => $queryString
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
        $queryString = $arguments['queryString'];
        $previousRequest = $renderingContext->getControllerContext()->getSearchResultSet()->getUsedSearchRequest();

        $uri = self::getSearchUriBuilder()->getNewSearchUri($previousRequest, $queryString);
        return $uri;
    }
}