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
class AttributeViewHelper extends \TYPO3\CMS\Fluid\Core\ViewHelper\AbstractViewHelper
{
	/**
	 * Renders a specific product attribute
	 * 
	 * @param \MageDeveloper\Magelink\Domain\Model\Product $product
	 * @param \string $attribute Code of the Attribute
	 * @param \string $path Array Path to go deeper
	 * @return \string Attribute Value
	 */
	public function render(\MageDeveloper\Magelink\Domain\Model\Product $product, $attribute, $path = null)
	{
		$attributeData = $product->getAttributeValue($attribute);
		
		if ($path !== null)
		{
			return \MageDeveloper\Magelink\Utility\Helper::getArrayValueByPath($attributeData, $path);
		}
		
		return $attributeData;
	}
	
	
	
	
}