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

/**
 * Class TranslateViewHelper
 */
class TranslateViewHelper extends \TYPO3\CMS\Fluid\ViewHelpers\TranslateViewHelper
{

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
        foreach ($arguments as $key => $value) {
            if (substr($key, 0, 1) === '@') {
                return $this->renderSolrTranslation(
                    $id ?: $key
                );
            }
        }
        return parent::render($key, $id, $default, $htmlEscape, $arguments, $extensionName);
    }

    /**
     * Translate a given key or use the tag body as default.
     * Use strtr instead of vsprintf to replace the arguments
     *
     * @param string $id The locallang id
     * @return string The translated key or tag body if key doesn't exist
     */
    protected function renderSolrTranslation($id)
    {
        $request = $this->controllerContext->getRequest();
        $extensionName = $this->arguments['extensionName'] === null ? $request->getControllerExtensionName() : $this->arguments['extensionName'];
        $value = \TYPO3\CMS\Extbase\Utility\LocalizationUtility::translate($id, $extensionName, $this->arguments['arguments']);
        if ($value === null) {
            $value = $this->arguments['default'] !== null ? $this->arguments['default'] : $this->renderChildren();
            if (is_array($this->arguments['arguments'])) {
                $value = strtr($value, $this->arguments['arguments']);
            }
        } elseif ($this->arguments['htmlEscape']) {
            $value = htmlspecialchars($value);
        }
        return $value;
    }
}
