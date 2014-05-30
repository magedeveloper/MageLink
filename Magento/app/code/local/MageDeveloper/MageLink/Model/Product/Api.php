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
class MageDeveloper_MageLink_Model_Product_Api extends Mage_Catalog_Model_Api_Resource
{
	/**
	 * Fetch product details
	 * 
	 * @param string|int $productId Id of the product
	 * @param string $store Store View Code
	 * @param string $identifierType Identifier Type (null|sku)
	 * @return json_encodes product details
	 */
	public function fetch($productId, $store = null, $identifierType = null)
	{
 		$product = $this->_getProduct($productId, $store, $identifierType);
		$storeId = $this->_getStoreId($store);
		
		if ($product->getId() == $productId) 
		{
			$data = array();
			$data = $product->getData();
			
			//$data["COPY1"] = array_keys($product->getAttributes());
			//$data["COPY2"] = array_keys($data);
			//$data["COPY"] = array_diff(array_keys($data), array_keys($product->getAttributes()));
		
			
			// Attributes
			$attributeData = array();
			$attributes = $product->getAttributes();
			
			foreach ($attributes as $attribute) 
			{
			    if ($attribute->getIsVisibleOnFront()) 
			    {
			        $value = $attribute->getFrontend()->getValue($product);
					if ($value == '') 
					{
						$value = $attribute->getDefaultValue();
					}
					
			        $attributeData[] = array(	'key'	=> $attribute->getAttributeCode(),
			        							'value' => $value,
			        							'label'	=> $attribute->getFrontendLabel(),
									   );
					
					// We don't need the additional attributes in our product array anymore
					unset($data[$attribute->getAttributeCode()]);
			    }

				// The whole rest (user defined attributes) 
				if ($attribute->getIsUserDefined()) //&& !$attribute->getIsVisibleOnFront())
				{
					$data["user_defined_attributes"][$attribute->getAttributeCode()] = array(
						"label"		=> $attribute->getFrontend()->getLabel(),
						"code"		=> $attribute->getAttributeCode(),
						"value"		=> $product->getAttributeText($attribute->getAttributeCode()),
					);
				}
			    
			}
			
			// Add additional information to the final data
			$data["qty"] 			= (int)Mage::getModel('cataloginventory/stock_item')->loadByProduct($product)->getQty();
			$data["final_price"]	= $product->getFinalPrice();
			$data["manage_stock"]	= $product->getStockItem()->getManageStock();
			$data["attributes"]		= $attributeData;
			$data["upsell_ids"]		= implode(',', $product->getUpSellProductIds());
			$data["crosssell_ids"]	= implode(',', $product->getCrossSellProductIds());
			
			$currencyCode = Mage::app()->getStore($storeId)->getCurrentCurrencyCode();
			$symbol = Mage::app()->getLocale()->currency($currencyCode)->getSymbol();
			$data["currency_symbol"] = html_entity_decode($symbol);
			$data["currency"]		 = $currencyCode;
			
			// Configurable product
			if ($product->isConfigurable())
			{
				$data["options"] 				= $this->getConfigByProduct($productId, $store, $identifierType);
				$data["associated_products"] 	= implode(',', $product->getTypeInstance()->getUsedProductIds());
			}
			
			// Grouped product
			if ($product->isGrouped())
			{
				$assoc = array();
				
				$associatedProducts = $product->getTypeInstance()->getAssociatedProducts($product);
				
				$minimalPrice = null;
				foreach ($associatedProducts as $_assocItem)
				{
					$assoc[$_assocItem->getId()] = array(
						"id"    => $_assocItem->getId(),
						"qty"   => (int)$_assocItem->getQty(),
						"price"	=> $_assocItem->getPrice(),
					);
					
					if ($minimalPrice === null)
					{
						$minimalPrice = $_assocItem->getPrice();
					}
					
					if ($_assocItem->getPrice() < $minimalPrice)
					{
						$minimalPrice = $_assocItem->getPrice();
					}
					
				}
				
				$data["minimal_price"]			= $minimalPrice;
				$data["associated_products"] 	= $assoc;
			}
			
			return json_encode($data);
		}
		
		return false;
	}
	
