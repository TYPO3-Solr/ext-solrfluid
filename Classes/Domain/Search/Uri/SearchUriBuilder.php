<?php
namespace ApacheSolrForTypo3\Solrfluid\Domain\Search\Uri;

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

use ApacheSolrForTypo3\Solr\Domain\Search\ResultSet\SearchResultSetService;
use ApacheSolrForTypo3\Solr\Domain\Search\SearchRequest;
use ApacheSolrForTypo3\Solr\System\Configuration\TypoScriptConfiguration;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Mvc\Web\Routing\UriBuilder;

/**
 * SearchUriBuilder
 *
 * Responsibility:
 *
 * The SearchUriBuilder is responsible to build uris, that are used in the
 * searchContext. It can use the previous request with it's persistent
 * arguments to build the url for a search sub request.
 *
 * @author Frans Saris <frans@beech.it>
 * @author Timo Schmidt <timo.schmidt@dkd.de>
 * @package ApacheSolrForTypo3\Solrfluid\Domain\Search\Uri
 */

class SearchUriBuilder
{

    /**
     * @var UriBuilder
     */
    protected $uriBuilder;

    protected static $preCompiledLinks = array();

    protected static $hitCount;

    protected static $missCount;

    /**
     * @param UriBuilder $uriBuilder
     */
    public function injectUriBuilder(UriBuilder $uriBuilder)
    {
        $this->uriBuilder = $uriBuilder;
    }

    /**
     * @param SearchRequest $previousSearchRequest
     * @param $facetName
     * @param $optionValue
     * @return string
     */
    public function getAddFacetOptionUri(SearchRequest $previousSearchRequest, $facetName, $optionValue)
    {
        $persistentAndFacetArguments = $previousSearchRequest
            ->getCopyForSubRequest()->addFacetValue($facetName, $optionValue)
            ->getAsArray();


        return $this->buildLinkWithInMemoryCache($persistentAndFacetArguments);
    }

    /**
     * @param SearchRequest $previousSearchRequest
     * @param $facetName
     * @param $optionValue
     * @return string
     */
    public function getRemoveFacetOptionUri(SearchRequest $previousSearchRequest, $facetName, $optionValue)
    {
        $persistentAndFacetArguments = $previousSearchRequest
            ->getCopyForSubRequest()->removeFacetValue($facetName, $optionValue)
            ->getAsArray();


        return $this->buildLinkWithInMemoryCache($persistentAndFacetArguments);
    }

    /**
     * @param SearchRequest $previousSearchRequest
     * @param $page
     * @return string
     */
    public function getResultPageUri(SearchRequest $previousSearchRequest, $page)
    {
        $persistentAndFacetArguments = $previousSearchRequest
            ->getCopyForSubRequest()->setPage($page)
            ->getAsArray();


        return $this->buildLinkWithInMemoryCache($persistentAndFacetArguments);
    }

    /**
     * @param SearchRequest $previousSearchRequest
     * @param $queryString
     * @return string
     */
    public function getNewSearchUri(SearchRequest $previousSearchRequest, $queryString)
    {
            /** @var $request SearchRequest */
        $contextConfiguration = $previousSearchRequest->getContextTypoScriptConfiguration();
        $contextSystemLanguage = $previousSearchRequest->getContextSystemLanguageUid();
        $contextPageUid = $previousSearchRequest->getContextPageUid();

        $request = GeneralUtility::makeInstance(SearchRequest::class, array(), $contextPageUid, $contextSystemLanguage, $contextConfiguration);
        $arguments = $request->setRawQueryString($queryString)->getAsArray();

        return $this->buildLinkWithInMemoryCache($arguments);
    }


    /**
     * @param array $arguments
     * @return string
     */
    protected function buildLinkWithInMemoryCache(array $arguments)
    {
        $values = array();
        $structure = $arguments;
        $this->getSubstitution($structure, $values);
        $hash = md5(json_encode($structure));

        if (isset(self::$preCompiledLinks[$hash])) {
            self::$hitCount++;
            $template = self::$preCompiledLinks[$hash];
        } else {
            self::$missCount++;
            $template = $this->uriBuilder->setArguments($structure)->setUseCacheHash(false)->build();
            self::$preCompiledLinks[$hash] = $template;
        }

        $keys = array_map(function ($value) { return urlencode($value); }, array_keys($values));
        $values = array_map(function ($value) { return urlencode($value); }, $values);
        $uri = str_replace($keys, $values, $template);
        return $uri;
    }

    /**
     * This method is used to build two arrays from a nested array. The first one represents the structure.
     * In this structure the values are replaced with the pass to the value. At the same time the values get collected
     * in the $values array, with the path as key. This can be used to build a comparable hash from the arguments
     * in order to reduce the amount of typolink calls
     *
     *
     * Example input
     *
     * $data = [
     *  'foo' => [
     *      'bar' => 111
     *   ]
     * ]
     *
     * will return:
     *
     * $structure = [
     *  'foo' => [
     *      'bar' => '###foo:bar###'
     *   ]
     * ]
     *
     * $values = [
     *  '###foo:bar###' => 111
     * ]
     *
     * @param $structure
     * @param $values
     * @param array $branch
     */
    protected function getSubstitution(array &$structure, array  &$values, array $branch = array())
    {
        foreach ($structure as $key => &$value) {
            $branch[] = $key;
            if (is_array($value)) {
                $this->getSubstitution($value, $values, $branch);
            } else {
                $path = '###' . implode(':', $branch) . '###';
                $values[$path] = $value;
                $structure[$key] = $path;
            }
        }
    }
}
