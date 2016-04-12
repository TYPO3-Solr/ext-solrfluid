<?php
namespace ApacheSolrForTypo3\Solrfluid\Domain\Search\ResultSet;

/***************************************************************
 *  Copyright notice
 *
 *  (c) 2015-2016 Timo Schmidt <timo.schmidt@dkd.de>
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

use ApacheSolrForTypo3\Solr\Tests\Unit\UnitTest;

/**
 * Unit test case for the ObjectReconstitutionProcessor.
 *
 * @author Timo Schmidt <timo.schmidt@dkd.de>
 */
class ResultSetReconstitutionProcessorTest extends UnitTest
{
    /**
     * @test
     */
    public function canReconstituteSpellCheckingModelsFromResponse()
    {
        $fakeResponseJson = $this->getFixtureContent('fake_solr_response_with_spellCheck.json');
        $httpResponseMock = $this->getDumbMock('\Apache_Solr_HttpTransport_Response');
        $httpResponseMock->expects($this->any())->method('getBody')->will($this->returnValue($fakeResponseJson));

        $fakeResponse = new \Apache_Solr_Response($httpResponseMock);

        $searchResultSet = new SearchResultSet();
        $searchResultSet->setResponse($fakeResponse);

            // before the reconstitution of the domain object from the response we expect that no spelling suggestions
            // are present
        $this->assertFalse($searchResultSet->getHasSpellCheckingSuggestions());


        $processor = new ResultSetReconstitutionProcessor();
        $processor->process($searchResultSet);

            // after the reconstitution they should be present
        $this->assertTrue($searchResultSet->getHasSpellCheckingSuggestions());
    }
}
