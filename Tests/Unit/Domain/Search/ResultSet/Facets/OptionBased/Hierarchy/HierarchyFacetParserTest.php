<?php
namespace ApacheSolrForTypo3\Solrfluid\Test\Domain\Search\ResultSet\Facets\OptionBased\Hierarchy;

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
use ApacheSolrForTypo3\Solrfluid\Domain\Search\ResultSet\Facets\OptionBased\Hierarchy\HierarchyFacet;
use ApacheSolrForTypo3\Solrfluid\Domain\Search\ResultSet\Facets\OptionBased\Hierarchy\HierarchyFacetParser;
use ApacheSolrForTypo3\Solrfluid\Test\Domain\Search\ResultSet\Facets\AbstractFacetParserTest;
use ApacheSolrForTypo3\Solrfluid\Tests\Unit\Helper\FakeObjectManager;

/**
 * Class HierarchyFacetParserTest
 *
 * @author Timo Hund <timo.hund@dkd.de>
 * @author Frans Saris <frans@beech.it>
 */
class HierarchyFacetParserTest extends AbstractFacetParserTest
{

    /**
     * @test
     */
    public function facetIsCreated()
    {
        $facetConfiguration = [
            'pageHierarchy.' => [
                'type' => 'hierarchy',
                'label' => 'Rootline',
                'field' => 'rootline',
            ]
        ];

        $searchResultSet = $this->initializeSearchResultSetFromFakeResponse(
            'fake_solr_response_with_hierarchy_facet.json',
            $facetConfiguration
        );

        /** @var $parser HierarchyFacetParser */
        $parser = $this->getInitializedParser(HierarchyFacetParser::class);
        $facet = $parser->parse($searchResultSet, 'pageHierarchy', $facetConfiguration['pageHierarchy.']);
        $this->assertInstanceOf(HierarchyFacet::class, $facet);

            // on the rootlevel there should only be one childNode
        $this->assertSame(1, $facet->getChildNodes()->getCount());
        $this->assertSame(8, $facet->getChildNodes()->getByPosition(0)->getChildNodes()->getCount());

        $this->assertSame('/1/14/', $facet->getChildNodes()->getByPosition(0)->getChildNodes()->getByPosition(0)->getValue());
        $this->assertSame('14', $facet->getChildNodes()->getByPosition(0)->getChildNodes()->getByPosition(0)->getKey());
    }

    /**
     * @test
     */
    public function facetIsNotActive()
    {
        $facetConfiguration = [
            'pageHierarchy.' => [
                'type' => 'hierarchy',
                'label' => 'Rootline',
                'field' => 'rootline',
            ]
        ];

        $searchResultSet = $this->initializeSearchResultSetFromFakeResponse(
            'fake_solr_response_with_hierarchy_facet.json',
            $facetConfiguration
        );

            /** @var $parser HierarchyFacetParser */
        $parser = $this->getInitializedParser(HierarchyFacetParser::class);
        $facet = $parser->parse($searchResultSet, 'pageHierarchy', $facetConfiguration['pageHierarchy.']);
        $this->assertFalse($facet->getIsUsed());
    }
}
