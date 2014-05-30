<?php
namespace MageDeveloper\Magelink\Import;

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
 * Database Controller
 * @package magelink
 * @license http://www.gnu.org/licenses/gpl.html GNU General Public License, version 3 or later
 *
 */
class CategoryImport extends \MageDeveloper\Magelink\Import\AbstractImport
{
	/**
	 * categoryRepository
	 *
	 * @var \MageDeveloper\Magelink\Domain\Repository\CategoryRepository
	 * @inject
	 */
	protected $categoryRepository;
	
	/**
	 * Api Category Request
	 * 
	 * @var \MageDeveloper\Magelink\Api\Requests\Category
	 * @inject
	 */
	protected $categoryRequest;

	/**
	 * Magento Category Data Model
	 *
	 * @var \MageDeveloper\Magelink\Magento\Data\CategoryData
	 * @inject
	 */
	protected $magentoCategoryData;
	
	/**
	 * Get a category by id
	 * If it exists, it will directly come from database,
	 * either it will be imported
	 * 
	 * @param \int $id Category Id
	 * @param \string $storeViewCode Store View Code
	 * @param \bool $reload Reload from Webservice
	 * @return \MageDeveloper\Magelink\Domain\Model\Category
	 */
	public function getCategoryById($id, $storeViewCode = "", $reload = false)
	{
		$category = $this->categoryRepository->findByEntityId($id, $storeViewCode);
		
		
		if ($category && !$category->getAutoRefresh() && $reload === false)
		{
			return $category;
		}

		// No Category was found in database, or it wants to be auto-refreshed, so we need to import it
		if ($this->importCategoriesByIdAction(array($id), $storeViewCode))
		{
			// If the import worked, we fetch it from the database
			return $this->categoryRepository->findByEntityId($id, $storeViewCode);
		}

		return $this->objectManager->get("MageDeveloper\\Magelink\\Domain\\Model\\Category");
	}

	/**
	 * Get a category repository by ids
	 * If it exists, it will directly come from database,
	 * either it will be imported
	 *
	 * @param \array $ids Category Ids
	 * @param \string $storeViewCode Store View Code
	 * @param \bool $reload Reload all products
	 * @return \MageDeveloper\Magelink\Domain\Model\Product|false
	 */
	public function getCategoryRepositoryByIds($ids, $storeViewCode = "", $reload = false)
	{
		if (empty($ids) || !is_array($ids))
		{
			// Return fresh and empty repository
			return $this->objectManager->get("MageDeveloper\\Magelink\\Domain\\Repository\\CategoryRepository");
		}

		$categories = $this->categoryRepository->findByEntityIds($ids, $storeViewCode);
		$importIds = array();

		// Do we need to reload all products?
		if ($reload === false)
		{
			$found = array();
			foreach ($categories as $_category)
			{
				if ($_category && !$_category->getAutoRefresh())
				{
					$found[] = $_category->getEntityId();
				}
			}

			$importIds = array_diff($ids, $found);
		}
		else
		{
			$importIds = $ids;
		}
		
		// All products were found
		if (empty($importIds))
		{
			return $categories;
		}
		// We try to import all missing products
		if ($this->importCategoriesByIdAction($importIds, $storeViewCode))
		{
			// If the import worked, we fetch the ids from the database
			return $this->categoryRepository->findByEntityIds($ids, $storeViewCode);
		}

		return $this->objectManager->get("MageDeveloper\\Magelink\\Domain\\Repository\\CategoryRepository");
	}

	/**
	 * Get all according category ids
	 * 
	 * @param \int $categoryId Id of the category
	 * @param \string $storeViewCode Store View Code
	 * @param \bool $reload Reload from webservice
	 * @return \array
	 */
	public function getAccordingCategoryIds($categoryId, $storeViewCode = "", $reload = false)
	{
		$category 	= $this->getCategoryById($categoryId, $storeViewCode, $reload);
		
		if ($category && $category instanceof \MageDeveloper\Magelink\Domain\Model\Category)
		{
			$children	= explode(',', $category->getChildIds());
			
			// We also need the parent id
			if ($category->getParent())
			{
				$children[] = $category->getParent();
			}
			
			return $children;
		}
		
		return array();
	}
	
	/**
	 * Import categories from webservice
	 * 
	 * @param \array $ids Array of Entity Ids to import
	 * @param \string $storeViewCode Store View Code
	 * @return bool
	 */
	public function importCategoriesByIdAction(array $ids, $storeViewCode = "")
	{
		$progress = array();
		
		foreach ($ids as $_id)
		{
			$category = $this->fetchCategoryById($_id, $storeViewCode);
			
			// Fallback (no store view code)
			if (!$category)
			{
				$category = $this->fetchCategoryById($_id, '');
			}
			
			if ($category && $category["entity_id"] == $_id)
			{
				if ($this->saveCategoryByData($category))
				{
					// We add the id to the progress, when the import was ok
					$progress[] = $_id;
				}
				
			}
			
		}
		
		$diff = array_diff($ids, $progress);
		
		return empty($diff);
	}	
	
