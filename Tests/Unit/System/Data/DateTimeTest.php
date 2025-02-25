<?php
namespace ApacheSolrForTypo3\Solrfluid\Test\System\Data;

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

use ApacheSolrForTypo3\Solr\Tests\Unit\UnitTest;
use ApacheSolrForTypo3\Solrfluid\System\Data\DateTime;

/**
 * @author Timo Hund <timo.hund@dkd.de>
 */
class DateTimeTest extends UnitTest
{
    /**
     * @test
     */
    public function testCanWrapDateTimeAndConvertToString()
    {
        $proxy = new DateTime('2003-12-13T18:30:02Z', new \DateTimeZone("UTC"));
        $this->assertSame('2003-12-13T18:30:02+0000', (string) $proxy);
    }

    /**
     * @test
     */
    public function testCanDispatchCallToUnderlyingDateTime()
    {
        $proxy = new DateTime('2003-12-13T18:30:02Z', new \DateTimeZone("UTC"));
        $this->assertSame('2003-12-13T18:30:02+0000', $proxy->format(\DateTime::ISO8601));
    }
}
