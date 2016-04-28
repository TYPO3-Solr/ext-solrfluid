<?php
namespace ApacheSolrForTypo3\Solrfluid\View;

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

use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Utility\ArrayUtility;
use TYPO3\CMS\Fluid\View\Exception\InvalidTemplateResourceException;
use TYPO3\CMS\Fluid\View\StandaloneView as CoreStandaloneView;

/**
 * Class StandaloneView
 *
 * @author Frans Saris <frans@beech.it>
 * @author Timo Schmidt <timo.schmidt@dkd.de>
 * @package ApacheSolrForTypo3\Solrfluid\View
 */
class StandaloneView extends CoreStandaloneView
{
    /**
     * Path(s) to the template root
     *
     * @var array
     */
    protected $templateRootPaths = null;

    /**
     * @var array
     */
    protected $templatePathCache = array();

    /**
     * Set templateRootPaths
     *
     * @param array $templateRootPaths
     */
    public function setTemplateRootPaths(array $templateRootPaths)
    {
        $this->templateRootPaths = $templateRootPaths;
    }

    /**
     * @param $templateName
     * @param bool $throwException
     * @return null
     * @throws InvalidTemplateResourceException
     */
    public function setTemplateName($templateName, $throwException = true)
    {
        $templateName = ucfirst($templateName);
        $cacheKey = 't_' . $templateName;
        if (!isset($this->templatePathCache[$cacheKey])) {
            $paths = ArrayUtility::sortArrayWithIntegerKeys($this->templateRootPaths);
            $paths = array_reverse($paths, true);
            $possibleTemplatePaths = array();
            foreach ($paths as $templateRootPath) {
                $possibleTemplatePaths[] = GeneralUtility::fixWindowsFilePath($templateRootPath . '/' . $templateName . '.html');
                $possibleTemplatePaths[] = GeneralUtility::fixWindowsFilePath($templateRootPath . '/' . $templateName);
            }
            foreach ($possibleTemplatePaths as $templatePathAndFilename) {
                if ($this->testFileExistence($templatePathAndFilename)) {
                    $this->templatePathCache[$cacheKey] = $templatePathAndFilename;
                    break;
                }
            }
        }
        if (isset($this->templatePathCache[$cacheKey])) {
            return $this->setTemplatePathAndFilename($this->templatePathCache[$cacheKey]);
        } elseif ($throwException) {
            throw new InvalidTemplateResourceException('Could not load template file. Tried following paths: "' . implode('", "', $possibleTemplatePaths) . '".', 1413190242);
        } else {
            return null;
        }
    }
}
