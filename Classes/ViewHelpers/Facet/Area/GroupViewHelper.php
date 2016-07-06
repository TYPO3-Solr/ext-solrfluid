<?php
namespace ApacheSolrForTypo3\Solrfluid\ViewHelpers\Facet\Area;

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

use ApacheSolrForTypo3\Solrfluid\Domain\Search\ResultSet\Facets\FacetCollection;
use ApacheSolrForTypo3\Solrfluid\ViewHelpers\AbstractViewHelper;
use TYPO3\CMS\Fluid\Core\Rendering\RenderingContextInterface;
use TYPO3\CMS\Fluid\Core\ViewHelper\Facets\CompilableInterface;

/**
 * Class PageBrowserRangeViewHelper
 *
 * @author Frans Saris <frans@beech.it>
 * @author Timo Hund <timo.hund@dkd.de> *
 * @package ApacheSolrForTypo3\Solrfluid\ViewHelpers
 */
class GroupViewHelper extends AbstractViewHelper implements CompilableInterface
{
    /**
     * @var bool
     */
    protected $escapeOutput = false;

    /**
     * @param FacetCollection $facets
     * @param string $groupName
     * @return string
     */
    public function render(FacetCollection $facets, $groupName = 'main')
    {
        return self::renderStatic(
            array(
                'facets' => $facets,
                'groupName' => $groupName
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
        /** @var  $facets FacetCollection */
        $facets = $arguments['facets'];
        $requiredGroup = isset($arguments['groupName']) ? $arguments['groupName'] :  'main';
        $filtered = $facets->getByGroupName($requiredGroup);

        $templateVariableContainer = $renderingContext->getTemplateVariableContainer();
        $templateVariableContainer->add('areaFacets', $filtered);
        $content = $renderChildrenClosure();
        $templateVariableContainer->remove('areaFacets');

        return $content;
    }
}
