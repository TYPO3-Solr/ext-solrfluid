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

use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Backend\Form\FormDataProvider\TcaSelectItems;
use ApacheSolrForTypo3\Solr\ConnectionManager;

/**
 * This class contains all user functions for flexforms.
 *
 * @author Daniel Siepmann <coding@daniel-siepmann.de>
 */
class FlexformUserFunctions
{
    /**
     * Provides all facet fields for a flexform select, enabling the editor to select one of them.
     *
     * @param array $parentInformation
     *
     * @return void
     */
    public function getFacetFieldsFromSchema(array &$parentInformation)
    {
        array_map(
            function ($fieldName) use ($parentInformation) {
                $parentInformation['items'][] = [
                    // TODO: Fetch TypoScript and provide readable names from TS Setup?!
                    // TODO: Provide black list?
                    // TODO: Check overwrite of items via TSConfig
                    $fieldName,
                    $fieldName,
                ];
            },
            array_keys(
                (array) $this->getConnection(
                    $parentInformation['flexParentDatabaseRow']
                )->getFieldsMetaData()
            )
        );
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
}
