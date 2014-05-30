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
class Product extends \MageDeveloper\Magelink\Domain\Model\AbstractObject 
{
	/**
	 * Product Types
	 * @var \string
	 */
	const TYPE_SIMPLE       = "simple";
	const TYPE_CONFIGURABLE = "configurable";
	const TYPE_GROUPED      = "grouped";

	/**
	 * Product Visibility
	 * @var \int
	 */
	const VISIBILITY_NOT_VISIBLE    = 1;
	const VISIBILITY_IN_CATALOG     = 2;
	const VISIBILITY_IN_SEARCH      = 3;
	const VISIBILITY_BOTH           = 4;

	/**
	 * Product Status
	 * @var \int
	 */
	const STATUS_ENABLED 	= 1;
	const STATUS_DISABLED 	= 2;

	/**
	 * Magento Entity ID
	 *
	 * @var \integer
	 * @validate NotEmpty
	 */
	protected $entityId;

	/**
	 * SKU
	 *
	 * @var \string
	 * @validate NotEmpty
	 */
	protected $sku;

	/**
	 * Productname
	 *
	 * @var \string
	 * @validate NotEmpty
	 */
	protected $name;

	/**
	 * Short Description
	 *
	 * @var \string
	 */
	protected $shortDescription;

	/**
	 * Description
	 *
	 * @var \string
	 */
	protected $description;

	/**
	 * Image
	 *
	 * @var \string
	 */
	protected $image;

	/**
	 * Price
	 *
	 * @var \float
	 * @validate NotEmpty
	 */
	protected $price;

	/**
	 * Currency
	 *
	 * @var \string
	 */
	protected $currency;

	/**
	 * Special Price
	 *
	 * @var \float
	 */
	protected $specialPrice;

	/**
	 * Final Price
	 *
	 * @var \float
	 */
	protected $finalPrice;

	/**
	 * Stock Quantity
	 *
	 * @var \integer
	 * @validate NotEmpty
	 */
	protected $qty;

	/**
	 * Auto Refresh Product
	 *
	 * @var boolean
	 */
	protected $autoRefresh = FALSE;

	/**
	 * Product is hidden
	 *
	 * @var \boolean
	 */
	protected $hidden = FALSE;

	/**
	 * Product is disabled
	 *
	 * @var \boolean
	 */
	protected $isDisabled = FALSE;

	/**
	 * Manually manage stock
	 *
	 * @var \boolean
	 */
	protected $manageStock = FALSE;

	/**
	 * Attribute Relation
	 *
	 * @var \TYPO3\CMS\Extbase\Persistence\ObjectStorage<\MageDeveloper\Magelink\Domain\Model\Attribute>
	 * @lazy
	 */
	protected $attributes;

	/**
	 * Store View Code
	 *
	 * @var \string
	 */
	protected $store;

	/**
	 * Timestamp
	 * 
	 * @var \DateTime
	 */
	protected $tstamp;

	/**
	 * __construct
	 *
	 * @return Product
	 */
	public function __construct() 
	{
		$this->initStorageObjects();
	}
	
	/**
	 * Returns the entityId
	 *
	 * @return \integer entityId
	 */
	public function getEntityId() 
	{
		return $this->entityId;
	}

	/**
	 * Sets the entityId
	 *
	 * @param \integer $entityId
	 * @return \integer entityId
	 */
	public function setEntityId($entityId) 
	{
		$this->entityId = $entityId;
	}

	/**
	 * Returns the sku
	 *
	 * @return \string $sku
	 */
	public function getSku() 
	{
		return $this->sku;
	}

	/**
	 * Sets the sku
	 *
	 * @param \string $sku
	 * @return void
	 */
	public function setSku($sku) 
	{
		$this->sku = $sku;
	}

	/**
	 * Returns the name
	 *
	 * @return \string $name
	 */
	public function getName() 
	{
		return $this->name;
	}

	/**
	 * Sets the name
	 *
	 * @param \string $name
	 * @return void
	 */
	public function setName($name) 
	{
		$this->name = $name;
	}

	/**
	 * Returns the shortDescription
	 *
	 * @return \string $shortDescription
	 */
	public function getShortDescription() 
	{
		return $this->shortDescription;
	}

	/**
	 * Sets the shortDescription
	 *
	 * @param \string $shortDescription
	 * @return void
	 */
	public function setShortDescription($shortDescription) 
	{
		$this->shortDescription = $shortDescription;
	}

	/**
	 * Returns the description
	 *
	 * @return \string $description
	 */
	public function getDescription() 
	{
		return $this->description;
	}

