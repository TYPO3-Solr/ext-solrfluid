<?php
$EM_CONF[$_EXTKEY] = array(
    'title' => 'Apache Solr for TYPO3 - Fluid Frontend Rendering',
    'description' => 'This addon provides the fluid templating for EXT:solr',
    'version' => '1.0.0-dev',
    'state' => 'stable',
    'category' => 'plugin',
    'author' => 'Timo Schmidt, Markus Friedrich, Frans Saris and Daniel Siepmann',
    'author_email' => 'timo.schmidt@dkd.de',
    'author_company' => 'dkd Internet Service GmbH',
    'module' => '',
    'uploadfolder' => 0,
    'createDirs' => '',
    'modify_tables' => '',
    'clearCacheOnLoad' => 0,
    'constraints' => array(
        'depends' => array(
            'scheduler' => '',
            'solr' => '5.0.0-dev',
            'extbase' => '7.6.0-8.0.99',
            'fluid' => '7.6.0-8.0.99',
            'typo3' => '7.6.0-8.0.99'
        ),
        'conflicts' => array(),
        'suggests' => array(
            'devlog' => '',
        ),
    ),
    'autoload' => array(
        'psr-4' => array(
            'ApacheSolrForTypo3\\Solrfluid\\' => 'Classes/',
            'ApacheSolrForTypo3\\Solrfluid\\Tests\\' => 'Tests/'
        )
    )
);
