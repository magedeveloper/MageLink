<?php
namespace MageDeveloper\Magelink\Domain\Model;

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
class Productfilter extends \MageDeveloper\Magelink\Domain\Model\AbstractObject
{
	/**
	 * Tags
	 *
	 * @var \string
	 */
	protected $tags;

	/**
	 * Categories
	 *
	 * @var \string
	 */
	protected $categories;

	/**
	 * SKUs
	 *
	 * @var \string
	 */
	protected $skus;	

	/**
	 * Product Relation
	 *
	 * @var \string
	 */
	protected $products;

	/**
	 * Store View Code
	 *
	 * @var \string
	 */
	protected $store;
	
	/**
	 * Auto Refresh Product
	 *
	 * @var \boolean
	 */
	protected $autoRefresh = FALSE;

	/**
	 * Sets the categories
	 * 
	 * @param \string $categories
	 * @return void
	 */
	public function setCategories($categories)
	{
		$this->categories = $categories;
	}

	/**
	 * Gets the category ids
	 * 
	 * @return \array
	 */
	public function getCategories()
	{
		return explode( ',', $this->categories );
	}

	/**
	 * Returns the products
	 *
	 * @return \array
	 */
	public function getProducts()
	{
		if (!$this->products)
		{
			return array();
		}
		return explode(',', $this->products);
	}

	/**
	 * Sets the products
	 *
	 * @param \string
	 * @return void
	 */
	public function setProducts($products)
	{
		$this->products = $products;
	}


	/**
	 * Sets the store view code
	 * 
	 * @param \string $store Store View Code
	 * @return void
	 */
	public function setStore($store)
	{
		$this->store = $store;
	}

	/**
	 * Gets the store view code
	 * 
	 * @return \string
	 */
	public function getStore()
	{
		return $this->store;
	}

	/**
	 * Sets the tags
	 * 
	 * @param \string $tags
	 * @return void
	 */
	public function setTags($tags)
	{
		$this->tags = $tags;
	}

	/**
	 * Gets the tags
	 * @return \array
	 */
	public function getTags()
	{
		return explode(',', $this->tags);
	}

	/**
	 * Sets the skus
	 *
	 * @param \string $skus
	 * @return void
	 */
	public function setSkus($skus)
	{
		$this->skus = $skus;
	}

	/**
	 * Gets the skus
	 * @return \array
	 */
	public function getSkus()
	{
		return explode(',', $this->skus);
	}

	/**
	 * Returns the autoRefresh
	 *
	 * @return boolean $autoRefresh
	 */
	public function getAutoRefresh()
	{
		return $this->autoRefresh;
	}

	/**
	 * Sets the autoRefresh
	 *
	 * @param boolean $autoRefresh
	 * @return void
	 */
	public function setAutoRefresh($autoRefresh)
	{
		$this->autoRefresh = $autoRefresh;
	}

}