	/**
	 * Sets the description
	 *
	 * @param \string $description
	 * @return void
	 */
	public function setDescription($description) 
	{
		$this->description = $description;
	}

	/**
	 * Returns the price
	 *
	 * @return \float $price
	 */
	public function getPrice() 
	{
		return $this->price;
	}

	/**
	 * Sets the price
	 *
	 * @param \float $price
	 * @return void
	 */
	public function setPrice($price) 
	{
		$this->price = $price;
	}

	/**
	 * Returns the currency
	 *
	 * @return \string $currency
	 */
	public function getCurrency()
	{
		return $this->currency;
	}

	/**
	 * Sets the currency
	 *
	 * @param \string $currency
	 * @return void
	 */
	public function setCurrency($currency)
	{
		$this->currency = $currency;
	}

	/**
	 * Returns the specialPrice
	 *
	 * @return \float $specialPrice
	 */
	public function getSpecialPrice() 
	{
		return $this->specialPrice;
	}

	/**
	 * Sets the specialPrice
	 *
	 * @param \float $specialPrice
	 * @return void
	 */
	public function setSpecialPrice($specialPrice) 
	{
		$this->specialPrice = $specialPrice;
	}

	/**
	 * Returns the finalPrice
	 *
	 * @return \float $finalPrice
	 */
	public function getFinalPrice()
	{
		return $this->finalPrice;
	}

	/**
	 * Sets the finalPrice
	 *
	 * @param \float $finalPrice
	 * @return void
	 */
	public function setFinalPrice($finalPrice)
	{
		$this->finalPrice = $finalPrice;
	}

	/**
	 * Returns the qty
	 *
	 * @return \integer $qty
	 */
	public function getQty() 
	{
		return $this->qty;
	}

	/**
	 * Sets the qty
	 *
	 * @param \integer $qty
	 * @return void
	 */
	public function setQty($qty) 
	{
		$this->qty = $qty;
	}

	/**
	 * Get Tstamp
	 *
	 * @return DateTime
	 */
	public function getTstamp() 
	{
		return $this->tstamp;
	}

