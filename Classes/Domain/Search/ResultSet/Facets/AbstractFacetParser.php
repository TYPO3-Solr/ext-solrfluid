<?php
namespace ApacheSolrForTypo3\Solrfluid\Domain\Search\ResultSet\Facets;

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
use TYPO3\CMS\Frontend\ContentObject\ContentObjectRenderer;

/**
 * Class AbstractFacetParser
 *
 * @author Frans Saris <frans@beech.it>
 * @author Timo Schmidt <timo.schmidt@dkd.de>
 * @package ApacheSolrForTypo3\Solrfluid\Domain\Search\ResultSet\Facets
 */
abstract class AbstractFacetParser implements FacetParserInterface
{
    /**
     * @var ContentObjectRenderer
     */
    protected static $reUseAbleContentObject;

    /**
     * @return ContentObjectRenderer
     */
    protected function getReUseAbleContentObject()
    {
        /** @var $contentObject ContentObjectRenderer */
        if (self::$reUseAbleContentObject !== null) {
            return self::$reUseAbleContentObject;
        }

        self::$reUseAbleContentObject = GeneralUtility::makeInstance(ContentObjectRenderer::class);
        return self::$reUseAbleContentObject;
    }

    /**
     * @param array $configuration
     * @return string
     */
    protected function getPlainLabelOrApplyCObject($configuration)
    {
        // when no label is configured we return an empty string
        if (!isset($configuration['label'])) {
            return '';
        }

        // when no sub configuration is set, we use the string, configured as label
        if (!isset($configuration['label.'])) {
            return $configuration['label'];
        }

        // when label and label. was set, we apply the cObject
        return $this->getReUseAbleContentObject()->cObjGetSingle($configuration['label'], $configuration['label.']);
    }

    /**
     * @param mixed $value
     * @param integer $count
     * @param string $facetName
     * @param array $facetConfiguration
     * @return string
     */
    protected function getLabelFromRenderingInstructions($value, $count, $facetName, $facetConfiguration)
    {
        $hasRenderingInstructions = isset($facetConfiguration['renderingInstruction']) && isset($facetConfiguration['renderingInstruction.']);
        if (!$hasRenderingInstructions) {
            return $value;
        }

        $this->getReUseAbleContentObject()->start(array('optionValue' => $value, 'optionCount' => $count, 'facetName' => $facetName));
        return $this->getReUseAbleContentObject()->cObjGetSingle(
            $facetConfiguration['renderingInstruction'],
            $facetConfiguration['renderingInstruction.']
        );
    }
}
