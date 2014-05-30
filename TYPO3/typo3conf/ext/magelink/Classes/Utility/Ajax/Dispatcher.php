<?php
namespace MageDeveloper\Magelink\Utility\Ajax;

	/***************************************************************
	 *  Copyright notice
	 *
	 *  (c) 2013 Bastian Zagar <zagar@aixdesign.net>, aixdesign.net
	 *
	 *  All rights reserved
	 *
	 *  This script is part of the TYPO3 project. The TYPO3 project is
	 *  free software; you can redistribute it and/or modify
	 *  it under the terms of the GNU General Public License as published by
	 *  the Free Software Foundation; either version 3 of the License, or
	 *  (at your option) any later version.
	 *
	 *  The GNU General Public License can be found at
	 *  http://www.gnu.org/copyleft/gpl.html.
	 *
	 *  This script is distributed in the hope that it will be useful,
	 *  but WITHOUT ANY WARRANTY; without even the implied warranty of
	 *  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
	 *  GNU General Public License for more details.
	 *
	 *  This copyright notice MUST APPEAR in all copies of the script!
	 ***************************************************************/

/**
 *
 * AJAX DISPATCHER
 * @package magelink
 * @license http://www.gnu.org/licenses/gpl.html GNU General Public License, version 3 or later
 *
 */

class Dispatcher
{
	/**
	 * Array of all request Arguments
	 *
	 * @var \array
	 */
	protected $requestArguments = array();

	/**
	 * Extension Name
	 * 
	 * @var \string
	 */
	protected $extensionName;

	/**
	 * The plugin name
	 * 
	 * @var \string
	 */
	protected $pluginName;

	/**
	 * Controller Name
	 * 
	 * @var \string
	 */
	protected $controllerName;

	/**
	 * Action Name
	 * 
	 * @var \string
	 */
	protected $actionName;
	
	/**
	 * Vendor Name
	 *
	 * @var \string
	 */
	protected $vendorName;


	/**
	 * Arguments
	 * 
	 * @var \array
	 */
	protected $arguments = array();

	/**
	 * Page UID
	 * 
	 * @var \int
	 */
	protected $pageUid;

	/**
	 * Object Manager
	 * @var \TYPO3\CMS\Extbase\Object\ObjectManager
	 * @inject
	 */
	protected $objectManager;
	
	/**
	 * settingsService
	 *
	 * @var \MageDeveloper\Magelink\Service\SettingsService
	 * @inject
	 */
	protected $settingsService;


	/**
	 * Constructor
	 */
	public function __construct()
	{
		$this->objectManager 	= \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance("TYPO3\\CMS\\Extbase\\Object\\ObjectManager");
	}
	
	/**
	 * Initialized and dispatches actions
	 * 
	 * Call this function if you want to use this dispatcher "standalone"
	 */
	public function initAndDispatch()
	{
		$this->initCallArguments()->dispatch();
	}
	
	/**
	 * Called by ajax
	 * Builds extbase context and returns the response
	 * 
	 * @return \string
	 */
	public function dispatch()
	{
		$configuration = array();
		
		$configuration["extensionName"] = $this->extensionName;
		$configuration["pluginName"]	= $this->pluginName;
		
		$bootstrap = $this->objectManager->get("TYPO3\\CMS\\Extbase\\Core\\Bootstrap");
		$bootstrap->initialize($configuration);
		$bootstrap->cObj = $this->objectManager->get("TYPO3\\CMS\\Frontend\\ContentObject\\ContentObjectRenderer");

		$request 	= $this->buildRequest();
		$response 	= $this->objectManager->get("TYPO3\\CMS\\Extbase\\Mvc\\Web\\Response");
		
		$dispatcher = $this->objectManager->get("TYPO3\\CMS\\Extbase\\Mvc\\Dispatcher");
		$dispatcher->dispatch($request, $response);
		
		return $response->getContent();
	}

	/**
	 * Prepare all call arguments that are valid
	 * 
	 * @return $this
	 */
	public function initCallArguments()
	{
		$request = \TYPO3\CMS\Core\Utility\GeneralUtility::_POST();
		$this->setRequestedArguments($request);
		
		$this->extensionName 	= $this->requestArguments["extensionName"];
		$this->pluginName		= $this->requestArguments["pluginName"];
		$this->controllerName	= $this->requestArguments["controllerName"];
		$this->actionName		= $this->requestArguments["actionName"];
		$this->vendorName 		= $this->requestArguments["vendorName"];
		
		$this->arguments				= $this->requestArguments["arguments"];
		
		if (!is_array($this->arguments))
		{
			$this->arguments = array();
		}
		
		return $this;
	}

	/**
	 * Sets the requested arguments
	 * 
	 * @param \array $request Request
	 * @return void
	 */
	public function setRequestedArguments($request)
	{
		$validArguments = array(
			"extensionName",
			"pluginName",
			"controllerName",
			"actionName",
			"vendorName",
			"arguments",
		);
		
		foreach ($validArguments as $argument)
		{
			if ($request[$argument])
			{
				$this->requestArguments[$argument] = $request[$argument];
			}
			
		}

	}

	/**
	 * Builds a request instance
	 * 
	 * @return \TYPO3\CMS\Extbase\Mvc\Web\Request
	 */
	public function buildRequest()
	{
		$request = $this->objectManager->get("TYPO3\\CMS\\Extbase\\Mvc\\Web\\Request");
	
		$request->setPluginName($this->pluginName);
		$request->setControllerExtensionName($this->extensionName);
		$request->setControllerName($this->controllerName);
		$request->setControllerActionName($this->actionName);
		$request->setControllerVendorName($this->vendorName);
		$request->setArguments($this->arguments);
	
		return $request;
	}
	
	
}