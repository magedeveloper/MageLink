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

class Core extends \MageDeveloper\Magelink\Domain\Model\AbstractObject
{
	/**
	 * Singleton instance
	 *
	 * @var \MageDeveloper\Magelink\Magento\Core
	 */
	protected static $_instance = null;

	/**
	 * Magento
	 * @var \Mage
	 */
	protected $mage;

	/**
	 * Magento Helper
	 * @var \MageDeveloper\Magelink\Magento\Helper
	 * @inject
	 */
	protected $magentoHelper;

	/**
	 * Already dispatched
	 * @var \bool
	 */
	protected $dispatched;

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
		// Inject Magento Helper
		$this->magentoHelper  = $this->objectManager->get("MageDeveloper\\Magelink\\Magento\\Helper");
	}

	/**
	 * Initialize Magento
	 * 
	 * @return bool
	 */
	public function init()
	{
		$path = $this->magentoHelper->getMagentoRootPath();

		// Try to include Magento
		if(!class_exists("Mage", false))
		{
			if (!file_exists($path."app/Mage.php"))
			{
				return false;
			}
			
			require_once($path."app/Mage.php");
		}

		// disable Notices
		error_reporting();
		//error_reporting( E_ALL & ~ E_NOTICE );
		

		// Disable Magento Autoload Functionality
		if(class_exists("Varien_Autoload", false))
		{
			spl_autoload_unregister( array(\Varien_Autoload::instance(), "autoload") );
		}
		spl_autoload_register( array(&$this, "autoload") );

		restore_error_handler();
		
		$store = $this->magentoHelper->getStoreViewCode();
		\Mage::app()->setCurrentStore( \Mage::app()->getStore($store) );

		// Initialize
		//\Mage::app()->init();	

		$this->mage = \Mage::getSingleton("magelink/core");
		
		return true;
	}

	/**
	 * Dispatcher
	 *
	 * @param array $params
	 */
	public function dispatch($params = array())
	{
		if(!$this->dispatched)
		{
			$this->getMage()->dispatch($params);
		}

		$this->dispatched = true;
		return true;
	}

	/**
	 * Sets the controller instance of the Magento Core
	 * 
	 * @param \TYPO3\CMS\Extbase\Mvc\Controller\AbstractController $controller
	 * @return void
	 */
	public function setTYPO3Controller(\TYPO3\CMS\Extbase\Mvc\Controller\AbstractController $controller)
	{
		$this->mage->setTYPO3Controller($controller);
	}

	/**
	 * Sets the base url to the MageLink Core
	 * 
	 * @param \string $url Base URL
	 * @return void
	 */ 
	public function setTYPO3BaseUrl($url)
	{
		$this->mage->setTYPO3BaseUrl($url);
	}

	/**
	 * Gets the Base Url from the MageLink Core
	 * 
	 * @return \string
	 */
	public function getTYPO3BaseUrl()
	{
		return $this->mage->getTYPO3BaseUrl();
	}

	/**
	 * Gets the Magento Instance
	 * 
	 * @return \Mage
	 */
	public function getMage()
	{
		return $this->mage;
	}

	/**
	 * Gets an Magento Content block by id
	 *
	 * @param \string $blockId
	 * @return \string HTML Code
	 */
	public function getBlock($blockId)
	{
		$block = $this->getMage()->getBlock($blockId);

		if($block instanceof \Mage_Core_Block_Abstract)
		{
			return $block;
		}
		
		return null;
		
	}

	/**
	 * Get the header data from Magento
	 *
	 * @return string
	 */
	public function getHeader()
	{
		$blockHead = $this->getBlock("head");
		$head = array();

		if($blockHead instanceof \Mage_Page_Block_Html_Head)
		{
			$head[] = '<script type="text/javascript">';
			$head[] = '//<![CDATA[';
			$head[] = '    var BLANK_URL = \''.$blockHead->helper("core/js")->getJsUrl("blank.html").'\'';
			$head[] = '    var BLANK_IMG = \''.$blockHead->helper("core/js")->getJsUrl("spacer.gif").'\'';
			$head[] = '//]]>';
			$head[] = '</script>';
			$head[] = $blockHead->getCssJsHtml();
			$head[] = $blockHead->getChildHtml();
			$head[] = $blockHead->helper("core/js")->getTranslatorScript();
		}

		return implode("\n", $head);
	}	
	
	/**
	 * Autoload of Magento Classes
	 *
	 * @param string $class
	 */
	public function autoload($class)
	{
		$classFile 	= $this->magentoHelper->getMagentoFilenameByClass($class);
		$subPath	= $this->magentoHelper->determinePathForMagentoClass($class);
		$filename	= $subPath.$classFile;

		if($classFile && $subPath && file_exists($filename))
		{
			try
			{
				//echo "INCLUDE: " . $filename . "<br />";
				@include($filename);
			}
			catch (Exception $e)
			{
				//echo $e->getMessage();	
			}

		}

	}	
	
}