	/**
	 * Set tstamp
	 *
	 * @param DateTime $tstamp tstamp
	 * @return void
	 */
	public function setTstamp($tstamp) 
	{
		$this->tstamp = $tstamp;
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

	/**
	 * Returns the disabled setting
	 *
	 * @return \boolean $isDisabled
	 */
	public function getIsDisabled()
	{
		return $this->isDisabled;
	}

	/**
	 * Sets the disabled
	 *
	 * @param \boolean $isDisabled
	 * @return void
	 */
	public function setIsDisabled($isDisabled)
	{
		$this->isDisabled = $isDisabled;
	}

	/**
	 * Returns the hidden setting
	 *
	 * @return \boolean $hidden
	 */
	public function getHidden()
	{
		return $this->hidden;
	}

	/**
	 * Sets the hidden setting
	 *
	 * @param \boolean $hidden
	 * @return void
	 */
	public function setHidden($hidden)
	{
		$this->hidden = $hidden;
	}

	/**
	 * Returns the manage stock setting
	 *
	 * @return \boolean $manageStock
	 */
	public function getManageStock()
	{
		return $this->manageStock;
	}

	/**
	 * Sets the manage stock setting
	 *
	 * @param \boolean $manageStock
	 * @return void
	 */
	public function setManageStock($manageStock)
	{
		$this->manageStock = $manageStock;
	}

	/**
	 * Returns the boolean state of autoRefresh
	 *
	 * @return boolean
	 */
	public function isAutoRefresh() 
	{
		return $this->getAutoRefresh();
	}

	/**
	 * Returns the store
	 *
	 * @return \string $store
	 */
	public function getStore() 
	{
		return $this->store;
	}

	/**
	 * Sets the store
	 *
	 * @param \string $store
	 * @return void
	 */
	public function setStore($store) 
	{
		$this->store = $store;
	}

	/**
	 * Returns media gallery
	 * 
	 * @return \array
	 */
	public function getMediaGallery()
	{
		$gallery = $this->getAttributeByCode("media_gallery");
		return $gallery["images"];
	}

	/**
	 * Initializes all ObjectStorage properties.
	 *
	 * @return void
	 */
	protected function initStorageObjects() 
	{
		$this->attributes = new \TYPO3\CMS\Extbase\Persistence\ObjectStorage();
	}

	/**
	 * Adds a Attribute
	 *
	 * @param \MageDeveloper\Magelink\Domain\Model\Attribute $attribute
	 * @return void
	 */
	public function addAttribute(\MageDeveloper\Magelink\Domain\Model\Attribute $attribute) 
	{
		$this->attributes->attach($attribute);
	}

	/**
	 * Removes a Attribute
	 *
	 * @param \MageDeveloper\Magelink\Domain\Model\Attribute $attributeToRemove The Attribute to be removed
	 * @return void
	 */
	public function removeAttribute(\MageDeveloper\Magelink\Domain\Model\Attribute $attributeToRemove) 
	{
		$this->attributes->detach($attributeToRemove);
	}

	/**
	 * Returns the attribute
	 *
	 * @return \TYPO3\CMS\Extbase\Persistence\ObjectStorage<\MageDeveloper\Magelink\Domain\Model\Attribute> $attribute
	 */
	public function getAttributes() 
	{
		return $this->attributes;
	}

	/**
	 * Sets the attributes
	 *
	 * @param \TYPO3\CMS\Extbase\Persistence\ObjectStorage<\MageDeveloper\Magelink\Domain\Model\Attribute> $attribute
	 * @return void
	 */
	public function setAttributes(\TYPO3\CMS\Extbase\Persistence\ObjectStorage $attributes) 
	{
		$this->attributes = $attributes;
	}

	/**
	 * Get an attribute value
	 * 
	 * @param \string $attribute Attribute Code
	 * @return \string
	 */
	public function getAttributeValue($attribute)
	{
		foreach ($this->getAttributes() as $_attribute)
		{
			
			if ($_attribute instanceof \MageDeveloper\Magelink\Domain\Model\Attribute)
			{
				if ($_attribute->getCode() == $attribute)
				{
					return json_decode($_attribute->getValue(), true);
				}
			}	
			
		}
		
		return "";
	}

	/**
	 * Get products media images
	 * 
	 * @return \array
	 */
	public function getMedia()
	{
		$media = $this->getAttributeValue("media");
		
		if (is_array($media))
		{
			return $media;
		}
		
		return array();
	}

	/**
	 * Gets product main image
	 * 
	 * @return \string
	 */
	public function getImage()
	{
		return $this->image;
	}
	/*public function getImage()
	{
		return $this->getAttributeValue("image");
	}*/

	/**
	 * Sets the product image 
	 * 
	 * @param \string $image
	 * @return void
	 */
	public function setImage($image)
	{
		$this->image = $image;
	}
	
	/**
	 * Gets the products url_path
	 * 
	 * @return \string
	 */
	public function getUrlPath()
	{
		return $this->getAttributeValue("url_path");
	}

	/**
	 * Gets the products type
	 * @return \string
	 */
	public function getType()
	{
		return $this->getAttributeValue("type_id");
	}

	/**
	 * Gets products options
	 * 
	 * @return \array
	 */
	public function getOptions()
	{
		$options = $this->getAttributeValue("options");

		if (is_array($options))
		{
			return $options;
		}

		return array();
	}

	/**
	 * Gets associated product ids
	 * 
	 * @return \array
	 */
	public function getAssociatedProductIds()
	{
		$data = $this->getAttributeValue("associated_products");
		if (!empty($data))
		{
			return implode(',', array_keys($data));
		}
		
		return array();
	}

	/**
	 * Gets associated product data
	 * 
	 * @return \array
	 */
	public function getAssociatedProducts()
	{
		return $this->getAttributeValue("associated_products");
	}

	/**
	 * Gets products additional attributes
	 * 
	 * @return \array
	 */
	public function getAdditionalAttributes()
	{
		return $this->getAttributeValue("attributes");
	}

	/**
	 * Gets the user defined attributes
	 * 
	 * @return \array
	 */
	public function getUserDefinedAttributes()
	{
		return $this->getAttributeValue("user_defined_attributes");
	}

	/**
	 * Gets cross selling product ids
	 *
	 * @return \array
	 */
	public function getCrossSellProductIds()
	{
		return $this->getAttributeValue("crosssell_ids");
	}

	/**
	 * Gets up-selling product ids
	 *
	 * @return \array
	 */
	public function getUpSellProductIds()
	{
		return $this->getAttributeValue("upsell_ids");
	}

	/**
	 * Gets a products bundle minimal price
	 * 
	 * @return \float
	 */
	public function getMinimalPrice()
	{
		return (float)$this->getAttributeValue("minimal_price");
	}

	/**
	 * Gets a products visibility setting
	 * 
	 * @return \int
	 */
	public function getVisibility()
	{
		return (int)$this->getAttributeValue("visibility");
	}

}

