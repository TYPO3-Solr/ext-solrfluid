<?php
namespace ApacheSolrForTypo3\Solrfluid\ViewHelpers\Uri\Facet;

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

use ApacheSolrForTypo3\Solr\Domain\Search\ResultSet\SearchResultSet;
use ApacheSolrForTypo3\Solrfluid\Domain\Search\ResultSet\Facets\AbstractFacet;
use ApacheSolrForTypo3\Solrfluid\Domain\Search\ResultSet\Facets\OptionsFacet\Option;
use ApacheSolrForTypo3\Solrfluid\Domain\Search\ResultSet\Facets\OptionsFacet\OptionsFacet;
use ApacheSolrForTypo3\Solrfluid\Domain\Search\Uri\SearchUriBuilder;
use ApacheSolrForTypo3\Solrfluid\Mvc\Controller\SolrControllerContext;
use ApacheSolrForTypo3\Solrfluid\ViewHelpers\AbstractTagBasedViewHelper;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Mvc\Web\Routing\UriBuilder;
use TYPO3\CMS\Extbase\Object\ObjectManager;
use TYPO3\CMS\Fluid\Core\Rendering\RenderingContextInterface;
use TYPO3\CMS\Fluid\Core\ViewHelper\Facets\CompilableInterface;
use ApacheSolrForTypo3\Solrfluid\ViewHelpers\AbstractViewHelper;

/**
 * Class FacetAddOptionViewHelper
 *
 * @author Frans Saris <frans@beech.it>
 * @author Timo Schmidt <timo.schmidt@dkd.de>
 * @package ApacheSolrForTypo3\Solrfluid\ViewHelpers\Link
 */
class AbstractOptionViewHelper extends AbstractViewHelper
{
    /**
     * @var SearchUriBuilder
     */
    protected static $searchUriBuilder;

    /**
     * @param SearchUriBuilder $searchUriBuilder
     */
    public function injectSearchUriBuilder(SearchUriBuilder $searchUriBuilder)
    {
        self::$searchUriBuilder = $searchUriBuilder;
    }

    /**
     * @return SearchUriBuilder|object
     */
    protected static function getSearchUriBuilder()
    {
        if(!isset(self::$searchUriBuilder)) {
            $objectManager = GeneralUtility::makeInstance(ObjectManager::class);
            self::$searchUriBuilder = $objectManager->get(SearchUriBuilder::class);
        }

        return self::$searchUriBuilder;
    }

    /**
     * @param $arguments
     * @return string
     * @throws \InvalidArgumentException
     */
    protected function getOptionValueFromArguments($arguments)
    {
        if (isset($arguments['option'])) {
            /** @var  $option Option */
            $option = $arguments['option'];
            $optionValue = $option->getValue();
        } elseif (isset($arguments['optionValue'])) {
            $optionValue = $arguments['optionValue'];
        } else {
            throw new \InvalidArgumentException('No option was passed, please pass either option or optionValue');
        }

        return $optionValue;
    }

}
