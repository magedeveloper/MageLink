<?php
namespace MageDeveloper\Magelink\ViewHelpers\Product;

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
class RepositoryByIdsViewHelper extends \TYPO3\CMS\Fluid\Core\ViewHelper\AbstractViewHelper
{
	/**
	 * Product Import Model
	 *
	 * @var \MageDeveloper\Magelink\Import\ProductImport
	 * @inject
	 */
	protected $productImport;

	/**
	 * Fetch product attribute
	 *
	 * @param \string $ids Magento Product Ids
	 * @param \string $store Magento Store View
	 * @param \bool $onlyEnabled Get only enabled products
	 * @param \bool $refreshAll Refresh all products
	 * @param \string $as Identifier
	 * @validate $ids StringValidator
	 * @validate $storeView StringValidator
	 * @return string Content
	 */
	public function render($ids, $store = "", $refreshAll = false, $onlyEnabled = true, $as = "products")
	{
		$divided = \MageDeveloper\Magelink\Utility\Helper::explodeString($ids);
		
		$repository = $this->productImport->getProductRepositoryByIds($divided, $store, (bool)$refreshAll, $onlyEnabled);
		
		$this->templateVariableContainer->add($as, $repository);
		
		return $repository;
	}

}