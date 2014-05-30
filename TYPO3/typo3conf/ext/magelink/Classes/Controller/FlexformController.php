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
class FlexformController extends \MageDeveloper\Magelink\Controller\AbstractController 
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
	 * Category Import Model
	 *
	 * @var \MageDeveloper\Magelink\Import\CategoryImport
	 * @inject
	 */
	protected $categoryImport;

	/**
	 * Product Import Model
	 *
	 * @var \MageDeveloper\Magelink\Import\ProductImport
	 * @inject
	 */
	protected $productImport;
	
	/**
	 * cacheService
	 * 
	 * @var \MageDeveloper\Magelink\Service\CacheService
	 * @inject
	 */
	protected $cacheService;
	
	
	/**
	 * Object Manager
	 * @var \TYPO3\CMS\Extbase\Object\ObjectManager
	 * @inject
	 */
	protected $objectManager;

	
	public function __construct()
	{
		$this->objectManager 	= \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance("TYPO3\\CMS\\Extbase\\Object\\ObjectManager");
		
		// Inject Product Import
		$this->productImport 	= $this->objectManager->get("MageDeveloper\\Magelink\\Import\\ProductImport");
		
		// Inject Category Import
		$this->categoryImport = $this->objectManager->get("MageDeveloper\\Magelink\\Import\\CategoryImport");
		
		// Inject Settings Service
		$this->settingsService	= $this->objectManager->get("MageDeveloper\\Magelink\\Service\\SettingsService");
		
		$this->flexFormService  = $this->objectManager->get("MageDeveloper\\Magelink\\Service\\FlexFormService");
		
		// Inject Cache Service
		$cacheName 				= "magelink_cache";
		$this->cacheService		= $this->objectManager->get("MageDeveloper\\Magelink\\Service\\CacheService",$cacheName);
		
		parent::__construct();
	}
	
	/**
	 * Populate flexform product list action
	 * 
	 * @param array $config Configuration Array
	 * @param array $parentObject Parent Object
	 * @return array
	 */
	public function populateProductListAction(array &$config, &$parentObject)
	{
		$storeViewCode 		= $this->flexFormService->extractFlexformConfig($config, "settings.store_view_code", "refresh_setting");
		if (!$storeViewCode)
		{
			// Gets the store view code from the global configuration
			$storeViewCode = $this->settingsService->getConfiguration("import.store_view_code");			
		}
		
		$cacheIdentifier 	= "productList".$storeViewCode;
		$options 			= array();
		
		if (FALSE === ($options = $this->cacheService->get($cacheIdentifier))) 
		{
			$list		= array();
			$list 		= $this->productImport->fetchProductList($storeViewCode);
			
			if (count($list) > 0)
			{
				foreach ($list as $_product)
				{
					$optionStr =  '['.$_product["type"].'] ' . '['.$_product["sku"].'] ' . $_product["name"];
					$options[] = array("label" => "customlabel", 0 => $optionStr, 1 => $_product["product_id"]);
				}
	
			}
			
			$lifetime = $this->settingsService->getCacheLifetime();
			$this->cacheService->set( $cacheIdentifier, $options, array(), $lifetime );
		
		} 
		
		
		$config["items"] = $options;
		
		return $config;
	}

	/**
	 * Populate flexform category list action
	 * 
	 * @param array $config Configuration Array
	 * @param array $parentObject Parent Object
	 * @return array
	 */
	public function populateCategoryListAction(array &$config, &$parentObject)
	{
		$storeViewCode 		= $this->flexFormService->extractFlexformConfig($config, "settings.store_view_code", "refresh_setting");
		if (!$storeViewCode)
		{
			// Gets the store view code from the global configuration
			$storeViewCode = $this->settingsService->getConfiguration("import.store_view_code");			
		}
		
		$cacheIdentifier 	= "categoryList".$storeViewCode;
		$options 			= array();
		
		if (FALSE === ($options = $this->cacheService->get($cacheIdentifier))) 
		{
			$list		= array();
			$list 		= $this->categoryImport->fetchCategoryList($storeViewCode);
			
			if (count($list) > 0)
			{
				foreach ($list as $_category)
				{
					$depth = (int)$_category["level"] - 1;
					$depth = ($depth < 0)?0:$depth; 
					$spacer = str_repeat("&nbsp;", $depth*4);
					
					if ($_category["name"] && $_category["entity_id"])
					{
						$optionStr = $spacer.$_category["name"].' '.'[ID: '.$_category["entity_id"].']';
						$options[] = array(0 => $optionStr, 1 => $_category["entity_id"]);
					}
					
				}
	
			}
			
			$lifetime = $this->settingsService->getCacheLifetime();
			$this->cacheService->set( $cacheIdentifier, $options, array(), $lifetime );
		} 
		
		$config["items"] = $options;
		
		return $config;
	}
	
	/**
	 * Populate flexform http error code list action
	 *
	 * @param array $config Configuration Array
	 * @param array $parentObject Parent Object
	 * @return array
	 */
	public function populateErrorCodeList(array &$config, &$parentObject)
	{
		$options = array();

		$reflection = new \ReflectionClass("\\TYPO3\\CMS\\Core\\Utility\\HttpUtility");

		foreach($reflection->getConstants() as $_const=>$_val)
		{
			if (strpos($_const, "HTTP") == 0)
			{
				$options[] = array(0 => $_val, 1 => (string)$_val);
			}
		}

		$config['items'] = array_merge($config['items'], $options);

		return $config;
	}


	
	
	
	
}