	/**
	 * Filters for product ids
	 * 
	 * @param array $tags Array with tags to filter
	 * @param array $categories Array with categories to filter
	 * @param array $skus Array with skus to filter
	 * @param string $store Store View Code
	 * @return array Array of filtered product ids
	 */
	public function filter($tags = array(), $categories = array(), $skus = array(), $store = null)
	{
		$storeId = $this->_getStoreId($storeCode);
		
		// Product Ids from Tags
		$productIdsTag = array();
		// Product Ids from Categories
		$productIdsCategory = array();
		// Product Ids from Skus
		$productIdsSkus = array();
		// Final product Ids
		$productIds = array();
		
		
		// Product Ids by Tag
		$tagIds = array();
		foreach ($tags as $_tag)
		{
			$tagIds = $this->getProductIdsByTag($_tag, $storeId);
			$productIdsTag = array_merge($productIdsTag, $tagIds);
		}

		// Product Ids by Category
		foreach ($categories as $_cat)
		{
			if ($_cat != "")
			{
				$category = Mage::getModel("catalog/category")
								->setStoreId( $storeId )
						 		->loadByAttribute("name", $_cat);
						 
				
				if ($category && $category->getId())
				{
					// We want the product ids of the category
					$collection = $category->getProductCollection();
					
					foreach ($collection as $_product) {
						$productIdsCategory[] = $_product->getId();
					}
				}
				
			}
			
		}
		
		// Product Ids by Sku
		$productIdsSkus = $this->getProductIdsBySkus($skus, $storeId);
		
		$tagsCount = (count($tags)>0)?true:false;
		$catCount = (count($categories)>0)?true:false;
		$skuCount = (count($skus)>0)?true:false;
		
		
		if ($tagsCount && !$catCount )
		{
			// Tags only filter
			$productIds = $productIdsTag;
		} 
		else if (!$tagsCount && $catCount)
		{
			// Categories only filter
			$productIds = $productIdsCategory;
		}
		else 
		{
			// Tags and categories
			// When we have tags and categories we need to compute 
			// in which product ids are same
			$productIds = array_intersect($productIdsTag, $productIdsCategory);
			
		}
		
		// Only from skus
		if (empty($productIds))
		{
			
			$productIds = $productIdsSkus;
		}
		else
		{
			$productIds = array_merge($productIds, $productIdsSkus);
		}
		
		
		/*
		Mage::log("____________STORE ID_______________");
		Mage::log($storeId);
		
		Mage::log("____________TAGS_______________");
		Mage::log($tags);
		
		Mage::log("____________CATEGORIES_______________");
		Mage::log($categories);
		
		Mage::log("____________TAGS IDS_______________");
		Mage::log($productIdsTag);
		
		Mage::log("____________CATEGORIES IDS_______________");
		Mage::log($productIdsCategory);
		
		Mage::log("____________FINAL IDS_______________");
		Mage::log($productIds);
		*/
		
		return $productIds;
	}

    /**
     * Retrieve list of products with basic info (id, sku, type, set, name)
     *
     * @param null|object|array $filters
     * @param string|int $store
     * @return array
     */
    public function items($filters = null, $store = null)
    {
    	$storeId = $this->_getStoreId($store);
		
        $collection = Mage::getModel('catalog/product')
        	->setStoreId($storeId)
        	->getCollection()
            ->addStoreFilter($storeId)
            ->addAttributeToSelect('name');
			
			
        /** @var $apiHelper Mage_Api_Helper_Data */
        $apiHelper = Mage::helper('api');
        $filters = $apiHelper->parseFilters($filters, $this->_filtersMap);
        try 
        {
            foreach ($filters as $field => $value) 
            {
                $collection->addFieldToFilter($field, $value);
            }
        } 
        catch (Mage_Core_Exception $e) 
        {
            $this->_fault('filters_invalid', $e->getMessage());
        }
		
        $result = array();
        foreach ($collection as $product) 
        {
            $result[] = array(
                'product_id' => $product->getId(),
                'sku'        => $product->getSku(),
                'name'       => $product->getName(),
                'type'       => $product->getTypeId(),
            );
        }
		
        return json_encode($result);
    }

