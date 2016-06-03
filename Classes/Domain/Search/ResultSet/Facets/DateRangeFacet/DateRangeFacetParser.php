<?php
namespace ApacheSolrForTypo3\Solrfluid\Domain\Search\ResultSet\Facets\DateRangeFacet;

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

use ApacheSolrForTypo3\Solrfluid\Domain\Search\ResultSet\Facets\AbstractFacet;
use ApacheSolrForTypo3\Solrfluid\Domain\Search\ResultSet\Facets\AbstractFacetParser;
use ApacheSolrForTypo3\Solrfluid\Domain\Search\ResultSet\Facets\FacetParserInterface;
use ApacheSolrForTypo3\Solrfluid\Domain\Search\ResultSet\SearchResultSet;
use TYPO3\CMS\Core\Utility\GeneralUtility;

/**
 * Class DateRangeFacetParser
 *
 * @author Frans Saris <frans@beech.it>
 * @author Timo Schmidt <timo.schmidt@dkd.de>
 * @package ApacheSolrForTypo3\Solrfluid\Domain\Search\ResultSet\Facets
 */
class DateRangeFacetParser extends AbstractFacetParser
{
    /**
     * @param SearchResultSet $resultSet
     * @param string $facetName
     * @param array $facetConfiguration
     * @return DateRangeFacet|null
     */
    public function parse(SearchResultSet $resultSet, $facetName, array $facetConfiguration)
    {
        $fieldName = $facetConfiguration['field'];
        $label = $this->getPlainLabelOrApplyCObject($facetConfiguration);
        $activeValue = $this->getActiveFacetValuesFromRequest($resultSet, $facetName);
        $response = $resultSet->getResponse();
        $valuesFromResponse = isset($response->facet_counts->facet_ranges->{$fieldName}) ? get_object_vars($response->facet_counts->facet_ranges->{$fieldName}) : [];

        /** @var $facet DateRangeFacet */
        $facet = GeneralUtility::makeInstance(
            DateRangeFacet::class,
            $resultSet,
            $facetName,
            $fieldName,
            $label,
            $facetConfiguration
        );

        $facet->setIsAvailable(count($valuesFromResponse) > 0);
        $facet->setIsUsed(count($activeValue) > 0);

        if (is_array($valuesFromResponse)) {
            $dateRangeCounts = [];
            $allCount = 0;

            $countsFromResponse = isset($valuesFromResponse['counts']) ? get_object_vars($valuesFromResponse['counts']) : [];
            foreach ($countsFromResponse as $date => $count) {
                $date = \DateTime::createFromFormat(\DateTime::ISO8601, $date);
                $dateRangeCount = new DateRangeCount($date, $count);
                $dateRangeCounts[] = $dateRangeCount;
                $allCount += $count;
            }

            $fromInResponse = \DateTime::createFromFormat(\DateTime::ISO8601, $valuesFromResponse['start']);
            $toInResponse = \DateTime::createFromFormat(\DateTime::ISO8601, $valuesFromResponse['end']);

            $rawValues = explode('-', $activeValue[0]);
            $rawFrom = $rawValues[0];
            $rawTo = $rawValues[1];

            $fromDate = $this->parseFacetArgumentToDateTimeOrNull($rawFrom);
            $toDate = $this->parseFacetArgumentToDateTimeOrNull($rawTo);

            $gap = $valuesFromResponse['gap'];
            $range = new DateRange($facet, $fromDate, $toDate, $fromInResponse, $toInResponse, $gap, $allCount, $dateRangeCounts, true);
            $facet->setDateRange($range);
        }

        return $facet;
    }

    protected function parseFacetArgumentToDateTimeOrNull($rawDate)
    {
        $date = \DateTime::createFromFormat('Ymd', substr($rawDate, 0, 8));

        if ($date === false) {
            return null;
        }
        $date->setTime(0, 0, 0);
        return $date;
    }
}
