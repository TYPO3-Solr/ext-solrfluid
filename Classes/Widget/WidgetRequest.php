<?php
namespace ApacheSolrForTypo3\Solrfluid\Widget;

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

/**
 * Class WidgetRequest
 *
 * @author Frans Saris <frans@beech.it>
 * @author Timo Schmidt <timo.schmidt@dkd.de>
 * @package ApacheSolrForTypo3\Solrfluid\Widget
 */
class WidgetRequest extends \TYPO3\CMS\Fluid\Core\Widget\WidgetRequest
{

    /**
     * Returns the unique URI namespace for this widget in the format pluginNamespace[widgetIdentifier]
     *
     * @return string
     */
    public function getArgumentPrefix()
    {
        // we skip the [@widget] part
        return $this->widgetContext->getParentPluginNamespace();
    }
}
