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

use TYPO3\CMS\Extbase\Utility\LocalizationUtility;
use TYPO3\CMS\Fluid\Core\Rendering\RenderingContextInterface;

/**
 * Class TranslateViewHelper
 *
 * @author Frans Saris <frans@beech.it>
 * @author Timo Hund <timo.hund@dkd.de>
 * @package ApacheSolrForTypo3\Solrfluid\ViewHelpers
 */
class TranslateViewHelper extends \TYPO3\CMS\Fluid\ViewHelpers\TranslateViewHelper
{
    /**
     * @var bool
     */
    protected $escapeChildren = true;

    /**
     * @var bool
     */
    protected $escapeOutput = false;

    /**
     * Render translation
     *
     * Wrapper function to support "old solr way" of doing translations
     *
     * @param string $key Translation Key
     * @param string $id Translation Key compatible to TYPO3 Flow
     * @param string $default If the given locallang key could not be found, this value is used. If this argument is not set, child nodes will be used to render the default
     * @param bool $htmlEscape TRUE if the result should be htmlescaped. This won't have an effect for the default value
     * @param array $arguments Arguments to be replaced in the resulting string
     * @param string $extensionName UpperCamelCased extension key (for example BlogExample)
     * @return string The translated key or tag body if key doesn't exist
     */
    public function render($key = null, $id = null, $default = null, $htmlEscape = null, array $arguments = null, $extensionName = null)
    {
        return self::renderStatic(
            array(
                'key' => $key,
                'id' => $id,
                'default' => $default,
                'htmlEscape' => $htmlEscape,
                'arguments' => $arguments,
                'extensionName' => $extensionName,
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
        $arguments['extensionName'] = $arguments['extensionName'] === null ? 'Solr' : $arguments['extensionName'];
        $result = parent::renderStatic($arguments, $renderChildrenClosure, $renderingContext);

        $result = self::replaceTranslationPrefixesWithAtWithStringMarker($result);
        if (trim($result) === '') {
            $result = $arguments['default'] !== null ? $arguments['default'] : $renderChildrenClosure();
        }

        $result = vsprintf($result, $arguments['arguments']);
        return $result;
    }

    /**
     * @param $result
     * @return mixed
     */
    protected static function replaceTranslationPrefixesWithAtWithStringMarker($result)
    {
        if (strpos($result, '@') !== false) {
            $result = preg_replace('~\"?@[a-zA-Z]*\"?~', '%s', $result);
        }
        return $result;
    }
}
