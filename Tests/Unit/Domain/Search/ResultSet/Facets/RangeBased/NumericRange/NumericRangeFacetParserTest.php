<?php
namespace ApacheSolrForTypo3\Solrfluid\Test\Domain\Search\ResultSet\Facets\RangeBased\NumericRange;

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
use ApacheSolrForTypo3\Solrfluid\Domain\Search\ResultSet\Facets\RangeBased\NumericRange\NumericRange;
use ApacheSolrForTypo3\Solrfluid\Domain\Search\ResultSet\Facets\RangeBased\NumericRange\NumericRangeFacet;
use ApacheSolrForTypo3\Solrfluid\Domain\Search\ResultSet\Facets\RangeBased\NumericRange\NumericRangeFacetParser;
use ApacheSolrForTypo3\Solrfluid\Test\Domain\Search\ResultSet\Facets\AbstractFacetParserTest;

/**
 * Class DateRangeFacetParserTest
 *
 * @author Timo Schmidt <timo.schmidt@dkd.de>
 */
class NumericRangeFacetParserTest extends AbstractFacetParserTest
{
    /**
     * @test
     */
    public function facetIsCreated()
    {
        $facetConfiguration = [
            'myPids.' => [
                'type' => 'numericRange',
                'label' => 'Pids',
                'field' => 'pid',
            ]
        ];

        $searchResultSet = $this->initializeSearchResultSetFromFakeResponse(
            'fake_solr_response_with_numericRange_facet.json',
            $facetConfiguration
        );
        $searchResultSet->getUsedSearchRequest()->expects($this->any())->method('getActiveFacetValuesByName')->will(
            $this->returnCallback(function ($name) {
                return $name == 'myPids' ? ['10-98'] : [];

            })
        );

        $parser = new NumericRangeFacetParser();
        $facet = $parser->parse($searchResultSet, 'myPids', $facetConfiguration['myPids.']);
        $this->assertInstanceOf(NumericRangeFacet::class, $facet);
        $this->assertSame($facet->getConfiguration(), $facetConfiguration['myPids.'], 'Configuration was not passed to new facets');
        $this->assertTrue($facet->getIsUsed());

        $this->assertEquals('10-98', $facet->getRange()->getLabel());
        $this->assertEquals(25, $facet->getRange()->getDocumentCount());
        $this->assertCount(4, $facet->getRange()->getRangeCounts(), 'We expected that there are three count items attached');

        $this->assertSame($facet->getRange()->getEndInResponse(), 100);
        $this->assertSame($facet->getRange()->getStartInResponse(), 0);
    }
}
