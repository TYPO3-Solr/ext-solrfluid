<?php
namespace ApacheSolrForTypo3\Solrfluid\Widget;

/*                                                                        *
 * This script is backported from the TYPO3 Flow package "TYPO3.Fluid".   *
 *                                                                        *
 * It is free software; you can redistribute it and/or modify it under    *
 * the terms of the GNU Lesser General Public License, either version 3   *
 *  of the License, or (at your option) any later version.                *
 *                                                                        *
 *                                                                        *
 * This script is distributed in the hope that it will be useful, but     *
 * WITHOUT ANY WARRANTY; without even the implied warranty of MERCHAN-    *
 * TABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU Lesser       *
 * General Public License for more details.                               *
 *                                                                        *
 * You should have received a copy of the GNU Lesser General Public       *
 * License along with the script.                                         *
 * If not, see http://www.gnu.org/licenses/lgpl.html                      *
 *                                                                        *
 * The TYPO3 project - inspiring people to share!                         *
 *                                                                        */
use TYPO3\CMS\Extbase\Object\ObjectManagerInterface;
use TYPO3\CMS\Fluid\Core\ViewHelper\AbstractViewHelper;
use TYPO3\CMS\Fluid\Core\ViewHelper\Facets\ChildNodeAccessInterface;
use TYPO3\CMS\Fluid\Core\Widget\AbstractWidgetController;
use TYPO3\CMS\Fluid\Core\Widget\AjaxWidgetContextHolder;
use TYPO3\CMS\Fluid\Core\Widget\Exception\MissingControllerException;
use TYPO3\CMS\Fluid\Core\Widget\WidgetRequest;

/**
 * This is almost a exact copy of \TYPO3\CMS\Fluid\Core\ViewHelper\AbstractViewHelper
 * because we need to switch the request object to overrule the widget prefix for url params
 * todo: find cleaner way to let widget listen to tx_solr[] instead of tx_solr[@widget]
 */
abstract class AbstractWidgetViewHelper extends AbstractViewHelper implements ChildNodeAccessInterface
{

    /**
     * The Controller associated to this widget.
     * This needs to be filled by the individual subclass by an @inject
     * annotation.
     *
     * @var AbstractWidgetController
     * @api
     */
    protected $controller;

    /**
     * If set to TRUE, it is an AJAX widget.
     *
     * @var boolean
     * @api
     */
    protected $ajaxWidget = false;

    /**
     * @var AjaxWidgetContextHolder
     */
    private $ajaxWidgetContextHolder;

    /**
     * @var ObjectManagerInterface
     */
    protected $objectManager;

    /**
     * @var \TYPO3\CMS\Extbase\Service\ExtensionService
     * @inject
     */
    protected $extensionService;

    /**
     * @var \TYPO3\CMS\Fluid\Core\Widget\WidgetContext
     */
    private $widgetContext;

    /**
     * @param AjaxWidgetContextHolder $ajaxWidgetContextHolder
     * @return void
     */
    public function injectAjaxWidgetContextHolder(AjaxWidgetContextHolder $ajaxWidgetContextHolder)
    {
        $this->ajaxWidgetContextHolder = $ajaxWidgetContextHolder;
    }

    /**
     * @param ObjectManagerInterface $objectManager
     * @return void
     */
    public function injectObjectManager(ObjectManagerInterface $objectManager)
    {
        $this->objectManager = $objectManager;
        $this->widgetContext = $this->objectManager->get('TYPO3\\CMS\\Fluid\\Core\\Widget\\WidgetContext');
    }

    /**
     * Initialize the arguments of the ViewHelper, and call the render() method of the ViewHelper.
     *
     * @return string the rendered ViewHelper.
     */
    public function initializeArgumentsAndRender()
    {
        $this->validateArguments();
        $this->initialize();
        $this->initializeWidgetContext();
        return $this->callRenderMethod();
    }

