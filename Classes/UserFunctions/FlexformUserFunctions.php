<?php
namespace ApacheSolrForTypo3\Solrfluid\UserFunctions;

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

use ApacheSolrForTypo3\Solr\ConnectionManager;
use TYPO3\CMS\Backend\Form\FormDataProvider\TcaSelectItems;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Configuration\BackendConfigurationManager;
use TYPO3\CMS\Extbase\Object\ObjectManager;
use TYPO3\CMS\Extbase\Reflection\ObjectAccess;
use TYPO3\CMS\Extbase\Service\TypoScriptService;

/**
 * This class contains all user functions for flexforms.
 *
 * @author Daniel Siepmann <coding@daniel-siepmann.de>
 */
class FlexformUserFunctions
{
    const TYPOSCRIPT_PATH = 'plugin.tx_solr';

    /**
     * Provides all facet fields for a flexform select, enabling the editor to select one of them.
     *
     * @param array $parentInformation
     *
     * @return void
     */
    public function getFacetFieldsFromSchema(array &$parentInformation)
    {
        $configuredFacets = $this->getTypoScriptSetup(static::TYPOSCRIPT_PATH . '.search.faceting.facets');
        $newItems = [];
        array_map(
            function ($fieldName) use (&$newItems, $parentInformation, $configuredFacets) {
                $value = $fieldName;
                $label = $fieldName;
                $configuredFacet = array_filter(
                    $configuredFacets,
                    function ($facet) use ($fieldName) {
                        return ($facet['field'] === $fieldName);
                    }
                );
                if (!empty($configuredFacet)) {
                    $configuredFacet = array_values($configuredFacet);
                    $label = $configuredFacet[0]['label'];
                }

                $newItems[$label] = [
                    $label,
                    $value,
                ];
            },
            array_keys(
                (array) $this->getConnection(
                    $parentInformation['flexParentDatabaseRow']
                )->getFieldsMetaData()
            )
        );

        ksort($newItems, SORT_NATURAL);
        $parentInformation['items'] = $newItems;
    }

    /**
     * Get solr connection.
     *
     * @param array $pageRecord
     *
     * @return ApacheSolrForTypo3\Solr\SolrService
     */
    protected function getConnection(array $pageRecord)
    {
        return GeneralUtility::makeInstance(ConnectionManager::class)->getConnectionByPageId(
            $pageRecord['pid'],
            $pageRecord['sys_language_uid']
        );
    }

    /**
     * Get TypoScript setup on current page for the given path.
     *
     * @TODO: Move to different class, as this is usefull in multiple places
     *        Also check whether the code already exists somewhere..
     *
     * @param string $path Dotted path like in TypoScript or Fluid.
     *
     * @return array
     */
    protected function getTypoScriptSetup($path = self::TYPOSCRIPT_PATH)
    {
        $objectManager = GeneralUtility::makeInstance(ObjectManager::class);
        $setup = $objectManager->get(BackendConfigurationManager::class)
            ->getTypoScriptSetup();

        $setup = $objectManager->get(TypoScriptService::class)
            ->convertTypoScriptArrayToPlainArray($setup);

        return ObjectAccess::getPropertyPath($setup, $path);
    }
}
