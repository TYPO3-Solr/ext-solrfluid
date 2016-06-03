<?php
namespace ApacheSolrForTypo3\Solrfluid\Test\Domain\Search\ResultSet\Facets;

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

use ApacheSolrForTypo3\Solr\Domain\Search\SearchRequest;
use ApacheSolrForTypo3\Solr\System\Configuration\TypoScriptConfiguration;
use ApacheSolrForTypo3\Solr\Tests\Unit\UnitTest;
use ApacheSolrForTypo3\Solrfluid\Domain\Search\ResultSet\SearchResultSet;

/**
 * Class QueryGroupFacetParserTest
 *
 * @author Timo Schmidt <timo.schmidt@dkd.de>
 * @author Frans Saris <frans@beech.it>
 */
abstract class AbstractFacetParserTest extends UnitTest
{
    /**
     * @param string $fixtureFile
     * @param array $facetConfiguration
     * @param array $activeFilters
     * @return SearchResultSet
     */
    protected function initializeSearchResultSetFromFakeResponse($fixtureFile, $facetConfiguration, array $activeFilters = [])
    {
        $fakeResponseJson = $this->getFixtureContent($fixtureFile);
        $httpResponseMock = $this->getDumbMock('\Apache_Solr_HttpTransport_Response');
        $httpResponseMock->expects($this->any())->method('getBody')->will($this->returnValue($fakeResponseJson));

        $searchRequestMock = $this->getDumbMock(SearchRequest::class);

        $fakeResponse = new \Apache_Solr_Response($httpResponseMock);

        $searchResultSet = new SearchResultSet();
        $searchResultSet->setUsedSearchRequest($searchRequestMock);
        $searchResultSet->setResponse($fakeResponse);

        $configuration = array();
        $configuration['plugin.']['tx_solr.']['search.']['faceting.']['facets.'] = $facetConfiguration;
        $typoScriptConfiguration = new TypoScriptConfiguration($configuration);
        $searchRequestMock->expects($this->any())->method('getContextTypoScriptConfiguration')->will($this->returnValue($typoScriptConfiguration));

        $activeFacetNames = [];
        $activeFacetValueMap = [];
        foreach ($activeFilters as $filter) {
            list($facetName, $value) = explode(':', $filter, 2);
            $activeFacetNames[] = $facetName;
            $activeFacetValueMap[] = [$facetName, $value, true];
        }

        $searchRequestMock->expects($this->any())->method('getActiveFacetNames')->will($this->returnValue($activeFacetNames));
        $searchRequestMock->expects($this->any())->method('getHasFacetValue')->will($this->returnValueMap($activeFacetValueMap));

        return $searchResultSet;
    }
}
