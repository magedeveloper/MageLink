<?php
namespace MageDeveloper\Magelink\ViewHelpers\Page;

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
class JsTranslationViewHelper extends \TYPO3\CMS\Fluid\Core\ViewHelper\AbstractViewHelper
{
	/**
	 * Set translations as javascript
	 *
	 * @param \string $keys Keys to translate. Separated by comma
	 * @param \string $extension Extension Name
	 * @return void
	 */
	public function render($keys, $extension = "Magelink")
	{
		$divided = \MageDeveloper\Magelink\Utility\FilterString::getExplodedValues($keys);
		
		$langKeys = "";
		
		if (is_array($divided) && $divided)
		{
			foreach ($divided as $_langKey)
			{
				$translated = \TYPO3\CMS\Extbase\Utility\LocalizationUtility::translate($_langKey, $extension);
				$translated = trim($translated);
				$langKey 	= trim($_langKey);
				
				$langKeys .= "locallang.set('{$langKey}','{$translated}');"."\n";	
			}
			return "<script type=\"text/javascript\">"."{$langKeys}"."</script>";
		}
		
	}
	
	
}