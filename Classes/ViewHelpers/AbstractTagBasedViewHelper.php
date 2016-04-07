<?php
namespace ApacheSolrForTypo3\Solrfluid\ViewHelpers;

use ApacheSolrForTypo3\Solr\System\Configuration\TypoScriptConfiguration;
use TYPO3\CMS\Fluid\Core\ViewHelper\AbstractTagBasedViewHelper as AbstractTagBasedCoreViewHelper;

class AbstractTagBasedViewHelper extends AbstractTagBasedCoreViewHelper
{

    /**
     * @return TypoScriptConfiguration
     */
    protected function getTypoScriptConfiguration()
    {
        return $this->controllerContext->getTypoScriptConfiguration();
    }
}