    /**
     * Initialize the Widget Context, before the Render method is called.
     *
     * @return void
     */
    private function initializeWidgetContext()
    {
        $this->widgetContext->setWidgetConfiguration($this->getWidgetConfiguration());
        $this->initializeWidgetIdentifier();
        $this->widgetContext->setControllerObjectName(get_class($this->controller));
        $extensionName = $this->controllerContext->getRequest()->getControllerExtensionName();
        $pluginName = $this->controllerContext->getRequest()->getPluginName();
        $this->widgetContext->setParentExtensionName($extensionName);
        $this->widgetContext->setParentPluginName($pluginName);
        $pluginNamespace = $this->extensionService->getPluginNamespace($extensionName, $pluginName);
        $this->widgetContext->setParentPluginNamespace($pluginNamespace);
        $this->widgetContext->setWidgetViewHelperClassName(get_class($this));
        if ($this->ajaxWidget === true) {
            $this->ajaxWidgetContextHolder->store($this->widgetContext);
        }
    }

    /**
     * Stores the syntax tree child nodes in the Widget Context, so they can be
     * rendered with <f:widget.renderChildren> lateron.
     *
     * @param array $childNodes The SyntaxTree Child nodes of this ViewHelper.
     * @return void
     */
    public function setChildNodes(array $childNodes)
    {
        $rootNode = $this->objectManager->get('TYPO3\\CMS\\Fluid\\Core\\Parser\\SyntaxTree\\RootNode');
        foreach ($childNodes as $childNode) {
            $rootNode->addChildNode($childNode);
        }
        $this->widgetContext->setViewHelperChildNodes($rootNode, $this->renderingContext);
    }

    /**
     * Generate the configuration for this widget. Override to adjust.
     *
     * @return array
     * @api
     */
    protected function getWidgetConfiguration()
    {
        return $this->arguments;
    }

    /**
     * Initiate a sub request to $this->controller. Make sure to fill $this->controller
     * via Dependency Injection.
     *
     * @return \TYPO3\CMS\Extbase\Mvc\ResponseInterface the response of this request.
     * @throws MissingControllerException
     */
    protected function initiateSubRequest()
    {
        if (!$this->controller instanceof AbstractWidgetController) {
            if (isset($this->controller)) {
                throw new MissingControllerException('initiateSubRequest() can not be called if there is no valid controller extending TYPO3\\CMS\\Fluid\\Core\\Widget\\AbstractWidgetController. Got "' . get_class($this->controller) . '" in class "' . get_class($this) . '".', 1289422564);
            }
            throw new MissingControllerException('initiateSubRequest() can not be called if there is no controller inside $this->controller. Make sure to add a corresponding injectController method to your WidgetViewHelper class "' . get_class($this) . '".', 1284401632);
        }
        $subRequest = $this->objectManager->get('ApacheSolrForTypo3\\Solrfluid\\Widget\\WidgetRequest');
        $subRequest->setWidgetContext($this->widgetContext);
        $this->passArgumentsToSubRequest($subRequest);
        $subResponse = $this->objectManager->get('TYPO3\\CMS\\Extbase\\Mvc\\Web\\Response');
        $this->controller->processRequest($subRequest, $subResponse);
        return $subResponse;
    }

    /**
     * Pass the arguments of the widget to the subrequest.
     *
     * @param WidgetRequest $subRequest
     * @return void
     */
    private function passArgumentsToSubRequest(WidgetRequest $subRequest)
    {
        $arguments = $this->controllerContext->getRequest()->getArguments();
        if (isset($arguments)) {
            if (isset($arguments['action'])) {
                $subRequest->setControllerActionName($arguments['action']);
                unset($arguments['action']);
            }
            $subRequest->setArguments($arguments);
        }
    }

    /**
     * The widget identifier is unique on the current page, and is used
     * in the URI as a namespace for the widget's arguments.
     *
     * @return string the widget identifier for this widget
     * @return void
     * @todo clean up, and make it somehow more routing compatible.
     */
    private function initializeWidgetIdentifier()
    {
        $this->widgetContext->setWidgetIdentifier('');
    }
}
