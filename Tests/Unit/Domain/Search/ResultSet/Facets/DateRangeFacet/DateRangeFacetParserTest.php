<?php
namespace ApacheSolrForTypo3\Solrfluid\Test\Domain\Search\ResultSet\Facets\DateRangeFacet;

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

use ApacheSolrForTypo3\Solr\Tests\Unit\UnitTest;
use ApacheSolrForTypo3\Solrfluid\Domain\Search\ResultSet\Facets\DateRangeFacet\DateRangeFacet;
use ApacheSolrForTypo3\Solrfluid\Domain\Search\ResultSet\Facets\DateRangeFacet\DateRangeFacetParser;
use ApacheSolrForTypo3\Solrfluid\Test\Domain\Search\ResultSet\Facets\AbstractFacetParserTest;

/**
 * Class DateRangeFacetParserTest
 *
 * @author Timo Schmidt <timo.schmidt@dkd.de>
 */
class DateRangeFacetParserTest extends AbstractFacetParserTest
{
    /**
     * @test
     */
    public function facetIsCreated()
    {
        $facetConfiguration = [
            'myCreated.' => [
                'type' => 'dateRange',
                'label' => 'Created',
                'field' => 'created',
            ]
        ];

        $searchResultSet = $this->initializeSearchResultSetFromFakeResponse(
            'fake_solr_response_with_dateRange_facet.json',
            $facetConfiguration
        );
        $searchResultSet->getUsedSearchRequest()->expects($this->any())->method('getActiveFacetValuesByName')->will(
            $this->returnCallback(function ($name) {
                return $name == 'myCreated' ? ['201506020000-201706020000'] : [];

            })
        );

        $parser = new DateRangeFacetParser();
        $facet = $parser->parse($searchResultSet, 'myCreated', $facetConfiguration['myCreated.']);
        $this->assertInstanceOf(DateRangeFacet::class, $facet);
        $this->assertSame($facet->getConfiguration(), $facetConfiguration['myCreated.'], 'Configuration was not passed to new facets');
        $this->assertTrue($facet->getIsUsed());

        $this->assertEquals('201506020000-201706020000', $facet->getDateRange()->getLabel());
        $this->assertEquals(32, $facet->getDateRange()->getDocumentCount());
        $this->assertCount(3, $facet->getDateRange()->getDateRangeCounts(), 'We expected that there are three count items attached');

        $this->assertSame($facet->getDateRange()->getEndInResponse()->format('Ymd'), '20170602');
        $this->assertSame($facet->getDateRange()->getStartInResponse()->format('Ymd'), '20150602');
    }
}
