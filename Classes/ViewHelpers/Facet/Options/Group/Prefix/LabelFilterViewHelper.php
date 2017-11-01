<?php
namespace ApacheSolrForTypo3\Solrfluid\ViewHelpers\Facet\Options\Group\Prefix;

/***************************************************************
 *  Copyright notice
 *
 *  (c) 2017 Timo Hund <timo.hund@dkd.de>
 *  All rights reserved
 *
 *  This script is part of the TYPO3 project. The TYPO3 project is
 *  free software; you can redistribute it and/or modify
 *  it under the terms of the GNU General Public License as published by
 *  the Free Software Foundation; either version 2 of the License, or
 *  (at your option) any later version.
 *
 *  The GNU General Public License can be found at
 *  http://www.gnu.org/copyleft/gpl.html.
 *
 *  This script is distributed in the hope that it will be useful,
 *  but WITHOUT ANY WARRANTY; without even the implied warranty of
 *  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *  GNU General Public License for more details.
 *
 *  This copyright notice MUST APPEAR in all copies of the script!
 ***************************************************************/

use ApacheSolrForTypo3\Solrfluid\Domain\Search\ResultSet\Facets\OptionBased\OptionCollection;
use ApacheSolrForTypo3\Solrfluid\ViewHelpers\AbstractViewHelper;
use TYPO3\CMS\Fluid\Core\ViewHelper\Facets\CompilableInterface;
use TYPO3\CMS\Fluid\Core\Rendering\RenderingContextInterface;

/**
 * Class LabelFilterViewHelper
 *
 * @author Timo Hund <timo.hund@dkd.de>
 * @package ApacheSolrForTypo3\Solr\ViewHelpers\Facet\Options
 */
class LabelFilterViewHelper extends AbstractViewHelper implements CompilableInterface
{

    /**
     * @var bool
     */
    protected $escapeOutput = false;

    /**
     * @param OptionCollection $options
     * @param string $prefix
     * @return string
     */
    public function render(OptionCollection $options, $prefix)
    {
        return self::renderStatic(
            [
                'options' => $options,
                'prefix' => $prefix
            ],
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
        /** @var  $options OptionCollection */
        $options = $arguments['options'];
        $requiredPrefix = strtolower($arguments['prefix']);
        $filtered = $options->getByLowercaseLabelPrefix($requiredPrefix);

        $templateVariableContainer = $renderingContext->getTemplateVariableContainer();
        $templateVariableContainer->add('filteredOptions', $filtered);
        $content = $renderChildrenClosure();
        $templateVariableContainer->remove('filteredOptions');

        return $content;
    }
}
