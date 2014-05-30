<?php
namespace MageDeveloper\Magelink\Controller;

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
 *
 * @package magelink
 * @license http://www.gnu.org/licenses/gpl.html GNU General Public License, version 3 or later
 *
 */
class AjaxController extends \MageDeveloper\Magelink\Controller\AbstractController
{
	/**
	 * flexFormService
	 *
	 * @var \MageDeveloper\Magelink\Service\FlexFormService
	 * @inject
	 */
	protected $flexFormService;

	/**
	 * settingsService
	 *
	 * @var \MageDeveloper\Magelink\Service\SettingsService
	 * @inject
	 */
	protected $settingsService;

	/**
	 * Api Product Request
	 *
	 * @var \MageDeveloper\Magelink\Api\Requests\Product
	 * @inject
	 */
	protected $product_request;

	/**
	 * Api Category Request
	 *
	 * @var \MageDeveloper\Magelink\Api\Requests\Category
	 * @inject
	 */
	protected $category_request;

	/**
	 * authenticationService
	 * @var \MageDeveloper\Magelink\Service\AuthenticationService
	 * @inject
	 */
	protected $authenticationService;


	/**
	 * Object Manager
	 * @var \TYPO3\CMS\Extbase\Object\ObjectManager
	 * @inject
	 */
	protected $objectManager;


	public function __construct()
	{
		$this->objectManager 	= \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance("TYPO3\\CMS\\Extbase\\Object\\ObjectManager");

		// Inject Product Request
		$this->product_request 	= $this->objectManager->get("MageDeveloper\\Magelink\\Api\\Requests\\Product");

		// Inject Category Request
		$this->category_request = $this->objectManager->get("MageDeveloper\\Magelink\\Api\\Requests\\Category");

		// Inject Settings Service
		$this->settingsService	= $this->objectManager->get("MageDeveloper\\Magelink\\Service\\SettingsService");

		$this->flexFormService  = $this->objectManager->get("MageDeveloper\\Magelink\\Service\\FlexFormService");

		// Authentication Service
		$this->authenticationService = $this->objectManager->get("MageDeveloper\\Magelink\\Service\\AuthenticationService");

		parent::__construct();
	}

	/**
	 * Adds an product to the cart with
	 * an ajax call
	 * 
	 * @return \string
	 */
	public function ajaxAddToCartAction()
	{
		die();
	}

}