	/**
	 * Get a product collection by given tag string
	 * 
	 * @param string $tagString Tag String
	 * @return array
	 */
	public function getProductIdsByTag($tagString, $storeId)
	{
		$tagByName = Mage::getModel("tag/tag")->loadByName($tagString);
		$tag = null;
		
		if ($tagByName->getId())
		{
			$tag = Mage::getModel("tag/tag")->load($tagByName->getId());
		}
		
		if (!$tag || !$tag->getId() || !$tag->isAvailableInStore($storeId))
		{
			return array();
		}
		
		return $tag->getRelatedProductIds();
	}


	/**
	 * Gets product ids by given skus
	 * 
	 * @param array $skus
	 * @return array
	 */
	public function getProductIdsBySkus(array $skus)
	{
		$productIds = array();
		
		$collection = Mage::getModel("catalog/product")
						->getCollection()
						->addAttributeToSelect(array("id"))
						->addFieldToFilter("sku", array("in" => $skus))
						->load();
		
		foreach ($collection as $_item)
		{
			$productIds[] = $_item->getId();
		}
		
		return $productIds;
	}




	/**
	 * Gets a config options from a product
	 * 
	 * @param int $productId Id of the product
	 * @return string
	 */	
	public function getConfigByProduct($productId, $store = null, $identifierType = null)
	{
		$currentProduct 	= $this->_getProduct($productId, $store, $identifierType);
		
		$allowedAttributes 	= $currentProduct->getTypeInstance(true)
            								 ->getConfigurableAttributes($currentProduct);
		
        $attributes = array();
        $options    = array();
		
        $preconfiguredFlag = $currentProduct->hasPreconfiguredValues();
        if ($preconfiguredFlag) {
            $preconfiguredValues = $currentProduct->getPreconfiguredValues();
            $defaultValues       = array();
        }

		$allowProducts = $this->_getAllowProducts($currentProduct);
		
		
        foreach ($allowProducts as $product) 
        {
            $productId  = $product->getId();

            foreach ($allowedAttributes as $attribute) 
            {
                $productAttribute   = $attribute->getProductAttribute();
                $productAttributeId = $productAttribute->getId();
                $attributeValue     = $product->getData($productAttribute->getAttributeCode());
                
                if (!isset($options[$productAttributeId])) 
                {
                    $options[$productAttributeId] = array();
                }

                if (!isset($options[$productAttributeId][$attributeValue])) 
                {
                    $options[$productAttributeId][$attributeValue] = array();
                }
                
                $options[$productAttributeId][$attributeValue][] = $productId;
            }
            
        }


        foreach ($allowedAttributes as $attribute) {

            $productAttribute = $attribute->getProductAttribute();
            $attributeId = $productAttribute->getId();
            
            $info = array(
               'id'        => $productAttribute->getId(),
               'code'      => $productAttribute->getAttributeCode(),
               'label'     => $attribute->getLabel(),
               'options'   => array()
            );


            $optionPrices = array();
            $prices = $attribute->getPrices();
            if (is_array($prices)) {
                foreach ($prices as $value) {
                    if(!$this->_validateAttributeValue($attributeId, $value, $options)) {
                        continue;
                    }
					
                    $currentProduct->setConfigurablePrice(
                        $this->_preparePrice($currentProduct, $value['pricing_value'], $value['is_percent'])
                    );
                    $currentProduct->setParentId(true);
                   
                    $configurablePrice = $currentProduct->getConfigurablePrice();
					
                    if (isset($options[$attributeId][$value['value_index']])) {
                        $productsIndex = $options[$attributeId][$value['value_index']];
                    } else {
                        $productsIndex = array();
                    }

                    $info['options'][] = array(
                        'id'        => $value['value_index'],
                        'label'     => $value['label'],
                        'price'     => $configurablePrice,
                        'oldPrice'  => $this->_prepareOldPrice($currentProduct, $value['pricing_value'], $value['is_percent']),
                        'products'  => $productsIndex,
                    );
                    $optionPrices[] = $configurablePrice;
                }
            }
            
            /**
             * Prepare formated values for options choose
             */
            foreach ($optionPrices as $optionPrice) {
                foreach ($optionPrices as $additional) {
                    $this->_preparePrice($currentProduct, abs($additional-$optionPrice));
                }
            }
            if($this->_validateAttributeInfo($info)) {
               $attributes[$attributeId] = $info;
            }

            // Add attribute default value (if set)
            if ($preconfiguredFlag) {
                $configValue = $preconfiguredValues->getData('super_attribute/' . $attributeId);
                if ($configValue) {
                    $defaultValues[$attributeId] = $configValue;
                }
            }
        }

		$prices = array(
		    'basePrice'         => $this->_registerJsPrice($currentProduct->getFinalPrice()),
            'oldPrice'          => $this->_registerJsPrice($currentProduct->getPrice()),
		);

        $config = array(
            'attributes'        => $attributes,
			'prices'			=> $prices,
            'productId'         => $currentProduct->getId(),
            'chooseText'        => Mage::helper('catalog')->__('Choose an Option...'),
        );

        if ($preconfiguredFlag && !empty($defaultValues)) {
            $config['defaultValues'] = $defaultValues;
        }

		return $config;
	}	


