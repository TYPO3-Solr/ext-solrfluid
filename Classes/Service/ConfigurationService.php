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

use TYPO3\CMS\Extbase\Reflection\ObjectAccess;
use TYPO3\CMS\Extbase\Service\FlexFormService;
use TYPO3\CMS\Extbase\Service\TypoScriptService;
use ApacheSolrForTypo3\Solr\System\Configuration\ConfigurationManager;

/**
 * Service to ease work with configurations.
 */
class ConfigurationService
{
    /**
     * @var \TYPO3\CMS\Extbase\Object\ObjectManagerInterface
     * @inject
     */
    protected $objectManager;

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
