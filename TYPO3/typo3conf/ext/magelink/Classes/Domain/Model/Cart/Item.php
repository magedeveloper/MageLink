<?php
namespace MageDeveloper\Magelink\Domain\Model\Cart;

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
class Item extends \MageDeveloper\Magelink\Domain\Model\AbstractObject
{
	/**
	 * @var int
	 */
	protected $id;

	/**
	 * @var int
	 */
	protected $productId;

	/**
	 * @var \MageDeveloper\Magelink\Domain\Model\Product
	 */
	protected $product;

	/**
	 * @var string
	 */
	protected $name;

	/**
	 * @var float
	 */
	protected $qty;

	/**
	 * @var string
	 */
	protected $price;

	/**
	 * @var string 
	 */
	protected $total;

	/**
	 * @var string
	 */
	protected $store;

	/**
	 * @var int
	 */
	protected $parentItemId;
	
	/**
	 * @var \MageDeveloper\Magelink\Domain\Model\Product
	 */
	protected $parentItem;

	/**
	 * @param int $id
	 */
	public function setId($id)
	{
		$this->id = $id;
	}

	/**
	 * @return int
	 */
	public function getId()
	{
		return $this->id;
	}

	/**
	 * @param string $name
	 */
	public function setName($name)
	{
		$this->name = $name;
	}

	/**
	 * @return string
	 */
	public function getName()
	{
		return $this->name;
	}

	/**
	 * @param \MageDeveloper\Magelink\Domain\Model\Product $parentItem
	 */
	public function setParentItem(\MageDeveloper\Magelink\Domain\Model\Product $parentItem)
	{
		$this->parentItem = $parentItem;
	}

	/**
	 * @return \MageDeveloper\Magelink\Domain\Model\Product
	 */
	public function getParentItem()
	{
		return $this->parentItem;
	}

	/**
	 * @param int $parentItemId
	 */
	public function setParentItemId($parentItemId)
	{
		$this->parentItemId = $parentItemId;
	}

	/**
	 * @return int
	 */
	public function getParentItemId()
	{
		return $this->parentItemId;
	}

	/**
	 * @param string $price
	 */
	public function setPrice($price)
	{
		$this->price = $price;
	}

	/**
	 * @return string
	 */
	public function getPrice()
	{
		return $this->price;
	}

	/**
	 * @param \MageDeveloper\Magelink\Domain\Model\Product $product
	 */
	public function setProduct(\MageDeveloper\Magelink\Domain\Model\Product $product)
	{
		$this->product = $product;
	}

	/**
	 * @return \MageDeveloper\Magelink\Domain\Model\Product
	 */
	public function getProduct()
	{
		return $this->product;
	}

	/**
	 * @param int $productId
	 */
	public function setProductId($productId)
	{
		$this->productId = $productId;
	}

	/**
	 * @return int
	 */
	public function getProductId()
	{
		return $this->productId;
	}

	/**
	 * @param float $qty
	 */
	public function setQty($qty)
	{
		$this->qty = $qty;
	}

	/**
	 * @return float
	 */
	public function getQty()
	{
		return $this->qty;
	}

	/**
	 * @param string $store
	 */
	public function setStore($store)
	{
		$this->store = $store;
	}

	/**
	 * @return string
	 */
	public function getStore()
	{
		return $this->store;
	}

	/**
	 * @param string $total
	 */
	public function setTotal($total)
	{
		$this->total = $total;
	}

	/**
	 * @return string
	 */
	public function getTotal()
	{
		return $this->total;
	}
	
	
	
}