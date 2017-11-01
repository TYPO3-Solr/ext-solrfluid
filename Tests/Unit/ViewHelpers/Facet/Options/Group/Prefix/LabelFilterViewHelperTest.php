<?php
namespace ApacheSolrForTypo3\Solrfluid\Test\ViewHelpers\Facet\Options\Group\Prefix;

/***************************************************************
 *  Copyright notice
 *
 *  (c) 2017 Timo Hund <timo.hund@dkd.de>
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

use ApacheSolrForTypo3\Solrfluid\Domain\Search\ResultSet\Facets\OptionBased\OptionCollection;
use ApacheSolrForTypo3\Solrfluid\Domain\Search\ResultSet\Facets\OptionBased\Options\Option;
use ApacheSolrForTypo3\Solr\Tests\Unit\UnitTest;
use ApacheSolrForTypo3\Solrfluid\ViewHelpers\Facet\Options\Group\Prefix\LabelFilterViewHelper;
use ApacheSolrForTypo3\Solrfluid\Domain\Search\ResultSet\Facets\OptionBased\Options\OptionsFacet;
use TYPO3\CMS\Fluid\Core\Rendering\RenderingContextInterface;
use TYPO3\CMS\Fluid\Core\ViewHelper\TemplateVariableContainer;

/**
 * @author Timo Hund <timo.hund@dkd.de>
 */
class LabelFilterViewHelperTest extends UnitTest
{
    /**
     * @test
     */
    public function canMakeOnlyExpectedFacetsAvailableInStaticContext()
    {
        $facet = $this->getDumbMock(OptionsFacet::class);

        $roseRed = new Option($facet, 'Rose Red', 'rose_red', 14);
        $blue = new Option($facet, 'Polar Blue', 'polar_blue', 12);
        $yellow = new Option($facet, 'Lemon Yellow', 'lemon_yellow', 3);
        $red = new Option($facet, 'Rubin Red', 'rubin_red', 9);
        $royalGreen = new Option($facet, 'Royal Green', 'royal_green', 14);

        $optionCollection = new OptionCollection();
        $optionCollection->add($roseRed);
        $optionCollection->add($blue);
        $optionCollection->add($yellow);
        $optionCollection->add($red);
        $optionCollection->add($royalGreen);

        $variableContainer = $this->getMockBuilder(TemplateVariableContainer::class)->setMethods(['remove'])->getMock();
        $renderingContextMock = $this->getDumbMock(RenderingContextInterface::class);
        $renderingContextMock->expects($this->any())->method('getTemplateVariableContainer')->will($this->returnValue($variableContainer));

        $testArguments['options'] = $optionCollection;
        $testArguments['prefix'] = 'p';

        LabelFilterViewHelper::renderStatic($testArguments, function () {}, $renderingContextMock);
        $this->assertTrue($variableContainer->exists('filteredOptions'), 'Expected that filteredOptions has been set');

        /** @var  $optionCollection OptionCollection */
        $optionCollection = $variableContainer->get('filteredOptions');
        $this->assertSame(1, $optionCollection->getCount());
        $this->assertSame('Polar Blue', $optionCollection->getByPosition(0)->getLabel(), 'Filtered option has unexpected label');
    }
}
