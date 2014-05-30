<?php
namespace MageDeveloper\Magelink\ViewHelpers\Magento\Product;

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
		$this->registerArgument("sku", "string", "Loads an product by an sku", false, false);
		$this->registerArgument("id", "int", "Loads an product by an id", false, false);
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
			if (!$this->arguments["sku"])
			{
				throw new \Exception("No id or sku given! (".__METHOD__.")");
			}
			
			// Initialize Magento and prepare
			if ($this->magentoCore->init())
			{
				// Fetch product id from sku
				$sku = $this->arguments["sku"];
				$product = \Mage::getModel("catalog/product")->loadByAttribute("sku", $sku);

				if ($product instanceof \Mage_Catalog_Model_Product)
				{
					$id = $product->getId();
				}
			}
		}
		
		$cacheIdentifier = "product_data_".$id;
		$productData = array();
		
		// Check if product is in cache, and caching can be used
		if ((FALSE === ($productData = $this->cacheService->get($cacheIdentifier)) && $id > 0) || !$this->arguments["cache"]) 
		{
			// Initialize Magento and prepare
			if ($this->magentoCore->init())
			{
				$product = \Mage::getModel("magelink/product_api")->fetch($id);
				$productData = json_decode( $product,true );
				
				// Add product data to cache
				$lifetime = $this->settingsService->getCacheLifetime();
				
				$this->cacheService->set( $cacheIdentifier, $productData, array(), $lifetime );
			}
		}

		$output = '';
		if ($this->arguments["as"])
		{
			$this->templateVariableContainer->add($this->arguments["as"], $productData);
			$output .= $this->renderChildren();
			$this->templateVariableContainer->remove($this->arguments["as"]);
		}
		else
		{
			foreach($productData as $key=>$value)
			{
				$this->templateVariableContainer->add($key, $value);
			}
			
			$output .= $this->renderChildren();
			
			foreach($productData as $key=>$value)
			{
				$this->templateVariableContainer->remove($key);
			}
		}
		
		return $output;
	}
	
	
	
	
}