	/**
	 * Save a category by data
	 * If the category does not exist in database,
	 * we create it
	 *
	 * @param \array $data Data for Object
	 * @return \bool
	 */
	public function saveCategoryByData(array $data)
	{
		// We try to load category from database
		$category = $this->categoryRepository->findByEntityId($data["entity_id"], $data["store_view_code"]);
	
		if (!$category)
		{
			$model = $this->objectManager->get("MageDeveloper\\Magelink\\Domain\\Model\\Category");
			
			// We need to save the category to database
			$model = $this->mergeCategoryData($data, $model);
			
			if ($model instanceof \MageDeveloper\Magelink\Domain\Model\Category)
			{
				// If category isn't active, we hide it
				if ($model->getIsActive() === false)
				{
					$model->setHidden(true);
				}
			
				$this->categoryRepository->add($model);	
				$this->persistenceManager->persistAll();
				
				if ($model->getUid())
				{
					return true;
				}
				
			}
			
		}
		else
		{
			// We need to update the category to the database
			$model = $this->mergeCategoryData($data, $category);
			
			if ($model instanceof \MageDeveloper\Magelink\Domain\Model\Category)
			{
				$this->categoryRepository->update($model);	
				$this->persistenceManager->persistAll();
				
				return true;
			}
		}
		
		return false;
	}

	/**
	 * Merge category data with a category model
	 * 
	 * @param \array $data Category Data
	 * @param \MageDeveloper\Magelink\Domain\Model\Category $categoryModel
	 * @return \MageDeveloper\Magelink\Domain\Model\Category
	 */
	public function mergeCategoryData(array $data, \MageDeveloper\Magelink\Domain\Model\Category $categoryModel)
	{
		if ($this->settingsService->getStoragePid())
		{
			$categoryModel->setPid( $this->settingsService->getStoragePid() );
		}
		
		$categoryModel->setEntityId( (int)$data["entity_id"] );		
		unset($data["entity_id"]);

		$categoryModel->setParent( $data["parent_id"] );
		unset($data["parent_id"]);
				
		$categoryModel->setName( $data["name"] );
		unset($data["name"]);
		
		$categoryModel->setDescription( $data["description"] );
		unset($data["description"]);
		
		$categoryModel->setPageTitle( $data["page_title"] );
		unset($data["page_title"]);
		
		$categoryModel->setUrl( $data["url"] );
		unset($data["url"]);
		
		$categoryModel->setUrlPath( $data["url_path"] );
		unset($data["url_path"]);
		
		$categoryModel->setProductCount( $data["product_count"] );
		unset($data["product_count"]);
		
		$categoryModel->setStore( $data["store_view_code"] );
		unset($data["store_view_code"]);
		
		$categoryModel->setProducts( $data["product_ids"] );
		unset($data["product_ids"]);
		
		$categoryModel->setSorting( (int)$data["position"] );
		unset($data["position"]);
		
		$categoryModel->setIsActive( (bool)$data["is_active"] );
		unset($data["is_active"]);

		// Import product images
		if (array_key_exists("image", $data) && $data["image"] != "")
		{
			$srcUrl 	= $this->settingsService->getMediaUrl() .'/'."category".'/'. $data["image"];
			$target 	= $this->settingsService->getImportFilePath().'/';
			$filename 	= basename($data["image"]);

			if ($this->imageService->importImage($srcUrl, $target, $filename))
			{
				$saveFilename = \MageDeveloper\Magelink\Service\ImageService::getCleanPath($target) . $filename;
				$categoryModel->setImage( $saveFilename );
			}
			unset($data["image"]);
		}
	
		// Update found attribute values
		$found = array();
		foreach($data as $_code=>$_value)
		{
			$value = json_encode($_value);
	
			foreach ($categoryModel->getAttributes() as $_attribute)
			{
				if($_attribute->getCode() == $_code)
				{
					$_attribute->setValue($value);
					$found[] = $_code;
				}
	
			}
	
		}
	
		// Attributes that we need to create
		$needToCreate = array();
		$needToCreate = array_diff(array_keys($data), $found);
		foreach($needToCreate as $_code)
		{
			if (array_key_exists($_code, $data))
			{
				$value = json_encode($data[$_code]);
				$attribute = $this->createAttribute($_code, $value, $categoryModel);
	
				if ($attribute instanceof \MageDeveloper\Magelink\Domain\Model\Attribute)
				{
					$categoryModel->addAttribute($attribute);
				}
	
			}
			
		}

		return $categoryModel;
	}

	/**
	 * Creates an attribute by code and value
	 *
	 * @param \string $attributeCode Code of the attribute
	 * @param \string $attributeValue Value of the attribute
	 * @param \MageDeveloper\Magelink\Domain\Model\Category $category Category Model
	 * @return \MageDeveloper\Magelink\Domain\Model\Attribute
	 */
	public function createAttribute($attributeCode, $attributeValue = "", \MageDeveloper\Magelink\Domain\Model\Category $category)
	{
		$attribute = $this->objectManager->get("MageDeveloper\\Magelink\\Domain\\Model\\Attribute");

		if ($this->settingsService->getStoragePid())
		{
			$attribute->setPid( $this->settingsService->getStoragePid() );
		}

		$attribute->setCode($attributeCode);
		$attribute->setValue($attributeValue);
		$attribute->setRelationCategory($category);

		return $attribute;
	}

	/**
	 * Fetches an category by an id
	 * Defines the way to get the category by comparing
	 * settings
	 *
	 * @param int $id Category Id
	 * @param $store Store View Code
	 * @return array|bool
	 */
	public function fetchCategoryById($id, $store)
	{
		if ($this->settingsService->isMagentoLocal())
		{
			return $this->magentoCategoryData->getCategoryById($id, $store);
		}

		return $this->categoryRequest->getCategoryById($id, $store);
	}

	/**
	 * Fetches an category list
	 * Defines the way to get the category list by comparing
	 * settings
	 *
	 * @param $store Store View Code
	 * @return array|bool
	 */
	public function fetchCategoryList($store)
	{
		if ($this->settingsService->isMagentoLocal())
		{
			return $this->magentoCategoryData->getCategoryList($store);
		}

		return $this->categoryRequest->getCategoryList($store);
	}	
		
}
	