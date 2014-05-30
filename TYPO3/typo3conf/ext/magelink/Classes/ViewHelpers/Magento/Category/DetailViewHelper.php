<?php
namespace MageDeveloper\Magelink\ViewHelpers\Magento\Category;

	/***************************************************************
	 *  Copyright notice
	 *
	 *  (c) 2013
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
class DetailViewHelper extends \MageDeveloper\Magelink\ViewHelpers\Magento\AbstractMagentoViewHelper
{
	/**
	 * Initialize arguments.
	 *
	 * @return void
	 * @api
	 */
	public function initializeArguments()
	{
		parent::initializeArguments();
		$this->registerArgument("id", "int", "Loads an category by an id", false, false);
		$this->registerArgument("name", "string", "Loads an category by name", true, false);
		$this->registerArgument("as", "string", "Product Data Template Identifier", false, false);
		$this->registerArgument("cache", "bool", "Caches Product Data", false, true);
	}
	
	/**
	 * Gets a product
	 * 
	 * @return \string Attribute Value
	 */
	public function render()
	{
		$id = $this->arguments["id"];

		// Fetch product id if it is not given		
		if (!$id)
		{
			// We require an sku argument to continue
			if (!$this->arguments["name"])
			{
				throw new \Exception("No id or name given! (".__METHOD__.")");
			}

			// Initialize Magento and prepare
			if ($this->magentoCore->init())
			{
				// Fetch product id from sku
				$name = $this->arguments["name"];
				$category = \Mage::getModel("catalog/category")->loadByAttribute("name", $name);
				
				if ($category instanceof \Mage_Catalog_Model_Category)
				{
					$id = $category->getId();
				}
			}
		}
		
		$cacheIdentifier = "category_data_".$id;
		$categoryData = array();
		
		// Check if product is in cache, and caching can be used
		if ((FALSE === ($categoryData = $this->cacheService->get($cacheIdentifier)) && $id > 0) || !$this->arguments["cache"]) 
		{
			// Initialize Magento and prepare
			if ($this->magentoCore->init())
			{
				$store 			= $this->settingsService->getStoreViewCode();
				$category 		= \Mage::getModel("magelink/category_api")->detail($id, $store);
				$categoryData 	= json_decode( $category, true );
				
				// Add product data to cache
				$lifetime = $this->settingsService->getCacheLifetime();
				
				$this->cacheService->set( $cacheIdentifier, $categoryData, array(), $lifetime );
			}
		}

		$output = '';
		if ($this->arguments["as"])
		{
			$this->templateVariableContainer->add($this->arguments["as"], $categoryData);
			$output .= $this->renderChildren();
			$this->templateVariableContainer->remove($this->arguments["as"]);
		}
		else
		{
			foreach($categoryData as $key=>$value)
			{
				$this->templateVariableContainer->add($key, $value);
			}
			
			$output .= $this->renderChildren();
			
			foreach($categoryData as $key=>$value)
			{
				$this->templateVariableContainer->remove($key);
			}
		}
		
		return $output;
	}
	
	
	
	
}