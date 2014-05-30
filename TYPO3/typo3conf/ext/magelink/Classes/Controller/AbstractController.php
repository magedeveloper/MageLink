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
abstract class AbstractController extends \TYPO3\CMS\Extbase\Mvc\Controller\ActionController
{
	/**
	 * settingsService
	 * @var \MageDeveloper\Magelink\Service\SettingsService
	 * @inject
	 */
	protected $settingsService;

	/**
	 * Gets the extension name
	 * 
	 * @return string
	 */
	public function getExtensionName()
	{
		return $this->controllerContext->getRequest()->getControllerExtensionName();
	}
	
	/**
	 * Gets the extension key
	 * 
	 * @return string
	 */
	public function getExtensionKey()
	{
		return $this->controllerContext->getRequest()->getControllerExtensionKey();
	}
	
	/**
	 * Gets the plugin name
	 * 
	 * @return string
	 */
	public function getPluginName()
	{
		return $this->controllerContext->getRequest()->getPluginName();
	}
	
	/**
	 * Adds an flash message
	 * 
	 * @param string $message The Message
	 * @param int $type Message Type
	 * @return void
	 */
	public function addFlashMessage($message, $type = \TYPO3\CMS\Core\Messaging\FlashMessage::ERROR)
	{
		$this->flashMessageContainer->add(
				     $message,
				     '',
				     $type
		);
	}
		
	/**
	 * helper function to use localized strings in BlogExample controllers
	 *
	 * @param string $key locallang key
	 * @param string $default the default message to show if key was not found
	 * @return string
	 */
	/*protected function translate($key, $defaultMessage = '') {
		$message = Tx_Extbase_Utility_Localization::translate($key, 'BlogExample');
		if ($message === NULL) {
			$message = $defaultMessage;
		}
		return $message;
	}*/
		
		
}
	