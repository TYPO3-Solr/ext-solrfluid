<?php
namespace ApacheSolrForTypo3\Solrfluid\Domain\Search\ResultSet\Grouped;

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
use ApacheSolrForTypo3\Solrfluid\Domain\Search\ResultSet\Grouped\GroupedResultParser\GroupedByFieldParser;
use ApacheSolrForTypo3\Solrfluid\Domain\Search\ResultSet\Grouped\GroupedResultParser\GroupedByQueryParser;
use TYPO3\CMS\Core\SingletonInterface;
use TYPO3\CMS\Core\Utility\GeneralUtility;

/**
 * Class GroupedResultParserRegistry
 *
 * @author Frans Saris <frans@beech.it>
 * @author Timo Hund <timo.hund@dkd.de>
 * @package ApacheSolrForTypo3\Solrfluid\Domain\Search\ResultSet\Grouped
 */
class GroupedResultParserRegistry implements SingletonInterface
{
    /**
     * Array of available parser classNames
     *
     * @var array
     */
    protected $parsers = [
        100 => GroupedByFieldParser::class,
        200 => GroupedByQueryParser::class,
    ];

    /**
     * @var GroupedResultParserInterface[]
     */
    protected $parserInstances;

    /**
     * Get registered parser classNames
     *
     * @return array
     */
    public function getParsers()
    {
        return $this->parsers;
    }

    /**
     * @param string $className
     * @param int $priority
     * @throws \InvalidArgumentException
     */
    public function registerParser($className, $priority)
    {
        // check if the class is available for TYPO3 before registering the driver
        if (!class_exists($className)) {
            throw new \InvalidArgumentException('Class ' . $className . ' does not exist.', 1468863997);
        }

        if (!in_array(FacetParserInterface::class, class_implements($className), true)) {
            throw new \InvalidArgumentException('Parser ' . $className . ' needs to implement the GroupedResultParserInterface.', 1468863998);
        }

        if (array_key_exists((int)$priority, $this->parsers)) {
            throw new \InvalidArgumentException('There is already a parser registerd with priority ' . (int)$priority . '.', 1468863999);
        }

        $this->parsers[(int)$priority] = $className;
    }

    /**
     * @param SearchResultSet $searchResultSet
     * @return GroupedResultParserInterface[]
     */
    public function getParserInstances(SearchResultSet $searchResultSet)
    {
        if ($this->parserInstances === null) {
            ksort($this->parsers);
            foreach ($this->parsers as $className) {
                $this->parserInstances[] = $this->createParserInstance($searchResultSet, $className);
            }
        }
        return $this->parserInstances;
    }

    /**
     * Get parser
     *
     * @param SearchResultSet $searchResultSet
     * @param string $groupedResultName
     * @param array $groupedResultConfiguration
     * @return GroupedResultParserInterface|null
     */
    public function getParser(SearchResultSet $searchResultSet, $groupedResultName, array $groupedResultConfiguration)
    {
        /** @var GroupedResultParserInterface $parser */
        foreach ($this->getParserInstances($searchResultSet) as $parser) {
            if ($parser->canParse($groupedResultName, $groupedResultConfiguration)) {
                return $parser;
            }
        }
        return null;
    }

    /**
     * Create an instance of a certain parser class
     *
     * @param SearchResultSet $searchResultSet
     * @param string $className
     * @return FacetParserInterface
     */
    protected function createParserInstance(SearchResultSet $searchResultSet, $className)
    {
        return GeneralUtility::makeInstance($className, $searchResultSet);
    }
}
