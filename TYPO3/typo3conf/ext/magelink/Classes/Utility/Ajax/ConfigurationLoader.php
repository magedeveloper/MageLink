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
 * AJAX CONFIGURATION LOADER
 * @package magelink
 * @license http://www.gnu.org/licenses/gpl.html GNU General Public License, version 3 or later
 *
 */

class ConfigurationLoader
{
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
		// TYPO3 Object Manager
		$this->objectManager 	= \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance("TYPO3\\CMS\\Extbase\\Object\\ObjectManager");

		$this->initGlobals();
	}

	/**
	 * init GLOBALS
	 * 
	 * @return void
	 */
	public function initGlobals()
	{
		$GLOBALS["TSFE"] = \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance("TYPO3\\CMS\\Frontend\\Controller\\TypoScriptFrontendController", $TYPO3_CONF_VARS, 0, 0);
		
		// Language
		\TYPO3\CMS\Frontend\Utility\EidUtility::initLanguage();

		// Get FE User Information
		$GLOBALS["TSFE"]->initFEuser();
		
		// Important: no Cache for Ajax stuff
		$GLOBALS["TSFE"]->set_no_cache();

		$GLOBALS["TSFE"]->checkAlternativeIdMethods();
		$GLOBALS["TSFE"]->determineId();
		$GLOBALS["TSFE"]->initTemplate();
		$GLOBALS["TSFE"]->getConfigArray();
		
		\TYPO3\CMS\Core\Core\Bootstrap::getInstance()->loadConfigurationAndInitialize();

		$GLOBALS["TSFE"]->cObj = \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance("TYPO3\\CMS\\Frontend\\ContentObject\\ContentObjectRenderer");
		$GLOBALS["TSFE"]->settingLanguage();
		$GLOBALS["TSFE"]->settingLocale();		
	}

	/**
	 * Gets full configuration
	 * 
	 * @return \array
	 */
	public function getConfiguration()
	{
		return $GLOBALS["TSFE"]->tmpl->setup;
	}


}