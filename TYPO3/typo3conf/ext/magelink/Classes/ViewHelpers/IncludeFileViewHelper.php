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
class IncludeFileViewHelper extends \TYPO3\CMS\Fluid\Core\ViewHelper\AbstractViewHelper
{
	/**
	 * Include a CSS/JS file
	 *
	 * @param string $path Path to the CSS/JS file which should be included
	 * @param boolean $compress Define if file should be compressed
	 * @return void
	 */
	public function render($path, $compress = FALSE)
	{
		if (TYPO3_MODE === 'FE')
		{
			$path = $GLOBALS['TSFE']->tmpl->getFileName($path);

			// JS
			if (strtolower(substr($path, -3)) === '.js')
			{
				$GLOBALS['TSFE']->getPageRenderer()->addJsFile($path, NULL, $compress);

				// CSS
			} elseif (strtolower(substr($path, -4)) === '.css')
			{
				$GLOBALS['TSFE']->getPageRenderer()->addCssFile($path, 'stylesheet', 'all', '', $compress);
			}
			
		}
		else
		{
			$doc = $this->objectManager->get("TYPO3\\CMS\\Backend\\Template\\DocumentTemplate");
			$pageRenderer = $doc->getPageRenderer();

			// JS
			if (strtolower(substr($path, -3)) === '.js') 
			{
				$pageRenderer->addJsFile($path, NULL, $compress);

				// CSS
			} 
			elseif (strtolower(substr($path, -4)) === '.css') 
			{
				$pageRenderer->addCssFile($path, 'stylesheet', 'all', '', $compress);
			}
			
		}
		
	}
	



}