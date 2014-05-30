<?php
namespace MageDeveloper\Magelink\Api\Requests;


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
 * API Category Calls
 * @package magelink
 * @license http://www.gnu.org/licenses/gpl.html GNU General Public License, version 3 or later
 *
 */
class AbstractRequest extends \MageDeveloper\Magelink\Domain\Model\AbstractObject
{
	/**
	 * Api Client
	 * @var \MageDeveloper\Magelink\Api\Client
	 * @inject
	 */
	protected $apiClient;

	/**
	 * Settings Service
	 * @var \MageDeveloper\Magelink\Service\SettingsService
	 * @inject
	 */
	protected $settingsService;

	/**
	 * Object Manager
	 * @var \TYPO3\CMS\Extbase\Object\ObjectManager
	 * @inject
	 */
	protected $objectManager;

	/**
	 * Sets the api client
	 * 
	 * @param \MageDeveloper\Magelink\Api\Client $apiClient
	 * @return void
	 */
	public function setApiClient(\MageDeveloper\Magelink\Api\Client $apiClient)
	{
		$this->apiClient = $apiClient;
	}

	/**
	 * Gets the api client
	 * 
	 * @return \MageDeveloper\Magelink\Api\Client
	 */
	public function getApiClient()
	{
		return $this->apiClient;
	}

	/**
	 * Initialize the object
	 */
	public function initializeObject()
	{
		$url 	= $this->settingsService->getApiUrl();
		$user	= $this->settingsService->getConfiguration("webservice.api_username");
		$key	= $this->settingsService->getConfiguration("webservice.api_key");

		$this->getApiClient()->setUrl($url);
		$this->getApiClient()->setUsername($user);
		$this->getApiClient()->setKey($key);

		parent::initializeObject();
	}
	
}