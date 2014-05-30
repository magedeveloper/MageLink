<?php
/**
 * MageDeveloper MageLink Module
 * ---------------------------------
 *
 * @category    Mage
 * @package     MageDeveloper_MageLink
 * @copyright   Magento Developers / magedeveloper.de <kontakt@magedeveloper.de>
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
class MageDeveloper_MageLink_Model_Category_Api extends Mage_Api_Model_Resource_Abstract
{
	/**
	 * Fetch category details
	 * 
	 * @param string $store Store View Code
	 * @return json_encoded Categories
	 */
	public function fetch($store = null)
	{
		// Fetch store front store code
		$storeCode = ($store == null)?'default':$store;
		$store = Mage::app()->getStore($storeCode);		
		
		// Get root category id from store
		$rootId = $store->getRootCategoryId();
		
		// Get root category model
		$category = Mage::getModel('catalog/category')
						->setStoreId($store->getId())
						->load($rootId);		
		
		if ($category instanceof Mage_Catalog_Model_Category)
		{
			// Fetch all available subcategories from the root category
			$subcategories = $this->_getSubcategories($category, $store->getId());	
			
			// Prepare result
			$result = array();
			$result['ROOT'] = array();
			$result['ROOT'] = $this->_getCategoryData($category->getId(), $store->getId());
			$result = array_merge($result, $subcategories);
			
			if (!empty($result)) {
				return json_encode( $result );
			}
		}
		
		return false;
	}
	
	/**
	 * Fetch single category details
	 * 
	 * @param int $categoryId Id of the category to fetch
	 * @param string $store Store View Code
	 * @return json_encoded Category
	 */
	public function detail($categoryId, $store)
	{
		// Fetch store front store code
		$storeCode = ($store == null)?'default':$store;
		$store = Mage::app()->getStore($storeCode);		
		
		// Get root category model
		$category = Mage::getModel('catalog/category')
						->setStoreId($store->getId())
						->load($categoryId);		
		
		// Fetch all available subcategories from the root category
		//$subcategories = $this->_getSubcategories($category, $store->getId());	
		
		// Prepare result
		$result = array();
		$result = $this->_getCategoryData($category->getId(), $store->getId());
		//$result = array_merge($result, $subcategories);
		
		if (!empty($result)) {
			return json_encode( $result );
		}
		return false;
		
	}
	
	
	/**
	 * Gets available category children ids
	 * 
	 * @param Mage_Catalog_Model_Category $category Category Model
	 * @return array
	 */
	public function _getCategoryChildren(Mage_Catalog_Model_Category $category)
	{
		$children = array();
		
		foreach ($category->getChildrenCategories() as $_child)
		{
			$children[] = $_child->getId();
		}
		
		return $children;
	}
	
	
	/**
	 * _getSubcategories
	 * Get all subcategories from a given category model
	 * 
	 * @param Mage_Catalog_Model_Category $category Category Model
	 * @param int $storeId Id of the store
	 * @return array
	 */
	public function _getSubcategories(Mage_Catalog_Model_Category $category, $storeId)
	{
		$subcategoryArr 	= array();	
		
		$_subcategories = $category->getChildrenCategories();
		
		if (count($_subcategories) > 0) 
		{
			foreach ($_subcategories as $_subcategory) 
			{
				$categoryData = $this->_getCategoryData($_subcategory->getId(), $storeId);
			
				$subcategoryArr[] = $categoryData;
			
				if ($subcats = $this->_getSubcategories($_subcategory, $storeId)) 
				{
					$subcategoryArr = array_merge($subcategoryArr, $subcats);
				}
				
			}
			
		}
		
		return $subcategoryArr;
	}
	
	/**
	 * Gets category data as an array
	 * 
	 * @param int $categoryId Category Id
	 * @param int $storeId Id of the store
	 * @return array
	 */
	protected function _getCategoryData($categoryId, $storeId)
	{
		$category = Mage::getModel('catalog/category')
						->setStoreId($storeId)
						->load($categoryId);

		// We want the product ids of the category
		$productcollection = $category->getProductCollection();
		$productIds = array();
		foreach ($productcollection as $_product) 
		{
			$productIds[] = $_product->getId();
		}

		$data = array();

		// If category is allowed in the navigation menu
		if ($category->getIncludeInMenu()) 
		{
			$categoryData = array(
				'entity_id'		=> $category->getId(),
				'name'			=> $category->getName(),
				'description'	=> $category->getDescription(),
				'page_title'	=> $category->getPageTitle(),
				'thumbnail'		=> $category->getThumbnail(),
				'url'			=> Mage::helper('catalog/category')->getCategoryUrl($category),
				'url_key'		=> $category->getUrlKey(),
				'url_path'		=> $category->getUrlPath(),
				'product_count'	=> $category->getProductCount(),
				'product_ids'	=> implode(',', $productIds),
				'children'		=> $category->getChildren(),
				'path'			=> $category->getPath(),
			);
					
			$data = array_merge($category->getData(), $categoryData);
				
		}
		
		return $data;
	}

	
	
}