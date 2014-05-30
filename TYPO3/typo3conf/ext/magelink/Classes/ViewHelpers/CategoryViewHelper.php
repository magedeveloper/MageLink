<?php
namespace MageDeveloper\Magelink\ViewHelpers;

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
class CategoryViewHelper extends \TYPO3\CMS\Fluid\Core\ViewHelper\AbstractViewHelper
{
	/**
	 * Category Import Model
	 *
	 * @var \MageDeveloper\Magelink\Import\CategoryImport
	 * @inject
	 */
	protected $categoryImport;

	/**
	 * Fetch a category by id
	 *
	 * @param \int $id Category Id
	 * @param \string $store Store View Code
	 * @param \string $as Identifier
	 * @internal param bool $refreshAll Refresh Category
	 * @return \string
	 */
	public function render($id, $store = "", $as = "category")
	{
		// Category Model
		$category 					= $this->categoryImport->getCategoryById($id, $store);

		$renderChildrenClosure 		= $this->buildRenderChildrenClosure();
		$renderingContext           = $this->renderingContext;
		$templateVariableContainer  = $renderingContext->getTemplateVariableContainer();

		$output = "";

		$this->templateVariableContainer->add($as, $category);

		$output .= $renderChildrenClosure();

		$this->templateVariableContainer->remove($as);
		
		return $output;
	}








	/**
	 * Fetch product attribute
	 *
	 * @param string $sku Magento Product SKU
	 * @param string $store Magento Store View
	 * @param int $pid Page ID where the products are stored
	 * @param bool $refreshAll Refresh all products
	 * @param bool $enableFilterCache Enable the filter cache
	 * @validate $sku StringValidator
	 * @validate $storeView StringValidator
	 * @validate $pid IntegerValidator
	 * @validate $refreshAll NotEmptyValidator
	 * @validate $enableFilterCache NotEmptyValidator
	 * @return string Content
	 */
	/*public function render($sku, $store = '', $pid, $refreshAll = false, $enableFilterCache = true)
	{
		$renderChildrenClosure      = $this->buildRenderChildrenClosure();
		$renderingContext           = $this->renderingContext;
		$templateVariableContainer  = $renderingContext->getTemplateVariableContainer();

		$productIds = $this->_productClass->getProductIdsByFilter(array(), array(), array($sku), $store, $enableFilterCache);
		$id = reset($productIds);

		$existing = $this->_productClass->getExistingProducts($store, $pid);

		if (!in_array($id, $existing))
		{
			// Need to import that product!	
			$result = $this->_productClass->importProducts(array($id), $store, $pid);
		}

		$products = $this->_productClass->getProductsFromDb(array($id), $store, $pid);

		$product = reset($products);

		$output = "";
		if ($product['product_id'] == $id)
		{
			$attributes = array();
			$attributes = $product['attributes'];
			unset($product['attributes']);
			$attributes = array_merge($attributes, $product);

			foreach ($attributes as $_attr=>$_val) {
				if (!is_array($_val)) {
					$product[$_attr] = $_val;
				}
			}

			// Add variables
			foreach ($product as $_attribute=>$_value)
			{
				$this->templateVariableContainer->add($_attribute, $_value);
			}

			// Generate output
			$output .= $renderChildrenClosure();

			// Remove variables
			foreach ($product as $_attribute=>$_value)
			{
				$this->templateVariableContainer->remove($_attribute);
			}

		}

		return $output;
	}*/
}