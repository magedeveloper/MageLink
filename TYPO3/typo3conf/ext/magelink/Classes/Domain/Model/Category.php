<?php
namespace MageDeveloper\Magelink\Domain\Model;


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
class Category extends \MageDeveloper\Magelink\Domain\Model\AbstractObject 
{

	/**
	 * Magento Entity ID
	 *
	 * @var \integer
	 * @validate NotEmpty
	 */
	protected $entityId;

	/**
	 * Magento Parent Category
	 *
	 * @var \integer
	 */
	protected $parent;

	/**
	 * Categoryname
	 *
	 * @var \string
	 * @validate NotEmpty
	 */
	protected $name;

	/**
	 * Category Description
	 *
	 * @var \string
	 */
	protected $description;

	/**
	 * Page Title
	 *
	 * @var \string
	 */
	protected $pageTitle;

	/**
	 * Image
	 *
	 * @var \string
	 */
	protected $image;

	/**
	 * URL
	 *
	 * @var \string
	 */
	protected $url;

	/**
	 * URL Path
	 *
	 * @var \string
	 */
	protected $urlPath;

	/**
	 * Product Count
	 *
	 * @var \integer
	 */
	protected $productCount;

	/**
	 * Auto Refresh Category
	 *
	 * @var \boolean
	 */
	protected $autoRefresh = FALSE;
	
	/**
	 * Category is hidden
	 *
	 * @var \boolean
	 */
	protected $hidden = FALSE;
	
	/**
	 * Is Active
	 *
	 * @var \boolean
	 */
	protected $isActive = TRUE;	

	/**
	 * Category is user defined
	 *
	 * @var \boolean
	 */
	protected $userDefined = FALSE;

	/**
	 * Store View Code
	 *
	 * @var \string
	 */
	protected $store;

	/**
	 * Sorting
	 *
	 * @var \int
	 */
	protected $sorting;

	/**
	 * Category Product Relation
	 *
	 * @var \string
	 */
	protected $products;

	/**
	 * Attribute Relation
	 *
	 * @var \TYPO3\CMS\Extbase\Persistence\ObjectStorage<\MageDeveloper\Magelink\Domain\Model\Attribute>
	 * @lazy
	 */
	protected $attributes;

	/**
	 * __construct
	 *
	 * @return Category
	 */
	public function __construct() 
	{
		//Do not remove the next line: It would break the functionality
		$this->initStorageObjects();
	}
	
	/**
	 * Returns the entityId
	 *
	 * @return \integer $entityId
	 */
	public function getEntityId() 
	{
		return $this->entityId;
	}

	/**
	 * Sets the entityId
	 *
	 * @param \integer $entityId
	 * @return void
	 */
	public function setEntityId($entityId) 
	{
		$this->entityId = $entityId;
	}

	/**
	 * Returns the parent
	 *
	 * @return \integer $parent
	 */
	public function getParent()
	{
		return $this->parent;
	}

	/**
	 * Sets the parent
	 *
	 * @param \integer $parent
	 * @return void
	 */
	public function setParent($parent)
	{
		$this->parent = $parent;
	}

	/**
	 * Returns the sorting
	 *
	 * @return \integer $sorting
	 */
	public function getSorting()
	{
		return $this->sorting;
	}

