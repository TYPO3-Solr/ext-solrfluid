<?php
namespace ApacheSolrForTypo3\Solrfluid\Test\Domain\Search\ResultSet\Facets\QueryGroup;

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
use ApacheSolrForTypo3\Solrfluid\Domain\Search\ResultSet\Facets\QueryGroupFacet\Option;
use ApacheSolrForTypo3\Solrfluid\Domain\Search\ResultSet\Facets\QueryGroupFacet\QueryGroupFacet;
use ApacheSolrForTypo3\Solrfluid\Domain\Search\ResultSet\SearchResultSet;

/**
 * Unit test for the QueryGroupFacet
 *
 * @author Timo Schmidt <timo.schmidt@dkd.de>
 * @author Frans Saris <frans@beech.it>
 */
class QueryGroupFacetTest extends UnitTest
{

    /**
     * @test
     */
    public function canGetTitleFromOptionsFacet()
    {
        $resultSetMock = $this->getDumbMock(SearchResultSet::class);
        $optionsFacet = new QueryGroupFacet($resultSetMock, 'myFacet', 'myFacetFieldName', 'myTitle');
        $this->assertSame('myTitle', $optionsFacet->getLabel(), 'Could not get title from queryGroup facet');
    }

    /**
     * @test
     */
    public function canAddOptionsToFacet()
    {
        $resultSetMock = $this->getDumbMock(SearchResultSet::class);
        $queryGroupFacet = new QueryGroupFacet($resultSetMock, 'myFacet', 'myFacetFieldName', 'myTitle');
        $option = new Option($queryGroupFacet);

            // before adding there should not be any facet present
        $this->assertEquals(0, $queryGroupFacet->getOptions()->getCount());
        $queryGroupFacet->addOption($option);

            // now we should have 1 option present
        $this->assertEquals(1, $queryGroupFacet->getOptions()->getCount());
    }

    /**
     * @test
     */
    public function getDefaultPartialName()
    {
        $resultSetMock = $this->getDumbMock(SearchResultSet::class);
        $queryGroupFacet = new QueryGroupFacet($resultSetMock, 'myFacet', 'myFacetFieldName', 'myTitle');

        $this->assertEquals('Options', $queryGroupFacet->getPartialName());
    }

    /**
     * @test
     */
    public function getCustomPartialName()
    {
        $resultSetMock = $this->getDumbMock(SearchResultSet::class);
        $queryGroupFacet = new QueryGroupFacet($resultSetMock, 'myFacet', 'myFacetFieldName', 'myTitle', ['partialName' => 'MyPartial']);

        $this->assertEquals('MyPartial', $queryGroupFacet->getPartialName());
    }
}
