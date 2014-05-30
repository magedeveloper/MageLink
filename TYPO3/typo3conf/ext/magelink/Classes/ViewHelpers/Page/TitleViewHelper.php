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
class TitleViewHelper extends \TYPO3\CMS\Fluid\Core\ViewHelper\AbstractViewHelper
{
	const PREPEND_TITLE 	= "prepend";
	const APPEND_TITLE 		= "append";
	const REPLACE_TITLE		= "replace";

	/**
	 * Set the page title
	 *
	 * @param \string $mode Mode for adding the title to the existing one or new one
	 * @param \string $glue The glue to add the custom title
	 * @return void
	 */
	public function render($mode = self::REPLACE_TITLE, $glue = " - ")
	{
		$renderedContent = $this->renderChildren();
		
		$existingTitle = $GLOBALS['TSFE']->page['title'];
    
		if ($mode === self::PREPEND_TITLE && !empty($existingTitle)) 
		{
			$newTitle = $renderedContent.$glue.$existingTitle;
		} 
		else if ($mode === self::APPEND_TITLE && !empty($existingTitle)) 
		{
			$newTitle = $existingTitle.$glue.$renderedContent;
        } 
        else 
        {
			$newTitle = $renderedContent;
		}
		
		$GLOBALS['TSFE']->page['title'] = $newTitle;
		$GLOBALS['TSFE']->indexedDocTitle = $newTitle;

		return;
	}
	
}