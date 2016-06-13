<?php

$extBaseBootstrap = new \TYPO3\CMS\Extbase\Core\Bootstrap();

$bootstrapClass = ApacheSolrForTypo3\Solrfluid\System\Eid\Bootstrap::class;
    /** @var  $eid ApacheSolrForTypo3\Solrfluid\System\Eid\Bootstrap */
$eid = TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance($bootstrapClass, $TYPO3_CONF_VARS, $extBaseBootstrap);

$callback = TYPO3\CMS\Core\Utility\GeneralUtility::removeXSS(TYPO3\CMS\Core\Utility\GeneralUtility::_GET('callback'));
$pid = (int) (TYPO3\CMS\Core\Utility\GeneralUtility::_GET('id') ? TYPO3\CMS\Core\Utility\GeneralUtility::_GET('id') : 1);

$response = new \stdClass();
$response->html = $eid->run($pid);

echo $callback . '(' . json_encode($response) . ')';