	/**
	 * Sets the sorting
	 *
	 * @param \integer $sorting
	 * @return void
	 */
	public function setSorting($sorting)
	{
		$this->sorting = $sorting;
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
	 * Returns the pageTitle
	 *
	 * @return \string $pageTitle
	 */
	public function getPageTitle() 
	{
		return $this->pageTitle;
	}

	/**
	 * Sets the pageTitle
	 *
	 * @param \string $pageTitle
	 * @return void
	 */
	public function setPageTitle($pageTitle) 
	{
		$this->pageTitle = $pageTitle;
	}

	/**
	 * Returns the image
	 *
	 * @return \string $image
	 */
	public function getImage() 
	{
		return $this->image;
	}

	/**
	 * Sets the image
	 *
	 * @param \string $image
	 * @return void
	 */
	public function setImage($image) 
	{
		$this->image = $image;
	}

	/**
	 * Returns the url
	 *
	 * @return \string $url
	 */
	public function getUrl() 
	{
		return $this->url;
	}

	/**
	 * Sets the url
	 *
	 * @param \string $url
	 * @return void
	 */
	public function setUrl($url) 
	{
		$this->url = $url;
	}

	/**
	 * Returns the urlPath
	 *
	 * @return \string $urlPath
	 */
	public function getUrlPath() 
	{
		return $this->urlPath;
	}

	/**
	 * Sets the urlPath
	 *
	 * @param \string $urlPath
	 * @return void
	 */
	public function setUrlPath($urlPath) 
	{
		$this->urlPath = $urlPath;
	}

	/**
	 * Returns the productCount
	 *
	 * @return \integer $productCount
	 */
	public function getProductCount() 
	{
		return $this->productCount;
	}

	/**
	 * Sets the productCount
	 *
	 * @param \integer $productCount
	 * @return void
	 */
	public function setProductCount($productCount) 
	{
		$this->productCount = $productCount;
	}

	/**
	 * Returns the autoRefresh
	 *
	 * @return boolean $isActive
	 */
	public function getIsActive()
	{
		return $this->isActive;
	}

	/**
	 * Sets the is active
	 *
	 * @param\ boolean $isActive
	 * @return void
	 */
	public function setIsActive($isActive)
	{
		$this->isActive = $isActive;
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
	 * Returns the boolean state of autoRefresh
	 *
	 * @return boolean
	 */
	public function isAutoRefresh() 
	{
		return $this->getAutoRefresh();
	}

	/**
	 * Returns the userDefined
	 *
	 * @return boolean $userDefined
	 */
	public function getUserDefined() 
	{
		return $this->userDefined;
	}

	/**
	 * Sets the userDefined
	 *
	 * @param boolean $userDefined
	 * @return void
	 */
	public function setUserDefined($userDefined) 
	{
		$this->userDefined = $userDefined;
	}

	/**
	 * Returns the boolean state of userDefined
	 *
	 * @return boolean
	 */
	public function isUserDefined() 
	{
		return $this->getUserDefined();
	}
	
	/**
	 * Initializes all ObjectStorage properties.
	 *
	 * @return void
	 */
	protected function initStorageObjects() 
	{
		/**
		 * Do not modify this method!
		 * It will be rewritten on each save in the extension builder
		 * You may modify the constructor of this class instead
		 */
		$this->products 	= new \TYPO3\CMS\Extbase\Persistence\ObjectStorage();
		$this->attributes	= new \TYPO3\CMS\Extbase\Persistence\ObjectStorage();
	}

	/**
	 * Returns the store
	 *
	 * @return \MageDeveloper\Magelink\Domain\Model\Store $store
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
	 * Returns the products
	 *
	 * @return \array
	 */
	public function getProducts() 
	{
		if ($this->products)
		{
			return explode(',', $this->products);
		}
	
		return array();
	}

	/**
	 * Get according product ids
	 * 
	 * @return \string
	 */
	public function getProductIds()
	{
		return $this->products;
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
	 * Gets all child ids
	 * 
	 * @return \array
	 */
	public function getChildIds()
	{
		return $this->getAttributeValue("children");
	}

	/**
	 * Gets the category depth level
	 * 
	 * @return \int
	 */
	public function getLevel()
	{
		return (int)$this->getAttributeValue("level");
	}

	/**
	 * Checks if the category is root
	 * 
	 * @return \bool
	 */
	public function getIsRoot()
	{
		if ($this->getParent() == 1)
		{
			return true;
		}
		
		return false;
	}

}
?>