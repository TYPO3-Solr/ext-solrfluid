<?php
namespace ApacheSolrForTypo3\Solrfluid\ViewHelpers\Uri\Facet;

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

use ApacheSolrForTypo3\Solrfluid\ViewHelpers\Uri\AbstractUriViewHelper;
use TYPO3\CMS\Fluid\Core\Rendering\RenderingContextInterface;
use TYPO3\CMS\Fluid\Core\ViewHelper\Facets\CompilableInterface;

/**
 * Class FacetAddOptionViewHelper
 *
 * @author Frans Saris <frans@beech.it>
 * @author Timo Hund <timo.hund@dkd.de>
 * @package ApacheSolrForTypo3\Solrfluid\ViewHelpers\Link
 */
class RemoveFacetViewHelper extends AbstractUriViewHelper implements CompilableInterface
{
    /**
     * @param \ApacheSolrForTypo3\Solrfluid\Domain\Search\ResultSet\Facets\AbstractFacet $facet
     * @return string
     */
    public function render($facet)
    {
        return static::renderStatic(
            [
                'facet' => $facet,
            ],
            $this->buildRenderChildrenClosure(),
            $this->renderingContext
        );
    }

    /**
     * @param array $arguments
     * @param callable $renderChildrenClosure
     * @param RenderingContextInterface $renderingContext
     * @return string
     * @throws \InvalidArgumentException
     */
    public static function renderStatic(array $arguments, \Closure $renderChildrenClosure, RenderingContextInterface $renderingContext)
    {
        /** @var  $facet OptionsFacet */
        $facet = $arguments['facet'];
        $previousRequest = $facet->getResultSet()->getUsedSearchRequest();
        $uri = self::getSearchUriBuilder()->getRemoveFacetUri($previousRequest, $facet->getName());
        return $uri;
    }
}
