<?php
namespace MageDeveloper\Magelink\Api\Requests;


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
use MageDeveloper\Magelink\Domain\Model\AbstractObject;

/**
 *
 * API Product Calls
 * @package magelink
 * @license http://www.gnu.org/licenses/gpl.html GNU General Public License, version 3 or later
 *
 */
class Product extends \MageDeveloper\Magelink\Api\Requests\AbstractRequest
{
	/**
	 * Get a product by a given id
	 *
	 * @param \int $productId Id of the product
	 * @param \string $storeViewCode Store View Code
	 * @param \string $productIdentifierType Product Identifier Type (sku/null)
	 * @throws \Exception
	 * @return \array
	 */
	public function getProductById($productId, $storeViewCode = "", $productIdentifierType = "")
	{
		try 
		{
			if ( $this->getApiClient()->connect() )
			{
				$data = $this->getApiClient()->getResource()->magelinkProductFetch(
					$this->getApiClient()->getSessionId(), // Session Id
					(int)$productId,
					$storeViewCode,
					$productIdentifierType
				);
				
				if ($data)
				{
					$productData = json_decode($data, true);
					$productData["store_view_code"] = $storeViewCode;

					return $productData;
				}
				
			}
			
		}
		catch (\Exception $e)
		{
			throw new \Exception ("Could not retrieve product details! Error: " . $e->getMessage());
		}
		
		return false;
	}

	/**
	 * Get a product list
	 *
	 * @param \string $storeViewCode Store View Code
	 * @param \array $filters Array of filters by attributes
	 * @throws \Exception
	 * @return \array|false
	 */
	public function getProductList($storeViewCode = "", $filters = array())
	{
		/*$filters = array(
			'complex_filter' => array(
				array(
					'key' => 'type',
					'value' => array('key' => 'in', 'value' => 'simple')
				)
			)
		);*/
		
		try 
		{
			if ( $this->getApiClient()->connect() )
			{
				$data = $this->getApiClient()->getResource()->magelinkProductItems(
					$this->getApiClient()->getSessionId(), // Session Id
					$filters,	
					$storeViewCode // Store View Code
				);
				
				if ($data)
				{
					$productData = json_decode($data, true);
					return $productData;
				}
				
			}	
			
			
		} 
		catch (\Exception $e) 
		{
			throw new \Exception ("Could not retrieve product list! Error: " . $e->getMessage());
		}
		
		return false;
	}

	/**
	 * Gets an array of product ids by tags and categories
	 * 
	 * @param \array $tags Array with Tags
	 * @param \array $categories Array with Categories
	 * @param \array $skus Array with Skus
	 * @param \string $storeViewCode Store View Code
	 * @throws \Exception
	 * @return \array|false
	 */
	public function getProductsByFilter(array $tags = array(), array $categories = array(), array $skus = array(), $storeViewCode = "")
	{
		try
		{
			if ( $this->getApiClient()->connect() )
			{
				$data = $this->getApiClient()->getResource()->magelinkProductFilter(
					$this->getApiClient()->getSessionId(), // Session Id
					$tags,          // Array with Tags
					$categories,    // Array with Categories
					$skus,			// Array with Sku's
					$storeViewCode  // Store View Code
				);

				if (is_array($data))
				{
					return $data;
				}

			}


		}
		catch (\Exception $e)
		{
			throw new \Exception ("Could not retrieve product list! Error: " . $e->getMessage());
		}

		return false;
		
	}
	
	
	

		
}
	