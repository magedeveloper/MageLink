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

class CategoryData extends \MageDeveloper\Magelink\Magento\Data\AbstractData
{
	/**
	 * Gets an category by an given id
	 *
	 * @param int $id Category Id
	 * @param string $store Store View Code
	 * @return array
	 * @throws \Exception
	 */
	public function getCategoryById($id, $store)
	{
		if ($this->magentoCore->init())
		{
			$category = \Mage::getModel("magelink/category_api")->detail($id, $store);
			$categoryData = json_decode( $category, true );
			$categoryData["store_view_code"] = $store;

			return $categoryData;
		}

		throw new \Exception("Could not establish a magento connection");
	}

	/**
	 * Gets an category list
	 *
	 * @param string $store Store View Code
	 * @return array
	 * @throws \Exception
	 */
	public function getCategoryList($store)
	{
		if ($this->magentoCore->init())
		{
			$categoryList = \Mage::getModel("magelink/category_api")->fetch($store);
			$categoryListData = json_decode( $categoryList, true );

			return $categoryListData;
		}

		throw new \Exception("Could not establish a magento connection");
	}
	
}