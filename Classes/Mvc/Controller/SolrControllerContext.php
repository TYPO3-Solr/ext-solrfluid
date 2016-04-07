<?php
namespace ApacheSolrForTypo3\Solrfluid\Mvc\Controller;

use ApacheSolrForTypo3\Solr\System\Configuration\TypoScriptConfiguration;
use TYPO3\CMS\Extbase\Mvc\Controller\ControllerContext;

class SolrControllerContext extends ControllerContext
{

    /**
     * @var TypoScriptConfiguration
     */
    protected $typoScriptConfiguration;

    /**
     * @param TypoScriptConfiguration $typoScriptConfiguration
     */
    public function setTypoScriptConfiguration($typoScriptConfiguration)
    {
        $this->typoScriptConfiguration = $typoScriptConfiguration;
    }

    /**
     * @return TypoScriptConfiguration
     */
    public function getTypoScriptConfiguration()
    {
        return $this->typoScriptConfiguration;
    }
}
