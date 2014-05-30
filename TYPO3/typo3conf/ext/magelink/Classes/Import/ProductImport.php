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
 * Product Import Model
 * @package magelink
 * @license http://www.gnu.org/licenses/gpl.html GNU General Public License, version 3 or later
 *
 */
class ProductImport extends \MageDeveloper\Magelink\Import\AbstractImport
{
	/**
	 * Api Product Request
	 *
	 * @var \MageDeveloper\Magelink\Api\Requests\Product
	 * @inject
	 */
	protected $productRequest;

	/**
	 * Magento Product Data Model
	 *
	 * @var \MageDeveloper\Magelink\Magento\Data\ProductData
	 * @inject
	 */
	protected $magentoProductData;
	
	/**
	 * productRepository
	 *
	 * @var \MageDeveloper\Magelink\Domain\Repository\ProductRepository
	 * @inject
	 */
	protected $productRepository;

	/**
	 * productfilterRepository
	 *
	 * @var \MageDeveloper\Magelink\Domain\Repository\ProductfilterRepository
	 * @inject
	 */
	protected $productfilterRepository;

	/**
	 * Get a product repository by ids
	 * If it exists, it will directly come from database,
	 * either it will be imported
	 *
	 * @param \array $ids
	 * @param \string $storeViewCode Store View Code
	 * @param \bool $reloadAll Reload all products
	 * @param \bool $onlyEnabled Get only enabled products
	 * @internal param array $id Product Ids
	 * @return \TYPO3\CMS\Extbase\Persistence\Generic\QueryResult|false
	 */
	public function getProductRepositoryByIds($ids, $storeViewCode = "", $reloadAll = false, $onlyEnabled = true)
	{
		if (empty($ids) || !is_array($ids))
		{
			// Return fresh and empty repository
			//return $this->objectManager->get("MageDeveloper\\Magelink\\Domain\\Repository\\ProductRepository");
		}

		$products = $this->productRepository->findByEntityIds($ids, $storeViewCode, false);
		$importIds = array();
		$visible = array();

		// Do we need to reload all products?
		if ($reloadAll === false)
		{
			$found = array();
			foreach ($products as $_product)
			{
				// Refreshing
				if ($_product && !$_product->getAutoRefresh())
				{
					$found[] = $_product->getEntityId();
				}

				// Only visible
				if (!$_product->getHidden())
				{
					$visible[] = $_product->getEntityId();
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
			return $this->productRepository->findByEntityIds($visible, $storeViewCode, $onlyEnabled);
		}
		
		// We try to import all missing products
		if ($this->importProductsByIdAction($importIds, $storeViewCode))
		{
			// If the import worked, we fetch the ids from the database
			return $this->productRepository->findByEntityIds($ids, $storeViewCode, $onlyEnabled);
		}

		return $this->objectManager->get("MageDeveloper\\Magelink\\Domain\\Repository\\ProductRepository");
	}

	/**
	 * Get a product by id
	 * If it exists, it will directly come from database,
	 * either it will be imported
	 *
	 * @param int $id Product Id
	 * @param string $storeViewCode Store View Code
	 * @param bool $reload Reload from Webservice
	 * @return \MageDeveloper\Magelink\Domain\Model\Product
	 */
	public function getProductById($id, $storeViewCode = "", $reload = false)
	{
		$product = $this->productRepository->findByEntityId($id, $storeViewCode, false);

		if ($product instanceof \MageDeveloper\Magelink\Domain\Model\Product && !$product->getAutoRefresh() && $reload === false)
		{
			return $product;
		}

		// No Product was found in database, or it wants to be auto-refreshed, so we need to import it
		if ($this->importProductsByIdAction(array($id), $storeViewCode))
		{
			// If the import worked, we fetch it from the database
			return $this->productRepository->findByEntityId($id, $storeViewCode, false);
		}

		return $this->objectManager->get("MageDeveloper\\Magelink\\Domain\\Model\\Product");
	}
	
	/**
	 * Get product ids filtered by tags and/or categories and skus
	 *
	 * @param \array $tags Array with Tag Names
	 * @param \array $categories Array with Category Names
	 * @param \array $skus Array with Skus
	 * @param \string $storeViewCode Store View Code
	 * @param \bool $reloadAll Reload all products
	 * @return \array
	 */
	public function getProductIdsByFilter(array $tags, array $categories, array $skus, $storeViewCode = "",  $reloadAll = false)
	{
		$filter = $this->productfilterRepository->findByFilters($tags, $categories, $skus, $storeViewCode);

		if ($reloadAll === false && $filter && !$filter->getAutoRefresh())
		{
			// We want to use product ids from a existing filter
			return $filter->getProducts();
		}
		else
		{
			$productIds = $this->fetchProductIdsByFilter($tags, $categories, $skus, $storeViewCode);
			$this->saveProductfilterByData($tags, $categories, $skus, $productIds, $storeViewCode);
			
			return $productIds;
		}
		
		return array();
	}

	/**
	 * Import products from webservice
	 *
	 * @param \array $ids Array of Entity Ids to import
	 * @param \string $storeViewCode Store View Code
	 * @return \bool
	 */
	public function importProductsByIdAction(array $ids, $storeViewCode = "")
	{
		$progress = array();

		foreach ($ids as $_id)
		{
			$product = $this->fetchProductById($_id, $storeViewCode);

			if ($product)
			{
				if ($this->saveProductByData($product))
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
	 * Save a product by data
	 * If the product does not exist in database,
	 * we create it
	 *
	 * @param \array $data Data for Object
	 * @return \bool
	 */
	public function saveProductByData(array $data)
	{
		// We try to load product from database
		$product = $this->productRepository->findByEntityId($data["entity_id"], $data["store_view_code"], false);
		
		if (!$product)
		{
			$model = $this->objectManager->get("MageDeveloper\\Magelink\\Domain\\Model\\Product");

			// We need to save the product to database
			$model = $this->mergeProductData($data, $model);

			if ($model instanceof \MageDeveloper\Magelink\Domain\Model\Product)
			{
				// If product is disabled we directly hide it
				if ($model->getIsDisabled() === true)
				{
					$model->setHidden(true);
				}
			
				$this->productRepository->add($model);
				$this->persistenceManager->persistAll();

				if ($model->getUid())
				{
					return true;
				}

			}

		}
		else
		{
			// We need to update the product to the database
			$model = $this->mergeProductData($data, $product);

			if ($model instanceof \MageDeveloper\Magelink\Domain\Model\Product)
			{
				$this->productRepository->update($model);
				$this->persistenceManager->persistAll();

				return true;
			}
		}

		return false;
	}

	/**
	 * Save a product filter by data
	 * If the productfilter does not exist in database,
	 * we create it
	 *
	 * @param \array $tags Array with Tags
	 * @param \array $categories Array with Categories
	 * @param \array $skus Array with Skus
	 * @param \array $productIds Array with product ids
	 * @param \string $storeViewCode Store View Code
	 * @return \bool
	 */
	public function saveProductfilterByData(array $tags, array $categories, array $skus, $productIds, $storeViewCode = "")
	{
		// We try to load productfilter from database
		$filter = $this->productfilterRepository->findByFilters($tags, $categories, $skus, $storeViewCode);
		
		if (!$filter)
		{
			$model = $this->objectManager->get("MageDeveloper\\Magelink\\Domain\\Model\\Productfilter");

			// We need to save the productfilter to database
			$model = $this->mergeProductfilterData($tags, $categories, $skus, $productIds, $model, $storeViewCode);

			if ($model instanceof \MageDeveloper\Magelink\Domain\Model\Productfilter)
			{
				$this->productfilterRepository->add($model);
				$this->persistenceManager->persistAll();

				if ($model->getUid())
				{
					return true;
				}

			}

		}
		else
		{
			// We need to update the productfilter to the database
			$model = $this->mergeProductfilterData($tags, $categories, $skus, $productIds, $filter, $storeViewCode);

			if ($model instanceof \MageDeveloper\Magelink\Domain\Model\Productfilter)
			{
				$this->productfilterRepository->update($model);
				$this->persistenceManager->persistAll();

				return true;
			}
		}

		return false;
	}

	/**
	 * Get a product model by given product data
	 *
	 * @param \array $data Product Data
	 * @param \MageDeveloper\Magelink\Domain\Model\Product $productModel
	 * @return \MageDeveloper\Magelink\Domain\Model\Product
	 */
	public function mergeProductData(array $data, \MageDeveloper\Magelink\Domain\Model\Product $productModel)
	{
		if ($this->settingsService->getStoragePid())
		{
			$productModel->setPid( $this->settingsService->getStoragePid() );
		}
		
		$productModel->setEntityId( (int)$data["entity_id"] );
		unset($data["entity_id"]);
		unset($data["product_id"]);
		
		$productModel->setSku( $data["sku"] );
		unset($data["sku"]);
		
		$productModel->setName( $data["name"] );
		unset($data["name"]);
		
		$productModel->setShortDescription( $data["short_description"] );
		unset($data["short_description"]);
		
		$productModel->setDescription( $data["description"] );
		unset($data["description"]);
		
		// Which field for price?
		$productModel->setPrice( $data["price"] );
		unset($data["price"]);
		
		$productModel->setSpecialPrice( $data["special_price"] );
		unset($data["special_price"]);

		$productModel->setFinalPrice( $data["final_price"] );
		unset($data["final_price"]);
		
		$productModel->setQty( $data["qty"] );
		unset($data["qty"]);
		
		$productModel->setIsDisabled( (bool)$data["is_disabled"] );
		unset($data["is_disabled"]);
		
		$status		= (int)$data["status"];
		if ($status		== \MageDeveloper\Magelink\Domain\Model\Product::STATUS_DISABLED) 
		{
			$productModel->setHidden( true );
		}
		else
		{
			$productModel->setHidden( false );
		}

		$productModel->setManageStock( (bool)$data["manage_stock"] );
		unset($data["manage_stock"]);
		
		$productModel->setStore( $data["store_view_code"] );
		unset($data["store_view_code"]);
		
		$productModel->setCurrency( $data["currency"] );
		unset($data["currency"]);

		unset($data["image"]);
		unset($data["small_image"]);
		unset($data["thumbnail"]);
		
		// Import product images
		if (array_key_exists("media_gallery", $data))
		{
			$data["media"] = array(); $i = 0;
			foreach ($data["media_gallery"]["images"] as $_image)
			{
				$srcUrl 	= $this->settingsService->getMediaUrl() .'/'."product".'/'.$_image["file"];
				$target 	= $this->settingsService->getImportFilePath().'/';
				$filename 	= basename($_image["file"]);
				
				if ($this->imageService->importImage($srcUrl, $target, $filename)) 
				{
					$saveFilename = \MageDeveloper\Magelink\Service\ImageService::getCleanPath($target) . $filename;
					
					if ($i > 0)
					{
						$data["media"][] = $saveFilename;
					}
					else
					{
						$productModel->setImage($saveFilename);
						//$data["image"] = $saveFilename;
					}
					
					$i++;
					
				}
				
			}
			
		}
		unset($data["media_gallery"]);
		
		// Update found attribute values
		$found = array();
		foreach($data as $_code=>$_value)
		{
			$value = json_encode($_value);
			
			foreach ($productModel->getAttributes() as $_attribute)
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
				
				$attribute = $this->createAttribute($_code, $value, $productModel);
			
				if ($attribute instanceof \MageDeveloper\Magelink\Domain\Model\Attribute)
				{
					$productModel->addAttribute($attribute);
				}
			}	
		}
		
		return $productModel;
	}

	/**
	 * Creates an attribute by code and value
	 * 
	 * @param \string $attributeCode Code of the attribute
	 * @param \string $attributeValue Value of the attribute
	 * @param \MageDeveloper\Magelink\Domain\Model\Product $product Product Model
	 * @return \MageDeveloper\Magelink\Domain\Model\Attribute
	 */
	public function createAttribute($attributeCode, $attributeValue = "", \MageDeveloper\Magelink\Domain\Model\Product $product)
	{
		$attribute = $this->objectManager->get("MageDeveloper\\Magelink\\Domain\\Model\\Attribute");

		if ($this->settingsService->getStoragePid())
		{
			$attribute->setPid( $this->settingsService->getStoragePid() );
		}
		
		$attribute->setCode($attributeCode);
		$attribute->setValue($attributeValue);
		$attribute->setRelationProduct($product);
		
		return $attribute;
	}

	/**
	 * Get a productfilter model by given filter data
	 *
	 * @param \array $tags Array with Tags
	 * @param \array $categories Array with Categories
	 * @param \array $skus Array with Skus
	 * @param \array $productIds Array with Product Ids
	 * @param \MageDeveloper\Magelink\Domain\Model\Productfilter $productfilterModel
	 * @param \string $storeViewCode Store View Code
	 * @return \MageDeveloper\Magelink\Domain\Model\Productfilter
	 */
	public function mergeProductfilterData(array $tags, array $categories, array $skus, array $productIds, \MageDeveloper\Magelink\Domain\Model\Productfilter $productfilterModel, $storeViewCode = "")
	{
		if ($this->settingsService->getStoragePid())
		{
			$productfilterModel->setPid( $this->settingsService->getStoragePid() );
		}
		
		$productfilterModel->setTags( implode(',', $tags) );
		$productfilterModel->setCategories( implode(',', $categories) );
		$productfilterModel->setProducts( implode(',', $productIds) );
		$productfilterModel->setSkus( implode(',', $skus));
		$productfilterModel->setStore( $storeViewCode );
		
		return $productfilterModel;
	}















	/**
	 * Fetches an product by an id
	 * Defines the way to get the product by comparing
	 * settings
	 * 
	 * @param int $id Product Id
	 * @param $store Store View Code
	 * @return array|bool
	 */
	public function fetchProductById($id, $store)
	{
		if ($this->settingsService->isMagentoLocal())
		{
			return $this->magentoProductData->getProductById($id, $store);
		}
		
		return $this->productRequest->getProductById($id, $store);
	}

	/**
	 * Fetches an product list
	 * Defines the way to get the product list by comparing
	 * settings
	 *
	 * @param $store Store View Code
	 * @return array|bool
	 */
	public function fetchProductList($store)
	{
		if ($this->settingsService->isMagentoLocal())
		{
			return $this->magentoProductData->getProductList($store);
		}
		
		return $this->productRequest->getProductList($store);
	}

	/**
	 * Fetches product ids from given filters
	 * Defines the way to get the product id list by comparing
	 * settings
	 *
	 * @param array $tags Tags to filter
	 * @param array $categories Categories to filter
	 * @param array $skus Skus to filter
	 * @param $store Store View Code
	 * @return array|bool
	 */
	public function fetchProductIdsByFilter(array $tags, array $categories, array $skus, $store)
	{
		if ($this->settingsService->isMagentoLocal())
		{
			return $this->magentoProductData->getProductsByFilter($tags, $categories, $skus, $store);
		}

		return $this->productRequest->getProductsByFilter($tags, $categories, $skus, $store);
	}
}
	