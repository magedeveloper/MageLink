<?php
namespace MageDeveloper\Magelink\Utility;

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

/**
 *
 *
 * @package magelink
 * @license http://www.gnu.org/licenses/gpl.html GNU General Public License, version 3 or later
 *
 */

class FilterString 
{
	/**
	 * Possible string dividers
	 * 
	 * @var array
	 */
	protected static $dividers = array(
		';',
		'/',
		'.',
		"\\",
		":",
		"-"
	);
	
	
	/**
	 * Gets exploded and trimmed values by
	 * a separated string
	 * 
	 * @param string $string String to separate
	 * @return array
	 */
	public static function getExplodedValues($string)
	{
		if (strlen($string))
		{
			// Check that the divider of the ids is comma separation
			foreach (self::$dividers as $divider) {
				$string = str_replace($divider, ',', $string);
			}
			
			$exploded 		= array_map('trim',explode(",",$string));
	
			return $exploded;
		}

		return array();
	}	
		
		
		
		
		
}