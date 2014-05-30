<?php
namespace MageDeveloper\Magelink\ViewHelpers\Magento\Customer;

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
class CollectionViewHelper extends \MageDeveloper\Magelink\ViewHelpers\Magento\AbstractMagentoViewHelper
{
	/**
	 * Initialize arguments.
	 *
	 * @return void
	 * @api
	 */
	public function initializeArguments()
	{
		parent::initializeArguments();
		$this->registerArgument("addAttributeToFilter", "array", "Method Params for 'addAttributeToFilter'", false, array());
		$this->registerArgument("addFieldToFilter", "array", "Method Params for 'addFieldToFilter'", false, array());

		$this->registerArgument("cache", "bool", "Caches Product Data", false, true);
		$this->registerArgument("as", "string", "Collection Alias for Template", false, "collection");
	}
	
	/**
	 * Gets a product
	 * 
	 * @return \string Attribute Value
	 */
	public function render()
	{
		$cacheIdentifier = "customer_collection_" .
			md5(implode("-", $this->arguments["addAttributeToFilter"])) .
			md5(implode("-", $this->arguments["addFieldToFilter"]))
		;

		// Check if product is in cache, and caching can be used
		if ((FALSE === ($customerArr = $this->cacheService->get($cacheIdentifier))) || !$this->arguments["cache"])
		{
			if ($this->magentoCore->init())
			{
				$customerArr = array();
				$customers 		= \Mage::getModel("customer/customer")
					->getCollection()
					->addAttributeToSelect("email");
					
				// addAttributeToFilter
				if (is_array($this->arguments["addAttributeToFilter"]) && !empty($this->arguments["addAttributeToFilter"]))
				{
					foreach ($this->arguments["addAttributeToFilter"] as $_name=>$_value)
					{
						$customers->addAttributeToFilter($_name, $_value);
					}
				}

				// addFieldToFilter
				if (is_array($this->arguments["addFieldToFilter"]) && !empty($this->arguments["addFieldToFilter"]))
				{
					foreach ($this->arguments["addFieldToFilter"] as $_name=>$_value)
					{
						$customers->addFieldToFilter($_name, $_value);
					}
				}

				foreach($customers as $_customer)
				{
					$data = array();
					$customer = \Mage::getModel("magelink/customer_api")->fetch($_customer->getData("email"));
					$data = json_decode($customer, true);
					$customerArr[] = $data;
				}

				$lifetime = $this->settingsService->getCacheLifetime();
				$this->cacheService->set( $cacheIdentifier, $customerArr, array(), $lifetime );
			}

		}

		if ($this->arguments["getFirstItem"])
		{
			$customerArr = reset($customerArr);
		}
		else if ($this->arguments["getLastItem"])
		{
			$customerArr = end($customerArr);
		}

		$output = '';
		$this->templateVariableContainer->add($this->arguments["as"], $customerArr);
		return $output;
	}
	
	
	
	
}