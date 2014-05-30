<?php
namespace MageDeveloper\Magelink\Service;

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

class ImageService
{
	/**
	 * Imports an image from a url to
	 * a desired target folder
	 * 
	 * @param \string $src Image Source URL 
	 * @param \string $path Target Path
	 * @param \string $filename Target Filename
	 * @return bool
	 */
	public function importImage($src, $path, $filename)
	{
		$path = self::getCleanPath($path);
		
		if (\TYPO3\CMS\Core\Utility\GeneralUtility::isValidUrl($src))
		{
			$contents = @file_get_contents($src);
			
			if ($contents)
			{
				$result = \TYPO3\CMS\Core\Utility\GeneralUtility::writeFile($path.$filename, $contents);
				return $result;
			}

		}

		return false;
	}

	/**
	 * Gets a cleaned path
	 * 
	 * @param \string $path Path to clean
	 * @return \string
	 */
	public static function getCleanPath($path)
	{
		$path = trim($path, '\\');
		$path = trim($path, '/');
		$path = str_replace('\\', DIRECTORY_SEPARATOR, $path);
		$path = str_replace('/', DIRECTORY_SEPARATOR, $path);
		$path = $path . DIRECTORY_SEPARATOR;
		
		return $path;
	}
	
}