<?php
namespace ApacheSolrForTypo3\Solrfluid\Service;

/*
 * Copyright (C) 2016  Daniel Siepmann <coding@daniel-siepmann.de>
 *
 * This program is free software; you can redistribute it and/or
 * modify it under the terms of the GNU General Public License
 * as published by the Free Software Foundation; either version 2
 * of the License, or (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA
 * 02110-1301, USA.
 */

use ApacheSolrForTypo3\Solr\System\Configuration\ConfigurationManager;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Configuration\ConfigurationManager as ExtbaseConfigurationManager;
use TYPO3\CMS\Extbase\Object\ObjectManager;
use TYPO3\CMS\Extbase\Reflection\ObjectAccess;
use TYPO3\CMS\Extbase\Service\FlexFormService;
use TYPO3\CMS\Extbase\Service\TypoScriptService;

/**
 * Service to ease work with configurations.
 */
class ConfigurationService
{
    const TYPOSCRIPT_PATH = 'plugin.tx_solr';

    /**
     * @var \TYPO3\CMS\Extbase\Object\ObjectManagerInterface
     * @inject
     */
    protected $objectManager;

    /**
     * Get TypoScript setup on current page for the given path.
     *
     * @param string $path Dotted path like in TypoScript or Fluid.
     *
     * @return array
     */
    public static function getTypoScriptSetup($path = self::TYPOSCRIPT_PATH)
    {
        $objectManager = GeneralUtility::makeInstance(ObjectManager::class);
        $setup = $objectManager->get(ExtbaseConfigurationManager::class)
            ->getConfiguration(ExtbaseConfigurationManager::CONFIGURATION_TYPE_FULL_TYPOSCRIPT);

        $setup = $objectManager->get(TypoScriptService::class)
            ->convertTypoScriptArrayToPlainArray($setup);

        return ObjectAccess::getPropertyPath($setup, $path);
    }

    /**
     * Override the given solrConfiguration with flex form configuration.
     *
     * @param string $flexFormData The raw data from database.
     * @param ConfigurationManager $solrConfigurationManager
     *
     * @return void
     */
    public function overrideConfigurationWithFlexFormSettings($flexFormData, ConfigurationManager $solrConfigurationManager)
    {
        if (empty($flexFormData)) {
            return $flexFormData;
        }

        $flexFormConfiguration = $this->objectManager->get(flexFormService::class)
            ->convertflexFormContentToArray($flexFormData);
        $flexFormConfiguration = $this->overrideFilter($flexFormConfiguration);

        $flexFormConfiguration = $this->objectManager->get(TypoScriptService::class)
            ->convertPlainArrayToTypoScriptArray($flexFormConfiguration);
        $solrConfigurationManager->getTypoScriptConfiguration()->mergeSolrConfiguration($flexFormConfiguration, true, false);
    }

    /**
     * Override filter in configuration.
     *
     * Will parse the filter from flex form structure and rewrite it as typoscript structure.
     *
     * @param array $flexFormConfiguration
     *
     * @return array
     */
    protected function overrideFilter(array $flexFormConfiguration)
    {
        $filter = $this->getFilterFromFlexForm($flexFormConfiguration);
        unset($flexFormConfiguration['search']['query']['filter']);
        if (empty($filter)) {
            return $flexFormConfiguration;
        }

        return array_merge_recursive(
            $flexFormConfiguration,
            [
                'search' => [
                    'query' => [
                        'filter' => $filter,
                    ],
                ],
            ]
        );
    }

    /**
     * Returns filter in typoscript form from flex form.
     *
     * @param array $flexFormConfiguration
     *
     * @return array
     */
    protected function getFilterFromFlexForm(array $flexFormConfiguration)
    {
        $filterConfiguration = [];
        $filters = ObjectAccess::getPropertyPath($flexFormConfiguration, 'search.query.filter');
        if ($filters === null) {
            return $filterConfiguration;
        }

        foreach ($filters as $filter) {
            $filter = $filter['field'];
            $filterConfiguration[] = $filter['field'] . ':' . $filter['value'];
        }

        return $filterConfiguration;
    }
}
