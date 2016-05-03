<?php
namespace ApacheSolrForTypo3\Solrfluid\ViewHelpers\Link;

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
use ApacheSolrForTypo3\Solrfluid\Mvc\Controller\SolrControllerContext;
use ApacheSolrForTypo3\Solrfluid\ViewHelpers\AbstractTagBasedViewHelper;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Mvc\Web\Routing\UriBuilder;
use TYPO3\CMS\Fluid\Core\Rendering\RenderingContextInterface;
use TYPO3\CMS\Fluid\Core\ViewHelper\Facets\CompilableInterface;

/**
 * Class FacetAddOptionViewHelper
 *
 * @author Frans Saris <frans@beech.it>
 * @author Timo Schmidt <timo.schmidt@dkd.de>
 * @package ApacheSolrForTypo3\Solrfluid\ViewHelpers\Link
 */
class FacetAddOptionViewHelper extends AbstractTagBasedViewHelper
{
    /**
     * @var string
     */
    protected $tagName = 'a';

    /**
     * Arguments initialization
     *
     * @return void
     */
    public function initializeArguments()
    {
        $this->registerUniversalTagAttributes();
        $this->registerTagAttribute('name', 'string', 'Specifies the name of an anchor');
        $this->registerTagAttribute('rel', 'string', 'Specifies the relationship between the current document and the linked document');
        $this->registerTagAttribute('rev', 'string', 'Specifies the relationship between the linked document and the current document');
        $this->registerTagAttribute('target', 'string', 'Specifies where to open the linked document');

        $this->registerTagAttribute('pageUid', 'int', 'Specifies alternative target page uid');
        $this->registerTagAttribute('returnUrl', 'bool', 'Specifies if only the url should be returned', false, false);

        $this->registerTagAttribute('facet', OptionsFacet::class, 'Specifies the facet', true);
        $this->registerTagAttribute('option', Option::class, 'Specifies the facet option');
        $this->registerTagAttribute('optionValue', 'string', 'Specifies the plain facet option value');

    }

    /**
     * Render
     *
     * @return string
     */
    public function render()
    {

        /** @var  $facet OptionsFacet */
        $facet = $this->arguments['facet'];
        if ($this->hasArgument('option')) {
            /** @var  $option Option */
            $option = $this->arguments['option'];
            $optionValue = $option->getValue();
        } elseif($this->hasArgument('optionValue')) {
            $optionValue = $this->arguments['optionValue'];
        } else {
            //todo
            throw new \InvalidArgumentException('...');
        }

        $arguments = $facet->getResultSet()
                        ->getUsedSearchRequest()
                        ->getCopyForSubRequest()->addFacetValue($facet->getName(), $optionValue)->getAsArray();

        /** @var $uriBuilder UriBuilder */
        $uriBuilder = $this->objectManager->get(UriBuilder::class);
        $uriBuilder->setArguments($arguments)->setUseCacheHash(false);

        $uri = $uriBuilder->build();

        if (!$this->arguments['returnUrl']) {
            $this->tag->addAttribute('href', $uri, false);
            $this->tag->setContent($this->renderChildren());
            $this->tag->forceClosingTag(true);
            return $this->tag->render();
        } else {
            return $uri;
        }
    }
}
