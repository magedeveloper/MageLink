<?php
namespace MageDeveloper\Magelink\Magento\Data;

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

class ProductData extends \MageDeveloper\Magelink\Magento\Data\AbstractData
{
	/**
	 * Gets an product by an given id
	 *
	 * @param int $id Product Id
	 * @param string $store Store View Code
	 * @return array
	 * @throws \Exception
	 */
	public function getProductById($id, $store)
	{
		if ($this->magentoCore->init())
		{
			$product = \Mage::getModel("magelink/product_api")->fetch($id, $store);
			$productData = json_decode( $product, true );
			$productData["store_view_code"] = $store;

			return $productData;
		}

		throw new \Exception("Could not establish a magento connection");
	}

	/**
	 * Gets an product list
	 *
	 * @param string $store Store View Code
	 * @param array $filters Filters
	 * @return array
	 * @throws \Exception
	 */
	public function getProductList($store, $filters = array())
	{
		if ($this->magentoCore->init())
		{
			$productList = \Mage::getModel("magelink/product_api")->items($filters, $store);
			$productListData = json_decode( $productList, true );

			return $productListData;
		}

		throw new \Exception("Could not establish a magento connection");
	}

	/**
	 * Gets an list of filtered product ids
	 *
	 * @param array $tags Array with Tags to filter
	 * @param array $categories Array with Categories to filter
	 * @param array $skus Array with skus to filter
	 * @param string $store Store View Code
	 * @return array
	 * @throws \Exception
	 */
	public function getProductsByFilter(array $tags, array $categories, array $skus, $store)
	{
		if ($this->magentoCore->init())
		{
			$productIds = \Mage::getModel("magelink/product_api")->filter($tags, $categories, $skus, $store);
			
			if (is_array($productIds))
			{
				return $productIds;
			}
			
			return array();
		}

		throw new \Exception("Could not establish a magento connection");
	}

}