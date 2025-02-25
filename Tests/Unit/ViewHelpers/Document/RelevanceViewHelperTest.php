<?php
namespace ApacheSolrForTypo3\Solrfluid\Test\ViewHelpers\Facet\Uri;

/***************************************************************
 *  Copyright notice
 *
 *  (c) 2015-2016 Timo Hund <timo.hund@dkd.de>
 *  All rights reserved
 *
 *  This script is part of the TYPO3 project. The TYPO3 project is
 *  free software; you can redistribute it and/or modify
 *  it under the terms of the GNU General Public License as published by
 *  the Free Software Foundation; either version 2 of the License, or
 *  (at your option) any later version.
 *
 *  The GNU General Public License can be found at
 *  http://www.gnu.org/copyleft/gpl.html.
 *
 *  This script is distributed in the hope that it will be useful,
 *  but WITHOUT ANY WARRANTY; without even the implied warranty of
 *  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *  GNU General Public License for more details.
 *
 *  This copyright notice MUST APPEAR in all copies of the script!
 ***************************************************************/

use ApacheSolrForTypo3\Solr\Search;
use ApacheSolrForTypo3\Solr\Tests\Unit\UnitTest;
use ApacheSolrForTypo3\Solrfluid\Domain\Search\ResultSet\SearchResult;
use ApacheSolrForTypo3\Solrfluid\Domain\Search\ResultSet\SearchResultSet;
use ApacheSolrForTypo3\Solrfluid\ViewHelpers\Document\RelevanceViewHelper;
use TYPO3\CMS\Fluid\Core\Rendering\RenderingContextInterface;

/**
 * @author Timo Hund <timo.hund@dkd.de>
 */
class RelevanceViewHelperTest extends UnitTest
{

    /**
     * @test
     */
    public function canCalculateRelevance()
    {
        $searchMock = $this->getDumbMock(Search::class);
        $searchMock->expects($this->once())->method('getMaximumResultScore')->will($this->returnValue(5.5));
        $resultSetMock = $this->getDumbMock(SearchResultSet::class);
        $resultSetMock->expects($this->any())->method('getUsedSearch')->will($this->returnValue($searchMock));
        $documentMock = $this->getDumbMock(SearchResult::class);
        $documentMock->expects($this->once())->method('getScore')->will($this->returnValue(0.55));
        $arguments = [
            'resultSet' => $resultSetMock,
            'document' => $documentMock
        ];
        $renderingContextMock = $this->getDumbMock(RenderingContextInterface::class);
        $score = RelevanceViewHelper::renderStatic($arguments, function () {}, $renderingContextMock);
        $this->assertEquals(10.0, $score, 'Unexpected score');
    }
}
