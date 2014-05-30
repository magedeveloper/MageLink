<?php
namespace MageDeveloper\Magelink\Magento;

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

class Helper extends \MageDeveloper\Magelink\Domain\Model\AbstractObject
{
	/**
	 * settingsService
	 *
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
	 * Constructor
	 */
	public function __construct()
	{
		// Load Object Manager
		$this->objectManager 	= \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance("TYPO3\\CMS\\Extbase\\Object\\ObjectManager");

		// Inject Settings Service
		$this->settingsService	= $this->objectManager->get("MageDeveloper\\Magelink\\Service\\SettingsService");
	}
	
	/**
	 * Gets the Magento Root Path
	 * 
	 * @return \string
	 */
	public function getMagentoRootPath()
	{
		return $this->settingsService->getMagentoRootPath();
	}

	/**
	 * Gets the store view code from configuration
	 * 
	 * @return \string
	 */
	public function getStoreViewCode()
	{
		return $this->settingsService->getStoreViewCode();
	}
	
	/**
	 * Gets the magento filename of a given
	 * class name
	 * 
	 * @param \string $class Class Name
	 * @return \string
	 */
	public function getMagentoFilenameByClass($class)
	{
		$filename = uc_words($class, DIRECTORY_SEPARATOR).".php";
		return $filename;
	}

	/**
	 * Determines the filepath for a Magento Class
	 * 
	 * @param \string $class Magento Class Name
	 * @return \string
	 */
	public function determinePathForMagentoClass($class)
	{
		$path 		= $this->getMagentoRootPath();
		$filename 	= $this->getMagentoFilenameByClass($class);
		
		$dirs = array(
			"app/code/core",
			"app/code/community",
			"app/code/local",
			"app/code/local/core",
			"lib",
		);
		
		foreach ($dirs as $_dir)
		{
			$directory = str_replace("/", DIRECTORY_SEPARATOR, $_dir);
			$directory = rtrim($directory, "/");
			$directory = rtrim($directory, "\\");
			
			if (file_exists($path.$directory.DIRECTORY_SEPARATOR.$filename))
			{
				return $path.$directory.DIRECTORY_SEPARATOR;
			}
			
		}
		
		return false;
	}

	/**
	 * Gets the url
	 */
	public function getUrl()
	{
		//echo $this->uriBuilder->uriFor("index", $this->request->getArguments(), "Magento", $this->getExtensionName(), $this->getPluginName());
	}
	
	
}