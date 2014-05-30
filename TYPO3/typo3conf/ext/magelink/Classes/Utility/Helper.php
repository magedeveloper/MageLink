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

class Helper
{
	/**
	 * Possible string dividers
	 * @var \array
	 */
	protected static $dividers = array(
		";",
		"/",
		".",
		"\\",
		":",
		"-",
	);


	/**
	 * Gets the memory usage as a readable string
	 * 
	 * @return string
	 */
	public static function getMemoryUsage() 
	{
		$usage = memory_get_usage(true);
		$str = "";

		if ($usage < 1024)
		{
			$str = $usage." bytes";
		}
		elseif ($usage < 1048576)
		{
			$str = round($usage/1024,2)." kB";
		}
		else
		{
			$str = round($usage/1048576,2)." MB";
		}
		
		return $str;
	}

	/**
	 * Explodes an string with known dividers
	 * 
	 * @param \string $string String to divide
	 * @return \array
	 */
	public static function explodeString($string)
	{
		if ($string)
		{
			// Check that the divider of the data is comma separation
			foreach (self::$dividers as $divider) 
			{
				$string = str_replace($divider, ",", $string);
			}
			
			return explode(",", $string);
		}
		
		return array();
	}

	/**
	 * Gets an array value by a given path
	 * 
	 * @param \array $array Array to search
	 * @param \string $path Path for array
	 * @return mixed 
	 */
	public static function getArrayValueByPath(array $array, $path)
	{
		$divided = self::explodeString($path);
		
		$func = function($arr, $k) {
			return $arr[$k];
		};
		
		$newArr = $array;
		foreach ($divided as $_key)
		{
			$newArr = $func($newArr, $_key);
		}
		
		return $newArr;
	}
	
	
	

}