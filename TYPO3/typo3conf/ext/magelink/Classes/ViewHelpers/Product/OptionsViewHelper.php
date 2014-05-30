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
class OptionsViewHelper extends \TYPO3\CMS\Fluid\Core\ViewHelper\AbstractViewHelper
{
	/**
	 * Renders product options
	 *
	 * @param \MageDeveloper\Magelink\Domain\Model\Product $product
	 * @param \string $decSep Decimal Separator
	 * @return \string Options Html
	 */
	public function render(\MageDeveloper\Magelink\Domain\Model\Product $product, $decSep = ",")
	{
		$html = "";
		
		if ($product->getType() == \MageDeveloper\Magelink\Domain\Model\Product::TYPE_CONFIGURABLE)
		{
			$options = array();
			$options["options"] 						= $product->getOptions();
			$options["options"]["prices"]["currency"]	= $product->getCurrency();
			$options["options"]["prices"]["decSep"]		= $decSep;
			
			foreach($options["options"]["attributes"] as $_id=>$_attribute)
			{
				$selectName = "super_attribute[{$_id}]";
				$selectId = "attribute{$_id}";
				
				$html .= "<div class=\"option_field\">";
				$html .= "<label for=\"{$selectName}\">".$_attribute["label"]."<em>*</em></label>";
				$html .= "<select name=\"{$selectName}\" id=\"{$selectId}\" class=\"required-entry super-attribute-select\">";
				$html .= "<option value=\"\"></option>";
				$html .= "</select>";
				$html .= "</div>";
			
			}

			$json = json_encode($options["options"]);
			
			// JSON Configuration for Javascript Change
			$html .= "<script type=\"text/javascript\">";
			$html .= "var spConfig = new ProductConfiguration({$json});";
			$html .= "</script>";
			
		}
		
		return $html;
	}


}