<?php
namespace MageDeveloper\Magelink\Api\Requests;


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
 * API Category Calls
 * @package magelink
 * @license http://www.gnu.org/licenses/gpl.html GNU General Public License, version 3 or later
 *
 */
class Category extends \MageDeveloper\Magelink\Api\Requests\AbstractRequest
{
	/**
	 * Category List Cache
	 * @var array
	 */
	protected $temp_list;

	/**
	 * Get a full category list
	 * 
	 * @param string $storeViewCode Store View Code
	 * @throws \Exception
	 * @return array
	 */
	public function getCategoryList($storeViewCode = "")
	{
		if ($this->temp_list)
		{
			return $this->temp_list;
		}
		
		try 
		{
			if ( $this->getApiClient()->connect() )
			{
				$data = $this->getApiClient()->getResource()->magelinkCategoryFetch(
					$this->getApiClient()->getSessionId(), 	// Session Id
					$storeViewCode 							// Store View Code
				);
				
				if ($data)
				{
					$data = json_decode($data, true);

					$this->temp_list = $data;
					return $this->temp_list;
				}
			}	
			
		}
		catch (\Exception $e)
		{
			throw new \Exception ("Could not retrieve category list! Error: " . $e->getMessage());
		}
		
		return false;
	}	
	
	/**
	 * Get a specified category by entity id
	 * 
	 * @param int $entityId Category Entity Id
	 * @param string $storeViewCode Store View Code
	 * @return array|false
	 */
	public function getCategoryById($entityId, $storeViewCode = "")
	{
		$list = $this->getCategoryList($storeViewCode);
		
		foreach ($list as $_category)
		{
			if ($_category["entity_id"] == $entityId)
			{
				$_category["store_view_code"] = $storeViewCode;
				return $_category;
			}
		}
	
		return false;
	}
	
		
}
	