    /**
     * Validating of super product option value
     *
     * @param array $attributeId
     * @param array $value
     * @param array $options
     * @return boolean
     */
    protected function _validateAttributeValue($attributeId, &$value, &$options)
    {
        if(isset($options[$attributeId][$value['value_index']])) {
        	
            return true;
        }

        return false;
    }

    /**
     * Validation of super product option
     *
     * @param array $info
     * @return boolean
     */
    protected function _validateAttributeInfo(&$info)
    {
        if(count($info['options']) > 0) {
            return true;
        }
        return false;
    }
	
    /**
     * Replace ',' on '.' for js
     *
     * @param float $price
     * @return string
     */
    protected function _registerJsPrice($price)
    {
        return str_replace(',', '.', $price);
    }

	
    /**
     * Calculation real price
     *
     * @param float $price
     * @param bool $isPercent
     * @return mixed
     */
    protected function _preparePrice(Mage_Catalog_Model_Product $product, $price, $isPercent = false)
    {
        if ($isPercent && !empty($price)) {
            $price = $product->getFinalPrice() * $price / 100;
        }

        return $this->_registerJsPrice($price, true);
    }

    /**
     * Calculation price before special price
     *
     * @param float $price
     * @param bool $isPercent
     * @return mixed
     */
    protected function _prepareOldPrice(Mage_Catalog_Model_Product $product, $price, $isPercent = false)
    {
        if ($isPercent && !empty($price)) {
            $price = $product->getPrice() * $price / 100;
        }

        return $this->_registerJsPrice($price, true);
    }

    /**
     * Get Allowed Products
     *
     * @return array
     */
    protected function _getAllowProducts(Mage_Catalog_Model_Product $product)
    {
		$products = array();
		
		$skipSaleableCheck = Mage::helper('catalog/product')->getSkipSaleableCheck();
		
		$allProducts = $product->getTypeInstance(true)
                			   ->getUsedProducts(null, $product);
                			   
		foreach ($allProducts as $_product) {
			//if ($_product->isAvailable()) {
				$products[] = $_product;
			//}
		}
        
        return $products;
    }
	



}