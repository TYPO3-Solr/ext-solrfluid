<?php
$EM_CONF[$_EXTKEY] = [
    'title' => 'Apache Solr for TYPO3 - Fluid Frontend Rendering',
    'description' => 'This addon provides the fluid templating for EXT:solr',
    'version' => '2.1.1',
    'state' => 'stable',
    'category' => 'plugin',
    'author' => 'Timo Hund, Markus Friedrich, Frans Saris and Daniel Siepmann',
    'author_email' => 'timo.hund@dkd.de',
    'author_company' => 'dkd Internet Service GmbH',
    'module' => '',
    'uploadfolder' => 0,
    'createDirs' => '',
    'modify_tables' => '',
    'clearCacheOnLoad' => 0,
    'constraints' => [
        'depends' => [
            'scheduler' => '',
            'solr' => '6.1.0-6.9.99',
            'extbase' => '7.6.0-8.9.99',
            'fluid' => '7.6.0-8.9.99',
            'typo3' => '7.6.0-8.9.99'
        ],
        'conflicts' => [],
        'suggests' => [
            'devlog' => '',
        ],
    ],
    'autoload' => [
        'psr-4' => [
            'ApacheSolrForTypo3\\Solrfluid\\' => 'Classes/',
            'ApacheSolrForTypo3\\Solrfluid\\Tests\\' => 'Tests/'
        ]
    ]
];
